<?php

namespace App\Http\Controllers;

use App\area;
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
}
