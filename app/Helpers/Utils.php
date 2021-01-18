<?php

use Carbon\Carbon;
use App\Helpers\MiscFunctions;
use App\Models\Member;
use App\Models\Otp;
use App\Models\PaymentGateway;
use App\Models\TempMember;
use Illuminate\Support\Facades\Http;
use Razorpay\Api\Api;
// use Exception;

function generateNewMemberOTP($mobile_no) {
    try{
        $expiryDate = Carbon::now()->addMinute(10);
        $tblTempMember = TempMember::where('mobile_no',$mobile_no)->first();
        if($tblTempMember == null){
            return false;
        }
        $otp = '1111'; // newOTP();
        $msg = urlencode("OTP is ".$otp." for Mansha Real Rupees");
        $tblOTP = Otp::where('mobile_no', $mobile_no)->first();
        if($tblOTP === null){
            $tblOTP = new Otp();
            $tblOTP->mobile_no =  $mobile_no;
            $tblOTP->otp =  $otp;
            $tblOTP->expiry_at = $expiryDate;
            $tblOTP->save();
        } else {
            $tblOTP->mobile_no =  $mobile_no;
            $tblOTP->otp =  $otp;
            $tblOTP->expiry_at = $expiryDate;
            $tblOTP->save();
        }
        $url = "http://bulksms.tejasgroup.co.in/api/sendmsg.php?user=manshaa&pass=manshaa&sender=MRECOM&phone=" . $mobile_no . "&text=".$msg."&priority=ndnd&stype=normal";
        $response1 = Http::get($url);
        return true;
    } catch(\Exception $e){
        return false;
    }
}

function generateMemberOTP($mobile_no) {
    try{
        $expiryDate = Carbon::now()->addMinute(10);
        $tblTempMember = Member::where('mobile_no',$mobile_no)->first();
        if($tblTempMember == null){
            return false;
        }
        $otp = '1111'; // newOTP();
        $msg = urlencode("OTP is ".$otp." for Mansha Real Rupees");
        $tblOTP = Otp::where('mobile_no', $mobile_no)->first();
        if($tblOTP === null){
            $tblOTP = new Otp();
            $tblOTP->mobile_no =  $mobile_no;
            $tblOTP->otp =  $otp;
            $tblOTP->expiry_at = $expiryDate;
            $tblOTP->save();
        } else {
            $tblOTP->mobile_no =  $mobile_no;
            $tblOTP->otp =  $otp;
            $tblOTP->expiry_at = $expiryDate;
            $tblOTP->save();
        }
        $url = "http://bulksms.tejasgroup.co.in/api/sendmsg.php?user=manshaa&pass=manshaa&sender=MRECOM&phone=" . $mobile_no . "&text=".$msg."&priority=ndnd&stype=normal";
        $response1 = Http::get($url);
        return true;
    } catch(\Exception $e){
        return false;
    }
}

function validateOTP($mobile_no) {
    try{
        $tblOTP = Otp::where('mobile_no', $mobile_no)->first();
        if($tblOTP === null){
            return false;
        } else {
            if(Carbon::now() > $tblOTP->expiry_at){
                return false;
            }
            return true;
        }
    } catch(\Exception $e){
        return false;
    }
}

function newOTP() { 
    $generator = "1357902468"; 
    $result = ""; 
    // for ($i = 1; $i <= 4; $i++) { 
    //     $result .= substr($generator, (rand()%(strlen($generator))), 1); 
    // } 
    while($result < 99999){
        $result .= substr($generator, (rand()%(strlen($generator))), 1); 
    }
    $result = substr('00000'.$result, -4);
    // if(strlen($result) != 4){
    //     $generator1 = "135792468"; 
    //     $result .= substr($generator1, (rand()%(strlen($generator1))), 1);         
    // }
    return $result; 
} 

