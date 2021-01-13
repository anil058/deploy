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
use App\Models\ClubMaster;
use App\Models\LevelAchiever;
use App\Models\LevelMaster;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\MemberIncome;
use App\Models\MemberUser;
use App\Models\MemberMap;
use App\Models\MemberRewards;
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
    private $CASHBACK_REWARD;
    private $TAX_PERCENT;
    private $MEMBER_COUNTER;
    private $MEMBERSHIP_FEE;

    //Memory Tables
    private $arrayParents = array();
    private $arrayLevelMaster = array();
    private $arrayClubMaster = array();
    private $arrayLevelWiseMemberCount = array();

    //Extract and Fill arrayParents for current member
    private function populateParents($member_id){
        DB::enableQueryLog();
        $tblMembers = MemberMap::join('members', 'member_maps.member_id', '=', 'members.member_id')
        ->where('member_maps.member_id', $member_id)
        ->where('member_maps.level_ctr', '<', 12)
        ->where('member_maps.level_ctr', '>', 0)
        ->get(['members.*','member_maps.level_ctr']);

        foreach ($tblMembers as $refTable){
            $this->arrayParents[] = $refTable;
        }
    }

    //Extract and Fill arrayParents for current member
    private function populateLevelMaster(){
        $tblTemp = LevelMaster::all();
        foreach ($tblTemp as $rec){
            $this->arrayLevelMaster[] = $rec;
        }
    }
    
    //Extract and Fill arrayParents for current member
    private function populateClubMaster(){
        $tblTemp = ClubMaster::all();
        foreach ($tblTemp as $rec){
            $this->arrayClubMaster[] = $rec;
        }
    }

    //Extract and Fill Level wise member count of parents
    private function populateLevelWiseMemberCount($memberID){
        $sql = 'SELECT m1.member_id,m1.level_ctr, COUNT(m1.member_id) AS MemberCount
            FROM member_maps m1
            WHERE m1.member_id IN (SELECT m2.parent_id FROM member_maps m2 WHERE m2.member_id = ' . $memberID . ')
            GROUP BY m1.member_id,m1.level_ctr
            ORDER BY m1.member_id,m1.level_ctr';

        $tblTemp = DB::select($sql);

        foreach ($tblTemp as $rec){
            $this->arrayLevelWiseMemberCount[] = $rec;
        }
    }

    //Extract and Fill Parameters from params
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
                    $this->CASHBACK_REWARD = $refTable->int_value;
                    break;
                default :
                $this->ROYALTY_REQ_NUM = 0;
            }
        }
    }

    //Get Level Commission Percent
    private function getCommissionPercent($levelCtr){
        foreach($this->arrayLevelMaster as $m){
            if ($m->level == $levelCtr)
            {
                return $m->level_percent;
            }
        }
        return 0;
    }

    //Get Level Reward
    private function getLevelReward($levelCtr){
        foreach($this->arrayLevelMaster as $m){
            if ($m->level == $levelCtr)
            {
                return $m->reward;
            }
            return '';
        }
    }

    //Get Level Commission Percent
    private function getRequiredMembers($levelCtr){
        foreach($this->arrayLevelMaster as $m){
            if ($m->level == $levelCtr)
            {
                return $m->required_members;
            }
            return 0;
        }
    }

    // private function getMemberCountAtLevel($levelCtr,$memberID){
    //     foreach($this->arrayLevelWiseMemberCount as $m){
    //         if (($m->required_members >= $memberCount)  && ($m->level_ctr))
    //         {
    //             return $m->member_count;
    //         }
    //         return 0;
    //     }
    // }

    private function getCalculatedLevel($memberCount){
        foreach($this->arrayLevelMaster as $m){
            if ($m->required_members >= $memberCount)
            {
                return $m->level;
            }
            return 0;
        }
    }



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
        $memberCode = $prefix . Str::padLeft($intValue,9,'0');
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
                'referal_code' => 'required|max:10|min:10',
                "email" => "required|email",
                "password" => "required|string|max:50",
                "address" => "string|max:200",
                // "otp" => "required|numeric|min:1000|max:9999",
            ]);
             
            // $txn_id = $this->newTxnID();
            // $txn_id = createRazorpayTempOrder()
            // dd($txn_id);
            $this->populateParams();
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
           
            generateOTP($request->mobile_no);

            $orderid = "";
            $orderid = createRazorpayTempOrder($tempUser->id, $membershipFee, $taxPercent);
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
        $request->password = $tblTempMember->password;
        $request->name = $tblTempMember->first_name;
        $request->member_fee = $tblTempMember->membership_fee;

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
            $this->populateLevelWiseMemberCount($request->member_id);


            $this->updateLevelIncomes($request);
            // $this->updateCurrentLevel($request);
            $this->updateParentLevelAndRewards($request);
            $this->addCashbackReward($request);
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



    //1. Create a member login [DONE]
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

    //2. Create a record in Member table [DONE]
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
        $tblMember -> recharge_points = $this->CASHBACK_REWARD + $request->member_fee;
        $tblMember -> image = 'dummy.jpg';
        $tblMember -> designation_id = 1;
        $tblMember -> current_level = 0;
        $tblMember -> joining_date = Carbon::now();
        $tblMember -> save();
        $request -> unique_id = $tblMember -> unique_id;

    }

    /**
     * 3. MAP MEMBER [DONE]
     * ==================================================================
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
            'level_ctr' => 0]);
        
        foreach ($tblMemberMap as $memberMap){
            MemberMap::create([
                'member_id' => $request->member_id, 
                'parent_id' => $memberMap->parent_id,
                'level_ctr' => $memberMap->level_ctr + 1]);
        };

    }

    /**
     * 4. Update Incomes (Level and Direct Sponser Incomes) [DONE]
     * ==================================================
     * Update Level Incomes
     * Update Direct Sponser Income
     * Distribute Income according to refables
     */
    private function updateLevelIncomes(Request $request){
        // $tblMembers = MemberMap::join('members', 'member_maps.member_id', '=', 'members.member_id')
        //             ->where('member_maps.member_id', $request->member_id)
        //             ->get(['members.*']);

        $l_totalPercent = 0;
        $l_l1Commission = 0;
        $l_l2Commission = 0;
        $l_level3Member_id = 0;
        $l_commission = 0;

        //Level Income Calculation
        //======================================
        //Update Member Income if there is a candidate
        $tblMemberMap = MemberMap::where('member_id',$request->member_id)->get();
        foreach ( $tblMemberMap as $memberMap){
            $level_ctr = $memberMap->level_ctr + 1;
            //Calculate Level Commission
            if($level_ctr < 13){
                //Update Commission
                $l_levelPercent = $this->getCommissionPercent($level_ctr);
                $l_totalPercent += $l_levelPercent;
                $l_commission = $request->member_fee * $l_levelPercent * 0.01;
        
                if($level_ctr == 1){
                    $l_l1Commission += $l_commission * 5 * 0.01;
                }
                if($level_ctr == 2){
                    $l_l2Commission += $l_commission * 3 * 0.01;
                }
                if($level_ctr == 3){
                    $l_level3Member_id = $memberMap->member_id;
                }
                $tblMemberIncome = new MemberIncome();
                $tblMemberIncome->member_id = $memberMap->parent_id;
                $tblMemberIncome->income_type = 'Level Income';
                $tblMemberIncome->ref_member_id = $request->member_id;
                $tblMemberIncome->level_percent = $l_levelPercent;
                $tblMemberIncome->commission = $l_commission;
                $tblMemberIncome->ref_amount = $request->member_fee;
                $tblMemberIncome->amount = $l_commission;
                $tblMemberIncome->save();
            }
        };

        //Direct Sponser Income Calculation
        //===============================
        //Calculate and Add a Record for Direct Income in Level 3
        // In no one is at level 3 then add the amount to mansha
        $l_level3Members = MemberMap::where('member_id', $l_level3Member_id)
                    ->where('level_ctr', 1)
                    ->get();
        if($l_level3Members != null){
            if($l_level3Members->count() ==5){
                $tblMemberIncome = new MemberIncome();
                $tblMemberIncome->member_id = $l_level3Member_id;
                $tblMemberIncome->income_type = 'Direct Income';
                $tblMemberIncome->ref_member_id = $request->member_id;
                $tblMemberIncome->direct_l1_percent = 5;
                $tblMemberIncome->direct_l2_percent = 3;
                $l_commission = $request->member_fee * (100 - $l_totalPercent) * 0.01;
                $tblMemberIncome->commission = $l_commission;
                $tblMemberIncome->ref_amount = $request->member_fee;
                $tblMemberIncome->amount = $l_l1Commission + $l_l2Commission;
                $tblMemberIncome->save();                
            }
        }

        //Rest amount to be credited to Manshaa
        $l_commission = $request->member_fee * (100 - $l_totalPercent) * 0.01;
        if($l_commission > 0){
            $l_leftovers = $l_commission - ($l_l1Commission + $l_l2Commission);
            // if($l_leftovers > 0){
            $tblMemberIncome = new MemberIncome();
            $tblMemberIncome->member_id = 0;
            $tblMemberIncome->income_type = 'Level Income';
            $tblMemberIncome->ref_member_id = $request->member_id;
            $tblMemberIncome->level_percent = (100 - $l_totalPercent);
            $tblMemberIncome->commission = $l_commission;
            $tblMemberIncome->ref_amount = $request->member_fee;
            $tblMemberIncome->amount = $l_leftovers;
            $tblMemberIncome->save();
        }
    }


    // private function updateCurrentLevel(Request $request){
    //      //Change Current Level
    //      $tblMember = Member::where('member_id',$request->member_id);
    //      $memberCount = MemberMap::where('parent_id', $request->member_id)->count();
    //      $l_calculated_level = $this->getCalculatedLevel($memberCount);
    //      if($l_calculated_level != $memberCount){
    //         $tblMember->current_level = $l_calculated_level;
    //         $tblMember->save();
    //      }
    // }
    
    //Update current levels of parents and Rewards thereof
    private function updateParentLevelAndRewards(Request $request){
        foreach($this->arrayParents as $m){
            $current_date = date("Y-m-d H:i:s");
            $l_memberID = $m->member_id;
            $l_memberCount = Member::where('member_id', $l_memberID)->count();
            $l_calculatedLevel = $this->getCalculatedLevel($l_memberCount);
            $l_currentLevel = $m->level_ctr;

            // $l_memberCount = $m->member_count + 1;
            
            if($l_calculatedLevel != $l_currentLevel){
                DB::update('update members set current_level = ? where member_id = ?',[$l_calculatedLevel,$l_memberID]);
                $l_reward = $this->getLevelReward($l_calculatedLevel);
                if(strlen(trim($l_reward)) >0){
                    $tblReward = new MemberRewards();
                    $tblReward->member_id = $l_memberID;
                    $tblReward->level_id = $l_calculatedLevel;
                    $tblReward->member_count = $l_memberCount;
                    $tblReward->tran_date = $current_date;
                    $tblReward->reward_name = $l_reward;
                    $tblReward->save();
                }

                $tblLevelAchiever = new LevelAchiever();
                $tblLevelAchiever->member_id = $l_memberID;
                $tblLevelAchiever->level_id = $l_calculatedLevel;
                $tblLevelAchiever->tran_date = $current_date;
                $tblLevelAchiever->qualifying_date = $current_date;
                $tblLevelAchiever->save();
            }
        }
    }

    private function updateDesignation(Request $request){

    }
    
    /**
     * 5. Update Mobile Recharge Points
     * ==============================================
     * 30 points added to recharge wallet 
     * [add in recharge_point_register]
     */
    private function updateMobileRechargePoints(){
        

    }

    private function addCashbackReward($request){
        $tbl = new RechargePointRegister();
        $tbl->member_id = $request->member_id;
        $tbl->payment_id = $request->payment_int_id;
        $tbl->tran_date = date('Y-m-d H:i:s');
        $tbl->recharge_points_added = $request->member_fee;
        $tbl->balance_points += $this->CASHBACK_REWARD;
        $tbl->save();

        $tbl = new RechargePointRegister();
        $tbl->member_id = $request->member_id;
        $tbl->payment_id = $request->payment_int_id;
        $tbl->tran_date = date('Y-m-d H:i:s');
        $tbl->recharge_points_added = $this->CASHBACK_REWARD;
        $tbl->balance_points += $this->CASHBACK_REWARD;
        $tbl->save();

    }

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

