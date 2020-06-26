<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\centro_costo;

class centrocostoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    
    public function store(Request $request){
        $centro_costo=new centro_costo();
        $centro_costo->centroC_descripcion=$request->get('centroC_descripcion');
        $centro_costo->save();
        return $centro_costo;
    }
}