/** RAZORPAY INTEGRATION
* https://github.com/razorpay/razorpay-php
* 
*  composer require razorpay/razorpay:2.*
* // Orders
* $order  = $api->order->create(array('receipt' => '123', 'amount' => 100, 'currency' => 'INR')); // Creates order
* $orderId = $order['id']; // Get the created Order ID
* $order  = $api->order->fetch($orderId);
* $orders = $api->order->all($options); // Returns array of order objects
* $payments = $api->order->fetch($orderId)->payments(); // Returns array of payment objects against an order
* 
* // Payments
* $payments = $api->payment->all($options); // Returns array of payment objects
* $payment  = $api->payment->fetch($id); // Returns a particular payment
* $payment  = $api->payment->fetch($id)->capture(array('amount'=>$amount)); // Captures a payment
* 
* // To get the payment details
* echo $payment->amount;
* echo $payment->currency;
* // And so on for other attributes
* 
* // Refunds
* $refund = $api->refund->create(array('payment_id' => $id)); // Creates refund for a payment
* $refund = $api->refund->create(array('payment_id' => $id, 'amount'=>$refundAmount)); // Creates partial refund for a payment
* $refund = $api->refund->fetch($refundId); // Returns a particular refund
* 
* // Cards
* $card = $api->card->fetch($cardId); // Returns a particular card
* 
* // Customers
* $customer = $api->customer->create(array('name' => 'Razorpay User', 'email' => 'customer@razorpay.com')); // Creates customer
* $customer = $api->customer->fetch($customerId); // Returns a particular customer
* $customer = $api->customer->edit(array('name' => 'Razorpay User', 'email' => 'customer@razorpay.com')); // Edits customer
* 
* // Tokens
* $token  = $api->customer->token()->fetch($tokenId); // Returns a particular token
* $tokens = $api->customer->token()->all($options); // Returns array of token objects
* $api->customer->token()->delete($tokenId); // Deletes a token
* 
* 
* // Transfers
* $transfer  = $api->payment->fetch($paymentId)->transfer(array('transfers' => [ ['account' => $accountId, 'amount' => 100, 'currency' => 'INR']])); // Create transfer
* $transfers = $api->transfer->all(); // Fetch all transfers
* $transfers = $api->payment->fetch($paymentId)->transfers(); // Fetch all transfers created on a payment
* $transfer  = $api->transfer->fetch($transferId)->edit($options); // Edit a transfer
* $reversal  = $api->transfer->fetch($transferId)->reverse(); // Reverse a transfer
* 
* // Payment Links
* $links = $api->invoice->all();
* $link  = $api->invoice->fetch('inv_00000000000001');
* $link  = $api->invoice->create(arary('type' => 'link', 'amount' => 500, 'description' => 'For XYZ purpose', 'customer' => array('email' => 'test@test.test')));
* $link->cancel();
* $link->notifyBy('sms');
* 
* // Invoices
* $invoices = $api->invoice->all();
* $invoice  = $api->invoice->fetch('inv_00000000000001');
* $invoice  = $api->invoice->create($params); // Ref: razorpay.com/docs/invoices for request params example
* $invoice  = $invoice->edit($params);
* $invoice->issue();
* $invoice->notifyBy('email');
* $invoice->cancel();
* $invoice->delete();
* 
* // Virtual Accounts
* $virtualAccount  = $api->virtualAccount->create(array('receiver_types' => array('bank_account'), 'description' => 'First Virtual Account', 'notes' => array('receiver_key' => 'receiver_value')));
* $virtualAccounts = $api->virtualAccount->all();
* $virtualAccount  = $api->virtualAccount->fetch('va_4xbQrmEoA5WJ0G');
* $virtualAccount  = $virtualAccount->close();
* $payments        = $virtualAccount->payments();
* $bankTransfer    = $api->payment->fetch('pay_8JpVEWsoNPKdQh')->bankTransfer();
* 
* // Bharat QR
* $bharatQR = $api->virtualAccount->create(array('receivers' => array('types' => array('qr_code')), 'description' => 'First QR code', 'amount_expected' => 100, 'notes' => array('receiver_key' => 'receiver_value'))); // Create Static QR
* $bharatQR = $api->virtualAccount->create(array('receivers' => array('types' => array('qr_code')), 'description' => 'First QR code', 'notes' => array('receiver_key' => 'receiver_value'))); // Create Dynamic QR
* 
* // Subscriptions
* $plan          = $api->plan->create(array('period' => 'weekly', 'interval' => 1, 'item' => array('name' => 'Test Weekly 1 plan', 'description' => 'Description for the weekly 1 plan', 'amount' => 600, 'currency' => 'INR')));
* $plan          = $api->plan->fetch('plan_7wAosPWtrkhqZw');
* $plans         = $api->plan->all();
* $subscription  = $api->subscription->create(array('plan_id' => 'plan_7wAosPWtrkhqZw', 'customer_notify' => 1, 'total_count' => 6, 'start_at' => 1495995837, 'addons' => array(array('item' => array('name' => 'Delivery charges', 'amount' => 30000, 'currency' => 'INR')))));
* $subscription  = $api->subscription->fetch('sub_82uBGfpFK47AlA');
* $subscriptions = $api->subscription->all();
* $subscription  = $api->subscription->fetch('sub_82uBGfpFK47AlA')->cancel($options); //$options = ['cancel_at_cycle_end' => 1];
* $addon         = $api->subscription->fetch('sub_82uBGfpFK47AlA')->createAddon(array('item' => array('name' => 'Extra Chair', 'amount' => 30000, 'currency' => 'INR'), 'quantity' => 2));
* $addon         = $api->addon->fetch('ao_8nDvQYYGQI5o4H');
* $addon         = $api->addon->fetch('ao_8nDvQYYGQI5o4H')->delete();
* 
* // Settlements
* $settlement    = $api->settlement->fetch('setl_7IZKKI4Pnt2kEe');
* $settlements   = $api->settlement->all();
* $reports       = $api->settlement->reports(array('year' => 2018, 'month' => 2));
*/



