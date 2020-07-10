<?php

namespace App\Http\Controllers;

header("Refresh:7200");

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\eventos;
use App\calendario;

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
    public function index()
    {

        //$calendario=calendario::all();
        $calendario = calendario::where('users_id', '=', Auth::user()->id)->get();
        //dd($calendario);
        if ($calendario->first()) {
            $variable = 1;
        } else {
            $variable = 0;
        }

        return view('dashboard', ['variable' => $variable]);
    }
}
