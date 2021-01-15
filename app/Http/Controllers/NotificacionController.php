<?php

namespace App\Http\Controllers;

use App\empleado;
use App\Notifications\NuevaNotification;
use App\persona;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function notificacionesUsuario()
    {
        $notificacion = Auth::user()->notifications;
        if (sizeof($notificacion) == 0) {
            $empleado = empleado::where('users_id', '=', Auth::user()->id)->where('emple_estado','=',1)->where('organi_id', '=', session('sesionidorg'))->get();
            foreach ($empleado as $emple) {
                if (is_null($emple->emple_Correo) === true) {
                    $persona = persona::where('perso_id', $emple->emple_persona)->get()->first();
                    $mensaje = [
                        "idOrgani" => session('sesionidorg'),
                        "idEmpleado" => $emple->emple_id,
                        "empleado" => [
                            $persona->perso_nombre,
                            $persona->perso_apPaterno,
                            $persona->perso_apMaterno
                        ],
                        "mensaje" => "Empleado no tiene registrado un correo electrónico."
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
                if (is_null($emple->emple_departamento) === true && is_null($emple->emple_distrito) === false) {
                    $persona = persona::where('perso_id', $emple->emple_persona)->get()->first();
                    $mensaje = [
                        "idOrgani" => session('sesionidorg'),
                        "idEmpleado" => $emple->emple_id,
                        "empleado" => [
                            $persona->perso_nombre,
                            $persona->perso_apPaterno,
                            $persona->perso_apMaterno
                        ],
                        "mensaje" => "Empleado no tiene asignado Departamento y provincia"
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
                if (is_null($emple->emple_departamentoN) === true && is_null($emple->emple_distritoN) === false) {
                    $persona = persona::where('perso_id', $emple->emple_persona)->get()->first();
                    $mensaje = [
                        "idOrgani" => session('sesionidorg'),
                        "idEmpleado" => $emple->emple_id,
                        "empleado" => [
                            $persona->perso_nombre,
                            $persona->perso_apPaterno,
                            $persona->perso_apMaterno
                        ],
                        "mensaje" => "Empleado no tiene asignado Departamento y provincia de Nacimiento"
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
            }
        } else {
            $empleado = empleado::where('users_id', Auth::user()->id)->where('emple_estado','=',1)->where('organi_id', session('sesionidorg'))->get();
            foreach ($empleado as $emple) {
                $respuestaEmpleado = false;
                foreach ($notificacion as $notifi) {
                    $aux = $notifi->data;
                    foreach ($aux as $ax) {
                        if ($ax["idOrgani"] == session('sesionidorg') && $ax["idEmpleado"] == $emple->emple_id /* && $ax["mensaje"]=="Empleado no tiene registrado un correo electrónico." */ ) {
                            $respuestaEmpleado = true;
                            if ($emple->emple_Correo != '') {
                                $notifi->read_at = Carbon::now();
                                $notifi->save();
                            }
                            if ($emple->emple_departamento != '' && $emple->emple_distrito != '') {
                                $notifi->read_at = Carbon::now();
                                $notifi->save();
                            }
                            if ($emple->emple_departamentoN != '' && $emple->emple_distritoN != '') {
                                $notifi->read_at = Carbon::now();
                                $notifi->save();
                            }

                        }

                    }
                }
                if ($respuestaEmpleado === false) {
                    if (is_null($emple->emple_Correo) === true) {
                        $persona = persona::where('perso_id', $emple->emple_persona)->get()->first();
                        $mensaje = [
                            "idOrgani" => session('sesionidorg'),
                            "idEmpleado" => $emple->emple_id,
                            "empleado" => [
                                $persona->perso_nombre,
                                $persona->perso_apPaterno,
                                $persona->perso_apMaterno
                            ],
                            "mensaje" => "Empleado no tiene registrado un correo electrónico."
                        ];
                        $recipient = User::find(Auth::user()->id);
                        $recipient->notify(new NuevaNotification($mensaje));
                    }
                    if (is_null($emple->emple_departamento) === true && is_null($emple->emple_distrito) === false) {
                        $persona = persona::where('perso_id', $emple->emple_persona)->get()->first();
                        $mensaje = [
                            "idOrgani" => session('sesionidorg'),
                            "idEmpleado" => $emple->emple_id,
                            "empleado" => [
                                $persona->perso_nombre,
                                $persona->perso_apPaterno,
                                $persona->perso_apMaterno
                            ],
                            "mensaje" => "Empleado no tiene asignado Departamento y provincia"
                        ];
                        $recipient = User::find(Auth::user()->id);
                        $recipient->notify(new NuevaNotification($mensaje));
                    }
                    if (is_null($emple->emple_departamentoN) === true && is_null($emple->emple_distritoN) === false) {
                        $persona = persona::where('perso_id', $emple->emple_persona)->get()->first();
                        $mensaje = [
                            "idOrgani" => session('sesionidorg'),
                            "idEmpleado" => $emple->emple_id,
                            "empleado" => [
                                $persona->perso_nombre,
                                $persona->perso_apPaterno,
                                $persona->perso_apMaterno
                            ],
                            "mensaje" => "Empleado no tiene asignado Departamento y provincia de Nacimiento"
                        ];
                        $recipient = User::find(Auth::user()->id);
                        $recipient->notify(new NuevaNotification($mensaje));
                    }
                }
            }
        }
        return response()->json($notificacion, 200);
    }
    public function showNotificaciones()
    {
        $notificacion = Auth::user()->notifications;
        $respuesta = [];
        foreach($notificacion as $notifi){
            $aux = $notifi->data;
            foreach($aux as $ax){
                if($ax["idOrgani"] == session('sesionidorg')){
                    array_push($respuesta,$notifi);
                }
            }
        }
        return response()->json($respuesta, 200);
    }

    public function checkNotification(Request $request){
       $affected = DB::table('notifications')
                  ->where('id', $request->id)
                  ->update(['read_at' => Carbon::now()]);            
    }
}
