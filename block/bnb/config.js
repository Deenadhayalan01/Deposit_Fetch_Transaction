const { ethers } = require("ethers");
const Redis = require("ioredis");
const { Queue } = require("bullmq");

// --- CONFIG ---
const HTTP_URL = "https://bsc-rpc.publicnode.com";
const REDIS_CONNECTION = { host: "127.0.0.1", port: 6379 };

const LAST_BLOCK_KEY = "bnb:last_checked_block_bnb";
const TX_LOCK_PREFIX = "bsc:bnb:tx:";
const CONFIRMATIONS = 3;
const SCAN_INTERVAL_MS = 5000;

const API_BASE_URL = "http://192.168.1.58/savita/new/savita_admin/public";
const FETCH_API_URL = `${API_BASE_URL}/api/get_bnb_transaction`;

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

// --- INIT ---
const provider = new ethers.JsonRpcProvider(HTTP_URL);
const redis = new Redis(REDIS_CONNECTION);
const depositQueue = new Queue("bnb-deposits", { connection: REDIS_CONNECTION });

module.exports = {
  provider,
  redis,
  depositQueue,
  LAST_BLOCK_KEY,
  TX_LOCK_PREFIX,
  CONFIRMATIONS,
  SCAN_INTERVAL_MS,
  FETCH_API_URL,
  REDIS_CONNECTION,
};
