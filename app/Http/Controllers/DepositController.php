<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserBNBAddress;
use App\Models\UserEthAddress;
use App\Models\UserBEP20Address;
use App\Models\UserErc20Address;
use App\Models\CryptoTransactions;
use App\Models\TokenMultinetwork;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


class DepositController extends Controller
{
    //Start - BNB Deposit Transactions
    public function addDemoBNBDepositAddress()
    {
        $useraddress = strtolower("0x4848489f0b2BEdd788c696e2D79b6b69D7484848");
            //"0xFc97258D4c7934F6bCCbEf2688914D1006c1c7B2"
            //"0xb3ec2d5f6B9f0d6ee1D9c1BeB17113254524B08E"
            //"0x3Ba69d352f6C70a41fC76C3Bc5850575386dfA56"
            //"0x6ebe7905f3589e89c7c74bd66d18ec9c2de14f12"
            //"0x4848489f0b2BEdd788c696e2D79b6b69D7484848"

        Redis::sadd('bnb_user_addresses', strtolower($useraddress));

        return 'success - '.$useraddress;
    }

    public function addUserBNBAddressesInRedis()
    {
        // 1. Fetch addresses from DB efficiently
        // We use chunkById to keep memory low if the DB table is massive
        $totalSynced = 0;

        // Clear the list before starting a fresh sync
        Redis::del('bnb_user_addresses');

        UserBNBAddress::orderBy('id')->chunk(5000, function ($users) use (&$totalSynced) {
            $addresses = $users->pluck('address')->map(function($addr) {
                return strtolower(trim($addr));
            })->toArray();

            // SADD in chunks of 1000 for Redis performance
            foreach (array_chunk($addresses, 1000) as $chunk) {
                Redis::sadd('bnb_user_addresses', ...$chunk);
                $totalSynced += count($chunk);
            }
        });

        // 2. Verified Count (Fast O(1) operation)
        $totalInRedis = Redis::scard('bnb_user_addresses');

        echo "<h3>Sync Results</h3>";
        echo "Database Count: $totalSynced <br>";
        echo "Redis Set Count: $totalInRedis <br><hr>";

        // 3. SAFE PREVIEW (Avoid memory crash)
        echo "<h3>Preview (First 100 addresses)</h3>";

        // Instead of SMEMBERS (which pulls all 100k), use SSCAN or array_slice on a limited pull
        // For a simple preview, we can pull just 100 random members
        $preview = Redis::srandmember('bnb_user_addresses', 100);

        if (!empty($preview)) {
            foreach ($preview as $index => $addr) {
                echo ($index + 1) . ". $addr <br>";
            }
        } else {
            echo "No addresses found in Redis.";
        }

        exit;
    }

    public function bnbDepositFetchTransaction()
    {
        try {
            $jsonreceived = file_get_contents("php://input"); 
            Log::info($jsonreceived);
            $data = json_decode($jsonreceived, true);

            if (!$data || !isset($data['txHash'])) {
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'invalid_payload'
                ], 200);
            }

            $txid    = $data['txHash'];
            $confirm = 3;
            $from    = $data['from'] ?? null;
            $to      = $data['to'] ?? null;
            $time    = date('Y-m-d H:i:s', strtotime($data['datetime']));
            $amount  = display_format($data['amount'], 8);

            DB::beginTransaction();

            // 🔒 1️⃣ Check user address with lock
            $user_bnb_data = UserBNBAddress::where('address', $to)
                ->lockForUpdate()
                ->first();