function createRazorpayTempOrder($tmpid,$amount,$taxPercent){
    try{
        $api_key = 'rzp_test_H4Hl4CW33loNwZ';
        $api_secret ='Rq9k7LaMa6FHOgz4ujcryTBz';

        $receiptID = getUniqueTicketNo();
        $api = new Api($api_key, $api_secret);
        $order  = $api->order->create(array('receipt' => $receiptID, 'amount' => $amount * 100, 'currency' => 'INR')); // Creates order
        $orderID = $order['id'];     

        $taxAmount = round($amount * $taxPercent * 0.01,2);
        $netAmount = $amount + $taxAmount;

        $tblPaymentGateway = new PaymentGateway();
        $tblPaymentGateway->temp_id = $tmpid;
        $tblPaymentGateway->amount = $amount;
        $tblPaymentGateway->tax_percent = $taxPercent;
        $tblPaymentGateway->tax_amount = $taxAmount;
        $tblPaymentGateway->net_amount =  $netAmount;
        $tblPaymentGateway->order_id = $orderID;
        $tblPaymentGateway->receipt_id = $receiptID;
        $tblPaymentGateway->fake = true;
        $tblPaymentGateway->save();

        return $orderID;
    } catch(Exception $e){
        return $e->getMessage();
    }
}
function createRazorpayMoneyOrder($tmpid,$amount,$taxPercent){
    try{
        $api_key = 'rzp_test_H4Hl4CW33loNwZ';
        $api_secret ='Rq9k7LaMa6FHOgz4ujcryTBz';

        $receiptID = getUniqueTicketNo();
        $api = new Api($api_key, $api_secret);
        $order  = $api->order->create(array('receipt' => $receiptID, 'amount' => $amount * 100, 'currency' => 'INR')); // Creates order
        $orderID = $order['id'];     

        $taxAmount = round($amount * $taxPercent * 0.01,2);
        $netAmount = $amount + $taxAmount;

        $tblPaymentGateway = new PaymentGateway();
        $tblPaymentGateway->temp_id = $tmpid;
        $tblPaymentGateway->amount = $amount;
        $tblPaymentGateway->tax_percent = $taxPercent;
        $tblPaymentGateway->tax_amount = $taxAmount;
        $tblPaymentGateway->net_amount =  $netAmount;
        $tblPaymentGateway->order_id = $orderID;
        $tblPaymentGateway->receipt_id = $receiptID;
        $tblPaymentGateway->fake = true;
        $tblPaymentGateway->save();
        
        $ret['txn_id'] = $orderID;
        $ret['id'] = $tblPaymentGateway->id;
        return $ret;
        // return $orderID;
    } catch(Exception $e){
        return $e->getMessage();
    }
}

// function createRazorpayMoneyOrder($memberid,$amount,,$taxPercent){
//     try{
//         $api_key = 'rzp_test_H4Hl4CW33loNwZ';
//         $api_secret ='Rq9k7LaMa6FHOgz4ujcryTBz';

//         $tblMembers = Member::where('member_id', $memberid);
//         if($tblMembers == null)
//             return '';
//         $receiptID = getUniqueTicketNo();
//         $api = new Api($api_key, $api_secret);
//         $order  = $api->order->create(array('receipt' => $receiptID, 'amount' => $amount, 'currency' => 'INR')); // Creates order
//         $orderID = $order['id'];     

//         $tblPaymentGateway = new PaymentGateway();
//         $tblPaymentGateway->member_id = $memberid;
//         $tblPaymentGateway->amount = $amount;
//         $tblPaymentGateway->order_id = $orderID;
//         $tblPaymentGateway->receipt_id = $receiptID;
//         $tblPaymentGateway->fake = true;
//         $tblPaymentGateway->save();
//         $ret['txn_id'] = $orderID;
//         $ret['id'] = $tblPaymentGateway->id;

//         return $ret;
//     } catch(Exception $e){
//         return '';
//     }
// }


function getUniqueTicketNo(){
    $tmpid = (time() % 86400);
    $ticketnumber = 'Q' . date('YdMhms'). str_pad(($tmpid + 1), 3, '0', STR_PAD_LEFT);
    return $ticketnumber;
}

function getUniqueReferalCode(){
    $tmpid = (time() % 86400);
    // $ticketnumber = 'R' . date('YdMhms'). str_pad(($tmpid + 1), 3, '0', STR_PAD_LEFT);
    $ticketnumber = 'R'.date('sm').str_pad(($tmpid + 1), 3, '0', STR_PAD_LEFT);
    return $ticketnumber;
}



