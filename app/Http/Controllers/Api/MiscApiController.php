<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyTurnover;
use App\Models\Param;
use App\Models\RechargeProvider;
use App\Models\RechargeService;
use App\Models\Member;
use App\Models\MemberIncome;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MiscApiController extends Controller
{
    private $recharge_token_url = 'https://api.pay2all.in/token';
    private $recharge_providers_url ='https://api.pay2all.in/v1/app/providers';
    private $recharge_email = 'anil058@gmail.com';
    private $recharge_secret = 'Bxunjr';
    
    private $BRONZ_PERCENT = 5;
    private $SILVER_PERCENT = 5;
    private $GOLD_PERCENT = 3;
    private $DIAMOND_PERCENT = 2;

    private $clubMembersArray = array();
    private $sttoArray = Array();

    private $cto = 0;

    public function updateRechargeServices(Request $request){
        try {
            $token = $this->updateRechargeToken();
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->recharge_providers_url,
                ['headers' => 
                    [
                        'Authorization' => "Bearer {$token}"
                    ]
                ]
            );

            if($response->getStatusCode() == 200 ){
                $ret = $response->getBody()->getContents();
                $json = json_decode($ret, true);
                RechargeService::truncate();
                RechargeProvider::truncate();
                foreach($json['services'] as $i => $v)
                {
                    $tblRechargeServices = new RechargeService();
                    $tblRechargeServices->service_name = $v['service_name'];
                    $tblRechargeServices->description = $v['description'];
                    $tblRechargeServices->company_id = $v['company_id'];
                    $tblRechargeServices->active = $v['active'];
                    $tblRechargeServices->type = $v['type'];
                    $tblRechargeServices->save();
                }
                foreach($json['providers'] as $i => $v)
                {
                    $tblRechargeServices = new RechargeProvider();
                    $tblRechargeServices->provider_name = $v['provider_name'];
                    $tblRechargeServices->service_id = $v['service_id'];
                    $tblRechargeServices->description = $v['description'];
                    $tblRechargeServices->status = $v['status'];
                    $tblRechargeServices->save();
                }
            }

            return true;
        } catch(Exception $e){
            return false;
        }
    }

    public function updateRechargeToken(){
        try{
            $token = '';
            $client = new Client();
            $res = $client->request('POST', $this->recharge_token_url, [
                'form_params' => [
                    'email' => $this->recharge_email,
                    'password' => $this->recharge_secret,
                ]
            ]);
        
            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                $token = $json['access_token'];
                if(strlen($token) > 0){
                    $tblParam = Param::find(4);
                    $tblParam->long_text = $token;
                    $tblParam->save();
                }
            }
            return $token;
        }catch(Exception $e){
            return '';
        }
       
            // foreach($json['services'] as $i => $v)
            // {
            //     print $v['service_name'].'<br/>';
            // }
    }

    /*************************************************************************************** */
    private function calculateCTO($ask_date){
        $result = DB::table('member_deposits')
                ->select(DB::raw('SUM(amount) as cto'))
                ->whereRaw(DB::raw("Date(member_deposits.created_at) = str_to_date('$ask_date','%d-%m-%Y')"))->get()->first();
        $this->cto = $result->cto;
    }

    private function populateSTTO($ask_date){
        $sql = "SELECT m.member_id, m.designation_id, SUM(d.amount) AS STO
            FROM member_deposits d
            INNER JOIN member_maps p ON d.member_id = p.member_id 
            INNER JOIN members m ON m.member_id = p.parent_id
            WHERE DATE(d.created_at) = STR_TO_DATE('$ask_date','%d-%m-%Y')
            GROUP BY m.member_id,m.designation_id";
        $tblTemp = DB::select($sql);
        foreach ($tblTemp as $rec){
            $this->sttoArray[] = $rec;
        }
    }

    private function populateClubMembersArray(){
        $tblTemp = Member::where('designation_id', '>', 1)->get();
        foreach ($tblTemp as $rec){
            $this->clubMembersArray[] = $rec;
        }
    }

    private function getDesignationMembers($ask_designation_id){
        $tempArray = array();
        foreach ($this->sttoArray as $rec){
            if($rec->designation_id == $ask_designation_id){
                $tempArray[] = $rec;
            }
        }
        return $tempArray;
    }

    private function isTurnoverCalculated($tran_date){
        $tbl = CompanyTurnover::whereRaw(DB::raw("turnover_type = 'DAILY' AND Date(created_at) = str_to_date($tran_date, '%d-%m-%Y')"));
        $cnt = $tbl->count();
        return ($cnt == 0) ? false : true;
    }

    public function calculateClubIncome(Request $request){
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'tran_date' => 'required|date',
            ]);
             
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }
            if ($this->isTurnoverCalculated($request->tran_date) == true){
                $response = ['status' => false, 'message' => 'Already Calculated and Posted'];
                return response($response, 200);
            }

            $date = Carbon::createFromFormat('d-m-Y', $request->tran_date);
            
            $this->populateClubMembersArray();
            $this->calculateCTO($request->tran_date);
            $this->populateSTTO($request->tran_date);

            //Bronz Calculation
            $bronzMembersArray = $this->getDesignationMembers(2);
            $bronzMemberCount = count($bronzMembersArray);
            $distributionAmount = $this->cto * $this->BRONZ_PERCENT * 0.01;

            $oneUnit = round($distributionAmount / $bronzMemberCount , 2);
            $leftOver = $distributionAmount - ($oneUnit * $bronzMemberCount);

            $companyTurnover = new CompanyTurnover();
            $companyTurnover->turnover_from = $date;
            $companyTurnover->turnover_to = $date;
            $companyTurnover->turnover_type = 'DAILY';
            $companyTurnover->turnover = $this->cto;
            $companyTurnover->no_of_recepients = $bronzMemberCount;
            $companyTurnover->fraction_leftover = $leftOver;
            $companyTurnover->is_stto = false;
            $companyTurnover->save();

            foreach($bronzMembersArray as $member){
                $tbl = new MemberIncome();
                $tbl->member_id = $member->member_id;
                $tbl->turnover_id = $companyTurnover->id;
                $tbl->income_type = 'CLUB';
                $tbl->club_percent = $this->BRONZ_PERCENT;
                $tbl->cto = $oneUnit;
                $tbl->ref_amount =  $distributionAmount;
                $tbl->commission = $oneUnit;
                $tbl->amount = $oneUnit;
                $tbl->save();
            }

            //Silver Calculation
            $silverArray = $this->getDesignationMembers(3);
            foreach($silverArray as $arr){
                $tmp = $arr->STO;
                $clubAmount = $tmp * $this->SILVER_PERCENT * 0.01;

                $tbl = new MemberIncome();
                $tbl->member_id = $arr->member_id;
                $tbl->income_type = 'CLUB';
                $tbl->club_percent = $this->SILVER_PERCENT;
                $tbl->stto = $tmp;
                $tbl->ref_amount =  $tmp;
                $tbl->commission = $clubAmount;
                $tbl->amount = $clubAmount;
                $tbl->save();
            }

            //Gold Calculation
            $goldArray = $this->getDesignationMembers(4);
            foreach($goldArray as $arr){
                $tmp = $arr->STO;
                $clubAmount = $tmp * $this->GOLD_PERCENT * 0.01;

                $tbl = new MemberIncome();
                $tbl->member_id = $arr->member_id;
                $tbl->income_type = 'CLUB';
                $tbl->club_percent = $this->GOLD_PERCENT;
                $tbl->stto = $tmp;
                $tbl->ref_amount =  $tmp;
                $tbl->commission = $clubAmount;
                $tbl->amount = $clubAmount;
                $tbl->save();
            }

             //Diamond Calculation
            $diamondArray = $this->getDesignationMembers(5);
            foreach($diamondArray as $arr){
                $tmp = $arr->STO;
                $clubAmount = $tmp * $this->DIAMOND_PERCENT * 0.01;

                $tbl = new MemberIncome();
                $tbl->member_id = $arr->member_id;
                $tbl->income_type = 'CLUB';
                $tbl->club_percent = $this->DIAMOND_PERCENT;
                $tbl->stto = $tmp;
                $tbl->ref_amount =  $tmp;
                $tbl->commission = $clubAmount;
                $tbl->amount = $clubAmount;
                $tbl->save();
            }
            DB::commit();
            $response = ['status' => true, 'message' => 'Successfully calculated Club Income'];
            return response($response, 200);
        }catch(Exception $e){
            DB::rollBack();
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }
}
