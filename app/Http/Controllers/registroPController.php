<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\user;
use App\persona;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class registroPController extends Controller
{
    public function index()
    {
        return view('registro.registroPersona');
    }

    public function registrarDatos(Request $request)
    {
        user::insert($request->except(["_token"]));
    }
    public function create(Request $request)
    {

        $persona = new persona();
        $persona->perso_nombre = $request->get('nombres');
        $persona->perso_apPaterno = $request->get('apPaterno');
        $persona->perso_apMaterno = $request->get('apMaterno');
        $persona->perso_direccion = $request->get('direccion');
        $persona->perso_celular = $request->get('celular');
        $diaf = $request->get('dia_fecha');
        $mesf = $request->get('mes_fecha');
        $anof = $request->get('ano_fecha');
        //$f1 = explode("/", $request->get('fecha'));
        $fechaN = $anof . "-" . $mesf . "-" . $diaf;
        $persona->perso_fechaNacimiento = $fechaN;

        $persona->perso_sexo = $request->get('sexo');

        $persona->save();
        $user_persona = $persona->perso_id;

        $data['confirmation_code'] = STR::random(25);

        $User = new User();

        $User->email = $request->get('email');

        $User->perso_id = $user_persona;
        $User->user_estado = 0;
        $User->password = Hash::make($request->get('password'));
        $User->confirmation_code = $data['confirmation_code'];
        $User->save();
        $id = $User->id;
        $user1 = Crypt::encrypt($id);
        return $user1;
    }
    public function mensajes(Request $request)
    {
        $this->validate($request, [
            'nombres' => 'required',
            'apPaterno' => 'required',
            'apMaterno' => 'required',
            'direccion' => 'required',
            'fecha' => 'required|date',
            'email' => 'required',
            'password' => 'required',
            'sexo' => 'required',
            'celular' => 'required'
        ]);

        return $request->all();
    }
    public function comprobar(Request $request)
    {
        $email = $request->get('email');
        $UsuarioN = user::where('email', '=', $email)->first();


        if ($UsuarioN != null) {
            return 1;
        }
    }
}
