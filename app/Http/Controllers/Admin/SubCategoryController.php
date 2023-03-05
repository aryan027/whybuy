<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->ajax()){
                $subCategory = $this->subCategory->orderBy('id','DESC')->get();
                $data['status'] = 1;
                $data['subCategoryData'] = View('admin.sub-category.data',compact('subCategory'))->render();
                return $data;
            }
            return view('admin.sub-category.index');
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
            $category = $this->category->where('status',$this->category::IS_ACTIVE)->get();
            return view('admin.sub-category.create',compact('category'));
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
                'name' => 'required|unique:sub_categories,name',
                'category_id' => 'required',
                'status' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
            $subCategory = $this->subCategory;
            $subCategory->name = $request->name;
            $subCategory->category_id = $request->category_id;
            $subCategory->status = $request->status;
            $subCategory->save();
            return redirect(route('sub-category.index'))->with('success','SubCategory added Successfully');
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
            $subCategory = $this->subCategory->where('id',$id)->first();
            if(!empty($subCategory)){
                $category = $this->category->get();
                return view('admin.sub-category.edit',compact('subCategory','category'));
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
                'name' => 'required|unique:sub_categories,name,'.$id,
                'category_id' => 'required',
                'status' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
        
            $subCategory = $this->subCategory->where('id',$id)->first();
            if(!empty($subCategory)){
                $subCategory->name = $request->name;
                $subCategory->category_id = $request->category_id;
                $subCategory->status = $request->status;
                $subCategory->save();
                return redirect(route('sub-category.index'))->with('success','Category Updated Successfully');
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
                $subCategory = $this->subCategory->where('id',$categoryId)->first();
                $data['status'] = 0;
                if(!empty($subCategory)){
                    if($this->subCategory::checkChildCategoryOrNot($subCategory)){
                        session()->flash("warning","This sub category provide by multiple child category. So you can't delete this sub category.");
                        $data['status'] = 2;
                    }
                    else{
                        $subCategory->delete();
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
