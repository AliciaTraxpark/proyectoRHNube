<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\cargo;

class cargoController extends Controller
{
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function store(Request $request){
        $cargo=new cargo();
        $cargo->cargo_descripcion=$request->get('cargo_descripcion');
        $cargo->save();
        return $cargo;

    }
}
