<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DepositController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('addressgeneration', [AddressController::class, 'generateAddress']);

// BNB Deposit Fetch Transctions
Route::post('get_bnb_transaction', [DepositController::class, 'bnbDepositFetchTransaction']);
Route::get('add_demo_user_bnb_address', [DepositController::class, 'addDemoBNBDepositAddress']);
Route::get('add_user_bnb_address', [DepositController::class, 'addUserBNBAddressesInRedis']);

// BEP20 Deposit Fetch Transctions
Route::post('get_bep20_transaction', [DepositController::class, 'bep20DepositFetchTransaction']);
Route::get('add_user_bep20_address', [DepositController::class, 'addUserBEP20AddressesInRedis']);
Route::get('add_bep20_contract_address', [DepositController::class, 'addBEP20ContractAddressesInRedis']);
Route::get('add_demo_bep20_address', [DepositController::class, 'addDemoBEP20DepositAddress']);

// ETH Deposit Fetch Transctions
Route::post('get_eth_transaction', [DepositController::class, 'ethDepositFetchTransaction']);
Route::get('add_demo_user_eth_address', [DepositController::class, 'addDemoETHDepositAddress']);
Route::get('add_user_eth_address', [DepositController::class, 'addUserETHAddressesInRedis']);

// ERC20 Deposit Fetch Transctions
Route::post('get_erc20_transaction', [DepositController::class, 'erc20DepositFetchTransaction']);
Route::get('add_user_erc20_address', [DepositController::class, 'addUserERC20AddressesInRedis']);
Route::get('add_erc20_contract_address', [DepositController::class, 'addERC20ContractAddressesInRedis']);
Route::get('add_demo_erc20_address', [DepositController::class, 'addDemoERC20DepositAddress']);
