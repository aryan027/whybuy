<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OTP;
use App\Models\Temp_token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Return_;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|integer|between:1,3'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(200, $validator->errors()->first());
        }
        if($request["type"]== 1){
            $validator = Validator::make($request->all(), [
                'value' => 'required|digits:10'
            ],['value.required'=>'mobile no required']
            );
            if ($validator->fails()) {
                return $this->ErrorResponse(200, $validator->errors()->first());
            }
           return  $this->send_otp($request['value'],$request['type']);
        }
        elseif($request["type"]== 2){
            $validator = Validator::make($request->all(), [
                'value' => 'required|email'
            ],['value.required'=>'email field required ..!']);
            if ($validator->fails()) {
                return $this->ErrorResponse(200, $validator->errors()->first());
            }
            return  $this->send_otp($request['value'],$request['type']);
        }
        else{
            return $this->SuccessResponse(200,'Register with kal..??');
        }
    }

    public function authentication(){
        return $this->ErrorResponse(200,'authentication failed');
    }

    public function verify_otp(Request $request)
    {
        $validate= Validator::make($request->all(),[
            'token'=>'required',
            'otp'=>'required',
        ]);

        if($validate->fails()){
            return $this->ErrorResponse(200, $validate->errors()->messages());
        }
        $temp= Temp_token::where(['token'=> $request->token,'is_expired'=>false])->first();
        if(!Temp_token::where(['token'=> $request->token])->exists()){
            return $this->ErrorResponse(200,'Something went wrong ..!');
        }
        if($temp->is_login == true){
            return $this->login_otp($request->token,$request->otp);
        }
        if(!Temp_token::where(['token'=> $request->token,'otp'=>$request->otp,'is_login'=>false,'is_expired'=>false])->exists()){
            return $this->ErrorResponse(200,'Otp is not valid/expired ..!');
        }
        $data= Temp_token::where(['token' =>$request->token,'otp'=>$request->otp,'is_login'=>false,'is_expired'=>false])->first();
        $user= User::create([
            'mobile'=>$data->mobile ?? '',
            'email'=>$data->email ?? '',
            'status'=> 1
        ]);
        $data->delete();
        if(!$user){
            return $this->ErrorResponse(200,'Something went wrong ..!');
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
            return $this->ErrorResponse(200,'Otp is not valid/expired ..!');
        }
        $data= Temp_token::where(['token' =>$token,'otp'=>$otp,'is_login'=>true,'is_expired'=>false])->first();
        $user = User::when(function($query) use($data){
            return $query->where(['mobile'=> $data['mobile']]);
        })->when(function($query) use($data){
            return $query->Where(['email' => $data['email']]);
        })
            ->first();
        $data->delete();
        if(!$user){
            return $this->ErrorResponse(200,'Something went wrong ..!');
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
            return $this->ErrorResponse(200, $validator->errors()->messages());
        }
        $message= $request['mobile']==''?'email id':'mobile number';

        if (!user::where('mobile', $request->mobile)->orWhere('email',$request->email)->exists()) {
            return $this->ErrorResponse(200, 'User does not exists ..! Kindly register ');
        }

        $getUser = User::where(['status'=> false])->when(function($query) use($request){
            return $query->where(['mobile'=> $request->mobile]);
        })->when(function($query) use($request){
            return $query->Where(['email' => $request->email]);
        })
        ->first();

        if (!empty($getUser)) {
            return $this->ErrorResponse(400, 'Your account is disable kindly contact to administrator ..!');
        }

//        $otp = rand(99999, 1000000);
        $otp = '123456';
        $token = md5($request->mobile ?? $request->email).time();
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
            return $this->ErrorResponse(200, 'Something went wrong. Please try again after sometime');
        }
        return $this->SuccessResponse(200, "Otp has been sent to your register {$message}", array('token' => $send->token));
    }

    public function validateStatus(){
        if(is_null(auth()->id())){
            return $this->ErrorResponse(200,'Invalid User ..!');
        }
        if (user::where(['id'=> auth()->id(),'status'=> false])->exists()) {
            auth()->user()->tokens()->delete();
            return $this->ErrorResponse(200, 'Your account is disable kindly contact to administrator ..!');
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
            return $this->ErrorResponse(200, $validator->errors()->messages());
        }
        $user = User::find(auth()->id());
        if (!User::where('id',auth()->id())->exists()) {
            return $this->ErrorResponse(200,'User not found ..!');
        }
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
            return $this->ErrorResponse(200, $validator->errors()->first());
        }
        if (user::where('email', $request->email)->exists()) {
            return $this->ErrorResponse(200, 'User already exists ..! Kindly login');
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
            return $this->ErrorResponse(200, 'Something went wrong. Please try again after sometime');
        }
        return $this->SuccessResponse(200, 'Otp has been sent to your register email id', array('token' => $send->token));
    }

    public function forget_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier'=>'required'
        ],['identifier.required'=>"please enter you register Email or mobile no"]);
        if ($validator->fails()) {
            return $this->ErrorResponse(200, $validator->errors()->messages());
        }

        $user = User::where('email', $request->identifier)
            ->orWhere('mobile', $request->identifier)
            ->first();
        if (!$user) {
            return $this->ErrorResponse(200,'User not found ..!');
        }

