<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidarRegistroPRequest;
use App\user;
use App\persona;
use Illuminate\Support\Facades\Hash;
class registroPController extends Controller
{
    public function index(){
        return view('registro.registroPersona');
    }

    public function registrarDatos(ValidarRegistroPRequest $request){
        user::insert($request->except(["_token"]));
    }
    public function create(Request $request)
    {

  $persona=new persona();
    $persona->perso_nombre= $request->get('nombres');
    $persona->perso_apellidos= $request->get('apellidos');
    $persona->save();
    $user_persona= $persona->perso_id ;

    $User=new User();
    $User->email= $request->get('email');
    $User->rol_id= 1;
    $User->perso_id= $user_persona;
    $User->password= Hash::make($request->get('password'));
    $User->save();

}
}
