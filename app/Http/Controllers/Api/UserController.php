<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Hash;

class UserController extends Controller
{
   public function userDetail(Request $request)
   {
    try {
        $user = auth()->user();
        if(!empty($user)){
            $user->image = $user->getFirstMediaUrl('profile_picture');
            unset($user->media);
            return $this->SuccessResponse(200, 'User Fetched',$user);
        }
        return $this->ErrorResponse(500, 'Something Went Wrong');
    } catch (Exception $exception) {
        logger('error occurred in user fetching process');
        logger(json_encode($exception));
        return $this->ErrorResponse(500, 'Something Went Wrong');
    }
   }

   public function updateProfile(Request $request)
   {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'fname'=>'required|string',
                    'lname'=>'required|string',
                    'email'=>'required|email|unique:users,email,'.$user->id,
                    'dob' =>'required|date_format:Y-m-d|before:today',
                    'gender'=>'required|in:male,female',
                    'password'=>'required',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $user->fname = $request->fname;
                $user->lname = $request->lname;
                $user->email = $request->email;
                $user->dob = $request->dob;
                $user->gender = $request->gender;
                $user->password = Hash::make($request->password);
                $user->save();
                if($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()){
                    $user->clearMediaCollection('profile_picture');
                    $user->addMediaFromRequest('profile_picture')->toMediaCollection('profile_picture');
                }
                // $user->getFirstMediaUrl('profile_picture');
                $user->image = $user->getFirstMediaUrl('profile_picture');
                unset($user->media);
                return $this->SuccessResponse(200, 'User Updated Successfully',$user);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
   }

    // add user
   public function addAddress(Request $request)
   {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'address'=>'required',
                    'city'=>'required',
                    'state'=>'required',
                    'country_id'=>'required|exists:countries,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $addresses = $this->addresses;
                $addresses->user_id = $user->id;
                $addresses->address = $request->address;
                $addresses->city = $request->city;
                $addresses->state = $request->state;
                $addresses->country_id = $request->country_id;
                $addresses->latitude = $request->latitude;
                $addresses->longitude = $request->longitude;
                $addresses->save();
                return $this->SuccessResponse(200, 'Address Added Successfully',$addresses);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
   }

    // Get app address for particular user   
   public function getAddress(Request $request)
   {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $addresses = $this->addresses->where('user_id',$user->id)->get();
                return $this->SuccessResponse(200, 'Address get successfully',$addresses);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
   }

    // Get app address detail
    public function getAddressDetail(Request $request, $id)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $addresses = $this->addresses->where(['id' => $id,'user_id' => $user->id])->first();
                if(!empty($addresses)){
                    return $this->SuccessResponse(200, 'Address get successfully',$addresses);
                }
            return $this->ErrorResponse(404, 'Address not found');
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Update address
    public function updateAddress(Request $request, $id)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $addresses = $this->addresses->where(['id' => $id,'user_id' => $user->id])->first();
                if(!empty($addresses)){
                    $validator= Validator::make($request->all(),[
                        'address'=>'required',
                        'city'=>'required',
                        'state'=>'required',
                        'country_id'=>'required|exists:countries,id',
                    ]);
                    if($validator->fails()){
                        return $this->ErrorResponse(400,$validator->errors()->first());
                    }
                    $addresses->address = $request->address;
                    $addresses->city = $request->city;
                    $addresses->state = $request->state;
                    $addresses->country_id = $request->country_id;
                    $addresses->latitude = $request->latitude;
                    $addresses->longitude = $request->longitude;
                    $addresses->save();
                    return $this->SuccessResponse(200, 'Address updated successfully',$addresses);
                }
            return $this->ErrorResponse(404, 'Address not found');
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Delete address
    public function deleteAddress(Request $request, $id)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $addresses = $this->addresses->where(['id' => $id,'user_id' => $user->id])->first();
                if(!empty($addresses)){
                    $addresses->delete();
                    return $this->SuccessResponse(200, 'Address deleted successfully');
                }
                return $this->ErrorResponse(404, 'Address not found');
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // User Report
    public function userReport(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'report_type'=>'required',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $user->report_type = $request->report_type;
                $user->report_comment = $request->report_comment;
                $user->save();
                return $this->SuccessResponse(200, 'Report added successfully',$user);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }    

     // Delete Account
    public function deleteAccount(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                auth()->user()->tokens()->delete();
                $user->delete();
                return $this->SuccessResponse(200, 'Account has been deleted');
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }    
    
}