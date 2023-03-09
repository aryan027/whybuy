<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->ajax()){
                $categories = $this->countries->orderBy('id','DESC')->get();
                $data['status'] = 1;
                $data['categoriesData'] = View('admin.country.data',compact('categories'))->render();
                return $data;
            }
            return view('admin.country.index');
        }catch(Exception $e) {
            abort(500);
        } 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.country.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rule = [
                'name' => 'required|unique:countries,name',
                'short_name' => 'required',
                'status' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
            $countries = $this->countries;
            $countries->name = $request->name;
            $countries->short_name = $request->short_name;
            $countries->status = $request->status;
            $countries->save();
            return redirect(route('country.index'))->with('success','Country added successfully');
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
            $countries = $this->countries->where('id',$id)->first();
            if(!empty($countries)){
                return view('admin.country.edit',compact('countries'));
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
        $id = decrypt($id);
        try {
            $rule = [
                'name' => 'required|unique:countries,name,'.$id,
                'short_name' => 'required',
                'status' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
            $countries = $this->countries->where('id',$id)->first();
            if(!empty($countries)){
                $countries->name = $request->name;
                $countries->short_name = $request->short_name;
                $countries->status = $request->status;
                $countries->save();
                return redirect(route('country.index'))->with('success','Country updated successfully');
            }
            return redirect()->back()->with('error','Something went to wrong!');
        }catch(Exception $e) {
            abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            if($request->ajax()){
                $countryId = decrypt($id);
                $countries = $this->countries->where('id',$countryId)->first();
                $data['status'] = 0;
                if(!empty($countries)){
                    if($this->countries::checkAddressesOrNot($countries)){
                        session()->flash("warning","This countries provide by multiple addresses. So you can't delete this addresses.");
                        $data['status'] = 2;
                    }
                    else{
                        $countries->delete();
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
