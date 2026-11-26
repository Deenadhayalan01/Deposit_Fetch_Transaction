const { Worker } = require("bullmq");

// --------------------------------------------------
// WORKER CONFIG
// --------------------------------------------------
const REDIS_CONNECTION = { host: "127.0.0.1", port: 6379 };

const API_BASE_URL =
  "http://192.168.1.58/savita/new/savita_admin/public";

const FETCH_API_URL =
  `${API_BASE_URL}/api/get_eth_transaction`;

// --------------------------------------------------
// WORKER
// --------------------------------------------------
new Worker(
  "eth-deposits",
  async (job) => {
    const data = job.data;

    const res = await fetch(FETCH_API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    if (!res.ok) {
      throw new Error(`HTTP ${res.status}`);
    }

    const text = await res.text();
    let response;

    try {
      response = JSON.parse(text);
    } catch {
      throw new Error(`Invalid JSON: ${text}`);
    }

    if (response.status === "ok") {
      console.log(`✅ ETH Saved ${data.txHash}`);
      return true;
    }

    if (response.status === "ignored") {
      console.log(
        `⏭️ ETH Ignored ${data.txHash}: ${response.reason}`
      );
      return true;
    }

    throw new Error(`Unexpected response: ${text}`);
  },
  {
    connection: REDIS_CONNECTION,
    concurrency: 5,
  }
);

console.log("⚙️ ETH Worker Started");
