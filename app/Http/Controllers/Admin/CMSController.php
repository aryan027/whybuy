<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;

class CMSController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->ajax()){
                $cms = $this->cms->orderBy('id','DESC')->get();
                $data['status'] = 1;
                $data['cmsData'] = View('admin.cms.data',compact('cms'))->render();
                return $data;
            }
            return view('admin.cms.index');
        }catch(Exception $e) {
            abort(500);
        } 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rule = [
                'type' => 'required|unique:cms,type',
                'description' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
            $cms = $this->cms;
            $cms->type = $request->type;
            $cms->description = $request->description;
            $cms->save();
            return redirect(route('cms.index'))->with('success','CMS added successfully');
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
            $cms = $this->cms->where('id',$id)->first();
            if(!empty($cms)){
                return view('admin.cms.edit',compact('cms'));
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
            $cms = $this->cms->where('id',$id)->first();
            if(!empty($cms)){
                $rule = [
                    'type' => 'required|unique:cms,type,'.$id,
                    'description' => 'required',
                ];
                $valid = Validator::make($request->all(),$rule);
                if($valid->fails()){
                    return redirect()->back()->withErrors($valid->errors())->withInput();
                }

                $cms->type = $request->type;
                $cms->description = $request->description;
                $cms->save();
                return redirect(route('cms.index'))->with('success','CMS updated successfully');
            }
            return redirect()->back()->with('error','Something went to wrong!');
        }catch(Exception $e) {
            abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
