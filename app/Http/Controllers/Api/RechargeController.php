<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RechargeProvider;
use App\Models\RechargeRequest;
use App\Models\Param;
use App\Models\Member;
use App\Http\Controllers\Api\MiscApiController;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class RechargeController extends Controller
{
    private $rechargeUrl = 'https://api.pay2all.in/v1/payment/recharge';

    public function __construct()
    {
        $this->middleware('auth:api'); //->except(['RechargeMobile']);
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
            $tblProviders = RechargeProvider::where('service_id', 1)->get(['id','provider_name']);

            $tblMember = Member::where('member_id', $request->user()->id)->first();

            $response = ['status' => true, 
            'message' => 'Rs. ' . $tblMember->recharge_points,
            'data' => $tblProviders,
            ];
            return response($response, 200);
        } catch(Exception $e){
            $response = ['status' => false, 
            'message' => 'Successfully Created Temporary User',
            ];
            return response($response, 200);
        }
    }

    public function RechargeMobile(Request $request){
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

        //Check if there is an existing user with same mobile no
        // $tblMember = Member::where('mobile_no', $request->mobile_no)->first();
        $id = $request->user()->id;

        try{
            $token1 = new MiscApiController();
            $token = $token1->updateRechargeToken();

            $client_id = $this->AddRechargeRequest($request);
            if($client_id == 0){
                $response = ['status' => false, 'message' => 'Unable to add request data'];
                return response($response, 200);
            }

            $request_param = [
                'number'        => $request->number,
                'amount'        => $request->amount,
                'provider_id'   => $request->provider_id,
                'client_id'     => $client_id
            ];
            $request_data = json_encode($request_param);
            $client = new Client();
            $response = $client->request(
                'POST',
                $this->rechargeUrl,
                ['headers' => 
                    [
                        'Accept' => "application/json",
                        'Authorization' => "Bearer {$token}"
                    ],
                    'form_params' => [
                        'number' => $request->mobile_no,
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
                    $flag = false;
                    $successMessage = 'Could not recharge';
                }

                $tblRechargeRequest = RechargeRequest::find($client_id);
                $tblRechargeRequest->status_id = $json['status_id'];
                $tblRechargeRequest->message = $json['message'];
                $tblRechargeRequest->utr = $json['utr'];
                $tblRechargeRequest->report_id = $json['report_id'];
                $tblRechargeRequest->orderid = $json['orderid'];
                $tblRechargeRequest->verified = 1;
                $tblRechargeRequest->save();
                $response = [
                    'status' => $flag, 
                    'message' =>  $successMessage,
                    'status_id' => $json['status_id'] 
                ];
                return response($response, 200);
            }
            $response = ['status' => false, 'message' => 'Error connecting recharge server'];
            return response($response, 200);
        }catch(Exception $e){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }
}
