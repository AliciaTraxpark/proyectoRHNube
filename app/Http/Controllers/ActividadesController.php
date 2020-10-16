<?php

namespace App\Http\Controllers;

use App\actividad;
use App\actividad_empleado;
use App\captura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActividadesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function actividadesEmpleado(Request $request)
    {
        $respuesta = [];
        $id = $request->get('id');
        $actividad_empleado = actividad_empleado::where('idEmpleado', '=', $id)->get();
        foreach ($actividad_empleado as $a) {
            $actividad = actividad::findOrFail($a->idActividad);
            $actividad->eliminacionActividadEmpleado = $a->eliminacion;
            $actividad->estadoActividadEmpleado = $a->estado;
            array_push($respuesta, $actividad);
        }
        return response()->json($respuesta, 200);
    }

    public function editarActividadE(Request $request)
    {
        $idA = $request->get('idA');
        $actividad = actividad::where('Activi_id', '=', $idA)->get()->first();
        if ($actividad) {
            $actividad->Activi_Nombre = $request->get('actividad');
            $actividad->save();
            return response()->json($actividad, 200);
        }
    }

    public function editarEstadoActividad(Request $request)
    {
        $idA = $request->get('idA');
        $idE = $request->get('idE');
        $actividad_empleado = actividad_empleado::where('idEmpleado', '=', $idE)->where('idActividad', '=', $idA)->get()->first();
        if ($actividad_empleado) {
            $actividad_empleado->estado = $request->get('estado');
            $actividad_empleado->save();
            return response()->json($actividad_empleado, 200);
        }
    }

    // VISTAS DE ACTIVIDADES

    public function actividades()
    {
        return view('MantenedorActividades.actividades');
    }

    public function actividadesOrganizaciones()
    {
        $actividades = DB::table('actividad as a')
            ->leftJoin('captura as cp', 'cp.idActividad', '=', 'a.Activi_id')
            ->select(
                'a.Activi_id',
                'a.Activi_Nombre',
                'a.controlRemoto',
                'a.asistenciaPuerta',
                'a.eliminacion',
                DB::raw("CASE WHEN(a.codigoActividad) IS NULL THEN 'No definido' ELSE a.codigoActividad END AS codigoA"),
                DB::raw("CASE WHEN(cp.idCaptura) IS NULL THEN 'No' ELSE 'Si' END AS respuesta")
            )
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.estado', '=', 1)
            ->get();

        return response()->json($actividades, 200);
    }

    // REGISTRAR ACTIVIDAD PARA ORGANIZACION
    public function registrarActividadE(Request $request)
    {
        $actividadBuscar = actividad::where('Activi_Nombre', '=', $request->get('nombre'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($actividadBuscar) {
            return response()->json(array("estado" => 1, "actividad" => $actividadBuscar), 200);
        }
        $actividad = new actividad();
        $actividad->Activi_Nombre = $request->get('nombre');
        $actividad->controlRemoto = $request->get('cr');
        $actividad->asistenciaPuerta = $request->get('ap');
        $actividad->organi_id = session('sesionidorg');
        $actividad->codigoActividad = $request->get('codigo');
        $actividad->save();

        return response()->json($actividad, 200);
    }

    // EDITAR ACTIVIDAD

    public function editarActividad(Request $request)
    {
        $idA = $request->get('idA');
        $actividades = DB::table('actividad as a')
            ->leftJoin('captura as cp', 'cp.idActividad', '=', 'a.Activi_id')
            ->select(
                'a.Activi_id',
                'a.Activi_Nombre',
                'a.controlRemoto',
                'a.asistenciaPuerta',
                'a.codigoActividad'
            )
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.Activi_id', '=', $idA)
            ->where('a.estado', '=', 1)
            ->get()
            ->first();

        return response()->json($actividades, 200);
    }

    //REGISTRAR DATOS AL EDITAR
    public function editarCambios(Request $request)
    {
        $idA = $request->get('idA');
        $actividad = actividad::findOrFail($idA);
        if ($actividad) {
            $actividad->codigoActividad = $request->get('codigo');
            $actividad->controlRemoto = $request->get('cr');
            $actividad->asistenciaPuerta = $request->get('ap');
            $actividad->save();
        }

        return response()->json($actividad, 200);
    }

    // MODIFCAR ESTADOS ACTIVIDADES DE CONTROL

    public function cambiarEstadoActividadControl(Request $request)
    {
        $idActividad = $request->get('id');
        $control = $request->get('control');
        // BUSCAMOS ACTIVIDAD
        $actividad = actividad::findOrFail($idActividad);
        if ($actividad) {
            if ($control == "CR") {
                $actividad->controlRemoto = $request->get('valor');
            }
            if ($control == "AP") {
                $actividad->asistenciaPuerta = $request->get('valor');
            }
            $actividad->save();
        }

        return response()->json($actividad, 200);
    }

    //CAMBIAR ESTADO A ACTIVIDAD

    public function cambiarEstadoActividad(Request $request)
    {
        $idActividad = $request->get('id');
        $actividad = actividad::findOrFail($idActividad);
        $buscar_actividad = captura::where('idActividad', '=', $idActividad)->get()->first();
        if ($buscar_actividad) {
            return 1;
        }
        if ($actividad) {
            $actividad->estado = 0;
            $actividad->save();
        }

        return response()->json($actividad, 200);
    }

    // MOSTRAR TAREAS PARA ASIGNAR AL EMPLEADO

    public function asignarActividadesE(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        // DB::enableQueryLog();
        $actividadEmpleado = DB::table('actividad as a')
            ->join('actividad_empleado as ae', 'ae.idActividad', '=', 'a.Activi_id')
            ->select('a.Activi_id')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.controlRemoto', '=', 1)
            ->where('ae.idEmpleado', '=', $idEmpleado)
            ->where('a.estado', '=', 1)
            ->get();
        $actividad = DB::table('actividad as a')
            ->select('a.Activi_id', 'a.Activi_Nombre')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.controlRemoto', '=', 1)
            ->where('a.estado', '=', 1)
            ->get();

        $respuesta = [];
        for ($index = 0; $index < sizeof($actividad); $index++) {
            $estado = false;
            foreach ($actividadEmpleado as $ae) {
                if ($actividad[$index]->Activi_id == $ae->Activi_id) {
                    $estado = true;
                }
            }
            if ($estado == false) {
                array_push($respuesta, array("value" => $actividad[$index]->Activi_id, "text" => $actividad[$index]->Activi_Nombre));
            }
        }
        // dd(DB::getQueryLog());
        // foreach ($actividad as $a) {
        //     array_push($respuesta, array("value" => $a->Activi_id, "text" => $a->Activi_Nombre));
        // }

        return response()->json($respuesta, 200);
    }

    // ASIGNAR TAREAS
    public function registrarActividadEmpleado(Request $request)
    {
        $idE = $request->get('idE');
        $idActividad = $request->get('idA');

        foreach ($idActividad as $idA) {
            $actividad_empleado = new actividad_empleado();
            $actividad_empleado->idActividad = $idA;
            $actividad_empleado->idEmpleado = $idE;
            $actividad_empleado->estado = 1;
            $actividad_empleado->eliminacion = 1;
            $actividad_empleado->save();
        }

        return response()->json($actividad_empleado, 200);
    }
}
