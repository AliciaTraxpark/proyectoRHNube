<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class dispositivosController extends Controller
{
    //
    public function index(){
        return view('Dispositivos.dispositivos');
    }
}
