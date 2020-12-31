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

    //* DATOS PARA  TABLA DE PUNTOS DE ORGANIZACION
    public function puntosControlOrganizacion()
    {
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

    // * DATOS PARA UN PUNTO DE CONTROL
    public function puntoDeControl(Request $request)
    {
        //* FUNCION PARA AGRUPAR GEOLICALIZACION
        function agruparGeoEnPuntos($array)
        {
            $resultado = array();

            foreach ($array as $punto) {
                if (!isset($resultado[$punto->id])) {
                    $resultado[$punto->id] = (object) array(
                        "id" => $punto->id,
                        "descripcion" => $punto->descripcion,
                        "controlRuta" => $punto->controlRuta,
                        "asistenciaPuerta" => $punto->asistenciaPuerta,
                        "codigoControl" => $punto->codigoControl,
                        "porEmpleados" => $punto->porEmpleados,
                        "porAreas" => $punto->porAreas
                    );
                }
                if (!isset($resultado[$punto->id]->geo)) {
                    $resultado[$punto->id]->geo = array();
                }
                $arrayGeo = array(
                    "idGeo" => $punto->idGeo,
                    "latitud" => $punto->latitud,
                    "longitud" => $punto->longitud,
                    "radio" => $punto->radio
                );
                array_push($resultado[$punto->id]->geo, $arrayGeo);
            }
            return array_values($resultado);
        }

        $idPunto = $request->get('idPunto');

        $puntoC = DB::table('punto_control as pc')
            ->join('punto_control_geo as pcg', 'pcg.idPuntoControl', '=', 'pc.id')
            ->select(
                'pc.id',
                'pc.descripcion',
                'pc.controlRuta',
                'pc.asistenciaPuerta',
                'pc.codigoControl',
                'pc.porEmpleados',
                'pc.porAreas',
                'pcg.id as idGeo',
                'pcg.latitud',
                'pcg.longitud',
                'pcg.radio'
            )
            ->where('pc.organi_id', '=', session('sesionidorg'))
            ->where('pc.id', '=', $idPunto)
            ->get();

        $puntoC = agruparGeoEnPuntos($puntoC);

        return response()->json($puntoC, 200);
    }
}