//        $otp = mt_rand(100000, 999999);
        $otp = '654321';
        $token= md5($request->identifier . time());

        if ($user->email === $request->identifier) {
            $data = ['otp' => $otp];
//            Mail::to($user->email)->send(new ForgotPasswordMail($data));
        } else {

            // Replace the placeholder with your SMS API code
//            $smsApi->send($user->mobile, "Your OTP is $otp");
        }

        $otpData = [
            'user_id' => $user->id,
            'identifier' => $request->identifier,
            'otp' => $otp,
            'token'=>$token
        ];
        OTP::create($otpData);
    return $this->SuccessResponse(200,'Otp sent your register email/mobile no',$token);

    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp'=>'required',
            'token' =>'required'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(200, $validator->errors()->first());
        }

        $otpData = Otp::where(['otp'=> $request->otp,'token'=>$request->token])->latest()->first();
        if (!$otpData || $otpData->otp !== $request->otp) {
            return $this->ErrorResponse(200,'Invalid otp ..!');
        }

        $user = User::find($otpData->user_id);
        if (!User::where('id',$otpData->user_id)->exists()) {
            return $this->ErrorResponse(200,'User not found ..!');
        }
        $otpData->delete();
        $user['token'] = 'Bearer ' . $user->createToken('auth_token')->plainTextToken;
        return  $this->SuccessResponse(200,'Otp verify successfully ..!',$user);
    }

    /***
     * @return bool
     * check user exists or not and
     * send otp to  register mobile and email id
     */
    public function send_otp($value,$type)
    {
        //checking user exist or not
        if (user::where('mobile', $value)->orWhere('email',$value)->exists()) {
            return $this->ErrorResponse(200, 'User already exists ..! Kindly login');
        }
        // checking otp send or not
        $verified = Temp_token::where('mobile', $value)->orWhere('email',$value)->where('created_at', '>=', Carbon::now()->subMinutes(10)->toDateTimeString())->where(['is_expired'=> false,'is_login'=> false])->first();
        if ($verified) {
            return $this->SuccessResponse(200, 'Otp already send to your registered  mobile number', null);
        }

        //if not sent then sent now
//        $otp = rand(99999, 1000000);
        $otp= '123456';
        $token = md5($value . time());
        if($type== 1){
            //        $sms= "https://www.fast2sms.com/dev/bulkV2?authorization=wvicxQVfhgG17T8uSbjKoeJWPzpUlMRZ64IYqaFNy5nsE9Ck2DO5CQIwRhE14rxa0DtpTV7UieZfFlbk&variables_values=".$otp."&route=otp&numbers=".$request->mobile;
            //        file_get_contents($sms);
            $send = Temp_token::create([
                'mobile' => $value,
                'otp' => $otp,
                'token' => $token,
                'is_login'=> false
            ]);
            return $this->SuccessResponse(200, 'Otp has been sent to your register mobile number', array('token' => $send->token));
        }
        if($type== 2){
            $send = Temp_token::create([
                'email' => $value,
                'otp' => $otp,
                'token' => $token,
                'is_login'=> false
            ]);
            return $this->SuccessResponse(200, 'Otp has been sent to your register Email id', array('token' => $send->token));
        }

    }

    public function login_with_google(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'google_id'=>'required',
            'email'=>'required',
            'fname'=>'nullable',
            'lname'=>'nullable',
            'profile' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);
        if($validator->fails()){
            return $this->ErrorResponse(200,$validator->errors()->first());
        }

        if(User::where('google_id',$request->google_id)->exists()){
            $user= User::where('google_id')->get()->first();
            $user['token'] =  'Bearer ' . $user->createToken('auth_token')->plainTextToken;
            return $this->SuccessResponse(200,'user login successfully ..',$user);
        }

        if(User::where('email',$request->email)->exists()){
            $user= User::where('email',$request->email)->get()->first();
            $user->update([
               'fname'=>$request->fname,
               'lname'=>$request->lname,
               'google_id'=>$request['google_id']
            ]);
            $user['token'] =  'Bearer ' . $user->createToken('auth_token')->plainTextToken;
            return $this->SuccessResponse(200,'user login successfully ..',$user);
        }
        $u= User::create($request->all());
        if($u){
            if($request->hasFile('profile') && $request->file('profile')->isValid()){
                $u->addMediaFromRequest('image')->toMediaCollection('profile');
            }
        }
        $u['token'] =  'Bearer ' . $u->createToken('auth_token')->plainTextToken;
        return $this->SuccessResponse(200,'user login successfully ..',$u);
    }

}
