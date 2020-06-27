<?php

namespace App\Http\Controllers;

use App\Mail\CorreoMail;
use App\persona;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VerifyMailController extends Controller
{
    public function index()
    {
        $usuario = DB::table('users as u')
            ->where('u.id', '=', Auth::user()->id)
            ->get();
        $persona = DB::table('users as u')
            ->join('persona as p', 'u.perso_id', '=', 'p.perso_id')
            ->where('u.id', '=', Auth::user()->id)
            ->get();
        return view('Verificacion.verify', ["usuario" => $usuario, "persona" => $persona]);
    }
    public function verificarReenvio()
    {
        $data = DB::table('users as u')
            ->select('u.email', 'u.email_verified_at', 'confirmation_code')
            ->where('u.id', '=', Auth::user()->id)
            ->get();
        $idPersona = DB::table('users as u')
            ->join('persona as p', 'u.perso_id', 'p.perso_id')
            ->select('p.perso_id')
            ->where('u.id', '=', Auth::user()->id)
            ->get();

        $datos = [];
        $persona = [];
        $persona["id"] = $idPersona[0]->perso_id;
        $datos["email"] = $data[0]->email;
        $datos["email_verified_at"] = $data[0]->email_verified_at;
        $datos["confirmation_code"] = $data[0]->confirmation_code;
        if ($datos["confirmation_code"] != NULL) {
            $persona = persona::find($persona["id"]);
            $users = User::find(Auth::user()->id);
            $correo = array($datos['email']);

            Mail::to($correo)->queue(new CorreoMail($users, $persona));
        } else {
            return redirect('/');
        }

        return redirect('/dashboard')->with('notification', 'Has confirmado correctamente tu correo!');
    }
}
