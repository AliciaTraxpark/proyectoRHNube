<?php

namespace App\Http\Controllers;

use App\actividad;
use App\actividad_empleado;
use Illuminate\Http\Request;

class ActividadesController extends Controller
{
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
        $idE = $request->get('idE');
        $actividad = new actividad();
        $actividad->Activi_Nombre = $request->get('nombre');
        $actividad->empleado_emple_id = $idE;
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
}
