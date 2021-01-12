<?php

namespace App\Http\Controllers;

use App\area;
use App\cargo;
use App\centro_costo;
use App\condicion_pago;
use App\local;
use App\nivel;
use App\tipo_contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class editarAtributosController extends Controller
{
    public function area()
    {
        $area = area::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($area, 200);
    }
    public function buscarArea(Request $request)
    {
        $area = area::where('area_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($area) {
            return response()->json($area->area_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarArea(Request $request)
    {
        $area = area::where('area_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($area) {
            $area->area_descripcion = $request->get('objArea')['area_descripcion'];
            $area->save();
            return response()->json($area, 200);
        }
    }

    public function cargo()
    {
        $cargo = cargo::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($cargo, 200);
    }

    public function buscarCargo(Request $request)
    {
        $cargo = cargo::where('cargo_id', '=', $request->get('id'))
            ->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($cargo) {
            return response()->json($cargo->cargo_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarCargo(Request $request)
    {
        $cargo = cargo::where('cargo_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($cargo) {
            $cargo->cargo_descripcion = $request->get('objCargo')['cargo_descripcion'];
            $cargo->save();
            return response()->json($cargo, 200);
        }
    }

    public function centro()
    {
        $respuesta = [];
        $centro = centro_costo::where('organi_id', '=', session('sesionidorg'))->where('estado', '=', 1)->get();
        $centroE = DB::table('empleado as e')
            ->join('centro_costo as c', 'c.centroC_id', '=', 'e.emple_centCosto')
            ->select('c.centroC_id')
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->get();
        for ($index = 0; $index < sizeof($centro); $index++) {
            $estado = true;
            foreach ($centroE as $c) {
                if ($centro[$index]->centroC_id == $c->centroC_id) {
                    $estado = false;
                }
            }
            if ($estado) {
                array_push($respuesta, $centro[$index]);
            }
        }

        return response()->json($respuesta, 200);
    }

    public function buscarCentro(Request $request)
    {
        $centro = centro_costo::where('centroC_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($centro) {
            return response()->json($centro->centroC_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarCentro(Request $request)
    {
        $centro = centro_costo::where('centroC_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($centro) {
            $centro->centroC_descripcion = $request->get('objCentroC')['centroC_descripcion'];
            $centro->save();
            return response()->json($centro, 200);
        }
    }

    public function local()
    {
        $local = local::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($local, 200);
    }

    public function buscarLocal(Request $request)
    {
        $local = local::where('local_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($local) {
            return response()->json($local->local_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarLocal(Request $request)
    {
        $local = local::where('local_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($local) {
            $local->local_descripcion = $request->get('objLocal')['local_descripcion'];
            $local->save();
            return response()->json($local, 200);
        }
    }

    public function nivel()
    {
        $nivel = nivel::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($nivel, 200);
    }

    public function buscarNivel(Request $request)
    {
        $nivel = nivel::where('nivel_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($nivel) {
            return response()->json($nivel->nivel_descripcion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarNivel(Request $request)
    {
        $nivel = nivel::where('nivel_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($nivel) {
            $nivel->nivel_descripcion = $request->get('objNivel')['nivel_descripcion'];
            $nivel->save();
            return response()->json($nivel, 200);
        }
    }

    public function contrato()
    {
        $contrato = tipo_contrato::where('organi_id', '=', session('sesionidorg'))->get();
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

    public function condicion()
    {
        $condicion = condicion_pago::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($condicion, 200);
    }

    public function buscarCondicion(Request $request)
    {
        $condicion = condicion_pago::where('id', '=', $request->get('id'))->get()->first();
        if ($condicion) {
            return response()->json($condicion->condicion, 200);
        }
        return response()->json(null, 400);
    }

    public function editarCondicion(Request $request)
    {
        $condicion = condicion_pago::where('id', '=', $request->get('id'))->get()->first();
        if ($condicion) {
            $condicion->condicion = $request->get('objCondicion')['condicion'];
            $condicion->save();
            return response()->json($condicion, 200);
        }
    }
}
