<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Auth;
use App\Models\Category;
use App\Models\SubCategory;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $category = Category::count();
        $subCategory = SubCategory::count();
        return view('admin.home.dashboard',compact('category','subCategory'));
    }
    public function login()
    {
        if (Auth::check()){
            return redirect('admin/dashboard');
        }
        return view('auth.login');
    }
}
