<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ClubMaster;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\MemberUser;
use App\Models\Member;
use App\Models\Otp;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Designation;
use Exception;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Http;

//USE App\Helpers\Utils;

class ApiAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['register','login','generateOTP','validateOTP','forgotPassword']);
    }

    // public function register (Request $request) {
    //     $token=Str::random(80);
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:6',
    //     ]);
    //     if ($validator->fails())
    //     {
    //         return response(['errors'=>$validator->errors()->all()], 422);
    //     }
    //     $request['password']=Hash::make($request['password']);
    //     $request['api_token'] = $token;
    //     // $user = User::create($request->toArray());
    //     $user = MemberUser::create($request->toArray());
    //     $response = ['token' => $token];
    //     return response($response, 200);
    // }

    public function login (Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'password' => 'required|string|min:6',
                'otp' => 'required|digits:4',
            ]);
    
            if ($validator->fails())
            {
                $error = $validator->errors()->first();
                $response = ['status' => false, 'message' => $error];
                return response($response, 200);
                // return response(['errors' => $error], 422);
            }
            
            //Test Code *************************************
            // try{
            //     $tblOTP = Otp::Where('mobile_no', $request->mobile_no)->first();
            // } catch(Exception $e){
            //     $response = ['status' => false, 'message' => $e->getMessage()];
            //     return response($response, 200);
            // }
            // $response = ['status' => false, 'message' => 'Testing the loop'];
            // return response($response, 200);
            //***************************************** */

            $tblOTP = Otp::where('mobile_no', $request->mobile_no)->first();
            if($tblOTP === null) {
                $response = ['status' => false, 'message' => 'Expired or Invalid OTP'];
                return response($response, 200);
            }
    
            if($tblOTP->otp != $request->otp){
                $response = ['status' => false, 'message' => 'Expired or Invalid OTP'];
                return response($response, 200);
            }
    
            if($tblOTP->expiry_at < Carbon::now()) {
                $response = ['status' => false, 'message' => 'Expired or Invalid OTP'];
                return response($response, 200);
            }
    


            $user = MemberUser::where('mobile_no', $request->mobile_no)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $tblMember = Member::where('member_id', $user->id)->first();
                    $tblDesignation = ClubMaster::find($tblMember->designation_id);
    
                    $token=Str::random(80);
                    // $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                    $user->api_token = $token;
                    $user->save();
    
                    $image_path = public_path("/member_images/dummy.jpg");
                    $path = public_path("/member_images/") . $tblMember->unique_id;
                    if(file_exists($path)){
                        $files = array_diff(scandir($path), array('.', '..'));
                        $image_path = '';
    
                        foreach($files as $file) {
                            if (strpos( $file,"profile_img.") !== false){
                                $image_path = $path.'/'.$file;
                            }
                        }
    
                        // if (strlen($image_path) == 0){
                        //     $image_path = public_path("/member_images/dummy.jpg");
                        // }
                    }
                   

                    $imagedata = file_get_contents($image_path);
                    $profile_img = base64_encode($imagedata);
           
                    
                    // $path = public_path("/member_images/") . $tblMember->unique_id;
                    // if (file_exists($path) == false){
                    //     $path = public_path("/member_images/dummy.jpg");
                    // }
                    // // $path = public_path("/member_images/") . $tblMember->image;

                    // $imagedata = file_get_contents($path);
                    // $base64 = base64_encode($imagedata);
    
                    $response = [
                        'status' => true, 
                        'message' => 'Successful login',
                        'token' => $token, 
                        'name' => $tblMember->first_name, 
                        'designation' => $tblDesignation->designation, 
                        'image' => $profile_img];
                    return response($response, 200);
                } else {
                    $response = ['status' => false, 'message' => 'Expired or Invalid OTP'];
                    return response($response, 200);
                }
            } else {
                $response = ['status' => false, 'message' => 'Expired or Invalid OTP'];
                return response($response, 200);
            }
        } catch(Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }


    }

    public function logout (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token=Str::random(80);
                // $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $user->api_token = $token;
                $user->save();
                $response = ['message' => 'You have been successfully logged out!'];
                return response($response, 200);

            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }

        // $token = $request->user()->token();
        // $token->revoke();
    }

    public function forgotPassword (Request $request) {
        // dd($request->mobile_no);
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'password' => 'required|string|min:6',
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails())
        {
            return response(["status" => false, "message" => $validator->errors()->all()], 200);
        }
        
        $tblOTP = Otp::Where('mobile_no', $request->mobile_no)->first();
        if($tblOTP === null) {
            $response = ["status" => false, "message" => "Invalid otp"];
            return response($response, 200);
        }

        if($tblOTP->otp != $request->otp){
            $response = ["status" => false, "message" => "Invalid otp"];
            return response($response, 200);
        }

        if($tblOTP->expiry_at < Carbon::now()) {
            $response = ["status" => false, "message" => "Invalid otp"];
            return response($response, 200);
        }

        $user = MemberUser::where('mobile_no', $request->mobile_no)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $token=Str::random(80);
            $user->api_token = $token;
            $user->save();
            $response = ['status' => true, 'message' => 'successfully updated password'];
            return response($response, 200);
        } else {
            $response = ["status" => false, "message" =>'User does not exist or invalid otp'];
            return response($response, 200);
        }
    }

    public function generateOTP(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }

            if(generateMemberOTP($request->mobile_no) == true){
                $response = ['status' => true, 'message' => 'OTP Generated'];
                // $url = "http://bulksms.tejasgroup.co.in/api/sendmsg.php?user=manshaa&pass=manshaa&sender=MRECOM&phone=9835718779&text=Test%20SMS&priority=ndnd&stype=normal";
                // $response1 = Http::get($url);
                return response($response, 200);
            }
            $response = ['status' => false, 'message' => 'Could not Generate OTP'];
            return response($response, 200);
          
        } catch(\Exception $e){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }

    public function validateOTP(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                "otp" => "required|numeric|min:1000|max:9999",
            ]);
    
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }
            try{
                $tblOTP = Otp::where('mobile_no', $request->mobile_no)->first();
            } catch(\Exception $e) {
                $response = ['status' => false, 'message' => $e->getMessage()];
                return response($response, 200);
            }

            if($tblOTP == null){
                $response = ['status' => false, 'message' => 'Invalid Mobile or OTP'];
                return response($response, 200);
            }
    
            if($tblOTP->expiry_at < Carbon::now()) {
                $response = ['status' => false, 'message' => 'Invalid Mobile or OTP'];
                return response($response, 200);
            }

            if($tblOTP->otp == $request->otp) {
                $response = ['status' => true, 'message' => 'Validation Successful'];
                return response($response, 200);
            }

            $response = ['status' => false, 'message' => 'Invalid Mobile or OTP'];
            return response($response, 200);

        } catch(Exception $e){
            $response = ['status' => false, 'message' => 'Invalid Mobile or OTP'];
            return response($response, 200);
        }
    }
    
}
