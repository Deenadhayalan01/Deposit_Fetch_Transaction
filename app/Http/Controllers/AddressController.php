<?php

namespace App\Http\Controllers;
 
use App\Models\UserWallet;
use Illuminate\Http\Request;
use App\Models\UserEthAddress;
use App\Models\UserBNBAddress;
use App\Models\UserErc20Address;
use App\Models\UserBEP20Address;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;


class AddressController extends Controller
{
    public function generateAddress(Request $request)
    {
        $token = $request->token;
        $network = $request->network;

        if($token == 'ETH')
            $response = $this->createETHaddress($request->uid, $request->address);
        else if($token == 'BNB')
            $response = $this->createBNBaddress($request->uid, $request->address);
        else if($network == 'ERC20')
            $response = $this->createERC20address($request->uid, $request->address, $token);
        else if($network == 'BEP20')
            $response = $this->createBEP20address($request->uid, $request->address, $token);
        else
            $response = 'Field required';

        return $response;            
    }

    public function createETHaddress($id, $address)
    {
        $eth_address = UserEthAddress::where('address', $address)->value('address');

        if (!$eth_address) {
            $ethtable = new UserEthAddress;
            $ethtable->user_id = $id;
            $ethtable->address = $address;
            $ethtable->narcanru = TransactionString(60);
            $ethtable->balance = 0.00000000;
            $ethtable->save();

            // Add to Redis immediately so Node.js sees it
            // Redis 'SADD' adds to the set and ignores if it already exists
            Redis::sadd('eth_user_addresses', strtolower($address));
        }
        $walletaddress = UserWallet::where(['uid' => $id, 'currency' => 'ETH'])->first();
        $balance = 0;
        if (!$walletaddress) {
            $walletaddress = new UserWallet;
            $walletaddress->uid = $id;
            $walletaddress->currency = 'ETH';
        }

        $walletaddress->mukavari            = $address;
        $walletaddress->balance             = $walletaddress->balance + $balance;
        $walletaddress->site_balance        = $walletaddress->balance + $balance;
        $walletaddress->created_at          = date('Y-m-d H:i:s', time());
        $walletaddress->updated_at          = date('Y-m-d H:i:s', time());
        $walletaddress->save();

        return $address;
    }

    public function createBNBaddress($id, $address)
    {
        $bnb_address = UserBNBAddress::where('address', $address)->value('address');

        if (!$bnb_address) {
            $bnbtable = new UserBNBAddress;
            $bnbtable->user_id = $id;
            $bnbtable->address = $address;
            $bnbtable->narcanru = TransactionString(60);
            $bnbtable->balance = 0.00000000;
            $bnbtable->save();

            // Add to Redis immediately so Node.js sees it
            // Redis 'SADD' adds to the set and ignores if it already exists
            Redis::sadd('bnb_user_addresses', strtolower($address));
        }
        $walletaddress = UserWallet::where(['uid' => $id, 'currency' => 'BNB'])->first();
        $balance = 0;
        if (!$walletaddress) {
            $walletaddress = new UserWallet;
            $walletaddress->uid = $id;
            $walletaddress->currency = 'BNB';
        }

        $walletaddress->mukavari            = $address;
        $walletaddress->balance             = $walletaddress->balance + $balance;
        $walletaddress->site_balance        = $walletaddress->balance + $balance;
        $walletaddress->created_at          = date('Y-m-d H:i:s', time());
        $walletaddress->updated_at          = date('Y-m-d H:i:s', time());
        $walletaddress->save();

        return $address;
    }

    public function createERC20address($id, $address, $token)
    {
        $erc20_address = UserErc20Address::where('address', $address)->value('address');

        if (!$erc20_address) {
            $erc20table = new UserErc20Address;
            $erc20table->user_id = $id;
            $erc20table->address = $address;
            $erc20table->narcanru = TransactionString(20);
            $erc20table->balance = 0.00000000;
            $erc20table->save();

            // Add to Redis immediately so Node.js sees it
            // Redis 'SADD' adds to the set and ignores if it already exists
            Redis::sadd('erc20_user_addresses', strtolower($address));
        }
        $walletaddress = UserWallet::where(['uid' => $id, 'currency' => $token])->first();
        $balance = 0;
        if (!$walletaddress) {
            $walletaddress = new UserWallet;
            $walletaddress->uid = $id;
            $walletaddress->currency = $token;
        }

        $walletaddress->mukavari            = $address;
        $walletaddress->balance             = $walletaddress->balance + $balance;
        $walletaddress->site_balance        = $walletaddress->balance + $balance;
        $walletaddress->created_at          = date('Y-m-d H:i:s', time());
        $walletaddress->updated_at          = date('Y-m-d H:i:s', time());
        $walletaddress->save();

        return $address;
    }

    public function createBEP20address($id, $address, $token)
    {
        $bep20_address = UserBEP20Address::where('address', $address)->value('address');

        if (!$bep20_address) {
            $bep20table = new UserBEP20Address;
            $bep20table->user_id = $id;
            $bep20table->address = $address;
            $bep20table->narcanru = TransactionString(20);
            $bep20table->balance = 0.00000000;
            $bep20table->save();

            // Add to Redis immediately so Node.js sees it
            // Redis 'SADD' adds to the set and ignores if it already exists
            Redis::sadd('bep20_user_addresses', strtolower($address));
        }
        $walletaddress = UserWallet::where(['uid' => $id, 'currency' => $token])->first();
        $balance = 0;
        if (!$walletaddress) {
            $walletaddress = new UserWallet;
            $walletaddress->uid = $id;
            $walletaddress->currency = $token;
        }

        $walletaddress->mukavari            = $address;
        $walletaddress->balance             = $walletaddress->balance + $balance;
        $walletaddress->site_balance        = $walletaddress->balance + $balance;
        $walletaddress->created_at          = date('Y-m-d H:i:s', time());
        $walletaddress->updated_at          = date('Y-m-d H:i:s', time());
        $walletaddress->save();

        return $address;
    }
}
