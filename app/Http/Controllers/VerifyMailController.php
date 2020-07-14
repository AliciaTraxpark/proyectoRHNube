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
        $datoNuevo = explode("@", $usuario[0]->email);
        if (sizeof($datoNuevo) != 2) {
            return view('Verificacion.smsVerificacion', ["usuario" => $usuario, "persona" => $persona]);
        } else {
            return view('Verificacion.verify', ["usuario" => $usuario, "persona" => $persona]);
        }
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
        }

        return redirect('/dashboard')->with('notification', 'Has confirmado correctamente tu correo!');
    }

    public function comprobar(Request $request)
    {
        $usuario = DB::table('users as u')
            ->where('u.id', '=', Auth::user()->id)
            ->get();
        $persona = DB::table('users as u')
            ->join('persona as p', 'u.perso_id', '=', 'p.perso_id')
            ->where('u.id', '=', Auth::user()->id)
            ->get();
        if ($request->get('codigo') != null) {
            dd($request->get('codigo'));
            $codigo = $request->get('codigo');
            $decode = base_convert(intval($codigo), 10, 36);
            $explode = explode("c", $decode);
            if (Auth::user()->id == $explode[0]) {
                return response()->json("Licencia Correcta", 200);
                //return redirect('/dashboard')->with('notification', 'Has confirmado correctamente tu correo!');
            }
            return view('Verificacion.smsVerificacion', ["usuario" => $usuario, "persona" => $persona]);
        }
        return view('Verificacion.smsVerificacion', ["usuario" => $usuario, "persona" => $persona]);
    }
}
