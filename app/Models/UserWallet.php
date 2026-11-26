<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    protected $table = 'wallets';
    protected $fillable = ['uid', 'currency', 'balance', 'escrow_balance', 'created_at', 'updated_at'];

    public static function WalletSave($GetDetail = null, $Updatedata)
    {
        $ActualValue = 0;    
        if (is_object($GetDetail)) {
            if (isset($Updatedata['volumein'])) {
                if ($Updatedata['volumein'] > 0) {
                    $GetDetail->balance = ncAdd($GetDetail->balance, $Updatedata['volumein'], $Updatedata['decimal']);
                    $GetDetail->site_balance = ncAdd($GetDetail->site_balance, $Updatedata['volumein'], $Updatedata['decimal']);
                    $Updatedata['volumeout'] = 0;
                    $ActualValue = $Updatedata['volumein'];
                }
            } else if (isset($Updatedata['volumeout'])) {
                if ($Updatedata['volumeout'] > 0) {
                    $GetDetail->balance = ncSub($GetDetail->balance, $Updatedata['volumeout'], $Updatedata['decimal']);
                    $GetDetail->site_balance = ncSub($GetDetail->site_balance, $Updatedata['volumeout'], $Updatedata['decimal']);
                    $Updatedata['volumein'] = 0;
                    $ActualValue = $Updatedata['volumeout'];
                }
            }
            $GetDetail->save(); 
            $Updatedata['uid'] = $GetDetail->uid;
            $Updatedata['balance'] = $GetDetail->balance;
        } else { 
            $GetDetail=new UserWallet();
            $Updatedata['volumeout']=0;
            $Updatedata['balance']=$Updatedata['volumein'];
            $GetDetail->balance = $Updatedata['volumein'];
            $GetDetail->site_balance = $Updatedata['volumein'];
            $GetDetail->uid = $Updatedata['uid'];
            $GetDetail->created_at = date('Y-m-d H:i:s', time());
            $GetDetail->updated_at = date('Y-m-d H:i:s', time());
            $GetDetail->currency = $Updatedata['currency'];
            $ActualValue = $Updatedata['volumein'];
            $GetDetail->save();
        }
        $Updatedata['actualvalue'] = ncSub($ActualValue, $Updatedata['fee'], $Updatedata['decimal']);
        UserWalletDet::WalletDetInsert($Updatedata);
        unset($Updatedata);
        return true;
    }

    public static function WalletbalanceUpdate($Updatedata)
    {
        $oWalletDet = UserWallet::where([['uid', '=', $Updatedata['uid']], ['currency', $Updatedata['currency']]])->lockForUpdate()
            ->first();        
        if (isset($Updatedata['volumeout'])) {
            if (!empty($oWalletDet)) {
                if ($Updatedata['volumeout'] > 0) {               
                        if ($oWalletDet->balance < $Updatedata['volumeout']) {
                            return false;
                        }                   
                }
            } else {
                return false;
            }
        }
        UserWallet::WalletSave($oWalletDet, $Updatedata);
        return true;
    }

    public static function WalletEcrowSave($GetDetail = null, $Updatedata)
    {
        if(!isset($Updatedata['isescrowin']))
        $Updatedata['isescrowin'] == false;
        $ActualValue = 0;
        if (is_object($GetDetail)) {        
           
            if (isset($Updatedata['volumein'])) {              
                if ($Updatedata['volumein'] > 0) {
               
                    $GetDetail->balance = ncAdd($GetDetail->balance, $Updatedata['volumein'], $Updatedata['decimal']);                  
                    if(isset($Updatedata['istradecancel']) ){
                        $GetDetail->escrow_balance = ncSub($GetDetail->escrow_balance, $Updatedata['volumein'], $Updatedata['decimal']);
                    } else{
                        $GetDetail->site_balance = ncAdd($GetDetail->site_balance, $Updatedata['volumein'], $Updatedata['decimal']);
                    }
                    $Updatedata['volumeout'] = 0;
                    $ActualValue = $Updatedata['volumein'];
                }   
            } else if (isset($Updatedata['volumeout'])) {
                if ($Updatedata['volumeout'] > 0) {
                 
                    if ($Updatedata['isescrowin'] == true) {
                        $GetDetail->balance = ncSub($GetDetail->balance, $Updatedata['volumeout'], $Updatedata['decimal']);
                        $GetDetail->escrow_balance = ncAdd($GetDetail->escrow_balance, $Updatedata['volumeout'], $Updatedata['decimal']);
                    } else {
                        $GetDetail->escrow_balance = ncSub($GetDetail->escrow_balance, $Updatedata['volumeout'], $Updatedata['decimal']);
                        $GetDetail->site_balance = ncSub($GetDetail->site_balance, $Updatedata['volumeout'], $Updatedata['decimal']);
                    }

                    $Updatedata['volumein'] = 0;
                    $ActualValue = $Updatedata['volumeout'];
                }
            }        
            $GetDetail->save();
            $Updatedata['uid'] = $GetDetail->uid;
            $Updatedata['balance'] = $GetDetail->balance;
        } else {
            $GetDetail=new UserWallet();
            $Updatedata['volumeout']=0;
            $Updatedata['balance']=$Updatedata['volumein'];
            $GetDetail->balance = $Updatedata['volumein'];
            $GetDetail->site_balance = $Updatedata['volumein'];
            $GetDetail->uid = $Updatedata['uid'];
            $GetDetail->created_at = date('Y-m-d H:i:s', time());
            $GetDetail->updated_at = date('Y-m-d H:i:s', time());
            $GetDetail->currency = $Updatedata['currency'];
            $ActualValue = $Updatedata['volumein'];            
            $GetDetail->save();
        }    
        $Updatedata['actualvalue'] = ncSub($ActualValue, $Updatedata['fee'], $Updatedata['decimal']);        

        if (!($Updatedata['isescrowin'] == false &&  $Updatedata['volumeout'] > 0)  )
        UserWalletDet::WalletDetInsert($Updatedata);
        unset($Updatedata);
        return true;
    }
  
    public static function WalletEscrowbalanceUpdate($Updatedata)
    {
        if(!isset($Updatedata['isescrowin']))
        $Updatedata['isescrowin'] = false;
        $oWalletDet = UserWallet::where([['uid', '=', $Updatedata['uid']], ['currency', $Updatedata['currency']]])->lockForUpdate()
            ->first();
        if (isset($Updatedata['volumeout'])) {
            if (!empty($oWalletDet)) {
                if ($Updatedata['volumeout'] > 0) {
                    if ($Updatedata['isescrowin'] == true) {
                        if (($oWalletDet->balance < $Updatedata['volumeout'])) {
                            return false;
                        }
                    } else {
                        if ($oWalletDet->escrow_balance < $Updatedata['volumeout']) {
                            return false;
                        }
                    }
                }
            } else {
                return false;
            }
        }
        UserWallet::WalletEcrowSave($oWalletDet, $Updatedata);
        return true;
    }
}
