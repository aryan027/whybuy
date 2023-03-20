<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->ajax()){
                $user = $this->user->orderBy('id','DESC')->get();
                $data['status'] = 1;
                $data['userData'] = View('admin.user.data',compact('user'))->render();
                return $data;
            }
            return view('admin.user.index');
        }catch(Exception $e) {
            abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // try {
        //     return view('admin.user.create');
        // }catch(Exception $e) {
        //     abort(500);
        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // try {

        //     $rule = [
        //         'fname'=>'required|string',
        //         'lname'=>'required|string',
        //         'email'=>'required|email|unique:users,email,',
        //         'mobile'=>'required|unique:users,mobile,',
        //         'dob' =>'required|date_format:Y-m-d|before:today',
        //         'gender'=>'required|in:male,female',
        //         'password' => 'required',
        //         'confirm_password' => 'required_with:password|same:password',
        //     ];
        //     $valid = Validator::make($request->all(),$rule);
        //     if($valid->fails()){
        //         return redirect()->back()->withErrors($valid->errors())->withInput();
        //     }
        //     $user = $this->user;
        //     $user->fname = $request->fname;
        //     $user->lname = $request->lname;
        //     $user->email = $request->email;
        //     $user->mobile = $request->mobile;
        //     $user->dob = $request->dob;
        //     $user->gender = $request->gender;
        //     $user->password = Hash::make($request->password);
        //     $user->status = $request->status;
        //     $user->save();
        //     if($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()){
        //         $user->addMediaFromRequest('profile_picture')->toMediaCollection('profile_picture');
        //     }
        //     return redirect(route('user.index'))->with('success','User added Successfully');
        // }catch(Exception $e) {
        //     abort(500);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // try {
        //     $id = decrypt($id);
        //     $user = $this->user->where('id',$id)->first();
        //     if(!empty($user)){
        //         return view('admin.user.edit',compact('user'));
        //     }
        //     return redirect()->back()->with('error','Something went to wrong!');
        // }catch(Exception $e) {
        //     abort(500);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Change status the specified resource in storage.
     */
    public function userStatus(Request $request,string $id)
    {
        // try {
        //     if($request->ajax()){
        //         $id = decrypt($id);
        //         $user = $this->user->where('id',$id)->first();
        //         $data['status'] = 0;
        //         if(!empty($user)){
        //             $user->status = ($user->status == 0) ? 1 : 0;
        //             $user->save();
        //             $data['status'] = 1;
        //         }
        //         return $data;
        //     }
        // }catch(Exception $e) {
        //     abort(500);
        // }
    }

    
}
