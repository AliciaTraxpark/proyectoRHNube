<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\eventos_calendario;
use App\incidencias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventosUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        //Verificamos el tipo, si es 1 se registra nuevo, si es cero solo se relaciona con su id
        if( $request->get('tipoFeri')==1){

            //obtener tipo de incidencia
            $tipo_incidencia=DB::table('tipo_incidencia')
            ->where('organi_id','=',session('sesionidorg'))
            ->where('tipoInc_descripcion','=','Feriado')
            ->get()->first();

            $incidencias=new incidencias();
            $incidencias->idtipo_incidencia= $tipo_incidencia->idtipo_incidencia;
            $incidencias->inciden_codigo= $request->get('codigoFeriado');
            $incidencias->inciden_descripcion= $request->get('title');
            $incidencias->inciden_pagado= $request->get('pagadoFeriado');
            $incidencias->users_id=Auth::user()->id;
            $incidencias->organi_id=session('sesionidorg');
            $incidencias->estado=1;
            $incidencias->sistema=0;
            $incidencias->save();

            $idIncidencia=$incidencias->inciden_id;

        } else{
            $idIncidencia=$request->get('idFeriadoInc');
        }
        $eventos_calendario=new eventos_calendario();
        $eventos_calendario->color= $request->get('color');
        $eventos_calendario->textColor= $request->get('textColor');
        $eventos_calendario->start= $request->get('start');
        $eventos_calendario->end= $request->get('end');
        $eventos_calendario->id_calendario= $request->get('id_calendario');
        $eventos_calendario->users_id=Auth::user()->id;
        $eventos_calendario->organi_id=session('sesionidorg');
        $eventos_calendario->laborable=$request->get('laborable');
        $eventos_calendario->inciden_id=$idIncidencia;
        $eventos_calendario->save();

        return $eventos_calendario->id;
    }

    public function storeDescanso(Request $request)
    {
        //
        //Verificamos el tipo, si es 1 se registra nuevo, si es cero solo se relaciona con su id
        if( $request->get('tipoDes')==1){

            //obtener tipo de incidencia
            $tipo_incidencia=DB::table('tipo_incidencia')
            ->where('organi_id','=',session('sesionidorg'))
            ->where('tipoInc_descripcion','=','Descanso')
            ->get()->first();

            $incidencias=new incidencias();
            $incidencias->idtipo_incidencia= $tipo_incidencia->idtipo_incidencia;
            $incidencias->inciden_codigo= $request->get('codigoDescanso');
            $incidencias->inciden_descripcion= $request->get('title');
            $incidencias->inciden_pagado= $request->get('pagadoDescanso');
            $incidencias->users_id=Auth::user()->id;
            $incidencias->organi_id=session('sesionidorg');
            $incidencias->estado=1;
            $incidencias->sistema=0;
            $incidencias->save();

            $idIncidencia=$incidencias->inciden_id;

        } else{
            $idIncidencia=$request->get('idDescanoInc');
        }
        $eventos_calendario=new eventos_calendario();
        $eventos_calendario->color= $request->get('color');
        $eventos_calendario->textColor= $request->get('textColor');
        $eventos_calendario->start= $request->get('start');
        $eventos_calendario->end= $request->get('end');
        $eventos_calendario->id_calendario= $request->get('id_calendario');
        $eventos_calendario->users_id=Auth::user()->id;
        $eventos_calendario->organi_id=session('sesionidorg');
        $eventos_calendario->laborable=$request->get('laborable');
        $eventos_calendario->inciden_id=$idIncidencia;
        $eventos_calendario->save();

        return $eventos_calendario->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\eventos_calendario  $eventos_calendario
     * @return \Illuminate\Http\Response
     */
    public function show(eventos_calendario $eventos_calendario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\eventos_calendario  $eventos_calendario
     * @return \Illuminate\Http\Response
     */
    public function edit(eventos_calendario $eventos_calendario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\eventos_calendario  $eventos_calendario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, eventos_calendario $eventos_calendario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\eventos_calendario  $eventos_calendario
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $eventos_calendario=eventos_calendario::findOrFail($id);
        eventos_calendario::destroy($id);
        return response()->json($id);
    }
}
