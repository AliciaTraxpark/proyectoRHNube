<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrecioPlanesController extends Controller
{
    public function vistaPrecios()
    {
        return view('precios.precios');
    }
}
