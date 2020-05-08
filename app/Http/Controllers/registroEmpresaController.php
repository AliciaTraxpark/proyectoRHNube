<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\organizacion;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_districts;

class registroEmpresaController extends Controller
{
    public function provincias($id){
       return ubigeo_peru_provinces::where('department_id',$id)->get();
    }
    public function index(){
        $departamento=ubigeo_peru_departments::all();
        $provincia=ubigeo_peru_provinces::all();
        $distrito=ubigeo_peru_districts::all();
        return view('registro.registroEmpresa',['departamento'=>$departamento,'provincia'=>$provincia,'distrito'=>$distrito]);
    }

    public function registrarDatos(Request $request){
        organizacion::insert($request->except(["_token"]));
    }

    public function create(Request $request){
        $organizacion=new organizacion();
        $organizacion->organi_ruc= $request->get('ruc');
        $organizacion->organi_razonSocial= $request->get('razonSocial');
        $organizacion->organi_direccion= $request->get('direccion');
        $organizacion->organi_departamento= $request->get('departamento');
        $organizacion->organi_provincia= $request->get('provincia');
        $organizacion->organi_distrito= $request->get('distrito');
        $organizacion->organi_nempleados= $request->get('nempleados');
        $organizacion->organi_pagWeb= $request->get('pagweb');
        $organizacion->organi_tipo= $request->get('tipo');
        $organizacion->save();
    }
}
