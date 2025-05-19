<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */

    //  public function __invoke()
    //  {
    //      return view('admin.dashboard'); 
    //  }

    public function index()
    {
        return view('admin.dashboard');
    }

    public function notifications()
    {
        $notifications = Notification::where('user_id', 1)->orderBy('id','desc')->get();
        return view('admin.notifications',compact('notifications'));
    }
}
