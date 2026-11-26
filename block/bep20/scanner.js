const { ethers } = require("ethers");
const Redis = require("ioredis");
const { Queue } = require("bullmq");

// --------------------------------------------------
// CONFIG
// --------------------------------------------------
const HTTP_URL = "https://bsc-rpc.publicnode.com";
const REDIS_CONNECTION = { host: "127.0.0.1", port: 6379 };

const BLOCK_BATCH = 150;
const TOKEN_BATCH = 5;
const CONFIRMATIONS = 3;
const SCAN_INTERVAL_MS = 3000;

const LAST_BLOCK_PREFIX = "bep20:token:";
const TX_LOCK_PREFIX = "bep20:tx:";

// --------------------------------------------------
// INIT
// --------------------------------------------------
const provider = new ethers.JsonRpcProvider(HTTP_URL);
const redis = new Redis(REDIS_CONNECTION);

const depositQueue = new Queue("bep20-deposits", {
  connection: REDIS_CONNECTION
});

// --------------------------------------------------
const TRANSFER_TOPIC = ethers.id(
  "Transfer(address,address,uint256)"
);

const ERC20_IFACE = new ethers.Interface([
  "event Transfer(address indexed from, address indexed to, uint256 value)"
]);

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

let scanning = false;

// --------------------------------------------------
function chunkArray(arr, size) {
  const out = [];
  for (let i = 0; i < arr.length; i += size) {
    out.push(arr.slice(i, i + size));
  }
  return out;
}

// --------------------------------------------------
async function scanBEP20() {
  if (scanning) return;
  scanning = true;

  try {
    const latestBlock = await provider.getBlockNumber();
    const safeBlock = latestBlock - CONFIRMATIONS;

    const users = new Set(
      (await redis.smembers("bep20_user_addresses"))
        .map(a => a.toLowerCase())
    );

    const tokens = (await redis.smembers("bep20_contracts"))
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

        console.log(`🔍 Scanning ${fromBlock} → ${toBlock}`);

        const logs = await provider.getLogs({
          address: tokenBatch,
          topics: [TRANSFER_TOPIC],
          fromBlock,
          toBlock
        });

        for (const log of logs) {
          const parsed = ERC20_IFACE.parseLog(log);
          const to = parsed.args.to.toLowerCase();
          const value = parsed.args.value;

          if (!users.has(to) || value === 0n) continue;

          const lockKey = TX_LOCK_PREFIX + log.transactionHash;
          const locked = await redis.set(lockKey, 1, "NX", "EX", 86400);
          if (!locked) continue;

          if (!blockTimeCache.has(log.blockNumber)) {
            const block = await provider.getBlock(log.blockNumber);
            blockTimeCache.set(
              log.blockNumber,
              new Date(block.timestamp * 1000).toISOString()
            );
          }

          const payload = {
            txHash: log.transactionHash,
            tokenAddress: log.address,
            from: ethers.getAddress(
              ethers.dataSlice(log.topics[1], 12)
            ),
            to,
            amount: ethers.formatEther(value),
            blockNumber: log.blockNumber,
            datetime: blockTimeCache.get(log.blockNumber)
          };

          console.log(`🔥 DETECTED`, payload);

          await depositQueue.add("deposit", payload, {
            jobId: log.transactionHash + log.address,
            removeOnComplete: true
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
    console.error("❌ Scanner Error:", err);
  } finally {
    scanning = false;
  }
}

// --------------------------------------------------
console.log("🚀 BEP20 Scanner Started");
scanBEP20();
setInterval(scanBEP20, SCAN_INTERVAL_MS);
