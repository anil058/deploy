<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TempMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Helpers\StringHelper;
use App\Models\Member;
use App\Models\MemberUser;
use App\Models\MemberMap;
use App\Models\Otp;
use App\Models\Param;
use App\Models\PaymentGateway;
use App\Models\Referal;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * LEARNINGS 
 * =============================================================
 * 'last_login_at' => Carbon::now()->toDateTimeString()
 * 'last_login_ip' => $request->getClientIp()
 * DB::enableQueryLog();
 * dd(DB::getQueryLog());
 * dd($tblReferal->toSql())
 */

class MemberAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['createTempUser','updatePaymentStatus']);
    }

    public function showUser(){
        return "ANIL MISHRA"; //auth()->user();
    }

    public function newMemberCode()
    {
        // $prefix = config('app.member_prefix');
        $param = Param::where('param','MEMBER_COUNTER')->first();
        $intValue =$param->int_value;
        $prefix = $param->string_value;
        $param->int_value = $intValue + 1;
        $param->save();
        $memberCode = $prefix . Str::padLeft($intValue,5,'0');
        return $memberCode;
    } 

    /**
     * GENERATE NEW TXNID FOR PAYMENT GATEWAY
     * ==================================================================
     */
    public function newTxnID()
    {
        $dt = Carbon::now();
        $dt1 = $dt->format('Ymd');

        $param = Param::where('param','TXN_COUNTER')->first();
        $intValue =$param->int_value;
        $prefix = $param->string_value;
        $param->int_value = $intValue + 1;
        $param->save();
        $retVal = $prefix.$dt1. Str::padLeft($intValue,5,'0');
        return $retVal;
    } 

     /**
     * CREATE TEMPORARY MEMBER
     * ===================================================================
     * Once Payment is Done One record each for MemberUser and Members
     * otp, expiry_date, ip, expiry_at will be calculated
     * ParentID will be fetched from Referral
     */
    public function createTempUser(Request $request){
        DB::beginTransaction();
        try{
            //Validate request
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'referal_code' => 'required|max:8|min:8',
                "email" => "required|email",
                "password" => "required|string|max:50",
                "address" => "string|max:200",
                // "otp" => "required|numeric|min:1000|max:9999",
            ]);
             
            // $txn_id = $this->newTxnID();
            // $txn_id = createRazorpayTempOrder()
            // dd($txn_id);

            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
                // return response()->json(['status' => false, 'message' => $errors],200);
            }
    
            //Check if referal code is valid
            // $tblReferal = Referal::where('referal_code', $request->referal_code)
            //                 ->whereDate('expiry_at', '>=', Carbon::now()->toDateString())
            //                 ->whereNull('temp_id')->first();

            $tblReferal = Member::where('referal_code', $request->referal_code)->first();

            if($tblReferal === null){
                $response = ['status' => false, 'message' => 'Invalid Referal Code'];
                return response($response, 200);
            }

            //Check if there is an existing user with same mobile no
            $tblMember = Member::where('mobile_no', $request->mobile_no)->first();
            if($tblMember){
                $response = ['status' => false, 'message' => 'User with this mobile no is already registered'];
                return response($response, 200);
            }

            $tblParam = Param::where('param', 'MEMBERSHIP_FEE')->first();

            //Check if mobile no exists in TempMember if yes replace the record, else add
            $tempUser = TempMember::where('mobile_no', $request->mobile_no)->first();
            if(!$tempUser){
                $tempUser = new TempMember();
            }
            $tempUser->first_name = $request->first_name;
            $tempUser->last_name = $request->last_name;
            $tempUser->mobile_no = $request->mobile_no;
            $tempUser->referal_code = $request->referal_code;
            $tempUser->email = $request->email;
            $tempUser->password = Hash::make($request->password);
            $tempUser->address = $request->address;
            $tempUser->parent_id = $tblReferal->member_id;
            $tempUser->member_fee = $tblParam->int_value;
            $tempUser->expiry_at = Carbon::now()->addDays(3);
            $tempUser->ip = $request->ip();
            $tempUser->save();
           
            generateOTP($request->mobile_no);

            $orderid = "";
            $orderid = createRazorpayTempOrder($tempUser->id, $tblParam->int_value );
            if(strlen($orderid) == 0){
                throw new Exception("Could not generate order id");
            }
            DB::commit();
            $response = ['status' => true, 
            'temp_id' => $tempUser->id, 
            'txn_id' => $orderid, 
            'message' => 'Successfully Created Temporary User',
            'fee_amount' => $tblParam->int_value,
            ];
            return response($response, 200);
        } catch(Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            DB::rollBack();
            return response($response, 200);
        }


    }

    public function updateMemberInfo(Request $request) {
        try{
            //Validate request
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'father' => 'required|string|max:50',
                "email" => "required|email",
                "address" => "string|max:200",
                'pan_no' => 'required|string|max:10',
            ]);
             
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }
    
            //Check if there is an existing user with same mobile no
            // $tblMember = Member::where('mobile_no', $request->mobile_no)->first();
            $id = $request->user()->id;

            $tblMember = Member::where('member_id', $id)->first();

            if($tblMember == null){
                $response = ['status' => false, 'message' => 'invalid user'];
                return response($response, 200);
            }

            $tblMember->first_name = $request->first_name;
            $tblMember->last_name = $request->last_name;
            $tblMember->father = $request->father;
            $tblMember->email = $request->email;
            $tblMember->address = $request->address;
            $tblMember->pan_no = $request->pan_no;
            $tblMember->save();

            $response = ['status' => true, 
            'message' => 'Successfully Updated',
            ];
            return response($response, 200);
        } catch(Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }

    public function getMemberInfo(Request $request) {
        try {
            $id = $request->user()->id;
            $tblMember = Member::where('member_id', $id)->first();
            
            $path = public_path("/member_images/") . $tblMember->image;
            $imagedata = file_get_contents($path);
            $base64 = base64_encode($imagedata);

            $response = [
                'status' => true, 
                'first_name' => $tblMember->first_name,
                'last_name' => $tblMember->last_name,
                'father' => $tblMember->father,
                'email' => $tblMember->email,
                'address' => $tblMember->address,
                'pan_no' => $tblMember->pan_no,
                'image' => $base64
                ];
            return response($response,200);
    
        } catch(Exception $e){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }


    /**
     * UPDATE PAYMENT STATUS AS REPORTED BY MOBILE APP
     * ======================================================================
     * If success => Create new Member and MemberUser
     *            => update Member table with temp_id
     * 
     * Methods to Invoke
     *      1. createMemberUser
     *      2. addMember
     *      2. MapMember
     *      3. UpdateIncomes
     *      4. UpdateMobileRechargePoints
     *      5. UpdateLevelAchievers
     *      6. AddCashbackReward
     *      7. UpdateRewardIncome
     *      8. UpdateRewardTable
     *      9. UpdateDirectSponsers
     *      10. UpdateTeamMobileRechargeIncome
     *      11. NotifyMembers
     */
    public function updatePaymentStatus(Request $request){
        DB::beginTransaction();
        try{
            //Validate Input
            $validator = Validator::make($request->all(), [
                'txn_id' => 'required|string|max:50',
                'temp_id' => 'required|integer',
                'payment_id' => 'required|string|max:50',
                'status' => [
                    'required',
                    Rule::in(['SUCCESS', 'FAILURE', 'PENDING']),
                ],
            ]);

            //General request validation
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $errors]);
            }
    
            //Validate against TempMembers
            $tblTempMember = TempMember::where('id',$request->temp_id)->first();
            $request->password = $tblTempMember->password;
            $request->name = $tblTempMember->first_name;

            //If Temp ID doesnot exist
            if($tblTempMember === null){
                $response = ['status' => false, 'message' => 'Invalid Temp ID'];
                return response($response, 200);
            }

            //Validate against PaymentGateway table
            $tblPaymentGateway = PaymentGateway::where('temp_id', $request->temp_id)
                                ->where('order_id', $request->txn_id)->first();

            if($tblPaymentGateway == null){
                $response = ['status' => false, 'message' => 'Invalid txnid'];
                return response($response, 200);
            }

            $this->createMemberUser($request);

            //Update Payment Gateway
            switch($request->status){
                case 'SUCCESS':
                    $tblPaymentGateway->payment_id = $request->payment_id;
                    $tblPaymentGateway->member_id = $request->member_id;
                    $tblPaymentGateway->paid = true;
                    $tblPaymentGateway->failure = false;
                    $tblPaymentGateway->pending = false;
                    $tblPaymentGateway->fake = false;
                    $tblPaymentGateway->closed =true;
                    $tblPaymentGateway->save();
                    break;
                case 'FAILURE':
                    // $tblPaymentGateway->payment_id = $request->payment_id;
                    $tblPaymentGateway->paid = false;
                    $tblPaymentGateway->failure = true;
                    $tblPaymentGateway->pending = false;
                    $tblPaymentGateway->fake = false;
                    $tblPaymentGateway->closed =true;
                    $tblPaymentGateway->save();
                    break;
                default:
                    // $tblPaymentGateway->payment_id = $request->payment_id;
                    $tblPaymentGateway->paid = false;
                    $tblPaymentGateway->failure = false;
                    $tblPaymentGateway->pending = true;
                    $tblPaymentGateway->fake = false;
                    $tblPaymentGateway->closed =false;
                    $tblPaymentGateway->save();
                    break;
            }


            //Inject default values
            $request->jumboErrorStatus = false;
            $request->jumboErrorMessage = "";
            $request->name = $tblTempMember->first_name.' '.$tblTempMember->last_name;
            $request->mobile_no = $tblTempMember->mobile_no;
            $request->parent_id = $tblTempMember->parent_id;

            // dd($request->parent_id);
            // DB::rollBack();
            $this->addMember($request);
            $this->mapMember($request);

            DB::commit();
            $response = ['status' => true, 'message' => 'Member Created Successfully'];
            return response($response, 200);
        } catch(Exception $ex) {
            $response = ['status' => false, 'message' => $ex->getMessage()];
            DB::rollBack();
            return response($response, 200);
        }

    }

    //Create a member login
    private function createMemberUser(Request $request){
        $token=Str::random(80);
        $tblMemberUser = new MemberUser();
        // $tmpMember = TempMember::where('id', $request->temp_kd);

        $tblMemberUser->name = $request->name;
        $tblMemberUser->mobile_no = $request->mobile_no;
        $tblMemberUser->password = $request->password ; // Hash::make($request->password);
        $tblMemberUser->api_token = $token;
        $tblMemberUser->active = true;
        $tblMemberUser->save();

        $request->member_id = $tblMemberUser->id;
        
        $request->api_token = $token;
    }

    //Create a record in Member table
    private function addMember(Request $request){
        $tblTempMember = TempMember::where('id', $request->temp_id)->first();
        if ($tblTempMember === null){
            $request->jumboErrorStatus = true;
            $request->jumboErrorMessage = "Temp ID not found";
            return false;
        }
        $tblMember = new Member();
        $tblMember -> temp_id = $request->temp_id;
        $tblMember -> member_id = $request->member_id;
        $tblMember -> parent_id = $request->parent_id;
        $tblMember -> unique_id = $this->newMemberCode();
        $tblMember -> first_name = $tblTempMember->first_name;
        $tblMember -> last_name = $tblTempMember->last_name;
        $tblMember -> address = $tblTempMember->address;
        $tblMember -> email = $tblTempMember->email;
        $tblMember -> referal_code = getUniqueReferalCode();
        $tblMember -> mobile_no = $request->mobile_no;
        $tblMember -> image = 'dummy.jpg';
        $tblMember -> designation_id = 1;
        $tblMember -> current_level = 1;
        $tblMember -> joining_date = Carbon::now();
        $tblMember -> save();
        $request -> unique_id = $tblMember -> unique_id;

    }

    /**
     * MAP MEMBER
     * ==================================================================
     * 1. Find Parent of the member in question in member_maps table with level_ctr<=10
     * 2. Add a record in member_maps for the current member with level_ctr=1
     * 3. Add all the records from step 1 with levelctr+=1
     * 
     */
    private function mapMember(Request $request){
        $tblMemberMap = MemberMap::where('member_id',$request->parent_id)
                        ->where('level_ctr', '<=', 10)
                        ->get();
        MemberMap::create([
            'member_id' => $request->member_id, 
            'parent_id' => $request->parent_id,
            'level_ctr' => 1]);
        
        foreach ($tblMemberMap as $memberMap){
            $memberMap->level_ctr = $memberMap->level_ctr + 1;
            $memberMap->save();
            // MemberMap::create([
            //     'member_id' => $request->member_id, 
            //     'parent_id' => $memberMap->parent_id,
            //     'level_ctr' => $memberMap->level_ctr + 1]);
        };

    }

    private function updateInocmes(){

    }

    private function updateMobileRechargePoints(){

    }

    private function updateLevelAchievers(){

    }

    private function addCashbackReward(){

    }

    private function updateRewardIncome(){

    }

    private function updateDirectSponsers(){

    }

    private function updateTeamMobileRechargeIncome(){

    }

}

