<?php

namespace App\Helpers;

use App\Models\PaymentGateway;
// use Razorpay\Api\Api;
use GuzzleHttp\Client;
use Exception;

class RazorPayX {

    private $razor_baseuri = "https://api.razorpay.com/v1/";
    private $api_key;
    private $api_secret;
    private $razorx_account_no;
    private $razorx_ifsc_code;
    private $client;

    //Razor uri categories
    //**********************************
    //Contacts
    //Fund
    //Payout
    //transactions

    function __construct() {
        $this->api_key = env('RAZOR_KEY','');
        $this->api_secret = env('RAZOR_SECRET');
        $this->razorx_account_no = env('RAZORX_ACCOUNT_NUMBER');
        $this->razorx_ifsc_code = env('RAZORX_IFSC_CODE');
        $this->client = new Client();
    }

    //Contacts *********************************************************************
    public function createContact($askName, $askEmail, $askMobile, $askMemberID){
        try{
            $url = $this->razor_baseuri.'contacts';

            $res = $this->client->request('POST', $url, [
                'form_params' => [
                    "name" => $askName,
                    "email" => $askEmail,
                    "contact" => $askMobile,
                    "type" => "customer",
                    "reference_id" => $askMemberID,
                ],
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */

            // {
            //     "id": "cont_00000000000001",
            //     "entity": "contact",
            //     "name": "Gaurav Kumar",
            //     "contact": "9123456789",
            //     "email": "gaurav.kumar@example.com",
            //     "type": "employee",
            //     "reference_id": "Acme Contact ID 12345",
            //     "batch_id": null,
            //     "active": true,
            //     "notes": {
            //       "notes_key_1": "Tea, Earl Grey, Hot",
            //       "notes_key_2": "Tea, Earl Grey… decaf."
            //     },
            //     "created_at": 1545320320
            //   }

            if($res->getStatusCode() == 201){
                $json = json_decode($res->getBody(), true);
                return $json["id"];
            }
            return '';
        }catch(Exception $ex){
            return '';
        }
    }

    public function updateContact($id,$askName, $askEmail, $askMobile, $askMemberID){
        try{
            $url = $this->razor_baseuri.'contacts/'.$id;

            $res = $this->client->request('PATCH', $url, [
                'form_params' => [
                    "name" => $askName,
                    "email" => $askEmail,
                    "contact" => $askMobile,
                    "type" => "Member",
                    "reference_id" => $askMemberID,
                ],
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */
            // {
            //     "id": "cont_00000000000001",
            //     "entity": "contact",
            //     "name": "Gaurav Kumar",
            //     "contact": "9123456789",
            //     "email": "gaurav.kumar@example.com",
            //     "type": "self",
            //     "reference_id": "Acme Contact ID 12345",
            //     "batch_id": null,
            //     "active": true,
            //     "notes": {
            //       "notes_key_1":"Tea, Earl Grey, Hot",
            //       "notes_key_2":"Tea, Earl Grey… decaf."
            //     },
            //     "created_at": 1545320320
            //   }

            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

    public function fetchAllContacts(){
        try{
            $url = $this->razor_baseuri.'contacts';

            $res = $this->client->request('GET', $url, [
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */

            // {
            //     "entity": "collection",
            //     "count": 1,
            //     "items": [
            //       {
            //       "id": "cont_00000000000001",
            //       "entity": "contact",
            //       "name": "Gaurav Kumar",
            //       "contact": "9123456789",
            //       "email": "gaurav.kumar@example.com",
            //       "type": "self",
            //       "reference_id": "Acme Contact ID 12345",
            //       "batch_id": null,
            //       "active": true,
            //       "notes": {
            //         "notes_key_1":"Tea, Earl Grey, Hot",
            //         "notes_key_2":"Tea, Earl Grey… decaf."
            //       },
            //       "created_at": 1545322986
            //       }
            //      ]
            // }


            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

    public function fetchContactByID($id){
        try{
            $url = $this->razor_baseuri.'contacts/'.$id;

            $res = $this->client->request('GET', $url, [
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */

            // {
            //     "id": "cont_00000000000001",
            //     "entity": "contact",
            //     "name": "Gaurav Kumar",
            //     "contact": "9123456789",
            //     "email": "gaurav.kumar@example.com",
            //     "type": "self",
            //     "reference_id": "Acme Contact ID 12345",
            //     "batch_id": null,
            //     "active": true,
            //     "notes": {
            //       "notes_key_1":"Tea, Earl Grey, Hot",
            //       "notes_key_2":"Tea, Earl Grey… decaf."
            //     },
            //     "created_at": 1545322986
            // }

            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

    //Fund Account *********************************************************************

    public function createFundAccountBank($askContactID, $askFundACName,$askIFSC,$askAcNo){
        try{
            $url = $this->razor_baseuri.'fund_accounts';

            $res = $this->client->request('POST', $url, [
                'form_params' => [
                    "contact_id" => $askContactID,
                    "account_type" => 'bank_account',
                    "bank_account" => [
                        "name" => $askFundACName,
                        "ifsc" => $askIFSC,
                        "account_number" => $askAcNo
                    ]
                ],
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */

            // {
            //     "id" : "fa_00000000000001",
            //     "entity": "fund_account",
            //     "contact_id" : "cont_00000000000001",
            //     "account_type": "bank_account",
            //     "bank_account": {
            //       "ifsc": "HDFC0000053",
            //       "bank_name": "HDFC Bank",
            //       "name": "Gaurav Kumar",
            //       "account_number": "765432123456789",
            //       "notes": []
            //     },
            //     "active": true,
            //     "batch_id": null,
            //     "created_at": 1543650891
            // }


            if($res->getStatusCode() == 201){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

    public function fetchAllFundAccounts(){
        try{
            $url = $this->razor_baseuri.'fund_accounts';

            $res = $this->client->request('GET', $url, [
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */
            // {
            //     "entity": "collection",
            //     "count": 3,
            //     "items": [
            //     {
            //     "id": "fa_00000000000001",
            //     "entity": "fund_account",
            //     "contact_id": "cont_00000000000001",
            //     "account_type": "bank_account",
            //     "bank_account": {
            //       "ifsc": "HDFC0000053",
            //       "bank_name": "HDFC Bank",
            //       "name": "Gaurav Kumar",
            //       "account_number": "765432123456789",
            //       "notes": []
            //     },
            //     "active": false,
            //     "batch_id": null,
            //     "created_at": 1545312598
            //     },
            //     {
            //     "id": "fa_00000000000002",
            //     "entity": "fund_account",
            //     "contact_id": "cont_00000000000001",
            //     "account_type": "vpa",
            //     "vpa": {
            //       "username": "gaurav.kumar",
            //       "handle": "exampleupi",
            //       "address": "gaurav.kumar@exampleupi"
            //     },
            //     "active": true,
            //     "batch_id": null,
            //     "created_at": 1545313478
            //     },
            //     {
            //     "id": "fa_00000000000001",
            //     "entity": "fund_account",
            //     "contact_id": "cont_00000000000001",
            //     "account_type": "card",
            //     "card": {
            //       "name": "Gaurav Kumar",
            //       "last4": "6789",
            //       "network": "Visa",
            //       "type": "credit",
            //       "issuer": "HDFC"
            //     },
            //     "active": false,
            //     "batch_id": null,
            //     "created_at": 1545312598
            //     }
            //    ]
            //   }

            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

    public function fetchFundAccountByID($id){
        try{
            $url = $this->razor_baseuri.'fund_accounts/'.$id;

            $res = $this->client->request('GET', $url, [
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */
            // {
            //     "id": "fa_00000000000001",
            //     "entity": "fund_account",
            //     "contact_id": "cont_00000000000001",
            //     "account_type": "bank_account",
            //     "bank_account": {
            //       "ifsc": "HDFC0000053",
            //       "bank_name": "HDFC Bank",
            //       "name": "Gaurav Kumar",
            //       "account_number": "765432123456789",
            //       "notes": []
            //     },
            //     "active": false,
            //     "batch_id": null,
            //     "created_at": 1545312598
            //   }

            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

    //Payout *********************************************************************
    public function createPayout($askFundID,$askAmount,$askRefNo){
        try{
            $url = $this->razor_baseuri.'payouts';

            $res = $this->client->request('POST', $url, [
                'form_params' => [
                    "account_number" => $this->razorx_account_no,
                    "fund_account_id" => $askFundID,
                    "amount" => $askAmount * 100,
                    "currency" => "INR",
                    "mode" => "NEFT",
                    "purpose" => "refund",
                    "queue_if_low_balance" => true,
                    "reference_id" => $askRefNo,
                    "narration" => "Acme Corp Fund Transfer",
                ],
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */
            // {
            //     "id": "pout_00000000000001",
            //     "entity": "payout",
            //     "fund_account_id": "fa_00000000000001",
            //     "amount": 1000000,
            //     "currency": "INR",
            //     "notes": {
            //       "notes_key_1":"Tea, Earl Grey, Hot",
            //       "notes_key_2":"Tea, Earl Grey… decaf."
            //     },
            //     "fees": 0,
            //     "tax": 0,
            //     "status": "queued",
            //     "utr": null,
            //     "mode": "IMPS",
            //     "purpose": "refund",
            //     "reference_id": "Acme Transaction ID 12345",
            //     "narration": "Acme Corp Fund Transfer",
            //     "batch_id": null,
            //     "failure_reason": null,
            //     "created_at": 1545383037
            //   }

            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

    public function fetchAllPayouts(){
        try{
            $url = $this->razor_baseuri.'payouts?account_number='.$this->razorx_account_no;

            $res = $this->client->request('GET', $url, [
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */
            // {
            //     "entity": "collection",
            //     "count": 2,
            //     "items": [
            //       {
            //         "id": "pout_00000000000001",
            //         "entity": "payout",
            //         "fund_account_id": "fa_00000000000001",
            //         "amount": 1000000,
            //         "currency": "INR",
            //         "notes": {
            //           "notes_key_1": "Tea, Earl Grey, Hot",
            //           "notes_key_2": "Tea, Earl Grey… decaf."
            //         },
            //         "fees": 590,
            //         "tax": 90,
            //         "status": "processed",
            //         "purpose": "payout",
            //         "utr": null,
            //         "mode": "NEFT",
            //         "reference_id": "Acme Transaction ID 12345",
            //         "narration": "Acme Corp Fund Transfer",
            //         "batch_id": null,
            //         "failure_reason": null,
            //         "created_at": 1545382870,
            //         "fee_type": "",
            //         "error": {
            //           "description": null,
            //           "source": null,
            //           "reason": null
            //         }
            //       },
            //       {
            //         "id": "pout_00000000000002",
            //         "entity": "payout",
            //         "fund_account_id": "fa_00000000000002",
            //         "amount": 1000000,
            //         "contact_id": "cont_00000000000001",
            //         "currency": "INR",
            //         "notes": {
            //           "notes_key_1": "Tea, Earl Grey, Hot",
            //           "notes_key_2": "Tea, Earl Grey… decaf."
            //         },
            //         "fees": 590,
            //         "tax": 90,
            //         "status": "reversed",
            //         "purpose": "refund",
            //         "utr": null,
            //         "mode": "NEFT",
            //         "reference_id": "Acme Transaction ID 123456",
            //         "narration": "Acme Corp Fund Transfer",
            //         "batch_id": null,
            //         "failure_reason": null,
            //         "created_at": 1545382870,
            //         "fee_type": "",
            //         "error": {
            //           "description": "IMPS is not enabled on beneficiary account, please retry with different mode",
            //           "source": "beneficiary_bank",
            //           "reason": "imps_not_allowed"
            //         }
            //       }
            //     ]
            //   }

            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

    public function fetchPayoutByID($askPayoutID){
        try{
            $url = $this->razor_baseuri.'payouts/'.$askPayoutID;

            $res = $this->client->request('GET', $url, [
                'auth' => [
                    $this->api_key,
                    $this->api_secret
                ],
            ]);

            //Response
            //************************************* */

            // {
            //     "id": "pout_00000000000001",
            //     "entity": "payout",
            //     "fund_account_id": "fa_00000000000001",
            //     "amount": 1000000,
            //     "currency": "INR",
            //     "notes": {
            //       "note_key": "Beam me up Scotty"
            //     },
            //     "fees": 590,
            //     "tax": 90,
            //     "status": "processed",
            //     "purpose": "payout",
            //     "utr": null,
            //     "mode": "NEFT",
            //     "reference_id": "Acme Transaction ID 12345",
            //     "narration": "Acme Corp Fund Transfer",
            //     "batch_id": null,
            //     "failure_reason": null,
            //     "created_at": 1545382870,
            //     "fee_type": "",
            //     "error": {
            //       "description": null,
            //       "source": null,
            //       "reason": null
            //     }
            //   }

            if($res->getStatusCode() == 200){
                $json = json_decode($res->getBody(), true);
                return true;
            }
            return false;
        }catch(Exception $ex){
            return false;
        }
    }

}
