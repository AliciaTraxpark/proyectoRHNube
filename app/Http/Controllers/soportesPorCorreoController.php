<?php

namespace App\Http\Controllers;

use App\Mail\correoAdministrativo;
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
        $contenido = $request->get('contenido');
        $email = 'gaby020313@gmail.com';
        Mail::to($email)->queue(new correoAdministrativo($contenido));
        return response()->json($contenido, 200);
    }
}
