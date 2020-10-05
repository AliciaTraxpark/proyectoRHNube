<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrecioPlanesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function vistaPrecios()
    {
        return view('precios.precios');
    }
}
