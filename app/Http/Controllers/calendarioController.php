<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class calendarioController extends Controller
{
    //
    public function index(){
        if (Auth::check()) {
            return view ('calendario.calendario');
        }
        else{
            return redirect(route('principal'));
        }



    }
}
