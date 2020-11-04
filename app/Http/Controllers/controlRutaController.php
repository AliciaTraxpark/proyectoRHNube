<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class controlRutaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        return view('ruta.rutaDiaria');
    }

    public function show(Request $request)
    {
        function controlRJson($array)
        {
            $resultado = array();

            foreach ($array as $ubicacion) {
                $horaUbicacion = explode(":", $ubicacion->hora);
                if (!isset($resultado[$horaUbicacion[0]])) {
                    $resultado[$horaUbicacion[0]] = array("horaUbicacion" => $horaUbicacion[0], "minutos" => array());
                }
                if (!isset($resultado[$horaUbicacion[0]]["minutos"][$horaUbicacion[1][0]])) {
                    $resultado[$horaUbicacion[0]]["minutos"][$horaUbicacion[1][0]] = array();
                }
                array_push($resultado[$horaUbicacion[0]]["minutos"][$horaUbicacion[1][0]], $ubicacion);
            }

            return array_values($resultado);
        }

        $idempleado = $request->get('value');
        $fecha = $request->get('fecha');
        $control_ruta = DB::table('empleado as e')
            ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
            ->join('actividad as a', 'a.Activi_id', '=', 'u.idActividad')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'u.idHorario_dias')
            ->select(
                DB::raw('IF(hd.id is null, DATE(u.hora_ini), DATE(hd.start))'),
                'a.Activi_Nombre',
                'u.id as idUbicacion',
                DB::raw('DATE(u.hora_ini) as fecha'),
                DB::raw('TIME(u.hora_ini) as hora'),
                DB::raw('TIME(u.hora_ini) as hora_ini'),
                DB::raw('TIME(u.hora_fin) as hora_fin')
            )
            ->where(DB::raw('IF(hd.id is null, DATE(u.hora_ini), DATE(hd.start))'), '=', $fecha)
            ->where('e.emple_id', '=', $idempleado)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->orderBy('u.hora_ini', 'asc')
            ->get();

        foreach ($control_ruta as $cr) {
            $ubicaciones = DB::table('ubicacion_ruta as ur')
                ->select(
                    DB::raw('ST_AsText(ur.ubicacion_ini) as ubi_ini'),
                    DB::raw('ST_AsText(ur.ubicacion_fin) as ubi_fin')
                )
                ->where('ur.idUbicacion', '=', $cr->idUbicacion)
                ->get();
            $datos = [];
            foreach ($ubicaciones as $u) {
                array_push($datos, $u);
            }
            $cr->ubicaciones = $datos;
        }
        $control_ruta = controlRJson($control_ruta);

        return response()->json($control_ruta, 200);
    }
}
