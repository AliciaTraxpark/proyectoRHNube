<?php

namespace App\Http\Controllers;

use App\Mail\CorreoMail;
use App\organizacion;
use App\persona;
use App\User;
use App\usuario_organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class VerifyMailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    public function index()
    {
        $usuario = DB::table('users as u')
            ->where('u.id', '=', Auth::user()->id)
            ->get();
        $persona = DB::table('users as u')
            ->join('persona as p', 'u.perso_id', '=', 'p.perso_id')
            ->where('u.id', '=', Auth::user()->id)
            ->get();

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        if ($usuario_organizacion != null) {
            $datoNuevo = explode("@", $usuario[0]->email);
            if (sizeof($datoNuevo) != 2) {
                return view('Verificacion.smsVerificacion', ["usuario" => $usuario, "persona" => $persona]);
            } else {
                return view('Verificacion.verify', ["usuario" => $usuario, "persona" => $persona]);
            }
        } else {
            $id = Auth::user()->id;
            $user1 = Crypt::encrypt($id);
            return redirect('/registro/organizacion/' + $user1);
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

            $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();
            Mail::to($correo)->queue(new CorreoMail($users, $persona, $organizacion));
        }

        return redirect('/dashboard')->with('notification', 'Has confirmado correctamente tu correo!');
    }
}
