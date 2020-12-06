<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Member;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\MiscFunctions;

class UserApiController extends Controller
{
    use MiscFunctions;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['create','generateOTP']);
    }

    public function user(Request $request){
        return $request->user();
    }

    public function create(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'name' => 'required|string|max:50',
                'password' => 'required'
            ]);
             
            if ($validator->fails()) {
                $errors = $validator->errors();
                // return $errors->toJson();
                return response()->json(['Error' => $errors]);
            }
    
            $token=Str::random(80);
    
            User::Create([
                'name'	   =>	$request['name'],
                'email'	   =>	$request['email'],
                'password' =>	Hash::make($request['name']),
                'api_token'=>   $token,
            ]);
            return response()->json(['token' => $token]);
          
          } catch (\Exception $e) {
            return $e->getMessage();
          }
        
    }

    public function generateOTP(Request $request) {
        try{
            $expiryDate =  Carbon::now()->addMinute();
            $validator = Validator::make($request->all(), [
                'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['Error' => $errors]);
            }

            $tblOTP = Otp::where('mobile_no', $request->mobile_no)->first();
            if($tblOTP === null){
                $tblOTP = new Otp();
                $tblOTP->mobile_no =  $request->mobile_no;
                $tblOTP->otp =  $this->newOTP();
                $tblOTP->expiry_at = $expiryDate;
                $tblOTP->save();
            } else {
                $tblOTP->mobile_no =  $request->mobile_no;
                $tblOTP->otp =  $this->newOTP();
                $tblOTP->expiry_at = $expiryDate;
                $tblOTP->save();
            }
            $response = ['status' => true, 'message' => 'OTP Created'];
            return response($response, 200);
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
