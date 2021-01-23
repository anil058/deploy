<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api'); //->except(['getMemberDocuments']);;
    }

    public function userImage(Request $request) {
        try{
            $tblMember = Member::where('member_id', $request->user()->id)->first();
            if($tblMember == null){
                $response = ['status' => false, 'message' => 'Invalid Member'];
                return response($response, 200);
            }
            $path = public_path("/member_images/") . $tblMember->image;
            $imagedata = file_get_contents($path);
            $base64 = base64_encode($imagedata);
            $response = [
                'status' => true,
                'message' => 'Successfully fetched a photograph',
                'image' => $base64,
            ];
            return response($response, 200);
        } catch(Exception $e) {
            return response()->json(['status' => false,'message' => $e->getMessage()], 200);
        }

        // return response()->download(public_path('some.png'))
        // ->header('Content-Disposition': 'attachment');
        $path = public_path(). '/Some.png';
        $filaname = 'Some.png';
        return response()->download($path, $filaname, ['Content-Type' => 'image/png']);

        // return response()->download($file,$filename,$headers);
        // return response()->header('Content-Disposition','attachment')->download(public_path('some.png'));
    }

    public function saveUserImage(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'file_name' => 'required',
                'photo' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }
            $tblMember = Member::where('member_id', $request->user()->id)->first();
            if($tblMember == null){
                $response = ['status' => false, 'message' => 'Invalid Member'];
                return response($response, 200);
            }
            $fileName = $request->file_name;
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $finalName = 'profile_img.' . $ext;

            $memberUniqueID = $tblMember->unique_id;
            $path = public_path('/member_images/').$memberUniqueID;
            $flag = createPath($path);
            if($flag == false){
                DB::rollBack();
                return response()->json(['status' => false,'message' =>'Error uploading file'], 200);
            }

            $path .=  '/' .  $finalName;
            // $tblMember->image = $path;
            // $tblMember->save();

            $img = Image::make($request->photo);
            $img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);
            return response()->json(['status' => true,'message' => 'Successfully updated photo'], 200);

            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => false,'message' => $e->getMessage()], 200);
        }

        // DB::beginTransaction();
        // try {
        //     $validator = Validator::make($request->all(), [
        //         'file_name' => 'required',
        //         'photo' => 'required',
        //     ]);
        //     if ($validator->fails()) {
        //         $errors = $validator->errors()->first();
        //         $response = ['status' => false, 'message' => $errors];
        //         return response($response, 200);
        //     }
        //     $tblMember = Member::where('member_id', $request->user()->id)->first();
        //     if($tblMember == null){
        //         $response = ['status' => false, 'message' => 'Invalid Member'];
        //         return response($response, 200);
        //     }
        //     $fileName = $request->file_name;
        //     $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        //     $finalName = 'profile_img.' . $ext;

        //     $memberUniqueID = $tblMember->unique_id;
        //     $path = public_path('/member_images/').$memberUniqueID;
        //     $flag = createPath($path);
        //     if($flag == false){
        //         DB::rollBack();
        //         return response()->json(['status' => false,'message' =>'Error uploading file'], 200);
        //     }

        //     $path .=  '/' .  $finalName;
        //     $tblMember->image = $path;
        //     $tblMember->save();

        //     $img = Image::make($request->photo);
        //     $img->resize(300, 300, function ($constraint) {
        //         $constraint->aspectRatio();
        //     })->save($path);
        //     DB::commit();
        // } catch(Exception $e){
        //     DB::rollBack();
        //     return response()->json(['status' => false,'message' => $e->getMessage()], 200);
        // }
    }

    public function saveUserPANFrontImage(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'file_name' => 'required',
                'photo' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }
            $tblMember = Member::where('member_id', $request->user()->id)->first();
            if($tblMember == null){
                $response = ['status' => false, 'message' => 'Invalid Member'];
                return response($response, 200);
            }
            $fileName = $request->file_name;
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $finalName = 'pan_front_img.' . $ext;

            $memberUniqueID = $tblMember->unique_id;
            $path = public_path('/member_images/').$memberUniqueID;
            $flag = createPath($path);
            if($flag == false){
                DB::rollBack();
                return response()->json(['status' => false,'message' =>'Error uploading file'], 200);
            }

            $path .=  '/' .  $finalName;
            // $tblMember->image = $path;
            // $tblMember->save();

            $img = Image::make($request->photo);
            $img->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function saveUserPANBackImage(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'file_name' => 'required',
                'photo' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }
            $tblMember = Member::where('member_id', $request->user()->id)->first();
            if($tblMember == null){
                $response = ['status' => false, 'message' => 'Invalid Member'];
                return response($response, 200);
            }
            $fileName = $request->file_name;
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $finalName = 'pan_back_img.' . $ext;

            $memberUniqueID = $tblMember->unique_id;
            $path = public_path('/member_images/').$memberUniqueID;
            $flag = createPath($path);
            if($flag == false){
                DB::rollBack();
                return response()->json(['status' => false,'message' =>'Error uploading file'], 200);
            }

            $path .=  '/' .  $finalName;
            // $tblMember->image = $path;
            // $tblMember->save();

            $img = Image::make($request->photo);
            $img->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function saveUserIDFrontImage(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'file_name' => 'required',
                'photo' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }
            $tblMember = Member::where('member_id', $request->user()->id)->first();
            if($tblMember == null){
                $response = ['status' => false, 'message' => 'Invalid Member'];
                return response($response, 200);
            }
            $fileName = $request->file_name;
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $finalName = 'idproof_front_img.' . $ext;

            $memberUniqueID = $tblMember->unique_id;
            $path = public_path('/member_images/').$memberUniqueID;
            $flag = createPath($path);
            if($flag == false){
                DB::rollBack();
                return response()->json(['status' => false,'message' =>'Error uploading file'], 200);
            }

            $path .=  '/' .  $finalName;
            // $tblMember->image = $path;
            // $tblMember->save();

            $img = Image::make($request->photo);
            $img->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function saveUserIDBackImage(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'file_name' => 'required',
                'photo' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                $response = ['status' => false, 'message' => $errors];
                return response($response, 200);
            }
            $tblMember = Member::where('member_id', $request->user()->id)->first();
            if($tblMember == null){
                $response = ['status' => false, 'message' => 'Invalid Member'];
                return response($response, 200);
            }
            $fileName = $request->file_name;
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $finalName = 'idproof_back_img.' . $ext;

            $memberUniqueID = $tblMember->unique_id;
            $path = public_path('/member_images/').$memberUniqueID;
            $flag = createPath($path);
            if($flag == false){
                DB::rollBack();
                return response()->json(['status' => false,'message' =>'Error uploading file'], 200);
            }

            $path .=  '/' .  $finalName;
            // $tblMember->image = $path;
            // $tblMember->save();

            $img = Image::make($request->photo);
            $img->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function getMemberDocuments(Request $request) {
        try {
            $id = $request->user()->id;
            // $id = 1;
            $tblMember = Member::where('member_id', $id)->first();
            if($tblMember == null){
                $response = ['status' => false, 'message' => 'Invalid Member'];
                return response($response, 200);
            }

            $img_pan_front = '';
            $img_pan_back = '';
            $img_id_front = '';
            $img_id_back = '';

            $path = public_path("/member_images/") . $tblMember->unique_id;
            $files = array_diff(scandir($path), array('.', '..'));

            foreach($files as $file) {
                if (strpos( $file,"idproof_back_img.") !== false){
                    $imagedata = file_get_contents($path.'/'.$file);
                    $img_id_back = base64_encode($imagedata);
                }
                if (strpos( $file,"idproof_front_img.") !== false){
                    $imagedata = file_get_contents($path.'/'.$file);
                    $img_id_front = base64_encode($imagedata);
                }
                if (strpos( $file,"pan_back_img.") !== false){
                    $imagedata = file_get_contents($path.'/'.$file);
                    $img_pan_back = base64_encode($imagedata);
                }
                if (strpos( $file,"pan_front_img.") !== false){
                    $imagedata = file_get_contents($path.'/'.$file);
                    $img_pan_front = base64_encode($imagedata);
                }
            }
           
            $response = [
                'status' => true,
                'message' => 'Successfully fetched documents',
                'img_id_back' => $img_id_back,
                'img_id_front' => $img_id_front,
                'img_pan_back' => $img_pan_back,
                'img_pan_front' => $img_pan_front,
            ];
            return response($response, 200);

        } catch(Exception $e){
            $response = ['status' => false, 'message' => $e->getMessage()];
            return response($response, 200);
        }
    }

}
