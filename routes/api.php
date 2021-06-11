<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\MemberAPIController;
use App\Http\Controllers\Api\TranApiController;
use App\Http\Controllers\Api\MiscApiController;
use App\Http\Controllers\Api\QueryAPIController;
use App\Http\Controllers\Api\RechargeController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Api\UserImageController;
use App\Http\Controllers\Api\RedeemController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('user', [App\Http\Controllers\Api\UserController::class, 'user'])->middleware('auth:api')->name('user');
// Route::post('userdetails', [App\Http\Controllers\Api\UserApiController::class, 'user'])->name('user');
// Route::post('user', [App\Http\Controllers\Api\UserApiController::class, 'create']);

Route::group(['middleware' => ['cors', 'json.response', 'api.key']], function () {
    // public routes
    Route::post('/login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('/validatelogin', [ApiAuthController::class,'validateLogin']);

    // Route::post('/register','Auth\ApiAuthController@register')->name('register.api');
    Route::post('/logout', 'Auth\ApiAuthController@logout')->name('logout.api');

    Route::post('/login', [ApiAuthController::class,'login']);
    Route::post('/createtempmember', [MemberAPIController::class, 'createTempUser']);
    Route::post('/getreferername', [MemberAPIController::class,'getRefererName']);

    Route::post('/updatepaymentstatus', [MemberAPIController::class,'updatePaymentStatus']);
    Route::post('/updatememberinfo', [MemberAPIController::class, 'updateMemberInfo']);
    Route::post('/saveuserimage', [UserImageController::class,'saveUserImage']);
    Route::post('/getmemberinfo', [MemberAPIController::class,'getMemberInfo']);

    Route::post('/showuser', [MemberAPIController::class,'showUser']);
    Route::post('/changepassword', [ApiAuthController::class,'changePassword']);

    Route::post('/gettxnid', [TranApiController::class,'GetTxnID']);
    Route::post('/addmoney', [TranApiController::class,'AddMoney']);
    Route::post('/getpayments', [TranApiController::class,'GetPayments']);

    // ...
});

Route::get('/userimage', [UserImageController::class,'userImage']);
Route::post('/getmemberdocuments', [UserImageController::class,'getMemberDocuments']);

Route::post('/forgotpassword', [ApiAuthController::class,'forgotPassword']);
Route::post('/generateotp', [ApiAuthController::class,'generateOTP'])->middleware('api.key');
Route::post('/generatenewmemberotp', [ApiAuthController::class,'generateNewMemberOTP']);
//
Route::post('/validateotp', [ApiAuthController::class,'validateOTP'])->middleware('api.key');

Route::post('/usermembers', [TranApiController::class,'GetUserMembers'])->middleware('api.key');
Route::post('/immediatemembers', [TranApiController::class,'GetImmediateMembers'])->middleware('api.key');

Route::post('/memberincomes', [TranApiController::class,'GetTransactionsRedeemable'])->middleware('api.key');
Route::post('/clubincomes', [TranApiController::class,'GetTransactionsClub'])->middleware('api.key');

Route::post('/dashboard', [TranApiController::class,'Dashboard'])->middleware('api.key');
Route::post('/memberrewards', [TranApiController::class,'MemberRewards'])->middleware('api.key');; //->middleware('api.key');

// Route::post('/updatepayment', [ApiAuthController::class,'updatePaymentStatus'])->middleware('api.key');

Route::get('/updaterechargeservices', [MiscApiController::class,'updateRechargeServices']); //->middleware('api.key');
Route::get('/updatepaymenttoken', [MiscApiController::class,'updateRechargeToken']); //->middleware('api.key');


Route::post('/getproviders', [RechargeController::class,'GetProviders'])->middleware('api.key');
Route::post('/rechargemobile', [RechargeController::class,'RechargeMobile'])->middleware('api.key');; //->middleware('api.key');
Route::post('/nonredeemablewallet', [RechargeController::class,'GetNonRedeemableWallet'])->middleware('api.key');
Route::post('/getmobileplans', [RechargeController::class,'GetMobilePlans'])->middleware('api.key');
Route::post('/getbalances', [RechargeController::class,'GetBalances'])->middleware('api.key');

Route::post('/saveuserpanfrontimage', [UserImageController::class,'saveUserPANFrontImage'])->middleware('api.key');
Route::post('/saveuserpanbackimage', [UserImageController::class,'saveUserPANBackImage'])->middleware('api.key');
Route::post('/saveuseridfrontimage', [UserImageController::class,'saveUserIDFrontImage'])->middleware('api.key');
Route::post('/saveuseridbackimage', [UserImageController::class,'saveUserIDBackImage'])->middleware('api.key');

Route::post('/calculatedailyclubincome', [MiscApiController::class,'calculateClubIncome']);


//Query Controllers
Route::post('/redeemableincomequery', [QueryAPIController::class,'GetRedeemableIncomeQuery'])->middleware('api.key');
Route::post('/clubincomequery', [QueryAPIController::class,'GetClubIncomeQuery'])->middleware('api.key');
Route::post('/ledershipincomequery', [QueryAPIController::class,'GetLeadershipIncomeQuery'])->middleware('api.key');
Route::post('/levelincomequery', [QueryAPIController::class,'GetLevelIncomeQuery'])->middleware('api.key');

Route::post('/nonredeemableincomequery', [QueryAPIController::class,'GetNonRedeemableIncomeQuery'])->middleware('api.key');
Route::post('/transferinquery', [QueryAPIController::class,'GetTransferInQuery'])->middleware('api.key');
Route::post('/transferoutquery', [QueryAPIController::class,'GetTransferOutQuery'])->middleware('api.key');
Route::post('/membermobilerecharges', [QueryAPIController::class,'GetMobileRechargeHistory'])->middleware('api.key');
Route::post('/memberwallet', [QueryAPIController::class,'GetMemberWallet'])->middleware('api.key');


//Fund Transfer
Route::post('/fundtransfer', [RechargeController::class,'FundTransfer'])->middleware('api.key');
Route::post('/getrecipientname', [MemberAPIController::class,'getRecipientName']);
Route::post('/getfundtransferswithname', [TranApiController::class,'getFundTransfersWithBalance']);

//Redeem
Route::post('/reimbursments', [RedeemController::class,'GetRedeemes'])->middleware('api.key');
Route::post('/requestredeem', [RedeemController::class,'RequestRedeem'])->middleware('api.key');;




