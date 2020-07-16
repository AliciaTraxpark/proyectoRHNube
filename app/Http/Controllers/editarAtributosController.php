<?php

namespace App\Http\Controllers;

use App\area;
use App\cargo;
use App\centro_costo;
use App\local;
use App\nivel;
use App\tipo_contrato;
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

    public function centro()
    {
        $centro = centro_costo::all();
        return response()->json($centro, 200);
    }

    public function buscarCentro(Request $request)
    {
        $centro = centro_costo::where('centroC_id', '=', $request->get('id'))->get()->first();
        if ($centro) {
            return response()->json($centro->centroC_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarCentro(Request $request)
    {
        $centro = centro_costo::where('centroC_id', '=', $request->get('id'))->get()->first();
        if ($centro) {
            $centro->centroC_descripcion = $request->get('objCentroC')['centroC_descripcion'];
            $centro->save();
            return response()->json($centro, 200);
        }
    }

    public function local()
    {
        $local = local::all();
        return response()->json($local, 200);
    }

    public function buscarLocal(Request $request)
    {
        $local = local::where('local_id', '=', $request->get('id'))->get()->first();
        if ($local) {
            return response()->json($local->local_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarLocal(Request $request)
    {
        $local = local::where('local_id', '=', $request->get('id'))->get()->first();
        if ($local) {
            $local->local_descripcion = $request->get('objLocal')['local_descripcion'];
            $local->save();
            return response()->json($local, 200);
        }
    }

    public function nivel()
    {
        $nivel = nivel::all();
        return response()->json($nivel, 200);
    }

    public function buscarNivel(Request $request)
    {
        $nivel = nivel::where('nivel_id', '=', $request->get('id'))->get()->first();
        if ($nivel) {
            return response()->json($nivel->nivel_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarNivel(Request $request)
    {
        $nivel = nivel::where('nivel_id', '=', $request->get('id'))->get()->first();
        if ($nivel) {
            $nivel->nivel_descripcion = $request->get('objNivel')['nivel_descripcion'];
            $nivel->save();
            return response()->json($nivel, 200);
        }
    }

    public function contrato()
    {
        $contrato = tipo_contrato::all();
        return response()->json($contrato, 200);
    }

    public function buscarContrato(Request $request)
    {
        $contrato = tipo_contrato::where('contrato_id', '=', $request->get('id'))->get()->first();
        if ($contrato) {
            return response()->json($contrato->contrato_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarContrato(Request $request)
    {
        $contrato = tipo_contrato::where('contrato_id', '=', $request->get('id'))->get()->first();
        if ($contrato) {
            $contrato->contrato_descripcion = $request->get('objContrato')['contrato_descripcion'];
            $contrato->save();
            return response()->json($contrato, 200);
        }
    }
}
