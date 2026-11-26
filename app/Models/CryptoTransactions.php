<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserWallet;
use App\Models\User;

class CryptoTransactions extends Model
{
    protected $table = 'cryptotransactions';

    public static function createTransaction($uid, $coin, $txid, $from, $to, $amount, $confirm = 3, $time, $feestatus = 1, $fees = 0, $network = null)
    {
        if($coin == 'BTC')
        $tran = CryptoTransactions::where(['uid' => $uid, 'currency' => $coin, 'txid' => $txid])->where('to_addr',$to)->first();
       else
        $tran = CryptoTransactions::where(['uid' => $uid, 'currency' => $coin, 'txid' => $txid])->first();
       
        if (!is_object($tran)) {
            $tran = new CryptoTransactions();
            $tran->uid = $uid;
            $tran->currency = $coin;
            $tran->txtype = 'deposit';
            $tran->txid = $txid;
            $tran->from_addr = $from;
            $tran->to_addr = $to;
            $tran->amount = $amount;
            $tran->totalamount = $amount;
            $tran->status = 1;
            $tran->nirvaki_nilai = 0;
            if ($coin == 'XRP')
                $tran->nirvaki_nilai = 1;
            if ($network != null)
                $tran->multinet_token_network = $network;
            $tran->netfee = $fees;
            $tran->created_at = $time;
            $updateBal = UserWallet::where('uid', $uid)->where('currency', $coin)->first();
            $Updatedata['volumein'] = $amount;
            $Updatedata['currency'] = $coin;
            //   $Updatedata['currencytype'] = 'token';
            $Updatedata['fee'] = 0;
            $Updatedata['remarks'] = "User " . $coin . " Deposit";
            $Updatedata['refentry'] = 'CoinTransaction';
            $Updatedata['refentrysubtype'] = 'Deposit';
            $Updatedata['refid'] = $tran->id;
            $Updatedata['decimal'] = 8;
            $Updatedata['uid'] = $uid;
            UserWallet::WalletSave($updateBal, $Updatedata);
            unset($Updatedata);
            $tran->feestatus = $feestatus;
            $tran->confirmation = $confirm;
            $tran->updated_at = date('Y-m-d H:i:s', time());
            $tran->save();
            $user = User::where('id', $uid)->first();
            $deposit['deposit_amount'] = $amount;
            $deposit['coin'] = $coin;
            $deposit['username'] = $user->name;
            unset($deposit);
        }

        $tran->confirmation = $confirm;
        $tran->updated_at = date('Y-m-d H:i:s', time());
        $tran->save();

        return $tran;
    }
}
