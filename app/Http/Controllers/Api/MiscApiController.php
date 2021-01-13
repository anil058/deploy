<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Param;
use App\Models\RechargeProvider;
use App\Models\RechargeService;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MiscApiController extends Controller
{
    private $recharge_token_url = 'https://api.pay2all.in/token';
    private $recharge_providers_url ='https://api.pay2all.in/v1/app/providers';
    private $recharge_email = 'anil058@gmail.com';
    private $recharge_secret = 'Bxunjr';
    
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

}
