const { Worker } = require("bullmq");
const { FETCH_API_URL, REDIS_CONNECTION } = require("./config");

new Worker(
  "bnb-deposits",
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
      console.log(`✅ Saved ${data.txHash}`);
      return true;
    }

    if (response.status === "ignored") {
      console.log(`⏭️ Ignored ${data.txHash}: ${response.reason}`);
      return true;
    }

    // anything else → retry
    throw new Error(`Unexpected response: ${text}`);
  },
  {
    connection: REDIS_CONNECTION,
    concurrency: 5,
  }
);
