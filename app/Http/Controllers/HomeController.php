<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DbOperation;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application home page
     */
    public function index()
    {
        return view('home');
    }

    /**
     * The user home page after log in.
     */
    public function goToUserHome(){
        $recentTasks = DbOperation::GetAllJobsForBrowsing(1, 7);
        $mCategories = DbOperation::GetCategories();
        //dd($recentTasks);
        return view('user_home', compact('mCategories','recentTasks'));
    }
    
    //go to privacy policy page
    public function goToPrivacyPolicy()
    {
        return view('privacy_policy');
    }
}
