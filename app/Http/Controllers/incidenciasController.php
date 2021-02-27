<?php

namespace App\Http\Controllers;

use App\incidencias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class incidenciasController extends Controller
{
    //
    public function index(){
        $tipo_incidencia=DB::table('tipo_incidencia')
        ->where('organi_id','=',session('sesionidorg'))
        ->where('sistema','=',0)
        ->get();

        return view('incidencias.incidencias',['tipo_incidencia'=> $tipo_incidencia]);
    }

    public function verificaCodIncidencia(Request $request){

        //*PARAMETROS
        $tipoIncidencia=$request->tipoIncidencia;
        $codigoIncidencia=$request->codigoIncidencia;

         //*OBTENER ID DE TIPO_INCIDENCIA FERIADO
         $tipoFeriado=DB::table('tipo_incidencia')
         ->where('tipoInc_descripcion','=','Feriado')
         ->where('organi_id','=', session('sesionidorg'))
         ->get()->first();

         //*SI NO ES TIPO FERIADO
         if($tipoIncidencia!=$tipoFeriado->idtipo_incidencia){
            $incidencias=DB::table('incidencias')
            ->where('inciden_codigo','=',$codigoIncidencia)
            ->whereNotNull('inciden_codigo')
            ->where('organi_id','=', session('sesionidorg'))
            ->get();

            if($incidencias->isNotEmpty()){
                //*HAY REPETICION
                return 1;
            } else{
                //*NO HAY RPETICCION
                return 0;
            }

         } else{

            $incidencias=DB::table('incidencias')
            ->where('inciden_codigo','=',$codigoIncidencia)
            ->where('idtipo_incidencia','!=',$tipoFeriado->idtipo_incidencia)
            ->whereNotNull('inciden_codigo')
            ->where('organi_id','=', session('sesionidorg'))
            ->get();

             if($incidencias->isNotEmpty()){
                //*HAY REPETICION
                return 1;
             } else{
                 //*NO HAY RPETICCION
                 return 0;
             }

         }


    }

    public function registIncidencia(Request $request){

        //*OBTENER PARAMENTROS
        $tipoIncidencia=$request->tipoIncidencia;
        $descripIncidencia=$request->descripIncidencia;
        $codigoIncidencia=$request->codigoIncidencia;
        $pagoIncidencia=$request->pagoIncidencia;

        //*REGISTRAR INCIDENCIA
        $incidencias=new incidencias();
        $incidencias->idtipo_incidencia= $tipoIncidencia;
        $incidencias->inciden_codigo= $codigoIncidencia;
        $incidencias->inciden_descripcion= $descripIncidencia;
        $incidencias->inciden_pagado= $pagoIncidencia;
        $incidencias->users_id=Auth::user()->id;
        $incidencias->organi_id=session('sesionidorg');
        $incidencias->estado=1;
        $incidencias->sistema=0;
        $incidencias->save();

    }

    public function tablaIncidencias(Request $request){

        $incidencias=DB::table('incidencias as in')
        ->leftJoin('tipo_incidencia as tipI','in.idtipo_incidencia','=','tipI.idtipo_incidencia')
        ->select('in.inciden_id','in.idtipo_incidencia','tipI.tipoInc_descripcion','in.inciden_codigo',
        'in.inciden_descripcion','in.inciden_pagado','in.estado','in.sistema')
        ->where('in.organi_id','=',session('sesionidorg'))
        ->get();

        foreach($incidencias as $incidencia){
            $incidencias_dias = DB::table('incidencia_dias as id')
                ->join('incidencias as i', 'i.inciden_id', '=', 'id.id_incidencia')
                ->where('id.id_incidencia', '=', $incidencia->inciden_id)
                ->get();

                if($incidencias_dias->isNotEmpty()){
                    $incidencia->uso=1;
                }
                else{
                    $incidencia->uso=0;
                }
        }
        return json_encode($incidencias);
    }

    public function dataIncidencia(Request $request){

        $idIncidencia=$request->idIncidencia;

        $incidencia=DB::table('incidencias as in')
        ->leftJoin('tipo_incidencia as tipI','in.idtipo_incidencia','=','tipI.idtipo_incidencia')
        ->select('in.inciden_id','in.idtipo_incidencia','tipI.tipoInc_descripcion','in.inciden_codigo',
        'in.inciden_descripcion','in.inciden_pagado','in.estado','in.sistema')
        ->where('in.organi_id','=',session('sesionidorg'))
        ->where('in.inciden_id','=',$idIncidencia)
        ->get();


        $incidencias_dias = DB::table('incidencia_dias as id')
            ->join('incidencias as i', 'i.inciden_id', '=', 'id.id_incidencia')
            ->where('id.id_incidencia', '=', $incidencia[0]->inciden_id)
            ->get();

            if($incidencias_dias->isNotEmpty()){
                $incidencia->uso=1;
            }
            else{
                $incidencia->uso=0;
            }

        return ($incidencia);

    }
}
