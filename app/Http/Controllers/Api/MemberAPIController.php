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
use App\Models\ClubAchiever;
use App\Models\ClubMaster;
use App\Models\LevelAchiever;
use App\Models\LevelMaster;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\MemberIncome;
use App\Models\MemberUser;
use App\Models\MemberMap;
use App\Models\MemberRewards;
use App\Models\MemberWallet;
use App\Models\RefTable;
use App\Models\Otp;
use App\Models\Param;
use App\Models\PaymentGateway;
use App\Models\RechargePointRegister;
use App\Models\Referal;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * KNOWLEDGE
 * DB::enableQueryLog();
 * dd(DB::getQueryLog()); 
 */
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
    //fetch ref table
    //private $arrayRefTable;
    // private $arrayRefTable = array();

    //Paremters
    private $MEMBERSHIP_POINTS;
    private $TAX_PERCENT;
    private $MEMBER_COUNTER;
    private $MEMBERSHIP_FEE;
    private $LEVEL1_LEADERSHIP_INCOME;
    private $LEVEL2_LEADERSHIP_INCOME;
    private $ALLOW_COMPANY_REFERAL_CODE = false;

    //Memory Tables
    private $arrayParents = array();
    private $arrayLevelMaster = array();
    private $arrayClubMaster = array();
    private $arrayLevelWiseMemberCount = array();

    //Constructor
    public function __construct()
    {
        $this->middleware('auth:api')->except(['createTempUser','updatePaymentStatus','getRefererName','getRecipientName']);
    }

    /**
     * Extract and Fill arrayParents for current member
     * ------------------------------------------------------------------> Masters/ References
     */
    private function populateClubMaster(){
        $tblTemp = ClubMaster::all();
        foreach ($tblTemp as $rec){
            $this->arrayClubMaster[] = $rec;
        }
    }

    /**
     * Extract and Fill Parameters from params
     * ------------------------------------------------------------------> Masters/ References
     */
    //
    private function populateParams(){
        $tblParamTable = Param::all();
        foreach ($tblParamTable as $refTable){
            switch($refTable->param){
                case "MEMBERSHIP_FEE":
                    $this->MEMBERSHIP_FEE = $refTable->int_value;
                    break;
                case "TAX_PERCENT":
                    $this->TAX_PERCENT = $refTable->string_value;
                    break;
                case "CASHBACK_REWARD":
                    $this->MEMBERSHIP_POINTS = $refTable->int_value;
                    break;
                case "LEVEL1_LEADERSHIP_INCOME":
                    $this->LEVEL1_LEADERSHIP_INCOME = $refTable->int_value;
                    break;
                case "LEVEL2_LEADERSHIP_INCOME":
                    $this->LEVEL2_LEADERSHIP_INCOME = $refTable->int_value;
                    break;
                case "ALLOW_COMPANY_REFERAL_CODE":
                    $this->ALLOW_COMPANY_REFERAL_CODE = ($refTable->bool_value == 0) ? false : true;
                    break;
                case "ALLOW_NEW_MEMBERS":
                    $this->ALLOW_NEW_MEMBERS = ($refTable->bool_value == 0) ? false : true;
                    break;
                default :
                $this->ROYALTY_REQ_NUM = 0;
            }
        }
    }

     /**
     * Test Method
     * ===================================================================> Route Method
     */
    public function showUser(){
        return "ANIL MISHRA"; //auth()->user();
    }

