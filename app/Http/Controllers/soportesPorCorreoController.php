<?php

namespace App\Http\Controllers;

use App\Mail\correoAdministrativo;
use App\Mail\sugerenciaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class soportesPorCorreoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function soporte()
    {
        return view('correosA.soporte');
    }

    public function envioTicketSoporte(Request $request)
    {
        $valor = $request->get('contenido');
        $asunto = $request->get('asunto');
        $email = env('MAIL_FROM_ADDRESS');
        Mail::to($email)->queue(new correoAdministrativo($valor,$asunto));
        return response()->json($valor, 200);
    }

    public function sugerencia()
    {
        return view('correosA.sugerencia');
    }

    public function envioSugerencia(Request $request)
    {
        $valor = $request->get('contenido');
        $asunto = $request->get('asunto');
        $email = env('MAIL_FROM_ADDRESS');
        Mail::to($email)->queue(new sugerenciaMail($valor,$asunto));
        return response()->json($valor, 200);
    }
}
