<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->ajax()){
                $package = $this->package ->orderBy('id','DESC')->get();
                $data['status'] = 1;
                $data['packageData'] = View('admin.package.data',compact('package'))->render();
                return $data;
            }
            return view('admin.package.index');
        }catch(Exception $e) {
            abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.package.create');
        }catch(Exception $e) {
            abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rule = [
                'package_price' => 'required',
                'package_price' => 'required',
                'no_of_ads' => 'required',
                'status' => 'required',
                'durations' => 'required',
                'type' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
            $package = $this->package;
            $package->package_name = $request->package_name;
            $package->package_price = $request->package_price;
            $package->no_of_ads = $request->no_of_ads;
            $package->status = $request->status;
            $package->durations = $request->durations;
            $package->description = $request->description;
            $package->type = $request->type;
            $package->save();
            return redirect(route('package.index'))->with('success','Package added Successfully');
        }catch(Exception $e) {
            abort(500);
        }
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
        try {
            $id = decrypt($id);
            $package = $this->package->where('id',$id)->first();
            if(!empty($package)){
                return view('admin.package.edit',compact('package'));
            }
            return redirect()->back()->with('error','Something went to wrong!');
        }catch(Exception $e) {
            abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $id = decrypt($id);
            $rule = [
                'package_price' => 'required',
                'package_price' => 'required',
                'no_of_ads' => 'required',
                'status' => 'required',
                'durations' => 'required',
                'type' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
            $package = $this->package->where('id',$id)->first();
            if(!empty($package)){
                $package->package_name = $request->package_name;
                $package->package_price = $request->package_price;
                $package->no_of_ads = $request->no_of_ads;
                $package->status = $request->status;
                $package->durations = $request->durations;
                $package->description = $request->description;
                $package->type = $request->type;
                $package->save();
                return redirect(route('package.index'))->with('success','Package updated successfully');
            }
            return redirect()->back()->with('error','Something went to wrong!');
        }catch(Exception $e) {
            abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        try {
            if($request->ajax()){
                $packageId = decrypt($id);
                $package = $this->package->where('id',$packageId)->first();
                $data['status'] = 0;
                if(!empty($package)){
                    if($this->package::checkPackageOrNot($package)){
                        session()->flash("warning","This package provide by multiple users. So you can't delete this package.");
                        $data['status'] = 2;
                    }
                    else{
                        $package->delete();
                        $data['status'] = 1;
                    }
                }
                return $data;
            }
        }catch(Exception $e) {
            abort(500);
        }
    }
}
