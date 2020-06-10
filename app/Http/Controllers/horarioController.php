<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class horarioController extends Controller
{
    //
    public function index(){
        return view('horarios.horarios');
    }
}
