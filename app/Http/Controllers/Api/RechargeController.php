<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RechargeProvider;
use App\Models\RechargeRequest;
use App\Models\Param;
use App\Models\Member;
use App\Http\Controllers\Api\MiscApiController;
use App\Models\MemberIncome;
use App\Models\MemberMap;
use App\Models\MemberWallet;
use App\Models\RechargePointRegister;
use App\Models\RechargeCircle;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class RechargeController extends Controller
{
    private $DEBUG = false;
    private $RECHARGE_CASHBACK_PERCENT;
    private $ALLOW_MOBILE_RECAHRGE;
    private $MOBILE_WELCOME_ADJUSTMENT_PERCENT;
    private $TEAM_CASHBACK_PERCENT;
    private $RECHARGE_URL = 'https://api.pay2all.in/v1/payment/recharge';

    private $arrayParents = array();

    private $mobilePlanUrl = 'https://api.pay2all.in/v1/plan/mobile';
    // private $mobilePlanUrl = 'https://api.pay2all.in/v1/plan/mobile';

    public function __construct()
    {
        $this->middleware('auth:api');//->except(['GetNonRedeemableWallet']);
    }

    private function populateParams(){
        $tblParamTable = Param::all();
        foreach ($tblParamTable as $refTable){
            switch($refTable->param){
                case "RECHARGE_CASHBACK_PERCENT":
                    $this->RECHARGE_CASHBACK_PERCENT = $refTable->int_value;
                    break;
                case "ALLOW_MOBILE_RECAHRGE":
                    $this->ALLOW_MOBILE_RECAHRGE = ($refTable->bool_value == 0) ? false : true;
                    break;
                case "MOBILE_WELCOME_ADJUSTMENT_PERCENT":
                    $this->MOBILE_WELCOME_ADJUSTMENT_PERCENT = $refTable->int_value;
                    break;
                case "TEAM_CASHBACK_PERCENT":
                    $this->TEAM_CASHBACK_PERCENT = $refTable->int_value;
                    break;
                default :
                    $this->ROYALTY_REQ_NUM = 0;
            }
        }
    }

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

    private function AddRechargeRequest(Request $request){
        try{
            $tblProviders = RechargeProvider::find($request->provider_id);
            $tblRechargeRequest = new RechargeRequest();
            $tblRechargeRequest->member_id = $request->user()->id;
            $tblRechargeRequest->mobile_no = $request->mobile_no;
            $tblRechargeRequest->provider_id = $request->provider_id;
            $tblRechargeRequest->provider_name = $tblProviders->provider_name;
            $tblRechargeRequest->amount = $request->amount;
            // $tblRechargeRequest->status_id ;
            // $tblRechargeRequest->utr = $request->mobile_no;
            $tblRechargeRequest->save();
            return $tblRechargeRequest->id;
        }catch(Exception $e){
            return 0;
        }
    }

    public function GetProviders(Request $request){
        try{
            $tblProviders = RechargeProvider::where('service_id', 1)->get(['provider_id as id','provider_name']);
            // $tblMember = Member::where('member_id', $request->user()->id)->first();
            $tblMemberWallet = MemberWallet::where('member_id', $request->user()->id)->first();
            $tblCircles = RechargeCircle::all();

            $response = ['status' => true,
                'welcome' => strval(round($tblMemberWallet->welcome_amt)),
                'redeemable' => strval(round($tblMemberWallet->redeemable_amt)),
                'non-redeemable' => strval(round($tblMemberWallet->non_redeemable)),
                'message' => 'Balance will be deducted in order Non-redeemable -> Redeemable',
                'providers' => $tblProviders,
                'circles' => $tblCircles
            ];
            return response($response, 200);
        } catch(Exception $e){
            $response = ['status' => false,
            'message' => 'Successfully Created Temporary User',
            ];
            return response($response, 200);
        }
    }

    public function GetBalances(Request $request){
        try{
            $tblMemberWallet = MemberWallet::where('member_id', $request->user()->id)->first();

            $response = ['status' => true,
                'welcome' => strval($tblMemberWallet->welcome_amt),
                'redeemable' => strval( $tblMemberWallet->redeemable_amt),
                'non-redeemable' => strval( $tblMemberWallet->non_redeemable),
                'message' => 'Balance will be deducted in order Non-redeemable -> Redeemable'
            ];
            return response($response, 200);
        } catch(Exception $e){
            $response = ['status' => false,
            'message' => 'Unable to get balance',
            ];
            return response($response, 200);
        }
    }


    public function GetMobilePlans(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'circle_id' => 'required|numeric',
                'provider_id' => 'required|numeric',
                ]);

            //General request validation
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $errors]);
            }

            $miscApiController = new MiscApiController();
            // $token = Param::where("id", 4)->first()->long_text;

            $token = $miscApiController->updateRechargeToken();

            $client = new Client();
            $res = $client->request('POST', $this->mobilePlanUrl, [
                'form_params' => [
                    'provider_id' => $request->provider_id,
                    'circle_id' => $request->circle_id,
                ],
                'headers' =>
                [
                        'Authorization' => "Bearer {$token}"
                ]

            ]);

            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);

                // //Experimenting
                // $allKeys = array_keys($json['data']);
                // $response = array();
                // $response['status'] =  true;
                // foreach ($allKeys as $key) {
                //     $response['data'][$key] =  $json['data'][$key];
                // }
                // $response['message'] =  'Balance will be deducted in order Non-redeemable -> Redeemable';
                // //End Experimenbt

                $lfulltt = array();
                $ltopup = array();
                $l3g4g = array();
                $lratecutter =  array();
                $l2g =  array();
                $lsms = array();
                $lcombo = array();
                $lroaming = array();
                $lfrc = array();

                $data = $json['data'];

                if (array_key_exists("FULLTT",$data))
                    $lfulltt = $data['FULLTT'];
                if (array_key_exists("TOPUP",$data))
                    $ltopup = $data['TOPUP'];
                if (array_key_exists("3G/4G",$data))
                    $l3g4g = $data['3G/4G'];
                if (array_key_exists("RATE CUTTER",$data))
                    $lratecutter =  $data['RATE CUTTER'];
                if (array_key_exists("2G",$data))
                    $l2g =  $data['2G'];
                if (array_key_exists("SMS",$data))
                    $lsms = $data['SMS'];
                if (array_key_exists("COMBO",$data))
                    $lcombo = $data['COMBO'];
                if (array_key_exists("Romaing",$data))
                    $lroaming = $data['Romaing'];
                if (array_key_exists("FRC",$data))
                    $lfrc = $data['FRC'];

                $response = ['status' => true,
                    'FULLTT' => $lfulltt,
                    'TOPUP' => $ltopup,
                    '3G4G' => $l3g4g,
                    'RATE CUTTER' => $lratecutter,
                    'COMBO' => $lcombo,
                    '2G' => $l2g,
                    'SMS' => $lsms,
                    'Romaing' => $lroaming,
                    'FRC' => $lfrc,
                    'message' => 'Balance will be deducted in order Non-redeemable -> Redeemable',
                ];


                // $response = ['status' => true,
                //     'TOPUP' => $json['data']['TOPUP'],
                //     '3G4G' => $json['data']['3G/4G'],
                //     'RATE CUTTER' => $json['data']['RATE CUTTER'],
                //     '2G' => $json['data']['2G'],
                //     'SMS' => $json['data']['SMS'],
                //     'Romaing' => $json['data']['Romaing'],
                //     'message' => 'Balance will be deducted in order Non-redeemable -> Redeemable',
                // ];
            } else {
                $response = ['status' => false,
                    'message' => 'Could not Fetch data',
                ];
            }

        return response($response, 200);
        } catch(Exception $e){
            $response = ['status' => false,
            'message' => 'Unfortunately the request did not yield results',
            ];
            return response($response, 200);
        }

    }


    // public function RechargeMobile(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'provider_id' => 'required|numeric',
    //         'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
    //         'amount' => 'required|numeric:max:1000'
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = $validator->errors()->first();
    //         $response = ['status' => false, 'message' => $errors];
    //         return response($response, 200);
    //     }

    //     //Check if there is an existing user with same mobile no
    //     // $tblMember = Member::where('mobile_no', $request->mobile_no)->first();
    //     $id = $request->user()->id;

    //     try{
    //         $token1 = new MiscApiController();
    //         $token = $token1->updateRechargeToken();

    //         $client_id = $this->AddRechargeRequest($request);
    //         if($client_id == 0){
    //             $response = ['status' => false, 'message' => 'Unable to add request data'];
    //             return response($response, 200);
    //         }

    //         $request_param = [
    //             'number'        => $request->mobile_no,
    //             'amount'        => $request->amount,
    //             'provider_id'   => $request->provider_id,
    //             'client_id'     => $client_id
    //         ];
    //         $request_data = json_encode($request_param);
    //         $client = new Client();
    //         $response = $client->request(
    //             'POST',
    //             $this->rechargeUrl,
    //             ['headers' =>
    //                 [
    //                     'Accept' => "application/json",
    //                     'Authorization' => "Bearer {$token}"
    //                 ],
    //                 'form_params' => [
    //                     'number' => $request->mobile_no,
    //                     'amount' => $request->amount,
    //                     'provider_id' => $request->provider_id,
    //                     'client_id' => $client_id,
    //                 ]

    //             ]
    //         );

    //         if($response->getStatusCode() == 200 ){
    //             $ret = $response->getBody()->getContents();
    //             $json = json_decode($ret, true);
    //             $flag = true;
    //             $successMessage = 'Successfully Recharged';
    //             if($json['status_id'] == 2){
    //                 $flag = false;
    //                 $successMessage = $json['message'];
    //             }

    //             $tblRechargeRequest = RechargeRequest::find($client_id);
    //             $tblRechargeRequest->status_id = $json['status_id'];
    //             $tblRechargeRequest->message = $json['message'];
    //             $tblRechargeRequest->utr = $json['utr'];
    //             $tblRechargeRequest->report_id = $json['report_id'];
    //             $tblRechargeRequest->orderid = $json['orderid'];
    //             $tblRechargeRequest->verified = 1;
    //             $tblRechargeRequest->save();
    //             $response = [
    //                 'status' => $flag,
    //                 'message' =>  $successMessage,
    //                 'status_id' => $json['status_id']
    //             ];
    //             return response($response, 200);
    //         }
    //         $response = ['status' => false, 'message' => 'Error connecting recharge server'];
    //         return response($response, 200);
    //     }catch(Exception $e){
    //         $response = ['status' => false, 'message' => $e->getMessage()];
    //         return response($response, 200);
    //     }
    // }

    public function RechargeMobile(Request $request){
        //Check if Mobile recahrge is allowed
        $this->populateParams();
        if($this->ALLOW_MOBILE_RECAHRGE == false){
            $response = ['status' => false, 'message' => 'Mobile Recharge Service is temporarily halted'];
            return response($response, 200);
        }

        //Validate Input
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|numeric',
            'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'amount' => 'required|numeric:max:1000'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response = ['status' => false, 'message' => $errors];
            return response($response, 200);
        }

        //Initialization Job
        $id = $request->user()->id;
        $tblMember = Member::where('member_id', $id)->first();
        $this->populateParents($tblMember->parent_id);

        $tblMemberWallet = MemberWallet::where('member_id', $id)->first();
        $l_WELCOME_AMT = $tblMemberWallet->welcome_amt;
        $l_REDEEMABLE_AMT = $tblMemberWallet->redeemable_amt;
        $l_NON_REDEEMABLE = $tblMemberWallet->non_redeemable;
        $l_AMOUNT = $request->amount;
        $l_CASHBACK_AMT = $l_AMOUNT * $this->RECHARGE_CASHBACK_PERCENT * 0.01;

        //Until Testing make sure no one recharges more than Rs 10
        if($l_AMOUNT > 1000){
            $response = ['status' => false, 'message' => 'Only upto Rs 1000 allowed in test mode'];
            return response($response, 200);
        }

        //Check if welcome amount has sufficient money
        if($request->usewelcomededuction == true){
            $l_WELCOME_DEDUCTION = round($l_AMOUNT * $this->MOBILE_WELCOME_ADJUSTMENT_PERCENT * 0.01);
            if($l_WELCOME_AMT < $l_WELCOME_DEDUCTION){
                $l_WELCOME_DEDUCTION = 0;
            }
        } else {
            $l_WELCOME_DEDUCTION = 0;
        }

        $l_restAmt = $l_AMOUNT - $l_WELCOME_DEDUCTION;
        $l_deductable_amt = $l_REDEEMABLE_AMT +  $l_NON_REDEEMABLE +  $l_WELCOME_DEDUCTION;
        if($l_restAmt > $l_deductable_amt){
            $response = ['status' => false, 'message' => 'Insufficient Balance'];
            return response($response, 200);
        }

        //Distribute demand
        if ($l_restAmt <= $l_NON_REDEEMABLE){
            $l_NON_REDEEMABLE_DEDUCTION = $l_restAmt;
            $l_REDEEMABLE_DEDUCTION = 0;
        } else{
            $l_NON_REDEEMABLE_DEDUCTION = $l_NON_REDEEMABLE;
            $l_REDEEMABLE_DEDUCTION = $l_restAmt - $l_NON_REDEEMABLE_DEDUCTION;
        }

        //Recharge from pay2all
        $mobileNo = substr($request->mobile_no, -10);

        DB::beginTransaction();
        try{
            $client_id = $this->AddRechargeRequest($request);
            $token = 'testtoken';
            if($this->DEBUG == false){
                //Generate Recharge Token from pay2All
                $miscApiController = new MiscApiController();
                $token = $miscApiController->updateRechargeToken();

                //Generate local tran id to be sent to mobile operator
                if($client_id == 0){
                    DB::rollBack();
                    $response = ['status' => false, 'message' => 'Unable to add request data'];
                    return response($response, 200);
                }



                //Recharge to pay2all
                $request_param = [
                    'number'        => $mobileNo,
                    'amount'        => $request->amount,
                    'provider_id'   => $request->provider_id,
                    'client_id'     => $client_id
                ];
                $request_data = json_encode($request_param);
                $client = new Client();
                $response = $client->request(
                    'POST',
                    $this->RECHARGE_URL,
                    ['headers' =>
                        [
                            'Accept' => "application/json",
                            'Authorization' => "Bearer {$token}"
                        ],
                        'form_params' => [
                            'number' => $mobileNo,
                            'amount' => $request->amount,
                            'provider_id' => $request->provider_id,
                            'client_id' => $client_id,
                        ]

                    ]
                );

                if($response->getStatusCode() == 200 ){
                    $ret = $response->getBody()->getContents();
                    $json = json_decode($ret, true);
                    $flag = true;
                    $successMessage = 'Successfully Recharged';
                    if($json['status_id'] == 2){
                        // $flag = false;
                        DB::rollBack();
                        $successMessage = $json['message'];
                        $response = ['status' => false, 'message' => $successMessage];
                        return response($response, 200);
                    }
                } else {
                    DB::rollBack();
                    $response = ['status' => false, 'message' => "Could not Connect to the Server"];
                    return response($response, 200);
                }

                //Update Recharge Request
                $tblRechargeRequest = RechargeRequest::find($client_id);
                $tblRechargeRequest->status_id = $json['status_id'];
                $tblRechargeRequest->message = $json['message'];
                $tblRechargeRequest->utr = $json['utr'];
                $tblRechargeRequest->report_id = $json['report_id'];
                $tblRechargeRequest->orderid = $json['orderid'];
                $tblRechargeRequest->verified = 1;
                $tblRechargeRequest->save();

            }

            //Update non redeemable wallet
            $tblMemberWallet->non_redeemable -= $l_NON_REDEEMABLE_DEDUCTION;
            $tblMemberWallet->redeemable_amt -= $l_REDEEMABLE_DEDUCTION;
            $tblMemberWallet->welcome_amt -= $l_WELCOME_DEDUCTION;
            $tblMemberWallet->save();

            //Non Redeemable CONSUMED
            if($l_NON_REDEEMABLE_DEDUCTION > 0){
                $tblRechargePointRegister = new RechargePointRegister();
                $tblRechargePointRegister->member_id = $id;
                $tblRechargePointRegister->ref_member_id =  $id;
                $tblRechargePointRegister->tran_date = Carbon::now();
                $tblRechargePointRegister->recharge_id = $client_id;
                $tblRechargePointRegister->recharge_points_consumed = $l_NON_REDEEMABLE_DEDUCTION;
                $tblRechargePointRegister->balance_points = $l_REDEEMABLE_AMT - $l_NON_REDEEMABLE_DEDUCTION;
                $tblRechargePointRegister->tran_type = "RECHARGE" ;//RECHARGE,CASHBACK
                $tblRechargePointRegister->remarks = "WALLET USED";
                $tblRechargePointRegister->save();


            }

             //Redeemable CONSUMED
             if($l_REDEEMABLE_DEDUCTION > 0){
                $tblMemberIncome = new MemberIncome();
                $tblMemberIncome->member_id = $id;
                $tblMemberIncome->income_type = 'Mobile Recahrge';
                $tblMemberIncome->ref_member_id = $id;
                $tblMemberIncome->deduction = $l_REDEEMABLE_DEDUCTION;
                $tblMemberIncome->ref_amount = $l_AMOUNT;
                $tblMemberIncome->balance = $l_REDEEMABLE_AMT -$l_REDEEMABLE_DEDUCTION;
                $tblMemberIncome->save();
            }

            //CASHBACK
            $tblRechargePointRegister = new RechargePointRegister();
            $tblRechargePointRegister->member_id = $id;
            $tblRechargePointRegister->ref_member_id = $id;
            $tblRechargePointRegister->tran_date = Carbon::now();
            $tblRechargePointRegister->recharge_id = $client_id;
            $tblRechargePointRegister->recharge_points_added = $l_CASHBACK_AMT;
            $tblRechargePointRegister->balance_points += $l_CASHBACK_AMT;
            $tblRechargePointRegister->tran_type = "CASHBACK" ;//RECHARGE,CASHBACK
            $tblRechargePointRegister->remarks = "WALLET CASHBACK";
            $tblRechargePointRegister->save();

            //Award cashback to parents
            $equalAmount = 0;
            $teamCashbackLeftover = 0;
            $cnt = count($this->arrayParents);
            $teamCashbackAmount = round($l_CASHBACK_AMT * $this->TEAM_CASHBACK_PERCENT * 0.01,2);
            if($teamCashbackAmount>0){
                $equalAmount = round($teamCashbackAmount/$cnt);
                $teamCashbackLeftover = round($teamCashbackAmount - ($equalAmount * $cnt),2);
            }

            if($equalAmount > 0){
                foreach($this->arrayParents as $parent){
                    $tblRechargePointRegister = new RechargePointRegister();
                    $tblMemberWallet = MemberWallet::where('member_id', $parent->member_id)->first();
                    $tblRechargePointRegister->member_id = $parent->member_id;
                    $tblRechargePointRegister->ref_member_id = $id;
                    $tblRechargePointRegister->tran_date = Carbon::now();
                    $tblRechargePointRegister->recharge_id = $client_id;
                    $tblRechargePointRegister->recharge_points_added = $equalAmount;
                    $tblRechargePointRegister->balance_points = $tblMemberWallet->non_redeemable + $equalAmount;
                    $tblRechargePointRegister->tran_type = "TEAM RECHARGE CASHBACK" ;//RECHARGE,CASHBACK
                    $tblRechargePointRegister->remarks = "WALLET CASHBACK";
                    $tblRechargePointRegister->save();
                }
            }

            if($equalAmount + $teamCashbackLeftover > 0){
                $tblRechargePointRegister = new RechargePointRegister();
                $tblRechargePointRegister->member_id = 0;
                $tblRechargePointRegister->ref_member_id = $id;
                $tblRechargePointRegister->tran_date = Carbon::now();
                $tblRechargePointRegister->recharge_id = $client_id;
                $tblRechargePointRegister->recharge_points_added = $equalAmount + $teamCashbackLeftover;
                // $tblRechargePointRegister->balance_points += $equalAmount + $teamCashbackLeftover;
                $tblRechargePointRegister->tran_type = "TEAM RECHARGE CASHBACK" ;//RECHARGE,CASHBACK
                $tblRechargePointRegister->remarks = "WALLET CASHBACK";
                $tblRechargePointRegister->save();
            }


            DB::commit();
           $response = ['status' => true, 'message' => 'Recharge Successful'];
           return response($response, 200);
        }catch(Exception $e){
            DB::rollBack();
            $response = ['status' => false, 'message' => "Could not Recarge"];
            return response($response, 200);
        }
    }

    public function GetNonRedeemableWallet(Request $request){
        try{
            $sql = "SELECT m.unique_id,
                            q.mobile_no AS member_name,r.tran_type,
                            DATE_FORMAT(r.created_at,'%d/%m/%Y') AS TranDate,
                            ifnull(r.recharge_points_added,0) as recharge_points_added,ifnull(r.recharge_points_consumed,0) as recharge_points_consumed,
                            r.balance_points
                        FROM recharge_point_registers r
                        LEFT JOIN recharge_requests q ON r.recharge_id=q.id
                        INNER JOIN members m ON r.ref_member_id=m.member_id
                        WHERE r.member_id = ".$request->user()->id;

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

    public function FundTransfer(Request $request){
         //Validate Input
         $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'amount' => 'required|numeric:max:1000',
            'wallet_type' => [
                'required',
                Rule::in(['REDEEMABLE', 'NON-REDEEMABLE']),
            ],
         ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $response = ['status' => false, 'message' => $errors];
            return response($response, 200);
        }

        try{
            DB::beginTransaction();
            //Check if mobile_no exists
            $tblMember = Member::where('mobile_no', trim($request->mobile_no))->first();
            if($tblMember == null){
                $response = ['status' => false, 'message' => "Invalid Member"];
                return response($response, 200);
            }

            $id = $request->user()->id;
            $refId = $tblMember->member_id;

            $tblMemberWallet = MemberWallet::where('member_id', $id)->first();
            $tblMemberWalletRef = MemberWallet::where('member_id', $refId)->first();

            //Check if wallet has sufficient balance
            if(trim($request->wallet_type) == 'NON-REDEEMABLE'){
                //Check if sufficient balance
                if($request->amount > $tblMemberWallet->non_redeemable){
                    $response = ['status' => false, 'message' => "Insufficient Balance"];
                    return response($response, 200);
                }

                //Deduct from current member
                $tblRechargePointRegister = new RechargePointRegister();
                $tblRechargePointRegister->member_id = $id;
                $tblRechargePointRegister->ref_member_id = $refId;
                $tblRechargePointRegister->tran_date = Carbon::now();
                $tblRechargePointRegister->recharge_points_consumed = $request->amount;
                $tblRechargePointRegister->balance_points = $tblMemberWallet->non_redeemable - $request->amount;
                $tblRechargePointRegister->tran_type = "TRANSFER-OUT" ;//RECHARGE,CASHBACK
                $tblRechargePointRegister->remarks = "TRANSFER OUT";
                $tblRechargePointRegister->save();

                //Add to recepient
                $tblRechargePointRegister = new RechargePointRegister();
                $tblRechargePointRegister->member_id = $refId;
                $tblRechargePointRegister->ref_member_id = $id;
                $tblRechargePointRegister->tran_date = Carbon::now();
                $tblRechargePointRegister->recharge_points_added = $request->amount;
                $tblRechargePointRegister->balance_points = $tblMemberWalletRef->non_redeemable + $request->amount;
                $tblRechargePointRegister->tran_type = "TRANSFER-IN" ;//RECHARGE,CASHBACK
                $tblRechargePointRegister->remarks = "TRANSFER IN FROM NON REDEEMABLE";
                $tblRechargePointRegister->save();

                //Deduct from host Wallet
                $tblMemberWallet->transferout_amount +=  $request->amount;
                $tblMemberWallet->non_redeemable -=  $request->amount;
                $tblMemberWallet->save();

                //Add to Recepient wallet
                $tblMemberWalletRef->transferin_amount +=  $request->amount;
                $tblMemberWalletRef->non_redeemable +=  $request->amount;
                $tblMemberWalletRef->save();


            }
            if(trim($request->wallet_type) == 'REDEEMABLE'){
                //Check if sufficient balance
                if($request->amount > $tblMemberWallet->redeemable_amt){
                    $response = ['status' => false, 'message' => "Insufficient Balance"];
                    return response($response, 200);
                }

                //Deduct from redeemable amount
                $tblMemberIncome = new MemberIncome();
                $tblMemberIncome->member_id = $id;
                $tblMemberIncome->income_type = 'TRANSFER-OUT';
                $tblMemberIncome->ref_member_id = $refId;
                $tblMemberIncome->level_percent = 0;
                $tblMemberIncome->commission = 0;
                $tblMemberIncome->deduction = $request->amount;
                $tblMemberIncome->ref_amount = $request->amount;
                $tblMemberIncome->balance = $tblMemberWalletRef->redeemable_amt - $request->amount;
                $tblMemberIncome->save();

                //Add to recepient
                $tblRechargePointRegister = new RechargePointRegister();
                $tblRechargePointRegister->member_id = $id;
                $tblRechargePointRegister->ref_member_id = $refId;
                $tblRechargePointRegister->tran_date = Carbon::now();
                $tblRechargePointRegister->recharge_points_added = $request->amount;
                $tblRechargePointRegister->balance_points = $tblMemberWalletRef->non_redeemable + $request->amount;
                $tblRechargePointRegister->tran_type = "TRANSFER-IN" ;//RECHARGE,CASHBACK
                $tblRechargePointRegister->remarks = "TRANSFER IN FROM REDEEMABLE";
                $tblRechargePointRegister->save();



                //Deduct from host Wallet
                $tblMemberWallet->transferout_amount +=  $request->amount;
                $tblMemberWallet->redeemable_amt -=  $request->amount;
                $tblMemberWallet->save();

                //Add to Recepient wallet
                $tblMemberWalletRef->transferin_amount +=  $request->amount;
                $tblMemberWalletRef->non_redeemable +=  $request->amount;
                $tblMemberWalletRef->save();
            }
            DB::commit();
            $response = ['status' => true, 'message' => "Successfully Updated Fund"];
            return response($response, 200);
        } catch(Exception $e){
            DB::rollBack();
            $response = ['status' => false, 'message' => "Could not transfer"];
            return response($response, 200);
        }
    }

}
