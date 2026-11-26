"use strict";

const Web3 = require("web3");
const Redis = require("ioredis");
const { Queue } = require("bullmq");

// --------------------------------------------------
// CONFIG
// --------------------------------------------------
const RPC_URL = "https://ethereum.publicnode.com";

const REDIS_CONNECTION = {
  host: "127.0.0.1",
  port: 6379,
};

const LAST_BLOCK_KEY = "eth:last_checked_block";
const TX_LOCK_PREFIX = "eth:tx:";
const CONFIRMATIONS = 12;
const SCAN_INTERVAL_MS = 7000;

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

// --------------------------------------------------
// INIT
// --------------------------------------------------
const web3 = new Web3(RPC_URL);

const redis = new Redis(REDIS_CONNECTION);

const depositQueue = new Queue("eth-deposits", {
  connection: REDIS_CONNECTION,
});

let isScanning = false;

// --------------------------------------------------
// SCANNER LOGIC
// --------------------------------------------------
async function scanETH() {
  if (isScanning) return;
  isScanning = true;

  try {
    const latestBlock = await web3.eth.getBlockNumber();
    const safeBlock = latestBlock - CONFIRMATIONS;

    const lastCheckedRaw = await redis.get(LAST_BLOCK_KEY);
    const lastChecked = lastCheckedRaw
      ? parseInt(lastCheckedRaw, 10)
      : safeBlock - 5;

    const startBlock = lastChecked + 1;
    if (startBlock > safeBlock) {
      isScanning = false;
      return;
    }

    console.log(`🔍 ETH scanning ${startBlock} → ${safeBlock}`);

    for (let blockNumber = startBlock; blockNumber <= safeBlock; blockNumber++) {
      const block = await web3.eth.getBlock(blockNumber, true);

      if (!block || !block.transactions || block.transactions.length === 0) {
        continue;
      }

      for (const tx of block.transactions) {
        if (!tx.to || tx.value === "0") continue;

        const to = tx.to.toLowerCase();

        // --------------------------------------------------
        // TX DEDUPLICATION LOCK
        // --------------------------------------------------
        const lockKey = TX_LOCK_PREFIX + tx.hash;
        const locked = await redis.set(lockKey, "1", "NX", "EX", 86400);
        if (!locked) continue;

        // --------------------------------------------------
        // ADDRESS FILTER
        // --------------------------------------------------
        const isMonitored = await redis.sismember(
          "eth_user_addresses",
          to
        );

        if (!isMonitored) continue;

        const payload = {
          chain: "ETH",
          txHash: tx.hash,
          from: tx.from,
          to: tx.to,
          amount: web3.utils.fromWei(tx.value, "ether"),
          blockNumber: blockNumber,
          datetime: new Date(
            Number(block.timestamp) * 1000
          ).toISOString(),
        };

        console.log(
          `🔥 ETH deposit ${payload.amount} → ${payload.to}`
        );

        await depositQueue.add("deposit", payload, {
          jobId: tx.hash,
          attempts: 5,
          backoff: { type: "exponential", delay: 4000 },
          removeOnComplete: true,
        });
      }
    }

    await redis.set(LAST_BLOCK_KEY, safeBlock);
  } catch (err) {
    console.error("❌ ETH Scanner Error:", err.message);
  } finally {
    isScanning = false;
  }
}

// --------------------------------------------------
// START
// --------------------------------------------------
console.log("🚀 ETH Scanner Started (web3@1.10.0)");

scanETH();
setInterval(scanETH, SCAN_INTERVAL_MS);
