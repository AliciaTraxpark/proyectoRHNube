<?php

namespace App\Http\Controllers;

use App\actividad;
use App\actividad_empleado;
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

    public function registrarActividadE(Request $request)
    {
        $actividad = new actividad();
        $actividad->Activi_Nombre = $request->get('nombre');
        $actividad->controlRemoto = $request->get('cr');
        $actividad->asistenciaPuerta = $request->get('ap');
        $actividad->organi_id = session('sesionidorg');
        $actividad->save();

        return response()->json($actividad, 200);
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
            ->select('a.Activi_id', 'a.Activi_Nombre', 'a.controlRemoto', 'a.asistenciaPuerta', 'a.eliminacion')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.estado', '=', 1)
            ->get();

        return response()->json($actividades, 200);
    }

    // MODIFCAR ACTIVIDADES DE CONTROL

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
        if ($actividad) {
            $actividad->estado = 0;
            $actividad->save();
        }

        return response()->json($actividad, 200);
    }

    // ASIGNAR TAREAS A EMPLEADOS

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
        foreach ($actividadEmpleado as $a) {
            $actividad = DB::table('actividad as a')
                ->select('a.Activi_id', 'a.Activi_Nombre')
                ->where('a.organi_id', '=', session('sesionidorg'))
                ->where('a.controlRemoto', '=', 1)
                ->where('a.Activi_id', '!=', $a->Activi_id)
                ->where('a.estado', '=', 1)
                ->get();
        }
        // dd(DB::getQueryLog());
        $respuesta = [];
        foreach ($actividad as $a) {
            array_push($respuesta, array("value" => $a->Activi_id, "text" => $a->Activi_Nombre));
        }

        return response()->json($respuesta, 200);
    }
}
