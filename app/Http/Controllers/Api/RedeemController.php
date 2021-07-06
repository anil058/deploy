<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\PayoutRequest;
use App\Models\Param;
use App\Models\MemberDeposit;
use App\Models\MemberMap;
use App\Models\MemberWallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
// use Razorpay\Api\Api;

class RedeemController extends Controller
{
    // private $api_key;
    // private $api_secret;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['MemberRewards','RazorHooks']);
        // $this->api_key = env('RAZOR_KEY','');
        // $this->api_secret = env('RAZOR_SECRET');
    }

    // public function RazorHooks(Request $request){
    //     $api = new Api($this->api_key, $this->api_secret);
    //     $webhookSecret = config('services.razorpay.webhook_secret');
    //     $webhookSignature = $request->header('X-Razorpay-Signature');
    //     $payload = $request->getContent();
    //     // $this->razorpay->utility->verifyWebhookSignature($payload, $webhookSignature, $webhookSecret);
    //     $api->utility->verifyWebhookSignature($payload, $webhookSignature, $webhookSecret);

    //     //$api->utility->verifyWebhookSignature($webhookBody, $webhookSignature, "123456789");
    //     $response = ['status' => false, 'message' => $ex->getMessage()];
    //     return response($response, 200);
    // }

    public function RequestRedeem(Request $request){
        return response()->json(['status' => false, 'message' => "Server under maintenance, try again later"]);


        //Validate Input
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);

        //General request validation
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json(['status' => false, 'message' => $errors]);
        }

        DB::beginTransaction();
        try{
            $tblMemberWallet = MemberWallet::where('member_id', $request->user()->id)->first();
            if($tblMemberWallet->redeemable_amt < $request->amount){
                return response()->json(['status' => false, 'message' => "Insufficient Balance"]);
            }

            $tblPayoutRequest = new PayoutRequest();
            $tblPayoutRequest->member_id = $request->user()->id;
            $tblPayoutRequest->status = 'PENDING';
            $tblPayoutRequest->request_amount = $request->amount;
            $tblPayoutRequest->payment_amount = $request->amount;
            $tblPayoutRequest->approver_id =1;
            $tblPayoutRequest->approved_on = Carbon::now();
            $tblPayoutRequest->save();

            $tblMemberWallet->redeemable_amt -= $request->amount;
            $tblMemberWallet->save();

            $sql ="select cast(request_amount as char) request_amount,status,date_format(approved_on,'%d/%m/%Y') approved_on
                from payout_requests
                WHERE member_id=".$request->user()->id;

            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response['redeemable_amt'] = strval($tblMemberWallet->redeemable_amt);
            $response["data"]=$records;
            DB::commit();
            return response($response, 200);
        } catch(Exception $ex) {
            $response = ['status' => false, 'message' => $ex->getMessage()];
            DB::rollBack();
            return response($response, 200);
        }

    }

    public function GetRedeemes(Request $request){
        try{
            $tblMemberWallet = MemberWallet::where('member_id',  $request->user()->id)->first();

            $sql ="select cast(request_amount as char) request_amount,status,date_format(approved_on,'%d/%m/%Y') approved_on
            from payout_requests
            WHERE member_id=".$request->user()->id;

            $records = DB::select($sql);

            // $records = PayoutRequest::where('member_id', $request->user()->id)->get();
            $response['status'] = true;
            $response['message'] = 'Success';
            $response['redeemable_amt'] = strval($tblMemberWallet->redeemable_amt);
            $response["data"]=$records;
            return response($response, 200);
        } catch(Exception $e){
            $tblMemberWallet = MemberWallet::where('member_id', $request->id)->first();
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }

}
