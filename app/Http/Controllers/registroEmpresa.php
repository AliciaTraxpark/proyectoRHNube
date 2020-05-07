<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\organizacion;

class registroEmpresa extends Controller
{
    public function index(){
        return view('registro.registroEmpresa');
    }

    public function registrarDatos(Request $request){
        organizacion::insert($request->except(["_token"]));
    }

    public function create(Request $request){
        $organizacion=new organizacion();
        $organizacion->organi_ruc= $request->get('');
        $organizacion->organi_razonSocial= $request->get('');
        $organizacion->organi_direccion= $request->get('');
        $organizacion->organi_departamento= $request->get('');
        $organizacion->organi_provincia= $request->get('');
        $organizacion->organi_distrito= $request->get('');
        $organizacion->organi_pagWeb= $request->get('');
        $organizacion->organi_tipo= $request->get('');
        $organizacion->save();
    }
}
