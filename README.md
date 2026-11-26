# deposit_redis_nodejs

## 🧠 Architecture

BSC / ETH RPC
  ↓
Block Scanner (recent blocks only)
  ├─ Native BNB transfers / BEP20 Transfer logs / Native ETH transfers / ERC20 Transfer logs
  ├── scanner.js        // monitoring / scanning
        ↓
Redis Dedup Locks       // shared redis + queue
        ↓
BullMQ Queue
  ├── worker.js         // queue consumer / fetching
        ↓
Laravel API
  ├── api function      // save deposit fetched in db


## Installation
Redis must be installed & running

BullMQ and ioredis need Redis server running locally.

Check Redis: 
# command
redis-server --version

If not installed (Ubuntu):
# command
sudo apt install redis-server

Start it:
# command
sudo systemctl start redis

Check status:
# command
sudo systemctl status redis

Install ethers + web3 + redis + bullmq using npm: 
# command
npm install ethers web3 ioredis bullmq


## Usage
📦 What each one does (quick clarity)

    * ethers → Modern Ethereum / EVM interaction library

    * web3 → Older but still widely used EVM library

    * ioredis → Redis client

    * bullmq → Redis-based job queue (built on top of Redis)


## Roadmap
If you have ideas for releases in the future, it is a good idea to list them in the README.


## Contributing
State if you are open to contributions and what your requirements are for accepting them.

For people who want to make changes to your project, it's helpful to have some documentation on how to get started. Perhaps there is a script that they should run or some environment variables that they need to set. Make these steps explicit. These instructions could also be useful to your future self.

You can also document commands to lint the code or run tests. These steps help to ensure high code quality and reduce the likelihood that the changes inadvertently break something. Having instructions for running tests is especially helpful if it requires external setup, such as starting a Selenium server for testing in a browser.


## Authors and acknowledgment
Show your appreciation to those who have contributed to the project.


## License
For open source projects, say how it is licensed.


## Project status
If you have run out of energy or time for your project, put a note at the top of the README saying that development has slowed down or stopped completely. Someone may choose to fork your project or volunteer to step in as a maintainer or owner, allowing your project to keep going. You can also make an explicit request for maintainers.
