<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidarRegistroPRequest;
use App\user;

class registroPController extends Controller
{
    public function index(){
        return view('registro.registroPersona');
    }

    public function registrarDatos(ValidarRegistroPRequest $request){
        user::insert($request->except(["_token"]));
    }
}
