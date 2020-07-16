<?php

namespace App\Http\Controllers;

use App\area;
use App\cargo;
use Illuminate\Http\Request;

class editarAtributosController extends Controller
{
    public function area()
    {
        $area = area::all();
        return response()->json($area, 200);
    }
    public function buscarArea(Request $request)
    {
        $area = area::where('area_id', '=', $request->get('id'))->get()->first();
        if ($area) {
            return response()->json($area->area_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarArea(Request $request)
    {
        $area = area::where('area_id', '=', $request->get('id'))->get()->first();
        if ($area) {
            $area->area_descripcion = $request->get('objArea')['area_descripcion'];
            $area->save();
            return response()->json($area, 200);
        }
    }

    public function cargo()
    {
        $cargo = cargo::all();
        return response()->json($cargo, 200);
    }

    public function buscarCargo(Request $request)
    {
        $cargo = cargo::where('cargo_id', '=', $request->get('id'))->get()->first();
        if ($cargo) {
            return response()->json($cargo->cargo_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarCargo(Request $request)
    {
        $cargo = cargo::where('cargo_id', '=', $request->get('id'))->get()->first();
        if ($cargo) {
            $cargo->cargo_descripcion = $request->get('objCargo')['cargo_descripcion'];
            $cargo->save();
            return response()->json($cargo, 200);
        }
    }
}
