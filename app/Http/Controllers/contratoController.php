<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\tipo_contrato;

class contratoController extends Controller
{
    public function store(Request $request){
        $tipoC=new tipo_contrato();
        $tipoC->contrato_descripcion=$request->get('contrato_descripcion');
        $tipoC->contrato_fechaI=$request->get('contrato_fechaI');
        $tipoC->contrato_fechaF=$request->get('contrato_fechaF');
        $tipoC->save();
        return  $tipoC;
    }
}
