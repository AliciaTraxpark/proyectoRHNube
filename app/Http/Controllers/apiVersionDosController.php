<?php

namespace App\Http\Controllers;

use App\actividad;
use App\captura;
use Illuminate\Http\Request;

class apiVersionDosController extends Controller
{
    function selectActividad(Request $request)
    {
        $empleado = $request->get('emple_id');
        $respuesta = [];
        $actividad = actividad::where('empleado_emple_id', '=', $empleado)->get();
        foreach ($actividad as $act) {
            array_push($respuesta, $act);
        }
        return response()->json($respuesta, 200);
    }

    function actividad(Request $request)
    {
        $cambio = $request->get('cambio');
        if ($cambio == 'n') {
            $actividad = new actividad();
            $actividad->Activi_Nombre = $request['Activi_Nombre'];
            if ($request['Tarea_Tarea_id'] != '') {
                $actividad->Tarea_Tarea_id = $request['Tarea_Tarea_id'];
            }
            $actividad->empleado_emple_id = $request['emple_id'];
            $actividad->save();
        }
        if ($cambio == 'm') {
            $Activi_id = $request['Activi_id'];
            $actividad = actividad::where('Activi_id', $Activi_id)->first();
            if ($actividad) {
                $actividad->Activi_Nombre = $request['Activi_Nombre'];
                $actividad->save();
            }
        }
        if ($cambio == 'e') {
            $actividad = actividad::where('Activi_id', $request->get('idActividad'))->first();
            if ($actividad) {
                $actividad->estado = 0;
                $actividad->save();
            }
        }
        return response()->json($actividad, 200);
    }

    public function captura(Request $request){

        $captura = new captura();
        $captura->estado = $request->get('estado');
        $captura->imagen = $request->get('imagen');
        $captura->actividad = $request->get('actividad');
        $captura->hora_ini = $request->get('hora_ini');
        $captura->hora_fin = $request->get('hora_fin');
        $captura->ultimo_acumulado = $request->get('ultimo_acumulado');
        $captura->acumulador = $request->get('acumulador');
        $captura->idHorario_dias = $request->get('idHorario_dias');
        $captura->idActividad = $request->get('idActividad');
        $captura->save();

        return response()->json($captura, 200);

    }
}
