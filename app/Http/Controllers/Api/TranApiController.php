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
        $this->middleware('auth:api'); //->except(['GetUserMembers']);
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

            DB::enableQueryLog();
            $sql =  Member::join('member_maps','members.member_id', '=', 'member_maps.member_id')
                            ->where('member_maps.parent_id', $request->user()->id)
                            ->where('member_maps.level_ctr',$request->level_ctr)
                            ->get(['members.*']);

            if($request->level_ctr == "All"){
                $tblMembers =  Member::join('member_maps','members.member_id', '=', 'member_maps.member_id')
                ->where('member_maps.member_id', $request->user()->id)
                ->get(['members.*']);
            }
            else {
                $tblMembers =  Member::join('member_maps','members.member_id', '=', 'member_maps.member_id')
                ->where('member_maps.member_id', $request->user()->id)
                ->where('member_maps.level_ctr',$request->level_ctr)
                ->get(['members.*']);
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
                    'member_name' => $member->first_name.' '.$member->last_name,
                    'parent_name' => $member->parentMember->first_name . ' '.$member->parentMember->last_name,
                    'parent_code' =>$member->parentMember->unique_id,
                    'designation' => $member->designation->designation,
                    'current_level' => $member->current_level,
                    'joining_date' =>  Carbon::parse($member->joining_date)->format('d/m/Y'),
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
            $records = DB::table('member_incomes')
                            ->join('members', 'members.member_id', 'member_incomes.member_id')
                            ->selectRaw("concat(first_name,' ',last_name) as name, unique_id, income_type,level_percent,ref_amount,commission,date_format(member_incomes.created_at,'%d/%m/%Y') as Dated")
                            ->where('member_incomes.member_id', $request->user()->id)
                            ->get();
            $response['status'] = true;
            $response['message'] = 'Success';
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }

}