/**
     * CREATE TEMPORARY MEMBER
     * ===================================================================> Route Method
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
                'referal_code' => 'required|max:10|min:10',
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

            $this->populateParams();

            if ($this->ALLOW_NEW_MEMBERS == false){
                $response = ['status' => false, 'message' => 'System Error! Please try again after sometime'];
                return response($response, 200);
            }

//

            if ($request->referal_code == '0000000000'){
                if ($this->ALLOW_COMPANY_REFERAL_CODE == false){
                    $response = ['status' => false, 'message' => 'Company Referal Code has been prohibited'];
                    return response($response, 200);
                }
            }
            // $this->populateParams();
    
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
            $tblMember = Member::where('mobile_no', $request->mobile_no)
                        -> orWhere ('email', $request->email)->first();
            if($tblMember){
                $response = ['status' => false, 'message' => 'User with this mobile no or email already exists'];
                return response($response, 200);
            }

            // $tblParam = Param::where('param', 'MEMBERSHIP_FEE')->first();

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

            $membershipFee = $this->MEMBERSHIP_FEE;
            $taxPercent = $this->TAX_PERCENT;
            $taxAmount = round($this->MEMBERSHIP_FEE * $this->TAX_PERCENT * 0.01,2);
            $netAmount = $this->MEMBERSHIP_FEE + $taxAmount;
            
            $tempUser->membership_fee = $membershipFee;
            $tempUser->tax_percent = $taxPercent;
            $tempUser->tax_amount = $taxAmount;
            $tempUser->net_amount = $netAmount;

            $tempUser->expiry_at = Carbon::now()->addDays(3);
            $tempUser->ip = $request->ip();
            $tempUser->save();
           
            generateNewMemberOTP($request->mobile_no);

            $orderid = "";
            $orderid = createRazorpayTempOrder($tempUser->id, $membershipFee , $taxPercent);
            if(strlen($orderid) == 0){
                throw new Exception("Could not generate order id");
            }
            DB::commit();
            $response = ['status' => true, 
            'temp_id' => $tempUser->id, 
            'txn_id' => $orderid, 
            'message' => 'Successfully Created Temporary User',
            'fee_amount' => $netAmount * 100
            ];
            return response($response, 200);
        } catch(Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            DB::rollBack();
            return response($response, 200);
        }
    }
    

    /**
     * Update Member profile information
     * ===================================================================> Route Method
     */
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
                'bank_name' => 'required|string|max:30',
                'account_no' => 'required|string|max:30',
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
            $tblMember->bank_name = $request->bank_name;
            $tblMember->account_number = $request->account_no;

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

    /**
     * Fetch member info to show in the profile screen
     * ===================================================================> Route Method
     */
    public function getMemberInfo(Request $request) {
        try {
            $id = $request->user()->id;
            $tblMember = Member::where('member_id', $id)->first();
            
            $image_path = public_path("/member_images/dummy.jpg");
            $path = public_path("/member_images/") . $tblMember->unique_id;
            if(file_exists($path)){
                $path = public_path("/member_images/") . $tblMember->unique_id;
                $files = array_diff(scandir($path), array('.', '..'));
                $image_path = '';
    
                foreach($files as $file) {
                    if (strpos( $file,"profile_img.") !== false){
                        $image_path = public_path("/member_images/dummy.jpg");
                    }
                }
            }
            

            // if (strlen($image_path) == 0){
            //     $image_path = public_path("/member_images/dummy.jpg");
            // }

            $imagedata = file_get_contents($image_path);
            $profile_img = base64_encode($imagedata);

            $response = [
                'status' => true, 
                'first_name' => $tblMember->first_name,
                'last_name' => $tblMember->last_name,
                'father' => $tblMember->father,
                'email' => $tblMember->email,
                'address' => $tblMember->address,
                'pan_no' => $tblMember->pan_no,
                'image' => $profile_img
                ];
            return response($response,200);
    
        } catch(Exception $e){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }

    /**
     * Update payment and create members
     * ===================================================================> Route Method
     */
    public function updatePaymentStatus(Request $request){
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

       //If Temp ID doesnot exist
       if($tblTempMember === null){
           $response = ['status' => false, 'message' => 'Invalid Temp ID'];
           return response($response, 200);
       }
       $request->password = $tblTempMember->password;
       $request->name = $tblTempMember->first_name;
       $request->member_fee = $tblTempMember->membership_fee;

       //Validate against PaymentGateway table
       $tblPaymentGateway = PaymentGateway::where('temp_id', $request->temp_id)
                           ->where('order_id', $request->txn_id)->first();

       if($tblPaymentGateway == null){
           $response = ['status' => false, 'message' => 'Invalid txnid'];
           return response($response, 200);
       }

       $request->payment_int_id = $tblPaymentGateway->id;

       switch($request->status){
           case 'SUCCESS':
               $tblPaymentGateway->payment_id = $request->payment_id;
               $tblPaymentGateway->member_id = $request->member_id;
               $tblPaymentGateway->paid = true;
               $tblPaymentGateway->failure = false;
               $tblPaymentGateway->pending = false;
               $tblPaymentGateway->fake = false;
               $tblPaymentGateway->closed =false;
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
           default:
               // $tblPaymentGateway->payment_id = $request->payment_id;
               $tblPaymentGateway->paid = false;
               $tblPaymentGateway->failure = false;
               $tblPaymentGateway->pending = true;
               $tblPaymentGateway->fake = false;
               $tblPaymentGateway->closed =false;
               $tblPaymentGateway->save();
       }

       DB::beginTransaction();
       try{
           //Inject default values
           //$request->memberID = $request->user()->id;
           $request->jumboErrorStatus = false;
           $request->jumboErrorMessage = "";
           $request->name = $tblTempMember->first_name.' '.$tblTempMember->last_name;
           $request->mobile_no = $tblTempMember->mobile_no;
           $request->parent_id = $tblTempMember->parent_id;

           $this->populateParams();
           $this->createMemberUser($request);
           $this->addMember($request);
           $this->populateParents($request->parent_id);
           $this->mapMember($request);

           //Save Fee in Deposit
           $tblMemberDeposits = new MemberDeposit();
           $tblMemberDeposits->member_id = $request->member_id;
           $tblMemberDeposits->gateway_id = $tblPaymentGateway->id;
           $tblMemberDeposits->amount = $tblPaymentGateway->amount;
           $tblMemberDeposits->tax_percent = $tblPaymentGateway->tax_percent;
           $tblMemberDeposits->tax_amount = $tblPaymentGateway->tax_amount;
           $tblMemberDeposits->net_amount = $tblPaymentGateway->net_amount;
           $tblMemberDeposits->deposit_type = 'MEMBERSHIP_FEE';
           $tblMemberDeposits->save();

           $this->populateLevelMaster();
           $this->populateClubMaster();
           //$this->populateLevelWiseMemberCount($request->member_id);

           $this->updateMemberWallet($request->member_id);
           $this->updateLevelIncomes($request);
           // $this->updateCurrentLevel($request);
           $this->updateClub();
           $this->updateRewards($request);
           $this->addRechargePoints($request);
           //$this->updateLevelAchievers();

           //When confirm, updated closed flag
           $tblPaymentGateway->member_id = $request->member_id;
           $tblPaymentGateway->closed =true;
           $tblPaymentGateway->save();
           DB::commit();
           $response = ['status' => true, 'message' => 'Member Created Successfully'];
           return response($response, 200);
       } catch(Exception $ex) {
           $response = ['status' => false, 'message' => $ex->getMessage()];
           DB::rollBack();
           return response($response, 200);
       }

    }

/**
     * Get Name of the Member whose reference is being used as referer
     * ===================================================================> Route Method
     */
    public function getRefererName(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'referal_code' => 'required|string|max:10',
            ]);
    
            //General request validation
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                return response()->json(['status' => true, 'message' => $errors]);
            }
    
            $tblMember = Member::where('referal_code', $request->referal_code)->first();
    
            if($tblMember == null){
                return response()->json(['status' => true, 'message' => 'Invalid Referal Code']);
            }
            $memberName = $tblMember->first_name . ' ' . $tblMember->last_name;
            return response()->json(['status' => true, 'message' => $memberName]);
        }catch(Exception $e){
            return response()->json(['status' => true, 'message' => $e->getMessage()]);
        }

    }

    public function getRecipientName(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no' => 'required|string|max:10',
            ]);
    
            //General request validation
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                return response()->json(['status' => true, 'message' => $errors]);
            }
    
            $tblMember = Member::where('mobile_no', $request->mobile_no)->first();
    
            if($tblMember == null){
                return response()->json(['status' => true, 'message' => 'Invalid Referal Code']);
            }
            $memberName = $tblMember->first_name . ' ' . $tblMember->last_name;
            return response()->json(['status' => true, 'message' => $memberName]);
        }catch(Exception $e){
            return response()->json(['status' => true, 'message' => $e->getMessage()]);
        }

    }


    /**
     * Extract and Fill arrayParents for current member
     * ------------------------------------------------------------------> Masters/ References
     */
    private function populateParents($member_id){
        DB::enableQueryLog();
        $tblMembers = MemberMap::join('members', 'member_maps.member_id', '=', 'members.member_id')
        ->where('member_maps.member_id', $member_id)
        ->where('member_maps.level_ctr', '<', 12)
        // ->where('member_maps.level_ctr', '>', 0)
        ->get(['members.*','member_maps.level_ctr']);

        foreach ($tblMembers as $refTable){
            $this->arrayParents[] = $refTable;
        }
    }

    /**
     * Extract and Fill arrayParents for current member
     * ------------------------------------------------------------------> Masters/ References
     */
    private function populateLevelMaster(){
        $tblTemp = LevelMaster::all();
        foreach ($tblTemp as $rec){
            $this->arrayLevelMaster[] = $rec;
        }
    }
    
    

    //Get Level Commission Percent ***************************** called by updateLevelIncomes()
    private function getCommissionPercent($levelCtr){
        foreach($this->arrayLevelMaster as $m){
            if ($m->level == $levelCtr)
            {
                return $m->level_percent;
            }
        }
        return 0;
    }
   
    //Create new member code************************************ called by addMember()
    public function newMemberCode()
    {
        // $prefix = config('app.member_prefix');
        $param = Param::where('param','MEMBER_COUNTER')->first();
        $intValue =$param->int_value;
        $prefix = $param->string_value;
        $param->int_value = $intValue + 1;
        $param->save();
        $memberCode = $prefix . Str::padLeft($intValue,9,'0');
        return $memberCode;
    } 

    /**
     * updatePaymentStatus************************************** called by addMember()
     */
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

    /**
     * updatePaymentStatus************************************** called by updatePaymentStatus()
     */
    private function addMember(Request $request){
        $tblTempMember = TempMember::where('id', $request->temp_id)->first();
        if ($tblTempMember === null){
            $request->jumboErrorStatus = true;
            $request->jumboErrorMessage = "Temp ID not found";
            return false;
        }
        $tblParentMember = Member::where('member_id', $request->parent_id)->first();
        if ($tblParentMember === null){
            $request->jumboErrorStatus = true;
            $request->jumboErrorMessage = "Parent not found";
            return false;
        }

        $tblMember = new Member();
        $tblMember -> temp_id = $request->temp_id;
        $tblMember -> member_id = $request->member_id;
        $tblMember -> parent_id = $request->parent_id;
        $tblMember -> grand_parent_id = $tblParentMember->parent_id;
        $tblMember -> unique_id = $this->newMemberCode();
        $tblMember -> first_name = $tblTempMember->first_name;
        $tblMember -> last_name = $tblTempMember->last_name;
        $tblMember -> address = $tblTempMember->address;
        $tblMember -> email = $tblTempMember->email;
        $tblMember -> referal_code = getUniqueReferalCode();
        $tblMember -> mobile_no = $request->mobile_no;
        $tblMember -> recharge_points = $this->MEMBERSHIP_POINTS;
        $tblMember -> image = 'dummy.jpg';
        $tblMember -> designation_id = 1;
        $tblMember -> current_level = 0;
        $tblMember -> joining_date = Carbon::now();
        $tblMember -> save();
        $request -> unique_id = $tblMember -> unique_id;
    }

    /**
     * Map all the parents of the member************************ called by updatePaymentStatus()
     * 1. Find Parent of the member in question in member_maps table with level_ctr<=10
     * 2. Add a record in member_maps for the current member with level_ctr=1
     * 3. Add all the records from step 1 with levelctr+=1
     * 
     */
    private function mapMember(Request $request){
        $tblMemberMap = MemberMap::where('member_id',$request->parent_id)
                        ->get();
        MemberMap::create([
            'member_id' => $request->member_id, 
            'parent_id' => $request->parent_id,
            'level_ctr' => 1]);
        
        foreach ($tblMemberMap as $memberMap){
            MemberMap::create([
                'member_id' => $request->member_id, 
                'parent_id' => $memberMap->parent_id,
                'level_ctr' => $memberMap->level_ctr + 1]);
        };

    }

    /**
     * Update Incomes (Level and Direct Sponser Incomes) ******** called by updatePaymentStatus()
     * ==================================================
     * Update Level Incomes
     * Update Direct Sponser Income
     * Distribute Income according to refables
     */
    private function updateLevelIncomes(Request $request){
        $l_commission = 0;
        $l_totalCommission = 0;

        //Level Income Calculation
        //======================================
        //Update Member Income if there is a candidate
        $tblMemberMap = MemberMap::where('member_id',$request->member_id)->get();
        
        $tblMember = Member::where('member_id',$request->parent_id)->first();

        $l_Leadership1_beneficiary = $tblMember->parent_id;
        $l_Leadership2_beneficiary = $tblMember->grand_parent_id;

        foreach ( $tblMemberMap as $memberMap){
            $level_ctr = $memberMap->level_ctr; // + 1;
            //Calculate Level Commission
            if($level_ctr < 13){
                $l_levelPercent = $this->getCommissionPercent($level_ctr);
                $l_commission = $request->member_fee * $l_levelPercent * 0.01;
                $l_tmpCommission1 = $l_commission * $this->LEVEL1_LEADERSHIP_INCOME * 0.01;
                $l_tmpCommission2 = $l_commission * $this->LEVEL2_LEADERSHIP_INCOME * 0.01;
                $l_totalCommission += $l_commission;
                $l_totalCommission += $l_tmpCommission1;
                $l_totalCommission += $l_tmpCommission2;

                $tblMemberIncome = new MemberIncome();
                $tblMemberIncome->member_id = $memberMap->parent_id;
                $tblMemberIncome->income_type = 'Level Income';
                $tblMemberIncome->ref_member_id = $request->member_id;
                $tblMemberIncome->level_percent = $l_levelPercent;
                $tblMemberIncome->commission = $l_commission;
                $tblMemberIncome->deduction = 0;
                $tblMemberIncome->ref_amount = $request->member_fee;
                $tblMemberIncome->balance += $l_commission;
                $tblMemberIncome->save();

                $tbl_MemberWallet = MemberWallet::where('member_id',$memberMap->parent_id)->first();
                $tbl_MemberWallet->redeemable_amt += $l_commission;
                $tbl_MemberWallet->level_income +=  $l_commission;
                $tbl_MemberWallet->save();

                $tblCurrentParent = Member::where('member_id',$memberMap->parent_id)->first();

                if($l_Leadership1_beneficiary == $memberMap->parent_id){
                    $tblMemberIncome = new MemberIncome();
                    $tblMemberIncome->member_id =$l_Leadership1_beneficiary;
                    $tblMemberIncome->income_type = 'Leadership Income1';
                    $tblMemberIncome->ref_member_id = $request->member_id;
                    $tblMemberIncome->direct_l1_percent = $this->LEVEL1_LEADERSHIP_INCOME;
                    $tblMemberIncome->commission =  $l_tmpCommission1;
                    $tblMemberIncome->ref_amount = $l_commission;
                    $tblMemberIncome->deduction = 0;
                    $tblMemberIncome->balance += $l_tmpCommission1;
                    $tblMemberIncome->save();

                    $tbl_MemberWallet = MemberWallet::where('member_id',$l_Leadership1_beneficiary)->first();
                    $tbl_MemberWallet->redeemable_amt += $l_tmpCommission1;
                    $tbl_MemberWallet->leadership_income +=  $l_tmpCommission1;
                    $tbl_MemberWallet->save();
                }

                if($l_Leadership2_beneficiary == $memberMap->parent_id){
                    $tblMemberIncome = new MemberIncome();
                    $tblMemberIncome->member_id = $l_Leadership2_beneficiary;
                    $tblMemberIncome->income_type = 'Leadership Income2';
                    $tblMemberIncome->ref_member_id = $request->member_id;
                    $tblMemberIncome->direct_l2_percent = $this->LEVEL2_LEADERSHIP_INCOME;
                    $tblMemberIncome->commission =  $l_tmpCommission2;
                    $tblMemberIncome->ref_amount = $l_commission;
                    $tblMemberIncome->deduction = 0;
                    $tblMemberIncome->balance += $l_tmpCommission2;
                    $tblMemberIncome->save();

                    $tbl_MemberWallet = MemberWallet::where('member_id', $l_Leadership2_beneficiary)->first();
                    $tbl_MemberWallet->redeemable_amt += $l_tmpCommission2;
                    $tbl_MemberWallet->leadership_income +=  $l_tmpCommission2;
                    $tbl_MemberWallet->save();
                }

                // if($tblCurrentParent->parent_id>0){
                //     $tbl_MemberWallet = MemberWallet::where('member_id',$memberMap->parent_id)->first();
                //     $tbl_MemberWallet->redeemable_amt +=$l_commission;
                //     // $tbl_MemberWallet->leadership_income +=  $l_tmpCommission1 + $l_tmpCommission2;
                //     $tbl_MemberWallet->level_income +=  $l_commission;
                //     $tbl_MemberWallet->save();
                // }
            }
        };

        //Rest amount to be credited to Manshaa
        $l_leftovers = $request->member_fee - $l_totalCommission;
        $tblMemberIncome = new MemberIncome();
        $tblMemberIncome->member_id = 0;
        $tblMemberIncome->income_type = 'Company Income';
        $tblMemberIncome->ref_member_id = $request->member_id;
        // $tblMemberIncome->level_percent = (100 - $l_totalPercent);
        $tblMemberIncome->commission = $l_leftovers;
        $tblMemberIncome->ref_amount = $request->member_fee;
        $tblMemberIncome->deduction = 0;
        $tblMemberIncome->balance += $l_leftovers;

        $tblMemberIncome->save();
    }

    /**
     ************************************************************* called by updatePaymentStatus()
     */
    private function updateClub(){
        //loop through all the parents of newly created member        
        foreach($this->arrayParents as $parent){
            //fill DownlinesArray of the parentmember
            $downlineArray = array();
            $childLevelCountArray =array();
            $clubAchieversArray = array();

            //populate current parents club achiever records for want of un achieved clubs
            $tblClubAchievers = ClubAchiever::where('member_id', $parent->member_id)->get();
            foreach ($tblClubAchievers as $rec){
                $clubAchieversArray[] = $rec;
            }

            //fill downline of the parentmember
            $strSQL = 'SELECT m.member_id,m.designation_id
                FROM member_maps p
                INNER JOIN members m ON p.member_id=m.member_id
                WHERE p.parent_id=' . $parent->member_id;
            $tblDownline = DB::select($strSQL);
            foreach ($tblDownline as $rec){
                $downlineArray[] = $rec;
            }
            //Fill level wise count of childs
            $strSQL = 'SELECT level_ctr, COUNT(level_ctr) AS level_count
                FROM member_maps p
                WHERE p.parent_id=' . $parent->member_id . '  GROUP BY level_ctr';
            $tblTemp = DB::select($strSQL);
            foreach ($tblTemp as $rec){
                $childLevelCountArray[] = $rec;
            }
            //count club holders in DownlineArray
            $bronzCount = $this->getHierarchyClubCount($downlineArray, 2);
            $silverCount = $this->getHierarchyClubCount($downlineArray, 3);
            $goldCount = $this->getHierarchyClubCount($downlineArray, 4);
            $diamondCount = $this->getHierarchyClubCount($downlineArray, 5);
            $royaltyCount = $this->getHierarchyClubCount($downlineArray, 6);
    
            //loop through all the clubs
            foreach($this->arrayClubMaster as $club){
                $bronzRequired = $club->bronz_req;
                $silverRequired = $club->silver_req;
                $goldRequired = $club->gold_req;
                $diamondRequired = $club->diamond_req;
                $royaltyRequired = $club->royalty_req;

                //Bronz Achiever 
                if($club->id == 2){
                    $flag = false;
                    foreach($clubAchieversArray as $achiever){
                        if($achiever->designation_id == 2)
                            $flag = true;
                    }
    
                    if($flag == false){
                        $level0Members = $this->getDownlineLevelCount($childLevelCountArray, 0);
                        if($level0Members >= $club->level_req_members){
                            $tblClubAchiever = new ClubAchiever();
                            $tblClubAchiever->member_id = $parent->member_id;
                            $tblClubAchiever->designation_id = 2;
                            $tblClubAchiever->tran_date = Carbon::now();
                            $tblClubAchiever->save();
    
                            $tblMember = Member::where('member_id', $parent->member_id)->first();
                            $tblMember->designation_id = 2;
                            $tblMember->save();
                        }
                    }
                }
                

                //Silver Achiever 
                if($club->id == 3){
                    $flag = false;
                    //Check if already silver achiever
                    foreach($clubAchieversArray as $achiever){
                        if($achiever->designation_id == 3)
                            $flag = true;
                    }
                    if($flag == false){
                        $flag = true;
                        $level3Members = $this->getDownlineLevelCount($childLevelCountArray, 3);
                        if(!(($bronzRequired > 0) && ($bronzCount >= $bronzRequired)))
                            $flag = false;
                        if (!(($silverRequired > 0) && ($silverCount >= $silverRequired)))
                            $flag = false;
                        if (!(($goldRequired > 0) && ($goldCount >= $goldRequired)))
                            $flag = false;
                        if (!(($diamondRequired > 0) && ($diamondCount >= $diamondRequired)))
                            $flag = false;
                        if (!(($royaltyRequired > 0) && ($royaltyCount >= $royaltyRequired)))
                            $flag = false;  
                        if($level3Members < $club->level_req_members)
                            $flag = false;  
    
                        if($flag == true){
                            //if every criteria matches
                            $tblClubAchiever = new ClubAchiever();
                            $tblClubAchiever->member_id = $parent->member_id;
                            $tblClubAchiever->designation_id = 3;
                            $tblClubAchiever->tram_date = Carbon::now();
                            $tblClubAchiever->save();
    
                            $tblMember = Member::where('member_id', $parent->member_id)->first();
                            $tblMember->designation_id = 3;
                            $tblMember->save();
                        }
                    }
                }
                
                
                //Gold Achiever 
                if($club->id == 4){
                    $flag = false;
                    //Check if already gold achiever
                    foreach($clubAchieversArray as $achiever){
                        if($achiever->designation_id == 4)
                            $flag = true;
                    }
                    if($flag == false){
                        $flag = true;
                        $level6Members = $this->getDownlineLevelCount($childLevelCountArray, 6);
                        if(!(($bronzRequired > 0) && ($bronzCount >= $bronzRequired)))
                            $flag = false;
                        if (!(($silverRequired > 0) && ($silverCount >= $silverRequired)))
                            $flag = false;
                        if (!(($goldRequired > 0) && ($goldCount >= $goldRequired)))
                            $flag = false;
                        if (!(($diamondRequired > 0) && ($diamondCount >= $diamondRequired)))
                            $flag = false;
                        if (!(($royaltyRequired > 0) && ($royaltyCount >= $royaltyRequired)))
                            $flag = false;  
                        if($level6Members < $club->level_req_members)
                            $flag = false;  
    
                        if($flag == true){
                            //if every criteria matches
                            $tblClubAchiever = new ClubAchiever();
                            $tblClubAchiever->member_id = $parent->member_id;
                            $tblClubAchiever->designation_id = 4;
                            $tblClubAchiever->tram_date = Carbon::now();
                            $tblClubAchiever->save();
    
                            $tblMember = Member::where('member_id', $parent->member_id)->first();
                            $tblMember->designation_id = 4;
                            $tblMember->save();
                        }
                    }
                }
                

                //Diamond Achiever 
                if($club->id == 5){
                    $flag = false;
                    //Check if already diamond achiever
                    foreach($clubAchieversArray as $achiever){
                        if($achiever->designation_id == 5)
                            $flag = true;
                    }
                    if($flag == false){
                        $flag = true;
                        $level9Members = $this->getDownlineLevelCount($childLevelCountArray, 9);
                        if(!(($bronzRequired > 0) && ($bronzCount >= $bronzRequired)))
                            $flag = false;
                        if (!(($silverRequired > 0) && ($silverCount >= $silverRequired)))
                            $flag = false;
                        if (!(($goldRequired > 0) && ($goldCount >= $goldRequired)))
                            $flag = false;
                        if (!(($diamondRequired > 0) && ($diamondCount >= $diamondRequired)))
                            $flag = false;
                        if (!(($royaltyRequired > 0) && ($royaltyCount >= $royaltyRequired)))
                            $flag = false;  
                        if($level9Members < $club->level_req_members)
                            $flag = false;  
                        
                        if($flag == true){
                            //if every criteria matches
                            $tblClubAchiever = new ClubAchiever();
                            $tblClubAchiever->member_id = $parent->member_id;
                            $tblClubAchiever->designation_id = 5;
                            $tblClubAchiever->tram_date = Carbon::now();
                            $tblClubAchiever->save();
    
                            $tblMember = Member::where('member_id', $parent->member_id)->first();
                            $tblMember->designation_id = 5;
                            $tblMember->save();
                        }
                    }
                }
                
                
                //Royalty Achiever 
                if($club->id == 6){
                    $flag = false;
                    //Check if already royalty achiever
                    foreach($clubAchieversArray as $achiever){
                        if($achiever->designation_id == 6)
                            $flag = true;
                    }
                    if($flag == false){
                        $flag = true;
                        $level12Members = $this->getDownlineLevelCount($childLevelCountArray, 12);
                        if(!(($bronzRequired > 0) && ($bronzCount >= $bronzRequired)))
                            $flag = false;
                        if (!(($silverRequired > 0) && ($silverCount >= $silverRequired)))
                            $flag = false;
                        if (!(($goldRequired > 0) && ($goldCount >= $goldRequired)))
                            $flag = false;
                        if (!(($diamondRequired > 0) && ($diamondCount >= $diamondRequired)))
                            $flag = false;
                        if (!(($royaltyRequired > 0) && ($royaltyCount >= $royaltyRequired)))
                            $flag = false;  
                        if($level12Members < $club->level_req_members)
                            $flag = false; 
                        if ($flag == true){
                            //if every criteria matches
                            $tblClubAchiever = new ClubAchiever();
                            $tblClubAchiever->member_id = $parent->member_id;
                            $tblClubAchiever->designation_id = 6;
                            $tblClubAchiever->tram_date = Carbon::now();
                            $tblClubAchiever->save();
    
                            $tblMember = Member::where('member_id', $parent->member_id)->first();
                            $tblMember->designation_id = 6;
                            $tblMember->save();
                        }
                    }
                }
            }
        }
    }

    /**
     ************************************************************* called by updatePaymentStatus()
     */
    private function updateRewards(Request $request){
        //Loop through parents
        //Count levelwise members
        //loop through level_masters
        //      if required_members matches levelcount then update reward table

        foreach($this->arrayParents as $parent){
            $sql = 'SELECT level_ctr, COUNT(level_ctr) AS level_count
                FROM member_maps p
                WHERE p.parent_id=' . $parent->member_id . ' GROUP BY level_ctr';

            $tblMembers = DB::select($sql);
            foreach($tblMembers as $member){
                foreach($this->arrayLevelMaster as $level){
                    if ($level->level == $member->level_ctr){
                        if($level->required_members == $member->level_count){
                            $tblMemberRewards = new MemberRewards();
                            $tblMemberRewards->member_id = $parent->member_id;
                            $tblMemberRewards->level_id = $level->level;
                            $tblMemberRewards->member_count = $member->level_count;
                            $tblMemberRewards->tran_date = Carbon::now();
                            $tblMemberRewards->reward_name = $level->reward;
                            $tblMemberRewards->qualifying_date = Carbon::now();
                            $tblMemberRewards->save();
                        }
                    }
                }
            }

        }

        // foreach($this->arrayParents as $m){
        //     $current_date = date("Y-m-d H:i:s");
        //     $l_memberID = $m->member_id;
        //     $l_memberCount = MemberMap::where('parent_id', $l_memberID)->count();
        //     $l_calculatedLevel = $this->getCalculatedLevel($l_memberCount);
        //     $l_currentLevel = $m->level_ctr;

        //     // $l_memberCount = $m->member_count + 1;
            
        //     if($l_calculatedLevel != $l_currentLevel){
        //         DB::update('update members set current_level = ? where member_id = ?',[$l_calculatedLevel,$l_memberID]);
        //         $l_reward = $this->getLevelReward($l_calculatedLevel);
        //         if(strlen(trim($l_reward)) >0){
        //             $tblReward = new MemberRewards();
        //             $tblReward->member_id = $l_memberID;
        //             $tblReward->level_id = $l_calculatedLevel;
        //             $tblReward->member_count = $l_memberCount;
        //             $tblReward->tran_date = $current_date;
        //             $tblReward->reward_name = $l_reward;
        //             $tblReward->save();
        //         }

        //         // $tblLevelAchiever = new LevelAchiever();
        //         // $tblLevelAchiever->member_id = $l_memberID;
        //         // $tblLevelAchiever->level_id = $l_calculatedLevel;
        //         // $tblLevelAchiever->tran_date = $current_date;
        //         // $tblLevelAchiever->qualifying_date = $current_date;
        //         // $tblLevelAchiever->save();
        //     }
        // }
    }

    /**
     ************************************************************* called by updatePaymentStatus()
     */
    private function addRechargePoints($request){
        // $tbl = new RechargePointRegister();
        // $tbl->member_id = $request->member_id;
        // $tbl->ref_member_id = $request->member_id;
        // $tbl->payment_id = $request->payment_int_id;
        // $tbl->tran_date = date('Y-m-d H:i:s');
        // $tbl->recharge_points_added = $request->member_fee;
        // $tbl->balance_points +=$request->member_fee;
        // $tbl->save();

        $tbl = new RechargePointRegister();
        $tbl->member_id = $request->member_id;
        $tbl->ref_member_id = $request->member_id;
        $tbl->payment_id = $request->payment_int_id;
        $tbl->tran_type = 'MEMBERSHIP_BONUS';
        $tbl->tran_date = date('Y-m-d H:i:s');
        $tbl->recharge_points_added = $this->MEMBERSHIP_POINTS;
        $tbl->balance_points += $this->MEMBERSHIP_POINTS;
        $tbl->save();
    }    

    private function updateMemberWallet($member_id){
        $tbl1 = new MemberWallet();
        $tbl1->member_id = $member_id;
        $tbl1->total_members = 0;
        $tbl1->welcome_amt = $this->MEMBERSHIP_FEE;
        $tbl1->redeemable_amt = 0;
        $tbl1->non_redeemable = $this->MEMBERSHIP_POINTS;
        $tbl1->level_income = 0;
        $tbl1->leadership_income = 0;
        $tbl1->club_income = 0;
        $tbl1->transferin_amount = 0;
        $tbl1->transferout_amount = 0;
        $tbl1->save();
    }

     /**
     ************************************************************* called by updateClub()
     */
     private function getHierarchyClubCount($arr, $clubID){
        $cnt = 0;
        foreach($arr as $m){
            if ($m->designation_id == $clubID) 
            {
                $cnt++;
            }
        }
        return $cnt;
    }

     /**
     ************************************************************* called by updateClub()
     */
    private function getDownlineLevelCount($arr, $levelCtr){
        $cnt = 0;
        foreach($arr as $m){
            if ($m->level_ctr == $levelCtr) 
            {
                return $m->level_count;
            }
        }
        return 0;
    }

    
    // private function getQualifyingClubMembers($clubID, $designationID){
    //     $req = 0;
    //     foreach($this->arrayClubMaster as $club){
    //         if ($club->id == $clubID) 
    //         {
    //             switch($designationID){
    //                 case 2: //bronz
    //                     $req = $club->bronz_req;
    //                     break;
    //                 case 3: //bronz
    //                     $req = $club->silver_req;
    //                     break;
    //                 case 4: //bronz
    //                     $req = $club->gold_req;
    //                     break;
    //                 case 5: //bronz
    //                     $req = $club->diamond_req;
    //                     break;
    
    //             }
    //         }
    //     }
    //     return $req;
    // }


    // //Extract and Fill Level wise member count of parents
    // private function populateLevelWiseMemberCount($memberID){
    //     $sql = 'SELECT m1.member_id,m1.level_ctr, COUNT(m1.member_id) AS MemberCount
    //         FROM member_maps m1
    //         WHERE m1.member_id IN (SELECT m2.parent_id FROM member_maps m2 WHERE m2.member_id = ' . $memberID . ')
    //         GROUP BY m1.member_id,m1.level_ctr
    //         ORDER BY m1.member_id,m1.level_ctr';

    //     $tblTemp = DB::select($sql);

    //     foreach ($tblTemp as $rec){
    //         $this->arrayLevelWiseMemberCount[] = $rec;
    //     }
    // }


    // //Get Level Reward
    // private function getLevelReward($levelCtr){
    //     foreach($this->arrayLevelMaster as $m){
    //         if ($m->level == $levelCtr)
    //         {
    //             return $m->reward;
    //         }
    //         return '';
    //     }
    // }

    // //Get Level Commission Percent
    // private function getRequiredMembers($levelCtr){
    //     foreach($this->arrayLevelMaster as $m){
    //         if ($m->level == $levelCtr)
    //         {
    //             return $m->required_members;
    //         }
    //         return 0;
    //     }
    // }

 
    // private function getClub($count){
       
    //     foreach($this->arrayLevelWiseMemberCount as $m){
    //         if (($m->required_members >= $count)  && ($m->level_ctr))
    //         {
    //             return $m->member_count;
    //         }
    //         return 0;
    //     }
    // }

    // private function getCalculatedLevel($memberCount){
    //     foreach($this->arrayLevelMaster as $m){
    //         if ($m->required_members >= $memberCount)
    //         {
    //             return $m->level;
    //         }
    //         return 0;
    //     }
    // }



    // /**
    //  * GENERATE NEW TXNID FOR PAYMENT GATEWAY
    //  * ==================================================================
    //  */
    // public function newTxnID()
    // {
    //     $dt = Carbon::now();
    //     $dt1 = $dt->format('Ymd');

    //     $param = Param::where('param','TXN_COUNTER')->first();
    //     $intValue =$param->int_value;
    //     $prefix = $param->string_value;
    //     $param->int_value = $intValue + 1;
    //     $param->save();
    //     $retVal = $prefix.$dt1. Str::padLeft($intValue,5,'0');
    //     return $retVal;
    // } 

     

    /**
     * UPDATE PAYMENT STATUS AS REPORTED BY MOBILE APP
     * ======================================================================
     * If success => Create new Member and MemberUser
     *            => update Member table with temp_id
     * 
     * Methods to Invoke
     *      1. createMemberUser
     *      2. addMember
     *      3. MapMember
     *      4. UpdateLevelIncomes
     *      5. UpdateMobileRechargePoints
     *      6. UpdateLevelAchievers
     *      7. AddCashbackReward
     *      8. UpdateRewardIncome
     *      9. UpdateRewardTable
     *      10. UpdateTeamMobileRechargeIncome
     *      11. NotifyMembers
     */

    // private function updateDesignation(Request $request){

    // }
    
    // /**
    //  * 5. Update Mobile Recharge Points
    //  * ==============================================
    //  * 30 points added to recharge wallet 
    //  * [add in recharge_point_register]
    //  */
    // private function updateMobileRechargePoints(){
        

    // }

    // /**
    //  * 6. Update Level Achevers
    //  * ============================================
    //  * Depending upon no of members update level of a member
    //  */
    // private function updateLevelAchievers(){
    //     foreach($this->arrayParents as $parent){
    //         $current_level = $parent->current_level;
    //         $new_level = $current_level;

           
            
    //         $cnt = MemberMap::where('member_id',$parent->member_id)->get()->count();
    //         switch(true){
    //             case ($cnt >= $this->ROYALTY_REQ_NUM):
    //                 $new_level = 5;
    //                 break;
    //             case ($cnt >= $this->DIAMOND_REQ_NUM):
    //                 $new_level = 4;
    //                 break;
    //             case ($cnt >= $this->GOLD_REQ_NUM):
    //                 $new_level = 3;
    //                 break;
    //             case ($cnt >= $this->SILVER_REQ_NUM):
    //                 $new_level = 2;
    //                 break;
    //             case ($cnt >= $this->BRONZ_REQ_NUM):
    //                 $new_level = 1;
    //                 break;
    //             default:
    //                 $new_level = 0;
    //                 break;
    //         }
    //         if($current_level != $new_level){
    //             $tblMember = Member::where('member_id',$parent->member_id)->first();
    //             $tblMember->current_level = $new_level;
    //             $tblMember->save();
    //         }

    //     }
    // }
   

    // //Not needed at this point of time
    // private function updateRewardIncome(){

    // }

    // //Not needed at this point of time
    // private function updateTeamMobileRechargeIncome(){

    // }

}

