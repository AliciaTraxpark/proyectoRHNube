<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class incidenciasController extends Controller
{
    //
    public function index(){
        return view('incidencias.incidencias');
    }
}
