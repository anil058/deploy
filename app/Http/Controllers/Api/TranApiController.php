<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\PaymentGateway;
use App\Models\TempMember;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;


class TranApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['MemberRewards']);
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
    
            $result = createRazorpayMoneyOrder($request->user()->id, $request->amount);
            if(count($result) == 0){
                $response = ['status' => false, 
                'message' => 'Could not create payment order',
                ];
                return response($response, 200);
            } else {
                $response = ['status' => true, 
                'id' => $result['id'], 
                'txn_id' => $result['txn_id'], 
                'amount' => $request->amount,
                'message' => 'Successfully Created Payment Order',
                ];
                return response($response, 200);
            }
   
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
            $recs = PaymentGateway::where('member_id', $request->user()->id)
               ->orderBy('created_at', 'desc')
               ->take(40)
               ->get();

            $arr = array();
            foreach ($recs as $rec) {
                $val['amount'] = $rec->amount;
                $val['recharge_on'] = Carbon::parse($rec->created_at)->format('d/m/Y');
                array_push($arr,$val);
            }
            return $arr;

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

                $tblMembers = DB::select("SELECT m.member_id,d.designation,m.unique_id,m.image,
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

    public function GetTransactions(Request $request){
        try{
            // $records = DB::table('member_incomes')
            //                 ->join('members', 'members.member_id', 'member_incomes.member_id')
            //                 ->selectRaw("concat(first_name,' ',last_name) as name, unique_id, income_type,level_percent,ref_amount,commission,date_format(member_incomes.created_at,'%d/%m/%Y') as Dated")
            //                 ->where('member_incomes.member_id', $request->user()->id)
            //                 ->get();
            $sql = "SELECT i.commission,m.unique_id AS ref_id,
                    concat(m.first_name,' ',m.last_name) AS ref_name,
                    i.level_percent,
                    i.ref_amount,i.income_type,
                    DATE_FORMAT(i.created_at,'%d/%m/%Y') AS tran_date
                FROM member_incomes i
                LEFT JOIN members m ON i.ref_member_id=m.member_id
                WHERE i.member_id = ". $request->user()->id;

            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $e->getMessage()];
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
            // INNER JOIN member_maps p ON m.member_id=p.parent_id
            // WHERE p.parent_id=".$request->user()->id." GROUP BY m.referal_code,m.unique_id";

            $sql = "SELECT m.referal_code,
                m.unique_id,
                date_format(MAX(m.joining_date),'%d/%m/%Y') AS last_joining_date,
                count(m.member_id) AS TotalTeamSize,
                SUM(if((m.joining_date between date_sub(now(),INTERVAL 1 WEEK) and now()),1,0)) AS week_members
            FROM members m
            INNER JOIN member_maps p ON m.member_id=p.parent_id
            WHERE p.parent_id=".$request->user()->id." GROUP BY m.referal_code,m.unique_id";

            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response["referal_code"]=$records[0]->referal_code;
            $response["unique_id"]=$records[0]->unique_id;
            $response["last_joining_date"]=$records[0]->last_joining_date;
            $response["TotalTeamSize"]=$records[0]->TotalTeamSize;
            $response["week_members"]=$records[0]->week_members;
            $response["last_payment_transfer"]=0;
            $response["total_payment_transfer"]=0;

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
}
