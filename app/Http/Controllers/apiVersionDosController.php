<?php

namespace App\Http\Controllers;

use App\actividad;
use App\captura;
use App\empleado;
use App\Mail\SoporteApi;
use App\persona;
use App\promedio_captura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function captura(Request $request)
    {

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
        $captura->idEmpleado = $request->get('idEmpleado');
        $captura->save();

        $idCaptura = $captura->idCaptura;
        $idHorario = $captura->idHorario_dias;

        //  PROMEDIO CAPTURA
        $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
        $idHorario_dias = $idHorario;
        //RESTA POR FECHA HORA DE   CAPTURAS
        $fecha = Carbon::create($capturaRegistrada->hora_ini)->format('H:i:s');
        $explo = explode(":", $fecha);
        $calSegund = $explo[0] * 3600 + $explo[1] * 60 + $explo[2];
        $fecha1 = Carbon::create($capturaRegistrada->hora_fin)->format('H:i:s');
        $explo1 = explode(":", $fecha1);
        $calSegund1 = $explo1[0] * 3600 + $explo1[1] * 60 + $explo1[2];
        $totalP = $calSegund1 - $calSegund;
        // ACTIVIDAD DE CAPTURA
        $activ = $capturaRegistrada->actividad;
        //VALIDACION DE CERO
        if ($totalP == 0) {
            $round = 0;
        } else {
            //PROMEDIO
            $promedio = floatval($activ / $totalP);
            $promedioFinal = $promedio * 100;
            $round = round($promedioFinal, 2);
        }
        $promedio_captura = new promedio_captura();
        $promedio_captura->idCaptura = $idCaptura;
        $promedio_captura->idHorario = $idHorario_dias;
        $promedio_captura->promedio = $round;
        $promedio_captura->tiempo_rango = $totalP;
        $promedio_captura->save();

        return response()->json($captura, 200);
    }

    public function ticketSoporte(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $asunto = $request->get('asunto');
        $contenido = $request->get('contenido');
        $celular = $request->get('celular');
        $tipo = $request->get('tipo');

        $empleado = empleado::findOrFail($idEmpleado);
        if ($empleado) {
            $persona = persona::findOrFail($empleado->emple_persona);
            $email = $email = env('MAIL_FROM_ADDRESS');
            if ($tipo == "soporte") {
                Mail::to($email)->queue(new SoporteApi($contenido, $persona, $asunto, $celular));
                return response()->json("Correo Enviado con exito", 200);
            }
        }

        return response()->json("Empleado no se encuentra registrado.", 400);
    }
}
