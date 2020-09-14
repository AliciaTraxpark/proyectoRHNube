<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class soportesPorCorreoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function soporte()
    {
        return view('correosA.soporte');
    }
}
