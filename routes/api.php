<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\MemberAPIController;
use App\Http\Controllers\api\TranApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Api\UserImageController;

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
    // Route::post('/register','Auth\ApiAuthController@register')->name('register.api');
    Route::post('/logout', 'Auth\ApiAuthController@logout')->name('logout.api');

    Route::post('/login', [ApiAuthController::class,'login']);
    Route::post('/createtempmember', [MemberAPIController::class, 'createTempUser']);
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

Route::post('/forgotpassword', [ApiAuthController::class,'forgotPassword']);
Route::post('/generateotp', [ApiAuthController::class,'generateOTP'])->middleware('api.key');
Route::post('/validateotp', [ApiAuthController::class,'validateOTP'])->middleware('api.key');

Route::post('/usermembers', [TranApiController::class,'GetUserMembers'])->middleware('api.key');
Route::post('/memberincomes', [TranApiController::class,'GetTransactions'])->middleware('api.key');

// Route::post('/updatepayment', [ApiAuthController::class,'updatePaymentStatus'])->middleware('api.key');




