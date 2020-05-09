<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\eventos;
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

        $eventos=eventos::all();
        //$idevento=$eventos->id;
        if ($eventos->first()) {
            $variable=1;
         }


         else{
            $variable=0;
        }
       
        return view('dashboard',['variable'=>$variable]);



}
}
