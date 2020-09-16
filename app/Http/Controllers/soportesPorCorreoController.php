<?php

namespace App\Http\Controllers;

use App\Mail\correoAdministrativo;
use App\Mail\sugerenciaMail;
use App\organizacion;
use App\persona;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class soportesPorCorreoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function soporte()
    {
        $correoAdministrativo = env('MAIL_FROM_ADDRESS');
        return view('correosA.soporte', ["correo" => $correoAdministrativo]);
    }

    public function envioTicketSoporte(Request $request)
    {
        $valor = $request->get('contenido');
        $asunto = $request->get('asunto');
        $email = env('MAIL_FROM_ADDRESS');
        $idOrganizacion = session('sesionidorg');
        $organizacion = organizacion::find($idOrganizacion);
        $idEmpleado = Auth::user()->id;
        $usuario = User::find($idEmpleado);
        $persona = persona::find($usuario->perso_id);
        Mail::to($email)->queue(new correoAdministrativo($valor, $asunto,$organizacion,$persona,$usuario));
        return response()->json($valor, 200);
    }

    public function sugerencia()
    {
        $correoAdministrativo = env('MAIL_FROM_ADDRESS');
        return view('correosA.sugerencia', ["correo" => $correoAdministrativo]);
    }

    public function envioSugerencia(Request $request)
    {
        $valor = $request->get('contenido');
        $asunto = $request->get('asunto');
        $email = env('MAIL_FROM_ADDRESS');
        Mail::to($email)->queue(new sugerenciaMail($valor, $asunto));
        return response()->json($valor, 200);
    }
}
