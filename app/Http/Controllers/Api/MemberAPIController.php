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
use App\Helpers\DBFunctions;

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
    private $dbf;
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
        $this->dbf = new DBFunctions();
        $this->middleware('auth:api')->except(['showUser','createTempUser','updatePaymentStatus','getRefererName','getRecipientName']);
    }

    public function showUser(Request $request){
        return "ANIL MISHRA"; //auth()->user();
    }

    //Requests API ===============================================================
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

    /** ======================= UPDATE PAYMENT AND CREATE MEMBER ======================
     *
     *populateParams();
     *populateLevelMaster();
     *populateClubMaster();
     *createMemberUser($request);
     *addMember($request);
     *populateParents($request->parent_id);
     *mapMember($request);
     *addMemberWallet($request->member_id);
     *updateLevelIncomes($request);
     *updateCurrentLevel($request);
     *updateClub($request->member_id);
     *updateRewards($request);
     *addRechargePoints($request);
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

       $pmtFlag = true;
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
               $pmtFlag = false;
               break;
           default:
               // $tblPaymentGateway->payment_id = $request->payment_id;
               $tblPaymentGateway->paid = false;
               $tblPaymentGateway->failure = false;
               $tblPaymentGateway->pending = true;
               $tblPaymentGateway->fake = false;
               $tblPaymentGateway->closed =false;
               $tblPaymentGateway->save();
               $pmtFlag = false;
               break;
       }

       if($pmtFlag == false){
        return response()->json(['status' => false, 'message' => 'Unsuccessful Payment']);
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
           $this->populateLevelMaster();
           $this->populateClubMaster();
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

           $this->addMemberWallet($request->member_id);
           $this->updateLevelIncomes($request);
           $this->updateClub($request->member_id);
           $this->updateClubIncome($request);
           $this->updateRewards($request);
           $this->addRechargePoints($request);

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
                return response()->json(['status' => false, 'message' => 'Invalid Referal Code']);
            }
            $memberName = $tblMember->first_name . ' ' . $tblMember->last_name;
            return response()->json(['status' => true, 'message' => $memberName]);
        }catch(Exception $e){
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
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


    // Private methods Fillers ===============================================================
    private function populateClubMaster(){
        $tblTemp = ClubMaster::all();
        foreach ($tblTemp as $rec){
            $this->arrayClubMaster[] = $rec;
        }
    }

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

    private function populateLevelMaster(){
        $tblTemp = LevelMaster::all();
        foreach ($tblTemp as $rec){
            $this->arrayLevelMaster[] = $rec;
        }
    }

    private function populateParents($member_id){
        DB::enableQueryLog();
        $tblMembers = MemberMap::join('members', 'member_maps.parent_id', '=', 'members.member_id')
        ->where('member_maps.member_id', $member_id)
        ->where('member_maps.level_ctr', '<', 12)
        // ->where('member_maps.level_ctr', '>', 0)
        ->get(['members.*','member_maps.level_ctr']);

        foreach ($tblMembers as $refTable){
            $this->arrayParents[] = $refTable;
        }
    }



    //Private Methods Getters =================================================================
    private function getCommissionPercent($levelCtr){
        foreach($this->arrayLevelMaster as $m){
            if ($m->level == $levelCtr)
            {
                return $m->level_percent;
            }
        }
        return 0;
    }

    private function getLevelMemberRequirementsForReward($levelCtr){
        foreach($this->arrayLevelMaster as $m){
            if ($m->level == $levelCtr)
            {
                return $m;
            }
        }
        return 0;
    }

    private function getClubRequirement($arr, $designation_id){
        foreach($arr as $m){
            if ($m->id == $designation_id)
            {
                return $m;
            }
        }
    }

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

    //Misc Private Functions
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
        $tblMember -> recharge_points =0; // $this->MEMBERSHIP_POINTS;
        $tblMember -> image = 'dummy.jpg';
        $tblMember -> designation_id = 1;
        $tblMember -> current_level = 0;
        $tblMember -> joining_date = Carbon::now();
        $tblMember -> save();
        $request -> unique_id = $tblMember -> unique_id;
    }

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

    private function updateLevelIncomes(Request $request){
        $l_commission = 0;
        $l_totalCommission = 0;
        $tblMemberMap = MemberMap::where('member_id',$request->member_id)->get();

        foreach ( $tblMemberMap as $memberMap){
            $level_ctr = $memberMap->level_ctr;
            if($level_ctr < 13){
                $tblMember = Member::where('member_id' , $memberMap->parent_id)->first();
                $l_Leadership1_beneficiary = $tblMember->parent_id;
                $l_Leadership2_beneficiary = $tblMember->grand_parent_id;

                $l_levelPercent = $this->getCommissionPercent($level_ctr);
                $l_commission = round($request->member_fee * $l_levelPercent * 0.01, 2);
                $l_Leadership1_income = round($l_commission * $this->LEVEL1_LEADERSHIP_INCOME * 0.01, 2);
                $l_Leadership2_income = round($l_commission * $this->LEVEL2_LEADERSHIP_INCOME * 0.01,2);

                $l_totalCommission += $l_commission;
                $l_totalCommission += $l_Leadership1_income ;
                $l_totalCommission += $l_Leadership2_income;

                //Update Parent Commission
                $tbl_MemberWallet = MemberWallet::where('member_id',$memberMap->parent_id)->first();
                $tblMemberIncome = new MemberIncome();
                $tblMemberIncome->member_id = $memberMap->parent_id;
                $tblMemberIncome->income_type = 'Level Income';
                $tblMemberIncome->ref_member_id = $request->member_id;
                $tblMemberIncome->level_percent = $l_levelPercent;
                $tblMemberIncome->commission = $l_commission;
                $tblMemberIncome->deduction = 0;
                $tblMemberIncome->ref_amount = $request->member_fee;
                $tblMemberIncome->balance = $tbl_MemberWallet->redeemable_amt + $l_commission;
                $tblMemberIncome->save();

                //Update Parent Wallet
                $tbl_MemberWallet->total_members += 1;
                $tbl_MemberWallet->redeemable_amt += $l_commission;
                $tbl_MemberWallet->level_income +=  $l_commission;
                $tbl_MemberWallet->save();

                //Update Leadership Income1
                $tbl_MemberWallet = MemberWallet::where('member_id', $l_Leadership1_beneficiary)->first();
                $tblMemberIncome = new MemberIncome();
                $tblMemberIncome->member_id = $l_Leadership1_beneficiary;
                $tblMemberIncome->income_type = 'Leadership Income1';
                $tblMemberIncome->ref_member_id = $request->member_id;
                $tblMemberIncome->direct_l1_percent = $this->LEVEL1_LEADERSHIP_INCOME;
                $tblMemberIncome->commission =  $l_Leadership1_income;
                $tblMemberIncome->ref_amount = $l_commission;
                $tblMemberIncome->deduction = 0;
                $tblMemberIncome->balance =  $tbl_MemberWallet->redeemable_amt + $l_Leadership1_income;
                $tblMemberIncome->save();

                $tbl_MemberWallet->redeemable_amt += $l_Leadership1_income;
                $tbl_MemberWallet->leadership_income +=  $l_Leadership1_income;
                $tbl_MemberWallet->save();



                //Update Leadership Income2
                $tbl_MemberWallet = MemberWallet::where('member_id', $l_Leadership2_beneficiary)->first();
                $tblMemberIncome = new MemberIncome();
                $tblMemberIncome->member_id = $l_Leadership2_beneficiary;
                $tblMemberIncome->income_type = 'Leadership Income2';
                $tblMemberIncome->ref_member_id = $request->member_id;
                $tblMemberIncome->direct_l2_percent = $this->LEVEL2_LEADERSHIP_INCOME;
                $tblMemberIncome->commission =  $l_Leadership2_income;
                $tblMemberIncome->ref_amount = $l_commission;
                $tblMemberIncome->deduction = 0;
                $tblMemberIncome->balance = $tbl_MemberWallet->redeemable_amt + $l_Leadership2_income;
                $tblMemberIncome->save();


                $tbl_MemberWallet->redeemable_amt += $l_Leadership2_income;
                $tbl_MemberWallet->leadership_income +=  $l_Leadership2_income;
                $tbl_MemberWallet->save();
            }
        };

    }

    private function updateClub($member_id){
        $l_bronzCount = 0;
        $l_silverCount = 0;
        $l_goldCount = 0;
        $l_diamondCount = 0;
        $l_royaltyCount = 0;

        $l_level0Count = 0;
        $l_level1Count = 0;
        $l_level2Count = 0;
        $l_level3Count = 0;
        $l_level4Count = 0;
        $l_level5Count = 0;
        $l_level6Count = 0;
        $l_level7Count = 0;
        $l_level8Count = 0;
        $l_level9Count = 0;
        $l_level10Count = 0;
        $l_level11Count = 0;
        $l_level12Count = 0;

        $l_bronzAchieved = false;
        $l_silverAchieved = false;
        $l_goldAchieved = false;
        $l_diamondAchieved = false;
        $l_royaltyAchieved = false;

        $l_parents = $this->dbf->dbFuncGetParents($member_id);

        //Fill Achievers and Level wise member count
        foreach($l_parents as $lp){
            $ll_memberID = $lp->member_id;
            $ll_designationChildren = $this->dbf->dbFuncDesignationWiseChildrenCount($ll_memberID);
            $ll_levelChildren = $this->dbf->dbFuncLevelWiseChildrenCount($ll_memberID);
            $ll_clubsAchieved = $this->dbf->dbFuncGetAllAchievementsOfAMember($ll_memberID);

            //Fill Designation wise member count
            foreach($ll_designationChildren as $dc){
                if($dc->designation_id == 2){
                    $l_bronzCount = $dc->total_members;
                }
                if($dc->designation_id == 3){
                    $l_silverCount = $dc->total_members;
                }
                if($dc->designation_id == 4){
                    $l_goldCount = $dc->total_members;
                }
                if($dc->designation_id == 5){
                    $l_diamondCount = $dc->total_members;
                }
                if($dc->designation_id == 6){
                    $l_royaltyCount = $dc->total_members;
                }
            }

            //Fill Level wise member count
            foreach($ll_levelChildren as $lc){
                if($lc->level_ctr == 0){
                    $l_level0Count = $lc->total_members;
                }
                if($lc->level_ctr == 1){
                    $l_level1Count = $lc->total_members;
                }
                if($lc->level_ctr == 2){
                    $l_level2Count = $lc->total_members;
                }
                if($lc->level_ctr == 3){
                    $l_level3Count = $lc->total_members;
                }
                if($lc->level_ctr == 4){
                    $l_level4Count = $lc->total_members;
                }
                if($lc->level_ctr == 5){
                    $l_level5Count = $lc->total_members;
                }
                if($lc->level_ctr == 6){
                    $l_level6Count = $lc->total_members;
                }
                if($lc->level_ctr == 7){
                    $l_level7Count = $lc->total_members;
                }
                if($lc->level_ctr == 8){
                    $l_level8Count = $lc->total_members;
                }
                if($lc->level_ctr == 9){
                    $l_level9Count = $lc->total_members;
                }
                if($lc->level_ctr == 10){
                    $l_level10Count = $lc->total_members;
                }
                if($lc->level_ctr == 11){
                    $l_level11Count = $lc->total_members;
                }
                if($lc->level_ctr == 12){
                    $l_level12Count = $lc->total_members;
                }

            }

            //Fill Clubs already have
            foreach($ll_clubsAchieved as $ca){
                if($ca->designation_id == 2){
                    $l_bronzAchieved=true;
                }
                if($ca->designation_id == 3){
                    $l_silverAchieved=true;
                }
                if($ca->designation_id == 4){
                    $l_goldAchieved=true;
                }
                if($ca->designation_id == 5){
                    $l_diamondAchieved=true;
                }
                if($ca->designation_id == 6){
                    $l_royaltyAchieved=true;
                }
            }

            //Update Bronz Club
            if($l_bronzAchieved == false){
                $ll_clb = $this->getClubRequirement($this->arrayClubMaster, 2);

                $ll_bronzRequired = $ll_clb->bronz_req;
                $ll_silverRequired = $ll_clb->silver_req;
                $ll_goldRequired = $ll_clb->gold_req;
                $ll_diamondRequired = $ll_clb->diamond_req;
                $ll_required_id =  $ll_clb->level_req_id;
                $ll_level_req_members = $ll_clb->level_req_members;

                $flag = true;
                if(($ll_bronzRequired > 0) && ($l_bronzCount < $ll_bronzRequired))
                    $flag = false;
                if (($ll_silverRequired > 0) && ($l_silverCount < $ll_silverRequired))
                    $flag = false;
                if (($ll_goldRequired > 0) && ($l_goldCount < $ll_goldRequired))
                    $flag = false;
                if (($ll_diamondRequired > 0) && ($l_diamondCount < $ll_diamondRequired))
                    $flag = false;


                if($ll_required_id > 0){
                    if($ll_required_id == 1){
                        if($l_level1Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 2){
                        if($l_level2Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 4){
                        if($l_level4Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 5){
                        if($l_level5Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                }

                if($flag == true){
                    $tblClubAchiever = new ClubAchiever();
                    $tblClubAchiever->member_id = $ll_memberID;
                    $tblClubAchiever->designation_id = 2;
                    $tblClubAchiever->tran_date = Carbon::now();
                    $tblClubAchiever->save();

                    $tblMember = Member::where('member_id', $ll_memberID)->first();
                    $tblMember->designation_id = 2;
                    $tblMember->save();
                }
            }


            //Update Silver Club
            if($l_silverAchieved == false){
                $ll_clb = $this->getClubRequirement($this->arrayClubMaster, 3);

                $ll_bronzRequired = $ll_clb->bronz_req;
                $ll_silverRequired = $ll_clb->silver_req;
                $ll_goldRequired = $ll_clb->gold_req;
                $ll_diamondRequired = $ll_clb->diamond_req;
                $ll_royaltyRequired = $ll_clb->royalty_req;
                $ll_required_id =  $ll_clb->level_req_id;
                $ll_level_req_members = $ll_clb->level_req_members;

                $flag = true;
                if(($ll_bronzRequired > 0) && ($l_bronzCount < $ll_bronzRequired))
                    $flag = false;
                if (($ll_silverRequired > 0) && ($l_silverCount < $ll_silverRequired))
                    $flag = false;
                if (($ll_goldRequired > 0) && ($l_goldCount < $ll_goldRequired))
                    $flag = false;
                if (($ll_diamondRequired > 0) && ($l_diamondCount < $ll_diamondRequired))
                    $flag = false;


                if($ll_required_id > 0){
                    if($ll_required_id == 2){
                        if($l_level2Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 4){
                        if($l_level4Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 5){
                        if($l_level5Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                }

                if($flag == true){
                    $tblClubAchiever = new ClubAchiever();
                    $tblClubAchiever->member_id = $ll_memberID;
                    $tblClubAchiever->designation_id = 3;
                    $tblClubAchiever->tram_date = Carbon::now();
                    $tblClubAchiever->save();

                    $tblMember = Member::where('member_id', $ll_memberID)->first();
                    $tblMember->designation_id = 3;
                    $tblMember->save();
                }

            }


            //Update Gold Club
            if($l_silverAchieved == false){
                $ll_clb = $this->getClubRequirement($this->arrayClubMaster, 4);

                $ll_bronzRequired = $ll_clb->bronz_req;
                $ll_silverRequired = $ll_clb->silver_req;
                $ll_goldRequired = $ll_clb->gold_req;
                $ll_diamondRequired = $ll_clb->diamond_req;
                $ll_royaltyRequired = $ll_clb->royalty_req;
                $ll_required_id =  $ll_clb->level_req_id;
                $ll_level_req_members = $ll_clb->level_req_members;

                $flag = true;
                if(($ll_bronzRequired > 0) && ($l_bronzCount < $ll_bronzRequired))
                    $flag = false;
                if (($ll_silverRequired > 0) && ($l_silverCount < $ll_silverRequired))
                    $flag = false;
                if (($ll_goldRequired > 0) && ($l_goldCount < $ll_goldRequired))
                    $flag = false;
                if (($ll_diamondRequired > 0) && ($l_diamondCount < $ll_diamondRequired))
                    $flag = false;


                if($ll_required_id > 0){
                    if($ll_required_id == 2){
                        if($l_level2Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 4){
                        if($l_level4Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 5){
                        if($l_level5Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                }

                if($flag == true){
                    $tblClubAchiever = new ClubAchiever();
                    $tblClubAchiever->member_id = $ll_memberID;
                    $tblClubAchiever->designation_id = 4;
                    $tblClubAchiever->tram_date = Carbon::now();
                    $tblClubAchiever->save();

                    $tblMember = Member::where('member_id', $ll_memberID)->first();
                    $tblMember->designation_id = 4;
                    $tblMember->save();
                }

            }

            //Update Diamond Club
            if($l_diamondAchieved == false){
                $ll_clb = $this->getClubRequirement($this->arrayClubMaster, 5);

                $ll_bronzRequired = $ll_clb->bronz_req;
                $ll_silverRequired = $ll_clb->silver_req;
                $ll_goldRequired = $ll_clb->gold_req;
                $ll_diamondRequired = $ll_clb->diamond_req;
                $ll_royaltyRequired = $ll_clb->royalty_req;
                $ll_required_id =  $ll_clb->level_req_id;
                $ll_level_req_members = $ll_clb->level_req_members;

                $flag = true;
                if(($ll_bronzRequired > 0) && ($l_bronzCount < $ll_bronzRequired))
                    $flag = false;
                if (($ll_silverRequired > 0) && ($l_silverCount < $ll_silverRequired))
                    $flag = false;
                if (($ll_goldRequired > 0) && ($l_goldCount < $ll_goldRequired))
                    $flag = false;
                if (($ll_diamondRequired > 0) && ($l_diamondCount < $ll_diamondRequired))
                    $flag = false;


                if($ll_required_id > 0){
                    if($ll_required_id == 2){
                        if($l_level2Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 4){
                        if($l_level4Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 5){
                        if($l_level5Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                }

                if($flag == true){
                    $tblClubAchiever = new ClubAchiever();
                    $tblClubAchiever->member_id = $ll_memberID;
                    $tblClubAchiever->designation_id = 5;
                    $tblClubAchiever->tram_date = Carbon::now();
                    $tblClubAchiever->save();

                    $tblMember = Member::where('member_id', $ll_memberID)->first();
                    $tblMember->designation_id = 5;
                    $tblMember->save();
                }
            }

            //Update Royalty Club
            if($l_diamondAchieved == false){
                $ll_clb = $this->getClubRequirement($this->arrayClubMaster, 6);

                $ll_bronzRequired = $ll_clb->bronz_req;
                $ll_silverRequired = $ll_clb->silver_req;
                $ll_goldRequired = $ll_clb->gold_req;
                $ll_diamondRequired = $ll_clb->diamond_req;
                $ll_royaltyRequired = $ll_clb->royalty_req;
                $ll_required_id =  $ll_clb->level_req_id;
                $ll_level_req_members = $ll_clb->level_req_members;

                $flag = true;
                if(($ll_bronzRequired > 0) && ($l_bronzCount < $ll_bronzRequired))
                    $flag = false;
                if (($ll_silverRequired > 0) && ($l_silverCount < $ll_silverRequired))
                    $flag = false;
                if (($ll_goldRequired > 0) && ($l_goldCount < $ll_goldRequired))
                    $flag = false;
                if (($ll_diamondRequired > 0) && ($l_diamondCount < $ll_diamondRequired))
                    $flag = false;


                if($ll_required_id > 0){
                    if($ll_required_id == 2){
                        if($l_level2Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 3){
                        if($l_level3Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 4){
                        if($l_level4Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                    if($ll_required_id == 5){
                        if($l_level5Count < $ll_level_req_members){
                            $flag = false;
                        }
                    }
                }

                if($flag == true){
                    $tblClubAchiever = new ClubAchiever();
                    $tblClubAchiever->member_id = $ll_memberID;
                    $tblClubAchiever->designation_id = 6;
                    $tblClubAchiever->tram_date = Carbon::now();
                    $tblClubAchiever->save();

                    $tblMember = Member::where('member_id', $ll_memberID)->first();
                    $tblMember->designation_id = 6;
                    $tblMember->save();
                }

            }
        }
    }

    private function updateClubIncome(Request $request){
        $l_memberFee = $request->member_fee;
        foreach($this->arrayClubMaster as $level){
            if($level->is_stto == false){ //CTO
                $l_clubPercent = $level->club_percent;
                $l_clubMembers = $this->dbf->getCompanyLevelAchievers($level->id);
                $l_count = count($l_clubMembers);
                if($l_count > 0){
                    $l_equalAmount = round(($l_memberFee * $l_clubPercent * 0.01) / $l_count, 2 );
                    foreach($l_clubMembers as $mbr){
                        $tbl1 = MemberWallet::where('member_id', $mbr->member_id)->first();
                        $tbl1->redeemable_amt = $tbl1->redeemable_amt + $l_equalAmount;
                        $tbl1->club_income = $tbl1->club_income + $l_equalAmount;
                        $tbl1->save();

                        $tblMemberIncome = new MemberIncome();
                        $tblMemberIncome->member_id = $mbr->member_id;
                        $tblMemberIncome->income_type = 'Club Income';
                        $tblMemberIncome->ref_member_id = $request->member_id;
                        $tblMemberIncome->club_percent = $l_clubPercent;
                        // $tblMemberIncome->actual_percent
                        $tblMemberIncome->cto = true;
                        $tblMemberIncome->stto = false;
                        $tblMemberIncome->ref_amount = $l_memberFee;
                        $tblMemberIncome->commission = $l_equalAmount;
                        $tblMemberIncome->balance = $tbl1->redeemable_amt;
                        $tblMemberIncome->save();
                    }
                }

            } else { //STTO
                $l_clubPercent = $level->club_percent;
                $l_clubMembers = $this->dbf->getUplinkLevelAchievers($request->member_id, $level->id);
                $l_count = count($l_clubMembers);
                if($l_count > 0){
                    $l_equalAmount = round(($l_memberFee * $l_clubPercent * 0.01) / $l_count, 2) ;
                    foreach($l_clubMembers as $mbr){
                        $tbl1 = MemberWallet::where('member_id', $mbr->member_id)->first();
                        $tbl1->redeemable_amt = $tbl1->redeemable_amt + $l_equalAmount;
                        $tbl1->club_income = $tbl1->club_income + $l_equalAmount;
                        $tbl1->save();

                        $tblMemberIncome = new MemberIncome();
                        $tblMemberIncome->member_id = $mbr->member_id;
                        $tblMemberIncome->income_type = 'Club Income';
                        $tblMemberIncome->ref_member_id = $request->member_id;
                        $tblMemberIncome->club_percent = $l_clubPercent;
                        // $tblMemberIncome->actual_percent
                        $tblMemberIncome->cto = false;
                        $tblMemberIncome->stto = true;
                        $tblMemberIncome->ref_amount = $l_memberFee;
                        $tblMemberIncome->commission = $l_equalAmount;
                        $tblMemberIncome->balance = $tbl1->redeemable_amt;
                        $tblMemberIncome->save();
                    }
                }
            }
        }
    }


    private function updateClubDiscarded(){
        //loop through all the parents of newly created member
        foreach($this->arrayParents as $parent){
            //fill DownlinesArray of the parentmember
            $downlineArray = array();
            $childLevelCountArray =array();
            $clubAchieversArray = array();

            //populate current parents club achiever records for want of un achieved clubs
            $tblClubAchievers = ClubAchiever::where('member_id', $parent->parent_id)->get();
            foreach ($tblClubAchievers as $rec){
                $clubAchieversArray[] = $rec;
            }

            //fill downline of the parentmember
            $strSQL = 'SELECT m.member_id,m.designation_id
                FROM member_maps p
                INNER JOIN members m ON p.member_id=m.member_id
                WHERE p.parent_id=' . $parent->parent_id;
            $tblDownline = DB::select($strSQL);
            foreach ($tblDownline as $rec){
                $downlineArray[] = $rec;
            }
            //Fill level wise count of childs
            $strSQL = 'SELECT level_ctr, COUNT(level_ctr) AS level_count
                FROM member_maps p
                WHERE p.parent_id=' . $parent->parent_id . '  GROUP BY level_ctr';
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
                            $tblClubAchiever->member_id = $parent->parent_id;
                            $tblClubAchiever->designation_id = 2;
                            $tblClubAchiever->tran_date = Carbon::now();
                            $tblClubAchiever->save();

                            $tblMember = Member::where('member_id', $parent->parent_id)->first();
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
                            $tblClubAchiever->member_id = $parent->parent_id;
                            $tblClubAchiever->designation_id = 3;
                            $tblClubAchiever->tram_date = Carbon::now();
                            $tblClubAchiever->save();

                            $tblMember = Member::where('member_id', $parent->parent_id)->first();
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
                            $tblClubAchiever->member_id = $parent->parent_id;
                            $tblClubAchiever->designation_id = 4;
                            $tblClubAchiever->tram_date = Carbon::now();
                            $tblClubAchiever->save();

                            $tblMember = Member::where('member_id', $parent->parent_id)->first();
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
                            $tblClubAchiever->member_id = $parent->parent_id;
                            $tblClubAchiever->designation_id = 5;
                            $tblClubAchiever->tram_date = Carbon::now();
                            $tblClubAchiever->save();

                            $tblMember = Member::where('member_id', $parent->parent_id)->first();
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
                            $tblClubAchiever->member_id = $parent->parent_id;
                            $tblClubAchiever->designation_id = 6;
                            $tblClubAchiever->tram_date = Carbon::now();
                            $tblClubAchiever->save();

                            $tblMember = Member::where('member_id', $parent->parent_id)->first();
                            $tblMember->designation_id = 6;
                            $tblMember->save();
                        }
                    }
                }
            }
        }
    }

    private function updateRewards(Request $request){
        //Loop through parents
        //Count levelwise members
        //loop through level_masters
        //      if required_members matches levelcount then update reward table

        foreach($this->arrayParents as $parent){
            $l_levelWiseChildren = $this->dbf->dbFuncLevelWiseChildrenCount($parent->member_id);
            $l_memberRewards = $this->dbf->dbFuncMemberRewards($parent->member_id);

            $l_level1ChildrenCount = 0;
            $l_level2ChildrenCount = 0;
            $l_level3ChildrenCount = 0;
            $l_level4ChildrenCount = 0;
            $l_level5ChildrenCount = 0;
            $l_level6ChildrenCount = 0;
            $l_level7ChildrenCount = 0;
            $l_level8ChildrenCount = 0;
            $l_level9ChildrenCount = 0;
            $l_level10ChildrenCount = 0;
            $l_level11ChildrenCount = 0;
            $l_level12ChildrenCount = 0;

            $l_level1RewardTaken = false;
            $l_level2RewardTaken = false;
            $l_level3RewardTaken = false;
            $l_level4RewardTaken = false;
            $l_level5RewardTaken = false;
            $l_level6RewardTaken = false;
            $l_level7RewardTaken = false;
            $l_level8RewardTaken = false;
            $l_level9RewardTaken = false;
            $l_level10RewardTaken = false;
            $l_level11RewardTaken = false;
            $l_level12RewardTaken = false;

            foreach($l_levelWiseChildren as $cnt){
                if($cnt->level_ctr == 1){
                    $l_level1ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 2){
                    $l_level2ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 3){
                    $l_level3ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 4){
                    $l_level4ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 5){
                    $l_level5ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 6){
                    $l_level6ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 7){
                    $l_level7ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 8){
                    $l_level8ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 9){
                    $l_level9ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 10){
                    $l_level10ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 11){
                    $l_level11ChildrenCount = $cnt->total_members;
                }
                if($cnt->level_ctr == 12){
                    $l_level12ChildrenCount = $cnt->total_members;
                }

            }

            foreach($l_memberRewards as $rewards){
                if($rewards->level_id == 1){
                    $l_level1RewardTaken = true;
                }else
                if($rewards->level_id == 2){
                    $l_level2RewardTaken = true;
                }
                if($rewards->level_id == 3){
                    $l_level3RewardTaken = true;
                }
                if($rewards->level_id == 4){
                    $l_level4RewardTaken = true;
                }
                if($rewards->level_id == 5){
                    $l_level5RewardTaken = true;
                }
                if($rewards->level_id == 6){
                    $l_level6RewardTaken = true;
                }
                if($rewards->level_id == 7){
                    $l_level7RewardTaken = true;
                }
                if($rewards->level_id == 8){
                    $l_level8RewardTaken = true;
                }
                if($rewards->level_id == 9){
                    $l_level9RewardTaken = true;
                }
                if($rewards->level_id == 10){
                    $l_level10RewardTaken = true;
                }
                if($rewards->level_id == 11){
                    $l_level11RewardTaken = true;
                }
                if($rewards->level_id == 12){
                    $l_level12RewardTaken = true;
                }
            }

            if($l_level1RewardTaken == false){
                $l_levelMemberRequirement = $this->getLevelMemberRequirementsForReward(1);
                if($l_level1ChildrenCount > $l_levelMemberRequirement->required_members){
                    $tblMemberRewards = new MemberRewards();
                    $tblMemberRewards->member_id = $parent->member_id;
                    $tblMemberRewards->level_id = 1;
                    $tblMemberRewards->member_count = $l_level1ChildrenCount;
                    $tblMemberRewards->tran_date = Carbon::now();
                    $tblMemberRewards->reward_name = $l_levelMemberRequirement->reward;
                    $tblMemberRewards->qualifying_date = Carbon::now();
                    $tblMemberRewards->save();
                }
            }

            if($l_level2RewardTaken == false){
                $l_levelMemberRequirement = $this->getLevelMemberRequirementsForReward(2);
                if($l_level2ChildrenCount > $l_levelMemberRequirement->required_members){
                    $tblMemberRewards = new MemberRewards();
                    $tblMemberRewards->member_id = $parent->member_id;
                    $tblMemberRewards->level_id = 2;
                    $tblMemberRewards->member_count = $l_level2ChildrenCount;
                    $tblMemberRewards->tran_date = Carbon::now();
                    $tblMemberRewards->reward_name = $l_levelMemberRequirement->reward;
                    $tblMemberRewards->qualifying_date = Carbon::now();
                    $tblMemberRewards->save();
                }
            }

            if($l_level3RewardTaken == false){
                $l_levelMemberRequirement = $this->getLevelMemberRequirementsForReward(3);
                if($l_level3ChildrenCount > $l_levelMemberRequirement->required_members){
                    $tblMemberRewards = new MemberRewards();
                    $tblMemberRewards->member_id = $parent->member_id;
                    $tblMemberRewards->level_id = 3;
                    $tblMemberRewards->member_count = $l_level3ChildrenCount;
                    $tblMemberRewards->tran_date = Carbon::now();
                    $tblMemberRewards->reward_name = $l_levelMemberRequirement->reward;
                    $tblMemberRewards->qualifying_date = Carbon::now();
                    $tblMemberRewards->save();
                }
            }
            if($l_level4RewardTaken == false){
                $l_levelMemberRequirement = $this->getLevelMemberRequirementsForReward(4);
                if($l_level4ChildrenCount > $l_levelMemberRequirement->required_members){
                    $tblMemberRewards = new MemberRewards();
                    $tblMemberRewards->member_id = $parent->member_id;
                    $tblMemberRewards->level_id = 4;
                    $tblMemberRewards->member_count = $l_level4ChildrenCount;
                    $tblMemberRewards->tran_date = Carbon::now();
                    $tblMemberRewards->reward_name = $l_levelMemberRequirement->reward;
                    $tblMemberRewards->qualifying_date = Carbon::now();
                    $tblMemberRewards->save();
                }
            }
            if($l_level5RewardTaken == false){
                $l_levelMemberRequirement = $this->getLevelMemberRequirementsForReward(5);
                if($l_level5ChildrenCount > $l_levelMemberRequirement->required_members){
                    $tblMemberRewards = new MemberRewards();
                    $tblMemberRewards->member_id = $parent->member_id;
                    $tblMemberRewards->level_id = 5;
                    $tblMemberRewards->member_count = $l_level5ChildrenCount;
                    $tblMemberRewards->tran_date = Carbon::now();
                    $tblMemberRewards->reward_name = $l_levelMemberRequirement->reward;
                    $tblMemberRewards->qualifying_date = Carbon::now();
                    $tblMemberRewards->save();
                }
            }
            if($l_level6RewardTaken == false){
                $l_levelMemberRequirement = $this->getLevelMemberRequirementsForReward(6);
                if($l_level6ChildrenCount > $l_levelMemberRequirement->required_members){
                    $tblMemberRewards = new MemberRewards();
                    $tblMemberRewards->member_id = $parent->member_id;
                    $tblMemberRewards->level_id = 6;
                    $tblMemberRewards->member_count = $l_level6ChildrenCount;
                    $tblMemberRewards->tran_date = Carbon::now();
                    $tblMemberRewards->reward_name = $l_levelMemberRequirement->reward;
                    $tblMemberRewards->qualifying_date = Carbon::now();
                    $tblMemberRewards->save();
                }
            }
        }
    }

    private function addRechargePoints($request){
        $this->addRechargePointRecord($request->member_id,  $request->payment_int_id);
    }

    private function addRechargePointRecord($member_id, $payment_id){
        $tblMember =  Member::where('member_id', $member_id)->first();
        $l_parent_id = $tblMember->parent_id;

        $tblMemberWallet = MemberWallet::where('member_id', $member_id)->first();
        $tbl = new RechargePointRegister();
        $tbl->member_id = $member_id;
        $tbl->ref_member_id = $member_id;
        $tbl->tran_date = date('Y-m-d H:i:s');
        $tbl->payment_id = $payment_id;
        $tbl->welcome_points_added = $this->MEMBERSHIP_POINTS;
        $tbl->welcome_points_balance += $tblMemberWallet->welcome_amt + $this->MEMBERSHIP_POINTS;
        $tbl->tran_type = 'MEMBERSHIP_BONUS';
        $tbl->save();

        $tblMemberWallet->welcome_amt = $tblMemberWallet->welcome_amt + $this->MEMBERSHIP_POINTS;
        $tblMemberWallet->save();


        $tblMemberWallet = MemberWallet::where('member_id', $l_parent_id)->first();
        $tbl = new RechargePointRegister();
        $tbl->member_id = $l_parent_id;
        $tbl->ref_member_id = $member_id;
        $tbl->tran_date = date('Y-m-d H:i:s');
        $tbl->payment_id = $payment_id;
        $tbl->welcome_points_added = $this->MEMBERSHIP_POINTS;
        $tbl->welcome_points_balance += $tblMemberWallet->welcome_amt + $this->MEMBERSHIP_POINTS;
        $tbl->tran_type = 'LEADERSHIP_BONUS';
        $tbl->save();

        $tblMemberWallet->welcome_amt = $tblMemberWallet->welcome_amt + $this->MEMBERSHIP_POINTS;
        $tblMemberWallet->save();

    }

    private function addMemberWallet($member_id){
        $tbl1 = new MemberWallet();
        $tbl1->member_id = $member_id;
        $tbl1->total_members = 0;
        $tbl1->welcome_amt = $this->MEMBERSHIP_FEE; // + $this->MEMBERSHIP_POINTS;
        $tbl1->redeemable_amt = 0;
        $tbl1->non_redeemable = 0; //$this->MEMBERSHIP_POINTS;
        $tbl1->level_income = 0;
        $tbl1->leadership_income = 0;
        $tbl1->club_income = 0;
        $tbl1->transferin_amount = 0;
        $tbl1->transferout_amount = 0;
        $tbl1->save();
    }

}

