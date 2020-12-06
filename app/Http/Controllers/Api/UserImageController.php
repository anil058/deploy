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
        $this->middleware('auth:api');
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
            $finalName = 'profile_img'. $request->user()->id.'.'.$ext;
            $path = public_path("/member_images/") . $finalName;
            // $file = base64_decode($request->photo);

            $tblMember->image = $finalName;
            $tblMember->save();

            $img = Image::make($request->photo);
            $img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => false,'message' => $e->getMessage()], 200);
        }
    }

}
