<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function area()
    {
        $datos = [];
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->get();

        $area = DB::table('empleado as e')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->select('a.area_descripcion', DB::raw('COUNT(a.area_descripcion) as Total'))
            ->groupBy('a.area_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "area" => $area));
        return response()->json($datos, 200);
    }

    public function nivel()
    {
        $datos = [];
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->get();

        $nivel = DB::table('empleado as e')
            ->join('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
            ->select('n.nivel_descripcion', DB::raw('COUNT(n.nivel_descripcion) as Total'))
            ->groupBy('n.nivel_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "nivel" => $nivel));
        return response()->json($datos, 200);
    }

    public function contrato()
    {
        $datos = [];
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->get();

        $contrato = DB::table('empleado as e')
            ->join('tipo_contrato as c', 'e.emple_tipoContrato', '=', 'c.contrato_id')
            ->select('c.contrato_descripcion', DB::raw('COUNT(c.contrato_descripcion) as Total'))
            ->groupBy('c.contrato_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "contrato" => $contrato));
        return response()->json($datos, 200);
    }

    public function centro()
    {
        $datos = [];
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->get();

        $centro = DB::table('empleado as e')
            ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select('cc.centroC_descripcion', DB::raw('COUNT(cc.centroC_descripcion) as Total'))
            ->groupBy('cc.centroC_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "centro" => $centro));
        return response()->json($datos, 200);
    }

    public function local()
    {
        $datos = [];
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->get();

        $local = DB::table('empleado as e')
            ->join('local as l', 'e.emple_local', '=', 'l.local_id')
            ->select('l.local_descripcion', DB::raw('COUNT(l.local_descripcion) as Total'))
            ->groupBy('l.local_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "local" => $local));
        return response()->json($datos, 200);
    }

    public function edad()
    {
        $datos = [];
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->get();

        $edad = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select(DB::raw('YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) as edad'), DB::raw('COUNT(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento)) as total'))
            ->groupBy('edad')
            ->get();

        array_push($datos, array("empleado" => $empleado, "edad" => $edad));
        return response()->json($datos, 200);
    }
}
