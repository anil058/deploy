<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\PaymentGateway;
use App\Models\Param;
use App\Models\MemberDeposit;
use App\Models\MemberRewards;
use App\Models\MemberMap;
use App\Models\MemberWallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;


class TranApiController extends Controller
{
    private $CASHBACK_REWARD;
    private $TAX_PERCENT;
    private $MEMBER_COUNTER;
    private $MEMBERSHIP_FEE;

    private $arrayTeam = array();

    public function __construct()
    {
        $this->middleware('auth:api')->except(['MemberRewards']);
    }

    private function populateTeam($member_id){
        DB::enableQueryLog();
        $tblMembers = MemberMap::join('members', 'member_maps.member_id', '=', 'members.member_id')
        ->where('member_maps.parent_id', $member_id)
        ->where('member_maps.level_ctr', '<', 12)
        ->where('member_maps.level_ctr', '>', 0)
        ->get(['members.*','member_maps.level_ctr']);

        foreach ($tblMembers as $refTable){
            $this->arrayTeam[] = $refTable;
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
                    $this->CASHBACK_REWARD = $refTable->int_value;
                    break;
                default :
            }
        }
    }

    public function GetTxnID(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric'
                ]);
    
            //General request validation
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $errors]);
            }
    
            $this->populateParams();
            
            $result = createRazorpayMoneyOrder($request->user()->id, $request->amount, $this->TAX_PERCENT);
            if(count($result) != 2){
                $response = ['status' => false, 
                'message' => 'Could not create payment order',
                ];
                return response($response, 200);
            }
            $response = ['status' => true, 
            'id' => $result['id'],
            'txn_id' => $result['txn_id'], 
            'amount' => $request->amount,
            'message' => 'Successfully Created Payment Order',
            ];
            return response($response, 200);

            // if(strlen($orderid) == 0){
            //     $response = ['status' => false, 
            //     'message' => 'Could not create payment order',
            //     ];
            //     return response($response, 200);
            // }
            // $response = ['status' => true, 
            // 'ID' => 0,
            // 'txn_id' => $orderid, 
            // 'amount' => $request->amount,
            // 'message' => 'Successfully Created Payment Order',
            // ];
            // return response($response, 200);
   
        } catch(Exception $e) {
                $response = ['status' => false, 
                'message' => 'Could not create payment order',
                ];
                return response($response, 200);
        }     
    }

    public function AddMoney(Request $request){
        DB::beginTransaction();
        try{
            //Validate Input
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric',
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
    
            //Validate against PaymentGateway table
            $tblPaymentGateway = PaymentGateway::find($request->id);

            if($tblPaymentGateway == null){
                $response = ['status' => false, 'message' => 'Invalid txnid'];
                return response($response, 200);
            }

            //Update Payment Gateway
            switch($request->status){
                case 'SUCCESS':
                    $tblPaymentGateway->payment_id = $request->payment_id;
                    // $tblPaymentGateway->member_id = $request->member_id;
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

            $tblMemberDeposits = new MemberDeposit();
            $tblMemberDeposits->member_id = $request->user()->id;
            $tblMemberDeposits->gateway_id = $tblPaymentGateway->id;
            $tblMemberDeposits->amount = $tblPaymentGateway->amount;
            $tblMemberDeposits->tax_percent = $tblPaymentGateway->tax_percent;
            $tblMemberDeposits->tax_amount = $tblPaymentGateway->tax_amount;
            $tblMemberDeposits->net_amount = $tblPaymentGateway->net_amount;
            $tblMemberDeposits->deposit_type = 'TOPUP';
            $tblMemberDeposits->save();

            $tblMemberWallet = MemberWallet::find( $request->user()->id);
            $tblMemberWallet->non_redeemable = $tblPaymentGateway->amount;
            $tblMemberWallet->save();
            
            DB::commit();
            $response = ['status' => true, 'message' => 'Successfully Added Money'];
            return response($response, 200);
        } catch(Exception $ex) {
            $response = ['status' => false, 'message' => $ex->getMessage()];
            DB::rollBack();
            return response($response, 200);
        }

    }

    public function GetPayments(Request $request){
        try{
            $recs = MemberDeposit::where('member_id', $request->user()->id)
               ->orderBy('created_at', 'desc')
               ->take(40)
               ->get();

            $arr = array();
            foreach ($recs as $rec) {
                $val['amount'] = $rec->amount;
                $val['recharge_on'] = Carbon::parse($rec->created_at)->format('d/m/Y');
                array_push($arr,$val);
            }
            $response = ['status' => true, 
            'message' => 'successfully Added',
            'data' => $arr
            ];

            return $response;

        } catch(Exception $e){
            $response = ['status' => false, 
            'message' => 'Could not Fetch Data',
            ];
            return response($response, 200);
        }
    }

    public function GetUserMembers(Request $request) {
        try {

            // $users = User::join('posts', 'posts.user_id', '=', 'users.id')
            // ->where('users.status', 'active')
            // ->where('posts.status','active')
            // ->get(['users.*', 'posts.descrption']);
            $data_array = array();

            // $sql =  Member::join('member_maps','members.member_id', '=', 'member_maps.member_id')
            //                 ->where('member_maps.parent_id', $request->user()->id)
            //                 ->where('member_maps.level_ctr',$request->level_ctr)
            //                 ->get(['members.*']);

            if($request->level_ctr == "All"){
                DB::enableQueryLog();

                $tblMembers = DB::select("SELECT m.member_id,d.designation,m.mobile_no as unique_id,m.image,
                    CONCAT(m.first_name,' ',m.last_name) AS member_name,
                    CONCAT(m1.first_name,' ',m1.last_name) AS introducer,m1.unique_id AS parent_code,
                    DATE_FORMAT(m.joining_date,'%d/%m/%Y') AS joining_date,(p.level_ctr + 1) AS downline
                    FROM members m
                    LEFT JOIN members m1 ON m.parent_id = m1.member_id
                    INNER JOIN club_masters d ON m.designation_id=d.id
                    INNER JOIN member_maps p ON m.member_id=p.member_id
                    WHERE p.parent_id = ".$request->user()->id);

                // $tblMembers =  Member::join('member_maps','members.member_id', '=', 'member_maps.member_id')
                // ->where('member_maps.parent_id', $request->user()->id)
                // ->get(['members.*']);
            }
            else {
                // $tblMembers =  Member::join('member_maps','members.member_id', '=', 'member_maps.member_id')
                // ->where('member_maps.member_id', $request->user()->id)
                // ->where('member_maps.level_ctr',$request->level_ctr)
                // ->get(['members.*']);

                $tblMembers = DB::select("SELECT m.member_id,d.designation,m.unique_id,m.image,
                    CONCAT(m.first_name,' ',m.last_name) AS member_name,
                    CONCAT(m1.first_name,' ',m1.last_name) AS introducer,m1.unique_id AS parent_code,
                    DATE_FORMAT(m.joining_date,'%d/%m/%Y') AS joining_date,(p.level_ctr + 1) AS downline
                    FROM members m
                    LEFT JOIN members m1 ON m.parent_id = m1.member_id
                    INNER JOIN club_masters d ON m.designation_id=d.id
                    INNER JOIN member_maps p ON m.member_id=p.member_id
                    WHERE p.parent_id = ".$request->user()->id." AND p.level_ctr+1 =".$request->level_ctr);
            }

            // $tblMembers = Member::where('member_id', $request->user()->id);

            foreach ($tblMembers as $member)
            {
                if(strlen($member->image) > 0){
                    $path = public_path("member_images/") . $member->image;
                    $imagedata = file_get_contents($path);
                    $base64 = base64_encode($imagedata);
                } else{
                    $base64 = null;
                }

                $data_array[] = array(
                    'member_id' => $member->member_id,
                    'unique_id' => $member->unique_id,
                    'member_name' => $member->member_name,
                    'parent_name' => $member->introducer,
                    'parent_code' =>$member->parent_code,
                    'designation' => $member->designation,
                    'current_level' => $member->downline,
                    'joining_date' =>  $member->joining_date, //Carbon::parse($member->joining_date)->format('d/m/Y'),
                    'profile_pic' => $base64,
                );
            }
            // add these lines to your code.
            $response=array();
            $response['status'] = true;
            $response['message'] = 'Success';
            $response["data"]=$data_array;
            return response($response,200);
        } catch(Exception $e){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }

    public function GetTransactionsRedeemable(Request $request){
        try{
            // $records = DB::table('member_incomes')
            //                 ->join('members', 'members.member_id', 'member_incomes.member_id')
            //                 ->selectRaw("concat(first_name,' ',last_name) as name, unique_id, income_type,level_percent,ref_amount,commission,date_format(member_incomes.created_at,'%d/%m/%Y') as Dated")
            //                 ->where('member_incomes.member_id', $request->user()->id)
            //                 ->get();
            $sql = "SELECT ifnull(i.commission,'') as commission,m.unique_id AS ref_id,
                    concat(m.first_name,' ',m.last_name) AS ref_name,
                    cast(ifnull(level_percent,0)+ifnull(direct_l1_percent,0)+ifnull(direct_l2_percent,0) AS CHAR) as level_percent,
                    ifnull(i.ref_amount,'') as ref_amount,i.income_type,
                    DATE_FORMAT(i.created_at,'%d/%m/%Y') AS tran_date
                FROM member_incomes i
                LEFT JOIN members m ON i.ref_member_id=m.member_id
                WHERE income_type IN ('Level Income','Leadership Income1','Leadership Income2') AND i.member_id = ". $request->user()->id;

            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $ex->getMessage()];
            return response($response, 200);
        }
    }

    public function GetTransactionsClub(Request $request){
        try{
            $sql = "SELECT cast(i.commission as char) as commission,ifnull(t.no_of_recepients,'') AS ref_id,
                    '' AS ref_name,
                    ifnull(i.club_percent,'') as level_percent,
                    cast(t.turnover as char) as ref_amount,i.income_type,
                    DATE_FORMAT(i.created_at,'%d/%m/%Y') AS tran_date
                FROM member_incomes i
                LEFT JOIN company_turnovers t ON i.turnover_id = t.id
                LEFT JOIN members m ON i.ref_member_id=m.member_id
                WHERE income_type = 'CLUB' AND i.member_id = ". $request->user()->id;

            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $ex->getMessage()];
            return response($response, 200);
        }
    }

    public function Dashboard(Request $request)
    {
        try{

            // $sql = "SELECT m.referal_code,
            //     m.unique_id,
            //     date_format(MAX(m.joining_date),'%d/%m/%Y') AS last_joining_date,
            //     count(m.member_id) AS TotalTeamSize,
            //     SUM(if((m.joining_date between date_sub(now(),INTERVAL 1 WEEK) and now()),1,0)) AS week_members
            // FROM members m
            // LEFT JOIN member_maps p ON m.member_id=p.parent_id
            // WHERE m.member_id=".$request->user()->id." GROUP BY m.referal_code,m.unique_id";
            // $records = DB::select($sql);

            $member_id = $request->user()->id;
            // $l_memberCount = MemberMap::where('parent_id', $member_id)->count();
            $tblMember = Member::where('member_id', $member_id)->first();
            $tblMemberWallet = MemberWallet::where('member_id',$member_id)->first();
            // $tblReward = MemberRewards::where('member_id',$member_id)->last();
            $tblReward = DB::table('member_rewards')->where('member_id',$member_id)->latest('id')->first();

            $reward = 'NA';
            if($tblReward != null){
                $reward = $tblReward->reward_name;
            }
            $response['status'] = true;
            $response['message'] = 'Success';
            $response["referal_code"] = strval($tblMember->referal_code);
            $response["unique_id"] = strval($tblMember->unique_id);
            $response["total_members"] = strval($tblMemberWallet->total_members);
            $response["redeemable_amt"] = strval($tblMemberWallet->redeemable_amt);
            $response["welcome_amt"] = strval($tblMemberWallet->welcome_amt);
            $response["non_redeemable"] = strval($tblMemberWallet->non_redeemable);
            $response["level_income"] = strval($tblMemberWallet->level_income);
            $response["leadership_income"] = strval($tblMemberWallet->leadership_income);
            $response["club_income"] = strval($tblMemberWallet->club_income);
            $response["transferin_amount"] = strval($tblMemberWallet->transferin_amount);
            $response["transferout_amount"] = strval($tblMemberWallet->transferout_amount);
            $response["reward"] = $reward;
            return response($response,200);
        } catch(Exception $e){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }

    }

    public function MemberRewards(Request $request){
        try{
            // $str = "SELECT m.level,
            //         m.required_members,
            //         (m.required_members - ifnull(a.cnt,0)) AS required,
            //         m.reward,
            //         if(r.tran_date IS NULL,'',DATE_FORMAT(r.tran_date,'%d/%m/%Y')) AS qualifying_date,
            //         if(r.payment_date IS NULL,'',DATE_FORMAT(r.payment_date,'%d/%m/%Y')) AS payment_date
            //     FROM level_masters m
            //     LEFT JOIN member_rewards r ON m.`level` = r.level_id AND r.member_id = ".$request->user()->id."
            //     LEFT JOIN (
            //         SELECT level_ctr, COUNT(parent_id) AS cnt
            //         FROM member_maps
            //         WHERE parent_id=".$request->user()->id."
            //         GROUP BY level_ctr) AS a ON m.`level` = a.level_ctr";

            $sql = "SELECT m.level,
                    m.required_members as target,
                    (m.required_members - ifnull(a.cnt,0)) AS required,
                    m.reward,
                    if(r.tran_date IS NULL,'',DATE_FORMAT(r.tran_date,'%d/%m/%Y')) AS qualifying_date,
                    if(r.payment_date IS NULL,'',DATE_FORMAT(r.payment_date,'%d/%m/%Y')) AS payment_date
                FROM level_masters m
                LEFT JOIN member_rewards r ON m.`level` = r.level_id AND r.member_id = 1
                LEFT JOIN (
                    SELECT level_ctr, COUNT(parent_id) AS cnt
                    FROM member_maps
                    WHERE parent_id=1
                    GROUP BY level_ctr) AS a ON m.`level` = a.level_ctr";

                    
            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $e){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }

    public function CalculateClubIncome(Request $request){
    }

    public function getFundTransfersWithBalance(Request $request){
        try{
            $id = $request->user()->id;
            $tblMemberWallet = MemberWallet::where('member_id', $id)->first();

            $strSQL = "SELECT date_format(i.created_at,'%d/%m/%Y %h:%i %p') tran_date,
                    'REDEEMABLE' tran_type,
                    cast(i.deduction as char) amount,
                    CONCAT(m1.first_name,' ',m1.last_name) ref_name,
                        m1.mobile_no
                    FROM member_incomes i
                    INNER JOIN members m ON i.member_id=m.member_id
                    INNER JOIN members m1 ON i.ref_member_id = m1.member_id
                    WHERE (i.income_type = 'TRANSFER-OUT') AND (i.member_id = $id)
                    UNION ALL
                    SELECT date_format(r.created_at,'%d/%m/%Y %h:%i %p') tran_date,
                        'NON-REDEEMABLE' tran_type,
                        cast(r.recharge_points_consumed as char) amount,
                        CONCAT(m2.first_name,' ',m2.last_name) ref_name,
                        m2.mobile_no
                    FROM recharge_point_registers r
                    INNER JOIN members m ON r.member_id=m.member_id
                    INNER JOIN members m2 ON r.ref_member_id = m2.member_id
                    WHERE (r.tran_type = 'TRANSFER-OUT') AND (r.member_id = $id)
                    ORDER BY tran_date";

            $tblResult = DB::select($strSQL);

            $response['status'] = true;
            $response['message'] = 'Success';
            $response["redeemable_amt"] = strval($tblMemberWallet->redeemable_amt);
            $response["non_redeemable_amt"] = strval($tblMemberWallet->non_redeemable);
            $response["data"] = $tblResult;
            return response($response, 200);
        } catch(Exception $e){
            $response = ['status' => false, 'message' => $e];
            return response($response, 200);
        }
    }
}
