<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function area()
    {
        $area = DB::table('empleado as e')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->select('a.area_descripcion', DB::raw('COUNT(a.area_descripcion) as Total'))
            ->groupBy('a.area_id')
            ->get();

        return response()->json($area, 200);
    }

    public function nivel()
    {
        $nivel = DB::table('empleado as e')
            ->join('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
            ->select('n.nivel_descripcion', DB::raw('COUNT(n.nivel_descripcion) as Total'))
            ->groupBy('n.nivel_id')
            ->get();

        return response()->json($nivel, 200);
    }

    public function contrato()
    {
        $contrato = DB::table('empleado as e')
            ->join('tipo_contrato as c', 'e.emple_tipoContrato', '=', 'c.contrato_id')
            ->select('c.contrato_descripcion', DB::raw('COUNT(c.contrato_descripcion) as Total'))
            ->groupBy('c.contrato_id')
            ->get();

        return response()->json($contrato, 200);
    }
}
