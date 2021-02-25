<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class incidenciasController extends Controller
{
    //
    public function index(){
        $tipo_incidencia=DB::table('tipo_incidencia')
        ->where('organi_id','=',session('sesionidorg'))->get();

        return view('incidencias.incidencias',['tipo_incidencia'=> $tipo_incidencia]);
    }
}
