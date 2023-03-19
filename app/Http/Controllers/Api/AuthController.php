<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Temp_token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(403, $validator->errors()->first());
        }
        if (user::where('mobile', $request->mobile)->exists()) {
            return $this->ErrorResponse(409, 'User already exists ..! Kindly login');
        }
        $verified = Temp_token::where('mobile',  $request['mobile'])->where('created_at', '>=', Carbon::now()->subMinutes(10)->toDateTimeString())->where(['is_expired'=> false,'is_login'=> false])->first();
        if ($verified) {
            return $this->SuccessResponse(200, 'Otp already send to your registered  mobile number', null);
        }
//        $otp = rand(99999, 1000000);
            $otp= '123456';
        $token = md5($request->mobile . time());
        $send = Temp_token::create([
            'mobile' => $request['mobile'],
            'otp' => $otp,
            'token' => $token,
            'is_login'=> false
        ]);
//        $sms= "https://www.fast2sms.com/dev/bulkV2?authorization=wvicxQVfhgG17T8uSbjKoeJWPzpUlMRZ64IYqaFNy5nsE9Ck2DO5CQIwRhE14rxa0DtpTV7UieZfFlbk&variables_values=".$otp."&route=otp&numbers=".$request->mobile;
//        file_get_contents($sms);

        if (!$send) {
            return $this->ErrorResponse(400, 'Something went wrong. Please try again after sometime');
        }
        return $this->SuccessResponse(200, 'Otp has been sent to your register mobile number', array('token' => $send->token));
    }

    public function authentication(){
        return $this->ErrorResponse(401,'authentication failed');
    }

    public function verify_otp(Request $request)
    {
        $validate= Validator::make($request->all(),[
            'token'=>'required',
            'otp'=>'required',
        ]);

        if($validate->fails()){
            return $this->ErrorResponse(400, $validate->errors()->messages());
        }
        $temp= Temp_token::where(['token'=> $request->token,'is_expired'=>false])->first();
        if(!Temp_token::where(['token'=> $request->token])->exists()){
            return $this->ErrorResponse(400,'Something went wrong ..!');
        }
        if($temp->is_login == true){
            return $this->login_otp($request->token,$request->otp);
        }
        if(!Temp_token::where(['token'=> $request->token,'otp'=>$request->otp,'is_login'=>false,'is_expired'=>false])->exists()){
            return $this->ErrorResponse(400,'Otp is not valid/expired ..!');
        }
        $data= Temp_token::where(['token' =>$request->token,'otp'=>$request->otp,'is_login'=>false,'is_expired'=>false])->first();
        $user= User::create([
            'mobile'=>$data->mobile ?? '',
            'email'=>$data->email ?? '',
            'status'=>true
        ]);
        $data->delete();
        if(!$user){
            return $this->ErrorResponse(400,'Something went wrong ..!');
        }

        $user['token'] = 'Bearer ' . $user->createToken('auth_token')->plainTextToken;
        unset($user['created_at']);
        unset($user['updated_at']);
        return $this->SuccessResponse(200,'User register successfully ..!',$user);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->SuccessResponse(200, 'You have successfully logged out', null);
    }



    public function login_otp($token,$otp){
        if(!Temp_token::where(['token'=> $token,'otp'=>$otp,'is_login'=>true,'is_expired'=>false])->exists()){
            return $this->ErrorResponse(400,'Otp is not valid/expired ..!');
        }
        $data= Temp_token::where(['token' =>$token,'otp'=>$otp,'is_login'=>true,'is_expired'=>false])->first();
        $user= User::where('mobile',$data->mobile)->orWhere('email',$data->email)->first();
        $data->delete();
        if(!$user){
            return $this->ErrorResponse(400,'Something went wrong ..!');
        }
        $user['token'] = 'Bearer ' . $user->createToken('auth_token')->plainTextToken;
        unset($user['created_at']);
        unset($user['updated_at']);
        return $this->SuccessResponse(200,'User login successfully ..!',$user);
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile' => 'required_without:email|digits:10',
            'email'=>'required_without:mobile|email'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(403, $validator->errors()->messages());
        }
        $message= $request['mobile']==''?'email id':'mobile number';

        if (!user::where('mobile', $request->mobile)->orWhere('email',$request->email)->exists()) {
            return $this->ErrorResponse(400, 'User does not exists ..! Kindly register ');
        }
        if (user::where(['status'=> false])->orWhere(['mobile'=> $request->mobile])->orWhere('email',$request->email)->exists()) {
            return $this->ErrorResponse(400, 'Your account is disable kindly contact to administrator ..!');
        }

