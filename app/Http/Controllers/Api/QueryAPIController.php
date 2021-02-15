<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

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

    public function GetRedeemableIncomeQuery(Request $request){
        try{
            $sql = "SELECT ifnull(i.commission,'') as commission,m.unique_id AS ref_id,
                    concat(m.first_name,' ',m.last_name) AS ref_name,
                    cast(ifnull(level_percent,0)+ifnull(direct_l1_percent,0)+ifnull(direct_l2_percent,0) AS CHAR) as level_percent,
                    ifnull(i.ref_amount,'') as ref_amount,i.income_type,
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
            $response = ['status' => false, 'message' => $ex->getMessage()];
            return response($response, 200);
        }
    }

    public function GetNonRedeemableIncomeQuery(Request $request){
        
    }

    public function GetTransferInQuery(Request $request){
        
    }

    public function GetTransferOutQuery(Request $request){
        
    }

}

