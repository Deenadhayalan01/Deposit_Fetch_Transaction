const { ethers } = require("ethers");
const Redis = require("ioredis");
const { Queue } = require("bullmq");

// --------------------------------------------------
// CONFIG
// --------------------------------------------------
const HTTP_URL = "https://bsc-rpc.publicnode.com";
const REDIS_CONNECTION = { host: "127.0.0.1", port: 6379 };

const LAST_BLOCK_KEY = "bnb:last_checked_block_bnb";
const TX_LOCK_PREFIX = "bsc:bnb:tx:";
const CONFIRMATIONS = 3;
const SCAN_INTERVAL_MS = 5000;

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

// --------------------------------------------------
// INIT
// --------------------------------------------------
const provider = new ethers.JsonRpcProvider(HTTP_URL);
const redis = new Redis(REDIS_CONNECTION);
const depositQueue = new Queue("bnb-deposits", { connection: REDIS_CONNECTION });

let isScanning = false;

// --------------------------------------------------
// SCANNER
// --------------------------------------------------
async function scanBNB() {
  if (isScanning) return;
  isScanning = true;

  try {
    const latestBlock = await provider.getBlockNumber();
    const safeBlock = latestBlock - CONFIRMATIONS;

    const lastChecked =
      parseInt(await redis.get(LAST_BLOCK_KEY)) || safeBlock - 5;

    const startBlock = lastChecked + 1;
    if (startBlock > safeBlock) return;

    console.log(`🔍 BNB Scanning ${startBlock} → ${safeBlock}`);

    for (let blockNumber = startBlock; blockNumber <= safeBlock; blockNumber++) {
      const block = await provider.getBlock(blockNumber, true);
      if (!block?.prefetchedTransactions?.length) continue;

      for (const tx of block.prefetchedTransactions) {
        if (!tx.to || tx.value === 0n) continue;

        const to = tx.to.toLowerCase();

        // 🔐 TX-level lock
        const lockKey = TX_LOCK_PREFIX + tx.hash;
        const locked = await redis.set(lockKey, 1, "NX", "EX", 86400);
        if (!locked) continue;

        const isMonitored = await redis.sismember("bnb_user_addresses", to);
        if (!isMonitored) continue;

        const payload = {
          txHash: tx.hash,
          from: tx.from,
          to: tx.to,
          amount: ethers.formatEther(tx.value),
          blockNumber,
          datetime: new Date(block.timestamp * 1000).toISOString(),
        };

        console.log(`🔥 BNB Deposit Match: ${payload.amount} → ${payload.to}`);

        await depositQueue.add("deposit", payload, {
          jobId: tx.hash,
          attempts: 5,
          backoff: { type: "exponential", delay: 3000 },
          removeOnComplete: true,
        });
      }
    }

    // ✅ Save progress after scan
    await redis.set(LAST_BLOCK_KEY, safeBlock);
  } catch (err) {
    console.error("❌ Scanner Error:", err.message);
  } finally {
    isScanning = false;
  }
}

// --------------------------------------------------
// START
// --------------------------------------------------
console.log("🚀 BNB Scanner Started");

scanBNB();
setInterval(scanBNB, SCAN_INTERVAL_MS);