//        $otp = rand(99999, 1000000);
        $otp = '123456';
        $token = md5($request->mobile ??$request->email . time());
        $send = Temp_token::create([
            'mobile' => $request['mobile']??'',
            'email' => $request['email']??'',
            'otp' => $otp,
            'token' => $token,
            'is_login'=> true
        ]);
//        $sms= "https://www.fast2sms.com/dev/bulkV2?authorization=wvicxQVfhgG17T8uSbjKoeJWPzpUlMRZ64IYqaFNy5nsE9Ck2DO5CQIwRhE14rxa0DtpTV7UieZfFlbk&variables_values=".$otp."&route=otp&numbers=".$request->mobile;
//        file_get_contents($sms);

        if (!$send) {
            return $this->ErrorResponse(400, 'Something went wrong. Please try again after sometime');
        }
        return $this->SuccessResponse(200, "Otp has been sent to your register {$message}", array('token' => $send->token));
    }

    public function validateStatus(){
        if(is_null(auth()->id())){
            return $this->ErrorResponse(400,'Invalid User ..!');
        }
        if (user::where(['id'=> auth()->id(),'status'=> false])->exists()) {
            auth()->user()->tokens()->delete();
            return $this->ErrorResponse(400, 'Your account is disable kindly contact to administrator ..!');
        }
    }

    public function update_token(Request $request)
    {
        $user= User::find(auth()->id());
        $user->update([
            'name'=> $request->name ?? $user->name,
            'fcm_token'=> $request->fcm_token ?? $user->fcm_token
        ]);
        return $this->SuccessResponse(200, 'Token update successfully');
    }

    public function password_create(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'required|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation'=>'required'

        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(404, $validator->errors()->messages());
        }
        $user = User::find(auth()->id());
        $user->update([
            'password'=> Hash::make($request->password)
        ]);
        auth()->user()->tokens()->delete();
        return $this->SuccessResponse(200, 'password created successfully ..!');

    }

    public function register_with_email(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(403, $validator->errors()->first());
        }
        if (user::where('email', $request->email)->exists()) {
            return $this->ErrorResponse(409, 'User already exists ..! Kindly login');
        }
        $verified = Temp_token::where('email',  $request['email'])->where('created_at', '>=', Carbon::now()->subMinutes(10)->toDateTimeString())->where(['is_expired'=> false,'is_login'=> false])->first();
        if ($verified) {
            return $this->SuccessResponse(200, 'Otp already send to your registered  mobile number', null);
        }
//        $otp = rand(99999, 1000000);
        $otp= '123456';
        $token = md5($request->email . time());
        $send = Temp_token::create([
            'email' => $request['email'],
            'otp' => $otp,
            'token' => $token,
            'is_login'=> false
        ]);
//        $sms= "https://www.fast2sms.com/dev/bulkV2?authorization=wvicxQVfhgG17T8uSbjKoeJWPzpUlMRZ64IYqaFNy5nsE9Ck2DO5CQIwRhE14rxa0DtpTV7UieZfFlbk&variables_values=".$otp."&route=otp&numbers=".$request->mobile;
//        file_get_contents($sms);

        if (!$send) {
            return $this->ErrorResponse(400, 'Something went wrong. Please try again after sometime');
        }
        return $this->SuccessResponse(200, 'Otp has been sent to your register email id', array('token' => $send->token));
    }


}
