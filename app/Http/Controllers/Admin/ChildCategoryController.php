<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;

class ChildCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->ajax()){
                $childCategory = $this->childCategory->orderBy('id','DESC')->get();
                $data['status'] = 1;
                $data['childCategoryData'] = View('admin.child-category.data',compact('childCategory'))->render();
                return $data;
            }
            return view('admin.child-category.index');
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
            $subCategory = $this->subCategory->where('status',$this->subCategory::IS_ACTIVE)->get();
            return view('admin.child-category.create',compact('subCategory'));
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
                'name' => 'required|unique:child_categories,name',
                'sub_category_id' => 'required',
                'status' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }

            $subCategory = $this->subCategory->where('id',$request->sub_category_id)->first();
            $categoryId = 0;
            if(!empty($subCategory)){
                $categoryId = $subCategory->category_id;
            }

            $childCategory = $this->childCategory;
            $childCategory->name = $request->name;
            $childCategory->category_id = $categoryId;
            $childCategory->sub_category_id = $request->sub_category_id;
            $childCategory->status = $request->status;
            $childCategory->save();
            return redirect(route('child-category.index'))->with('success','Child Category added Successfully');
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
    public function edit(Request $request, $id)
    {
        try {
            $id = decrypt($id);
            $childCategory = $this->childCategory->where('id',$id)->first();
            if(!empty($childCategory)){
                $subCategory = $this->subCategory->get();
                return view('admin.child-category.edit',compact('childCategory','subCategory'));
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
                'name' => 'required|unique:child_categories,name,'.$id,
                'sub_category_id' => 'required',
                'status' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
        
            $childCategory = $this->childCategory->where('id',$id)->first();
            if(!empty($childCategory)){

                $subCategory = $this->subCategory->where('id',$request->sub_category_id)->first();
                $categoryId = 0;
                if(!empty($subCategory)){
                    $categoryId = $subCategory->category_id;
                }
                $childCategory->name = $request->name;
                $childCategory->category_id = $categoryId;
                $childCategory->sub_category_id = $request->sub_category_id;
                $childCategory->status = $request->status;
                $childCategory->save();
                return redirect(route('child-category.index'))->with('success','Child Category Updated Successfully');
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
                $categoryId = decrypt($id);
                $childCategory = $this->childCategory->where('id',$categoryId)->first();
                $data['status'] = 0;
                if(!empty($childCategory)){
                    $childCategory->delete();
                    $data['status'] = 1;
                }
                return $data;
            }
        }catch(Exception $e) {
            abort(500);
        } 
    }
}