            if (!is_object($user_bnb_data)) {
                DB::commit();
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'address_not_found'
                ], 200);
            }

            $uid = $user_bnb_data->user_id;

            // 🔒 2️⃣ Check if transaction already exists (CRITICAL)
            $exists = CryptoTransactions::where('txid', $txid)->lockForUpdate()->first();

            if ($exists) {
                DB::commit();
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'already_processed',
                    'txHash' => $txid
                ], 200);
            }

            // ✅ 3️⃣ Save transaction (only once)
            CryptoTransactions::createTransaction($uid, 'BNB', $txid, $from, $to, $amount, $confirm, $time, 1, 0, 'BEP2');
            DB::commit();

            return response()->json([
                'status' => 'ok',
                'txHash' => $txid
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error in BNB transactions (publicnode): " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'reason' => 'db_error'
            ], 200);
        }
    }
    //End - BNB Deposit Transactions

    //Start - BEP20 Deposit Transactions
    public function addDemoBEP20DepositAddress()
    {
        $useraddress = strtolower("0x07964f135f276412b3182a3B2407b8dd45000000");
            //"0x0a7e5c7fb4e11393cc44554c90f7c87b02312a36"
            //"0x3ED8Dc546096ACA5A0436541c172F0A5D8220D93"
            //"0xA96Be652A08D9905F15B7FbE2255708709BeCD09"
            //"0x07964f135f276412b3182a3B2407b8dd45000000"

        Redis::sadd('bep20_user_addresses', strtolower($useraddress));

        return 'success - '.$useraddress;
    }

    public function addBEP20ContractAddressesInRedis()
    {
        // 1. Fetch addresses from DB efficiently
        // We use chunkById to keep memory low if the DB table is massive
        $totalSynced = 0;

        // Clear the list before starting a fresh sync
        Redis::del('bep20_contracts');

        TokenMultinetwork::where('network','BEP20')->orderBy('id')->chunk(5000, function ($users) use (&$totalSynced) {
            $addresses = $users->pluck('contractaddress')->map(function($addr) {
                return strtolower(trim($addr));
            })->toArray();

            // SADD in chunks of 1000 for Redis performance
            foreach (array_chunk($addresses, 1000) as $chunk) {
                Redis::sadd('bep20_contracts', ...$chunk);
                $totalSynced += count($chunk);
            }
        });

        // 2. Verified Count (Fast O(1) operation)
        $totalInRedis = Redis::scard('bep20_contracts');

        echo "<h3>Sync Results</h3>";
        echo "Database Count: $totalSynced <br>";
        echo "Redis Set Count: $totalInRedis <br><hr>";

        // 3. SAFE PREVIEW (Avoid memory crash)
        echo "<h3>Preview (First 100 addresses)</h3>";

        // Instead of SMEMBERS (which pulls all 100k), use SSCAN or array_slice on a limited pull
        // For a simple preview, we can pull just 100 random members
        $preview = Redis::srandmember('bep20_contracts', 100);

        if (!empty($preview)) {
            foreach ($preview as $index => $addr) {
                echo ($index + 1) . ". $addr <br>";
            }
        } else {
            echo "No addresses found in Redis.";
        }

        exit;
    }

    public function addUserBEP20AddressesInRedis()
    {
        // 1. Fetch addresses from DB efficiently
        // We use chunkById to keep memory low if the DB table is massive
        $totalSynced = 0;

        // Clear the list before starting a fresh sync
        Redis::del('bep20_user_addresses');

        UserBEP20Address::orderBy('id')->chunk(5000, function ($users) use (&$totalSynced) {
            $addresses = $users->pluck('address')->map(function($addr) {
                return strtolower(trim($addr));
            })->toArray();

            // SADD in chunks of 1000 for Redis performance
            foreach (array_chunk($addresses, 1000) as $chunk) {
                Redis::sadd('bep20_user_addresses', ...$chunk);
                $totalSynced += count($chunk);
            }
        });

        // 2. Verified Count (Fast O(1) operation)
        $totalInRedis = Redis::scard('bep20_user_addresses');

        echo "<h3>Sync Results</h3>";
        echo "Database Count: $totalSynced <br>";
        echo "Redis Set Count: $totalInRedis <br><hr>";

        // 3. SAFE PREVIEW (Avoid memory crash)
        echo "<h3>Preview (First 100 addresses)</h3>";

        // Instead of SMEMBERS (which pulls all 100k), use SSCAN or array_slice on a limited pull
        // For a simple preview, we can pull just 100 random members
        $preview = Redis::srandmember('bep20_user_addresses', 100);

        if (!empty($preview)) {
            foreach ($preview as $index => $addr) {
                echo ($index + 1) . ". $addr <br>";
            }
        } else {
            echo "No addresses found in Redis.";
        }

        exit;
    }

    public function bep20DepositFetchTransaction()
    {
        try {
            $jsonreceived = file_get_contents("php://input"); 
            Log::info($jsonreceived);
            $data = json_decode($jsonreceived, true);

            if (!$data || !isset($data['txHash'])) {
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'invalid_payload'
                ], 200);
            }
            
            $txid     = $data['txHash'];
            $confirm  = 3;
            $from     = $data['from'];
            $to       = $data['to'];
            $time     = date('Y-m-d H:i:s', strtotime($data['datetime']));
            $amount   = display_format($data['amount'],8);
            $contractAddress = $data['tokenAddress'];

            DB::beginTransaction();

            // 🔒 1️⃣ Check user address with lock

            $user_bep20_data = UserBEP20Address::where('address', $to)->lockForUpdate()->first();

            if(!is_object($user_bep20_data)) {
                DB::commit();
                return response()->json(['status' => 'ignored','reason' => 'address_not_found'], 200);
            }

            $coins = TokenMultiNetwork::where(['contractaddress' => $contractAddress, 'network' => 'BEP20'])->select('ref_comm_coin')->first();

            if(!is_object($coins))
                return response()->json(['status' => 'ignored','reason' => 'token_not_found'], 200);

            $tokenSymbol = $coins->ref_comm_coin;
            $uid = $user_bep20_data->user_id;

            // 🔒 2️⃣ Check if transaction already exists (CRITICAL)
            $exists = CryptoTransactions::where('txid', $txid)->lockForUpdate()->first();

            if ($exists) {
                DB::commit();
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'already_processed',
                    'txHash' => $txid
                ], 200);
            }
            
            // ✅ 3️⃣ Save transaction (only once)
            CryptoTransactions::createTransaction($uid, $tokenSymbol, $txid, $from, $to, $amount, $confirm, $time, 0, 0, 'BEP20');
            DB::commit();

            return response()->json(['status' => 'ok','txHash' => $txid],200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in BEP20 transactions (publicnode): " . $e->getMessage());
            return response()->json(['status' => 'ignored','reason' => 'error_found'], 200);
        }
    }
    //End - BEP20 Deposit Transactions

    //Start - ETH Deposit Transactions
    public function addDemoETHDepositAddress()
    {
        $useraddress = strtolower("0x1231deb6f5749ef6ce6943a275a1d3e7486f4eae");
            //"0xf1f13b26FC0D6B3bb31338cC2221e619F3003abA"
            //"0x1231deb6f5749ef6ce6943a275a1d3e7486f4eae"
            //"0xDFaa75323fB721e5f29D43859390f62Cc4B600b8"
            //"0x9c1BECd2f442e86A326032eDBb644f38CE8BcE96"
            //"0x2ebEaaa44126d64142433620fb1d33195D5867C7"

        Redis::sadd('eth_user_addresses', strtolower($useraddress));

        return 'success - '.$useraddress;
    }

    public function addUserETHAddressesInRedis()
    {
        // 1. Fetch addresses from DB efficiently
        // We use chunkById to keep memory low if the DB table is massive
        $totalSynced = 0;

        // Clear the list before starting a fresh sync
        Redis::del('eth_user_addresses');

        UserETHAddress::orderBy('id')->chunk(5000, function ($users) use (&$totalSynced) {
            $addresses = $users->pluck('address')->map(function($addr) {
                return strtolower(trim($addr));
            })->toArray();

            // SADD in chunks of 1000 for Redis performance
            foreach (array_chunk($addresses, 1000) as $chunk) {
                Redis::sadd('eth_user_addresses', ...$chunk);
                $totalSynced += count($chunk);
            }
        });

        // 2. Verified Count (Fast O(1) operation)
        $totalInRedis = Redis::scard('eth_user_addresses');

        echo "<h3>Sync Results</h3>";
        echo "Database Count: $totalSynced <br>";
        echo "Redis Set Count: $totalInRedis <br><hr>";

        // 3. SAFE PREVIEW (Avoid memory crash)
        echo "<h3>Preview (First 100 addresses)</h3>";

        // Instead of SMEMBERS (which pulls all 100k), use SSCAN or array_slice on a limited pull
        // For a simple preview, we can pull just 100 random members
        $preview = Redis::srandmember('eth_user_addresses', 100);

        if (!empty($preview)) {
            foreach ($preview as $index => $addr) {
                echo ($index + 1) . ". $addr <br>";
            }
        } else {
            echo "No addresses found in Redis.";
        }

        exit;
    }

    public function ethDepositFetchTransaction()
    {
        try {
            $jsonreceived = file_get_contents("php://input"); 
            Log::info($jsonreceived);
            $data = json_decode($jsonreceived, true);

            if (!$data || !isset($data['txHash'])) {
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'invalid_payload'
                ], 200);
            }

            $txid    = $data['txHash'];
            $confirm = 3;
            $from    = $data['from'] ?? null;
            $to      = $data['to'] ?? null;
            $time    = date('Y-m-d H:i:s', strtotime($data['datetime']));
            $amount  = display_format($data['amount'], 8);

            DB::beginTransaction();

            // 🔒 1️⃣ Check user address with lock
            $user_bnb_data = UserETHAddress::where('address', $to)
                ->lockForUpdate()
                ->first();

            if (!is_object($user_bnb_data)) {
                DB::commit();
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'address_not_found'
                ], 200);
            }

            $uid = $user_bnb_data->user_id;

            // 🔒 2️⃣ Check if transaction already exists (CRITICAL)
            $exists = CryptoTransactions::where('txid', $txid)->lockForUpdate()->first();

            if ($exists) {
                DB::commit();
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'already_processed',
                    'txHash' => $txid
                ], 200);
            }

            // ✅ 3️⃣ Save transaction (only once)
            CryptoTransactions::createTransaction($uid, 'ETH', $txid, $from, $to, $amount, $confirm, $time, 1, 0, 'ETH');
            DB::commit();

            return response()->json([
                'status' => 'ok',
                'txHash' => $txid
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error in ETH transactions (publicnode): " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'reason' => 'db_error'
            ], 200);
        }
    }
    //End - ETH Deposit Transactions

    //Start - ERC20 Deposit Transactions
    public function addDemoERC20DepositAddress()
    {
        $useraddress = strtolower("0xaA8ba7D4611437141192e7ceCed531Bc0A133efb");
            //"0xA9D1e08C7793af67e9d92fe308d5697FB81d3E43"
            //"0x7b09FC3bDD9a1Eb0059f0C9D391F5D684e0f9918"
            //"0xa03400E098F4421b34a3a44A1B4e571419517687"
            //"0xaA8ba7D4611437141192e7ceCed531Bc0A133efb"

        Redis::sadd('erc20_user_addresses', strtolower($useraddress));

        return 'success - '.$useraddress;
    }

    public function addERC20ContractAddressesInRedis()
    {
        // 1. Fetch addresses from DB efficiently
        // We use chunkById to keep memory low if the DB table is massive
        $totalSynced = 0;

        // Clear the list before starting a fresh sync
        Redis::del('erc20_contracts');

        TokenMultinetwork::where('network','ERC20')->orderBy('id')->chunk(5000, function ($users) use (&$totalSynced) {
            $addresses = $users->pluck('contractaddress')->map(function($addr) {
                return strtolower(trim($addr));
            })->toArray();

            // SADD in chunks of 1000 for Redis performance
            foreach (array_chunk($addresses, 1000) as $chunk) {
                Redis::sadd('erc20_contracts', ...$chunk);
                $totalSynced += count($chunk);
            }
        });

        // 2. Verified Count (Fast O(1) operation)
        $totalInRedis = Redis::scard('erc20_contracts');

        echo "<h3>Sync Results</h3>";
        echo "Database Count: $totalSynced <br>";
        echo "Redis Set Count: $totalInRedis <br><hr>";

        // 3. SAFE PREVIEW (Avoid memory crash)
        echo "<h3>Preview (First 100 addresses)</h3>";

        // Instead of SMEMBERS (which pulls all 100k), use SSCAN or array_slice on a limited pull
        // For a simple preview, we can pull just 100 random members
        $preview = Redis::srandmember('erc20_contracts', 100);

        if (!empty($preview)) {
            foreach ($preview as $index => $addr) {
                echo ($index + 1) . ". $addr <br>";
            }
        } else {
            echo "No addresses found in Redis.";
        }

        exit;
    }

    public function addUserERC20AddressesInRedis()
    {
        // 1. Fetch addresses from DB efficiently
        // We use chunkById to keep memory low if the DB table is massive
        $totalSynced = 0;

        // Clear the list before starting a fresh sync
        Redis::del('erc20_user_addresses');

        UserErc20Address::orderBy('id')->chunk(5000, function ($users) use (&$totalSynced) {
            $addresses = $users->pluck('address')->map(function($addr) {
                return strtolower(trim($addr));
            })->toArray();

            // SADD in chunks of 1000 for Redis performance
            foreach (array_chunk($addresses, 1000) as $chunk) {
                Redis::sadd('erc20_user_addresses', ...$chunk);
                $totalSynced += count($chunk);
            }
        });

        // 2. Verified Count (Fast O(1) operation)
        $totalInRedis = Redis::scard('erc20_user_addresses');

        echo "<h3>Sync Results</h3>";
        echo "Database Count: $totalSynced <br>";
        echo "Redis Set Count: $totalInRedis <br><hr>";

        // 3. SAFE PREVIEW (Avoid memory crash)
        echo "<h3>Preview (First 100 addresses)</h3>";

        // Instead of SMEMBERS (which pulls all 100k), use SSCAN or array_slice on a limited pull
        // For a simple preview, we can pull just 100 random members
        $preview = Redis::srandmember('erc20_user_addresses', 100);

        if (!empty($preview)) {
            foreach ($preview as $index => $addr) {
                echo ($index + 1) . ". $addr <br>";
            }
        } else {
            echo "No addresses found in Redis.";
        }

        exit;
    }

    public function erc20DepositFetchTransaction()
    {
        try {
            $jsonreceived = file_get_contents("php://input"); 
            Log::info($jsonreceived);
            $data = json_decode($jsonreceived, true);

            if (!$data || !isset($data['txHash'])) {
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'invalid_payload'
                ], 200);
            }
            
            $txid     = $data['txHash'];
            $confirm  = 3;
            $from     = $data['from'];
            $to       = $data['to'];
            $time     = date('Y-m-d H:i:s', strtotime($data['datetime']));
            $amount   = display_format($data['amount'],8);
            $contractAddress = $data['tokenAddress'];

            DB::beginTransaction();

            // 🔒 1️⃣ Check user address with lock

            $user_erc20_data = UserErc20Address::where('address', $to)->lockForUpdate()->first();

            if(!is_object($user_erc20_data)) {
                DB::commit();
                return response()->json(['status' => 'ignored','reason' => 'address_not_found'], 200);
            }

            $coins = TokenMultiNetwork::where(['contractaddress' => $contractAddress, 'network' => 'ERC20'])->select('ref_comm_coin')->first();

            if(!is_object($coins))
                return response()->json(['status' => 'ignored','reason' => 'token_not_found'], 200);

            $tokenSymbol = $coins->ref_comm_coin;
            $uid = $user_erc20_data->user_id;

            // 🔒 2️⃣ Check if transaction already exists (CRITICAL)
            $exists = CryptoTransactions::where('txid', $txid)->lockForUpdate()->first();

            if ($exists) {
                DB::commit();
                return response()->json([
                    'status' => 'ignored',
                    'reason' => 'already_processed',
                    'txHash' => $txid
                ], 200);
            }
            
            // ✅ 3️⃣ Save transaction (only once)
            CryptoTransactions::createTransaction($uid, $tokenSymbol, $txid, $from, $to, $amount, $confirm, $time, 0, 0, 'ERC20');
            DB::commit();

            return response()->json(['status' => 'ok','txHash' => $txid],200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in ERC20 transactions (publicnode): " . $e->getMessage());
            return response()->json(['status' => 'ignored','reason' => 'error_found'], 200);
        }
    }
    //End - ERC20 Deposit Transactions
}
