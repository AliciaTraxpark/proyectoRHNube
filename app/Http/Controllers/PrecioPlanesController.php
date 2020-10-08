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
        if(session('sesionidorg')==null || session('sesionidorg')=='null' ){
            return redirect('/elegirorganizacion');
        } else{
        return view('precios.precios');}
    }
}
