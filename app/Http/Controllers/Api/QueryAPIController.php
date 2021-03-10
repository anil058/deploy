<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberWallet;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class QueryAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['MemberRewards']);
    }

    public function GetLevelIncomeQuery(Request $request){
        try{
            $sql = "SELECT ifnull(i.commission,'') as commission,m.unique_id AS ref_id,
                    concat(m.first_name,' ',m.last_name) AS ref_name,
                    cast(ifnull(level_percent,0)+ifnull(direct_l1_percent,0)+ifnull(direct_l2_percent,0) AS CHAR) as level_percent,
                    ifnull(i.ref_amount,'') as ref_amount,i.income_type,
                    DATE_FORMAT(i.created_at,'%d/%m/%Y') AS tran_date
                FROM member_incomes i
                LEFT JOIN members m ON i.ref_member_id=m.member_id
                WHERE income_type = 'Level Income' and i.member_id = ". $request->user()->id;

            // $tblMemberWallet = MemberWallet::where('member_id', $request->user()->id)->first();
            // $tblTotalAmount = MemberIncome::where('member_id', $request->user()->id)->;
            $balance = DB::table('member_incomes')->where('member_id', $request->user()->id)->sum('commission');

            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response['total_amount'] = strval($balance);
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $ex->getMessage()];
            return response($response, 200);
        }
    }

    public function GetLeadershipIncomeQuery(Request $request){
        try{
            $sql = "SELECT ifnull(i.commission,'') as commission,m.unique_id AS ref_id,
                    concat(m.first_name,' ',m.last_name) AS ref_name,
                    cast(ifnull(level_percent,0)+ifnull(direct_l1_percent,0)+ifnull(direct_l2_percent,0) AS CHAR) as level_percent,
                    ifnull(i.ref_amount,'') as ref_amount,i.income_type,
                    DATE_FORMAT(i.created_at,'%d/%m/%Y') AS tran_date
                FROM member_incomes i
                LEFT JOIN members m ON i.ref_member_id=m.member_id
                WHERE income_type IN ('Leadership Income1','Leadership Income2') and i.member_id = ". $request->user()->id;

            $tblMemberWallet = MemberWallet::where('member_id', $request->user()->id)->first();
            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response['total_amount'] = strval($tblMemberWallet->leadership_income);
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $ex->getMessage()];
            return response($response, 200);
        }
    }

    public function GetClubIncomeQuery(Request $request){
        try{
            $sql = "SELECT ifnull(i.commission,'') as commission,m.unique_id AS ref_id,
                    concat(m.first_name,' ',m.last_name) AS ref_name,
                    cast(ifnull(level_percent,0)+ifnull(direct_l1_percent,0)+ifnull(direct_l2_percent,0) AS CHAR) as level_percent,
                    ifnull(i.ref_amount,'') as ref_amount,i.income_type,
                    DATE_FORMAT(i.created_at,'%d/%m/%Y') AS tran_date
                FROM member_incomes i
                LEFT JOIN members m ON i.ref_member_id=m.member_id
                WHERE i.income_type = 'CLUB' and i.member_id = ". $request->user()->id;

            $tblMemberWallet = MemberWallet::where('member_id', $request->user()->id)->first();
            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = 'Success';
            $response['total_amount'] = strval($tblMemberWallet->club_income);
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $ex->getMessage()];
            return response($response, 200);
        }
    }

    public function GetRedeemableIncomeQuery(Request $request){
         //Validate Input
         $validator = Validator::make($request->all(), [
            'start_date' => 'string|min:10|max:10',
            'end_date' => 'string|min:10|max:10',
            'status' => [
                'required',
                Rule::in(['ALL', 'RANGE']),
            ],
        ]);
 
        //General request validation
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json(['status' => false, 'message' => $errors]);
        }

        $l_message = 'Transactions from last 60 days';

        try{
            if($request->status == 'ALL'){
                $sql = "SELECT ifnull(i.commission,'') as commission,m.unique_id AS ref_id,
                    concat(m.first_name,' ',m.last_name) AS ref_name,
                    cast(ifnull(level_percent,0)+ifnull(direct_l1_percent,0)+ifnull(direct_l2_percent,0) AS CHAR) as level_percent,
                    ifnull(i.ref_amount,'') as ref_amount,i.income_type,
                    DATE_FORMAT(i.created_at,'%d/%m/%Y') AS tran_date
                FROM member_incomes i
                LEFT JOIN members m ON i.ref_member_id=m.member_id
                WHERE (date(i.created_at) BETWEEN NOW() - INTERVAL 60 DAY AND NOW()) and (i.member_id = ". $request->user()->id.") ORDER BY i.created_at desc";
            } else {
                $sql = "SELECT ifnull(i.commission,'') as commission,m.unique_id AS ref_id,
                    concat(m.first_name,' ',m.last_name) AS ref_name,
                    cast(ifnull(level_percent,0)+ifnull(direct_l1_percent,0)+ifnull(direct_l2_percent,0) AS CHAR) as level_percent,
                    ifnull(i.ref_amount,'') as ref_amount,i.income_type,
                    DATE_FORMAT(i.created_at,'%d/%m/%Y') AS tran_date
                FROM member_incomes i
                LEFT JOIN members m ON i.ref_member_id=m.member_id
                WHERE (date(i.created_at) BETWEEN str_to_date('".$request->start_date."','%d/%m/%Y') AND str_to_date('".$request->end_date."','%d/%m/%Y')) and (i.member_id = ". $request->user()->id.") ORDER BY i.created_at desc";
                $l_message = $request->start_date.' and '.$request->end_date;
            }

            $tblMemberWallet = MemberWallet::where('member_id', $request->user()->id)->first();

            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = $l_message;
            $response['total_amount'] = strval($tblMemberWallet->redeemable_amt);
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $ex->getMessage()];
            return response($response, 200);
        }
    }

    public function GetNonRedeemableIncomeQuery(Request $request){
         //Validate Input
         $validator = Validator::make($request->all(), [
            'start_date' => 'string|min:10|max:10',
            'end_date' => 'string|min:10|max:10',
            'status' => [
                'required',
                Rule::in(['ALL', 'RANGE']),
            ],
        ]);
 
        //General request validation
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json(['status' => false, 'message' => $errors]);
        }

        $l_message = 'Transactions from last 60 days';

        try{
            if($request->status == 'ALL'){
                $sql = "SELECT m.unique_id,
                        concat(m.first_name,' ',m.last_name) member_name,
                        DATE_FORMAT(r.created_at, '%d/%m/%Y %h:%i %p') tran_date,
                        IFNULL(r.recharge_points_added,0) points_added,
                        IFNULL(r.recharge_points_consumed,0) points_consumed,
                        r.tran_type,
                        r.remarks
                    FROM recharge_point_registers r
                    INNER JOIN members m ON r.member_id=m.member_id
                    WHERE (date(r.created_at) BETWEEN NOW() - INTERVAL 60 DAY AND NOW()) and (r.member_id = ". $request->user()->id.") ORDER BY r.created_at desc";
              } else {
                $sql = "SELECT m.unique_id,
                        concat(m.first_name,' ',m.last_name) member_name,
                        DATE_FORMAT(r.created_at, '%d/%m/%Y %h:%i %p') tran_date,
                        IFNULL(r.recharge_points_added,0) points_added,
                        IFNULL(r.recharge_points_consumed,0) points_consumed,
                        r.tran_type,
                        r.remarks
                    FROM recharge_point_registers r
                    INNER JOIN members m ON r.member_id=m.member_id
                    WHERE (date(r.created_at) BETWEEN str_to_date('".$request->start_date."','%d/%m/%Y') AND str_to_date('".$request->end_date."','%d/%m/%Y')) and (r.member_id = ". $request->user()->id.") ORDER BY r.created_at desc";
                $l_message = $request->start_date.' and '.$request->end_date;
            }
        
            $tblMemberWallet = MemberWallet::where('member_id', $request->user()->id)->first();

            $records = DB::select($sql);
            $response['status'] = true;
            $response['message'] = $l_message;
            $response['total_amount'] = strval($tblMemberWallet->non_redeemable);
            $response["data"]=$records;
            return response($response,200);
        } catch(Exception $ex){
            $response = ['status' => false, 'message' => $ex->getMessage()];
            return response($response, 200);
        }
    }

    public function GetTransferInQuery(Request $request){
        
    }

    public function GetTransferOutQuery(Request $request){
        
    }

}

