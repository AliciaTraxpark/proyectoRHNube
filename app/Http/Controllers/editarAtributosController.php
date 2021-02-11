<?php

namespace App\Http\Controllers;

use App\area;
use App\cargo;
use App\centro_costo;
use App\centrocosto_empleado;
use App\condicion_pago;
use App\local;
use App\marcacion_puerta;
use App\marcacion_tareo;
use App\nivel;
use App\tipo_contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class editarAtributosController extends Controller
{
    // * ************************************** AREA *******************************************
    // : LISTAR AREAS
    public function area()
    {
        $area = area::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($area, 200);
    }
    // : BUSCAR AREA
    public function buscarArea(Request $request)
    {
        $area = area::where('area_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($area) {
            return response()->json($area->area_descripcion, 200);
        }
        return response()->json(null, 400);
    }
    // : EDITAR AREA
    public function editarArea(Request $request)
    {
        $area = area::where('area_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($area) {
            $area->area_descripcion = $request->get('objArea')['area_descripcion'];
            $area->save();
            return response()->json($area, 200);
        }
    }
    // * *************************************** CARGO *******************************************
    // : LISTAR CARGO
    public function cargo()
    {
        $cargo = cargo::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($cargo, 200);
    }
    // : BUSCAR CARGO
    public function buscarCargo(Request $request)
    {
        $cargo = cargo::where('cargo_id', '=', $request->get('id'))
            ->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($cargo) {
            return response()->json($cargo->cargo_descripcion, 200);
        }
        return response()->json(null, 400);
    }
    // : EDITAR CARGO
    public function editarCargo(Request $request)
    {
        $cargo = cargo::where('cargo_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($cargo) {
            $cargo->cargo_descripcion = $request->get('objCargo')['cargo_descripcion'];
            $cargo->save();
            return response()->json($cargo, 200);
        }
    }
    // * *************************************** CENTRO DE COSTOS ************************************
    // : LISTAR CENTRO
    public function centro()
    {
        $respuesta = [];
        $centro = centro_costo::where('organi_id', '=', session('sesionidorg'))->where('porEmpleado', '=', 1)->where('estado', '=', 1)->get();
        // : HISTORIAL EMPLEADO
        foreach ($centro as $c) {
            $estado = true;
            $historialEmpleado = centrocosto_empleado::where('idCentro', '=', $c->centroC_id)->where('estado', '=', 1)->get()->first();
            if ($historialEmpleado) $estado = false;
            else {
                // : BUSCAR EN REPORTE DE ASISTENCIA EN PUERTA
                $marcacion = marcacion_puerta::where('centC_id', '=', $c->centroC_id)->get()->first();
                if ($marcacion) $estado = false;
                else {
                    // : BUSCAR EN REPORTE TAREO
                    $tareo = marcacion_tareo::where('centroC_id', '=', $c->centroC_id)->get()->first();
                    if ($tareo) $estado = false;
                }
            }
            if ($estado) {
                array_push($respuesta, $c);
            }
        }
        return response()->json($respuesta, 200);
    }
    // : BUSCAR CENTRO 
    public function buscarCentro(Request $request)
    {
        $centro = centro_costo::where('centroC_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($centro) {
            return response()->json($centro->centroC_descripcion, 200);
        }
        return response()->json(null, 400);
    }
    // : EDITAR CENTRO
    public function editarCentro(Request $request)
    {
        $centro = centro_costo::where('centroC_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($centro) {
            $centro->centroC_descripcion = $request->get('objCentroC')['centroC_descripcion'];
            $centro->save();
            return response()->json($centro, 200);
        }
    }
    // * **************************************** LOCAL *******************************************************************
    // : LISTA DE LOCAL
    public function local()
    {
        $local = local::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($local, 200);
    }
    // : BUSCAR LOCAL
    public function buscarLocal(Request $request)
    {
        $local = local::where('local_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($local) {
            return response()->json($local->local_descripcion, 200);
        }
        return response()->json(null, 400);
    }
    // : EDITAR LOCAL
    public function editarLocal(Request $request)
    {
        $local = local::where('local_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($local) {
            $local->local_descripcion = $request->get('objLocal')['local_descripcion'];
            $local->save();
            return response()->json($local, 200);
        }
    }
    // * **********************************************  NIVEL ***********************************************************************
    // : LISTA DE NIVEL
    public function nivel()
    {
        $nivel = nivel::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($nivel, 200);
    }
    // : BUSCAR NIVEL
    public function buscarNivel(Request $request)
    {
        $nivel = nivel::where('nivel_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($nivel) {
            return response()->json($nivel->nivel_descripcion, 200);
        }
        return response()->json(null, 400);
    }
    // : EDITAR NIVEL
    public function editarNivel(Request $request)
    {
        $nivel = nivel::where('nivel_id', '=', $request->get('id'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($nivel) {
            $nivel->nivel_descripcion = $request->get('objNivel')['nivel_descripcion'];
            $nivel->save();
            return response()->json($nivel, 200);
        }
    }
    // * ***************************************** CONTRATO *******************************************
    // : LISTA DE CONTRATO
    public function contrato()
    {
        $contrato = tipo_contrato::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($contrato, 200);
    }
    // : BUSCAR CONTRATO
    public function buscarContrato(Request $request)
    {
        $contrato = tipo_contrato::where('contrato_id', '=', $request->get('id'))->get()->first();
        if ($contrato) {
            return response()->json($contrato->contrato_descripcion, 200);
        }
        return response()->json(null, 400);
    }
    // : EDITAR CONTRATO
    public function editarContrato(Request $request)
    {
        $contrato = tipo_contrato::where('contrato_id', '=', $request->get('id'))->get()->first();
        if ($contrato) {
            $contrato->contrato_descripcion = $request->get('objContrato')['contrato_descripcion'];
            $contrato->save();
            return response()->json($contrato, 200);
        }
    }
    // * ********************************************** CONDICION ****************************************
    // : LISTA DE CONDICION
    public function condicion()
    {
        $condicion = condicion_pago::where('organi_id', '=', session('sesionidorg'))->get();
        return response()->json($condicion, 200);
    }
    // : BUSCAR CONDICION
    public function buscarCondicion(Request $request)
    {
        $condicion = condicion_pago::where('id', '=', $request->get('id'))->get()->first();
        if ($condicion) {
            return response()->json($condicion->condicion, 200);
        }
        return response()->json(null, 400);
    }
    // : EDITAR CONDICION
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
