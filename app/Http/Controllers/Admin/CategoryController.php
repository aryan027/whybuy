<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->ajax()){
                $category = $this->category->orderBy('id','DESC')->get();
                $data['status'] = 1;
                $data['categoryData'] = View('admin.category.data',compact('category'))->render();
                return $data;
            }
            return view('admin.category.index');
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
            return view('admin.category.create');
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
                'name' => 'required|unique:categories,name',
                'status' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
            $category = $this->category;
            $category->name = $request->name;
            $category->status = $request->status;
            $category->save();
            return redirect(route('category.index'))->with('success','Category added Successfully');
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
            $getCategory = $this->category->where('id',$id)->first();
            if(!empty($getCategory)){
                return view('admin.category.edit',compact('getCategory'));
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
                'name' => 'required|unique:categories,name,'.$id,
                'status' => 'required',
            ];
            $valid = Validator::make($request->all(),$rule);
            if($valid->fails()){
                return redirect()->back()->withErrors($valid->errors())->withInput();
            }
        
            $category = $this->category->where('id',$id)->first();
            if(!empty($category)){
                $category->name = $request->name;
                $category->status = $request->status;
                $category->save();
                return redirect(route('category.index'))->with('success','Category Updated Successfully');
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
                $category = $this->category->where('id',$categoryId)->first();
                $data['status'] = 0;
                if(!empty($category)){
                    if($this->category::checkSubCategoryOrNot($category)){
                        session()->flash("warning","This category provide by multiple sub category. So you can't delete this category.");
                        $data['status'] = 2;
                    }
                    else{
                        $category->delete();
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
