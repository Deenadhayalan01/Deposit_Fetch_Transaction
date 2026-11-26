"use strict";

const Web3 = require("web3");
const Redis = require("ioredis");
const { Queue } = require("bullmq");

// --------------------------------------------------
// CONFIG (ERC20)
// --------------------------------------------------
const RPC_URL = "https://ethereum.publicnode.com";
const REDIS_CONNECTION = { host: "127.0.0.1", port: 6379 };

const BLOCK_BATCH = 150;
const TOKEN_BATCH = 5;
const CONFIRMATIONS = 12;
const SCAN_INTERVAL_MS = 5000;

const LAST_BLOCK_PREFIX = "erc20:token:";
const TX_LOCK_PREFIX = "erc20:tx:";

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

// --------------------------------------------------
// INIT
// --------------------------------------------------
const web3 = new Web3(RPC_URL);
const redis = new Redis(REDIS_CONNECTION);

const depositQueue = new Queue("erc20-deposits", {
  connection: REDIS_CONNECTION,
});

// --------------------------------------------------
// CONSTANTS
// --------------------------------------------------
const TRANSFER_TOPIC = web3.utils.sha3(
  "Transfer(address,address,uint256)"
);

const ERC20_ABI = [
  {
    anonymous: false,
    inputs: [
      { indexed: true, name: "from", type: "address" },
      { indexed: true, name: "to", type: "address" },
      { indexed: false, name: "value", type: "uint256" },
    ],
    name: "Transfer",
    type: "event",
  },
  {
    constant: true,
    inputs: [],
    name: "decimals",
    outputs: [{ name: "", type: "uint8" }],
    type: "function",
  },
];

let scanning = false;

// --------------------------------------------------
// HELPERS
// --------------------------------------------------
function chunkArray(arr, size) {
  const out = [];
  for (let i = 0; i < arr.length; i += size) {
    out.push(arr.slice(i, i + size));
  }
  return out;
}

async function getTokenDecimals(token) {
  const cacheKey = `erc20:decimals:${token}`;
  const cached = await redis.get(cacheKey);
  if (cached) return Number(cached);

  try {
    const contract = new web3.eth.Contract(ERC20_ABI, token);
    const decimals = Number(await contract.methods.decimals().call());
    await redis.set(cacheKey, decimals, "EX", 86400);
    return decimals;
  } catch {
    return 18; // safe fallback
  }
}

// --------------------------------------------------
// SCANNER
// --------------------------------------------------
async function scanERC20() {
  if (scanning) return;
  scanning = true;

  try {
    const latestBlock = await web3.eth.getBlockNumber();
    const safeBlock = latestBlock - CONFIRMATIONS;

    const users = new Set(
      (await redis.smembers("erc20_user_addresses"))
        .map(a => a.toLowerCase())
    );

    const tokens = (await redis.smembers("erc20_contracts"))
      .map(t => t.toLowerCase());

    if (!users.size || !tokens.length) return;

    const tokenChunks = chunkArray(tokens, TOKEN_BATCH);
    const blockTimeCache = new Map();

    for (const tokenBatch of tokenChunks) {
      let minLastBlock = safeBlock - 20;

      for (const token of tokenBatch) {
        const last = await redis.get(
          `${LAST_BLOCK_PREFIX}${token}:last_block`
        );
        if (last) minLastBlock = Math.min(minLastBlock, Number(last));
      }

      for (
        let fromBlock = minLastBlock + 1;
        fromBlock <= safeBlock;
        fromBlock += BLOCK_BATCH
      ) {
        const toBlock = Math.min(fromBlock + BLOCK_BATCH - 1, safeBlock);

        console.log(`🔍 ERC20 ${fromBlock} → ${toBlock}`);

        const logs = await web3.eth.getPastLogs({
          address: tokenBatch,
          topics: [TRANSFER_TOPIC],
          fromBlock,
          toBlock,
        });

        for (const log of logs) {
          const decoded = web3.eth.abi.decodeLog(
            ERC20_ABI[0].inputs,
            log.data,
            log.topics.slice(1)
          );

          const to = decoded.to.toLowerCase();
          const value = web3.utils.toBN(decoded.value);

          if (!users.has(to) || value.isZero()) continue;

          const lockKey = TX_LOCK_PREFIX + log.transactionHash;
          const locked = await redis.set(lockKey, 1, "NX", "EX", 86400);
          if (!locked) continue;

          if (!blockTimeCache.has(log.blockNumber)) {
            const block = await web3.eth.getBlock(log.blockNumber);
            blockTimeCache.set(
              log.blockNumber,
              new Date(block.timestamp * 1000).toISOString()
            );
          }

          const decimals = await getTokenDecimals(log.address);

          const payload = {
            txHash: log.transactionHash,
            tokenAddress: log.address,
            from: decoded.from,
            to,
            amount: web3.utils.fromWei(
              value.mul(web3.utils.toBN(10).pow(
                web3.utils.toBN(18 - decimals)
              )),
              "ether"
            ),
            blockNumber: log.blockNumber,
            datetime: blockTimeCache.get(log.blockNumber),
          };

          console.log("🔥 ERC20 DEPOSIT", payload);

          await depositQueue.add("deposit", payload, {
            jobId: log.transactionHash + log.address,
            removeOnComplete: true,
          });

          await redis.set(
            `${LAST_BLOCK_PREFIX}${log.address}:last_block`,
            log.blockNumber
          );
        }

        blockTimeCache.clear();
      }
    }
  } catch (err) {
    console.error("❌ ERC20 Scanner Error:", err.message);
  } finally {
    scanning = false;
  }
}

// --------------------------------------------------
// START
// --------------------------------------------------
console.log("🚀 ERC20 Scanner Started (web3@1.10.0)");

scanERC20();
setInterval(scanERC20, SCAN_INTERVAL_MS);
