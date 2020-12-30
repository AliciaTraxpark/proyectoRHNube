<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PuntosControlController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            return view("puntosControl.puntoControl");
        }
    }

    public function puntosControlOrganizacion()
    {

        //* DATOS PARA  TABLA DE PUNTOS DE ORGANIZACION
        $puntosC = DB::table('punto_control as pc')
            ->select(
                'pc.id',
                'pc.descripcion',
                'pc.controlRuta',
                'pc.asistenciaPuerta',
                DB::raw("CASE WHEN(pc.codigoControl) IS NULL THEN 'No definido' ELSE pc.codigoControl END AS codigoP"),
            )
            ->where('pc.organi_id', '=', session('sesionidorg'))
            ->where('pc.estado', '=', 1)
            ->groupBy('pc.id')
            ->get();

        return response()->json($puntosC, 200);
    }
}
