<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Str;
use App\Helpers\RazorPayX;

class WebhookController extends Controller
{
    private $api_key;
    private $api_secret;
    private $webhook_secret;

    public function __construct()
    {
        // $this->middleware('auth:api')->except(['MemberRewards','RazorHooks']);
        $this->api_key = env('RAZOR_KEY','');
        $this->api_secret = env('RAZOR_SECRET');
        $this->webhook_secret = env('RAZORX_WEBHOOK_SECRET');
        //
    }

    private function validateSignature(Request $request)
    {
        //https://github.com/msonowal/laravel-razor-pay-cashier/blob/master/src/CashierServiceProvider.php
        $api = new Api($this->api_key, $this->api_secret);
        $webhookSecret = $this->webhook_secret;
        $webhookSignature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();
        // $this->razorpay->utility->verifyWebhookSignature($payload, $webhookSignature, $webhookSecret);
        $api->utility->verifyWebhookSignature($payload, $webhookSignature, $webhookSecret);
    }

    public function HandleWebhook(Request $request)
    {
        $this->validateSignature($request);

        $payload = $request->all();

        if ((!isset($payload['entity'])) || $payload['entity'] != 'event') {
            //Unknown webhook as currrently configured to support only events
            return;
        }

        $method = 'handle'.Str::studly(str_replace('.', '_', $payload['event']));

        if (method_exists($this, $method)) {
            return $this->{$method}($payload);
        } else {
            return $this->missingMethod();
        }
    }
}
