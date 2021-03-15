<?php

namespace App\Http\Controllers;

use App\crd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class servicioVerifyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except('nuevaCredencial');
    }

    // * FUNCION DE REGISTO DE CREDENCIALES PARA LOGIN 
    public function nuevaCredencial(Request $request)
    {
        // : VALIDACION DE BACKEND 
        // ? usuario -> email de credencial a registrar
        // ? CLAVE -> clave de credencial a registrar
        $validacion = Validator::make($request->all(), [
            'usuario' => 'required',
            'confirmar_usuario' => 'required',
            'clave' => 'required|min:8',
            'confirmar_clave' => 'required|min:8'
        ], [
            'required' => ':attribute es obligatorio.',
            'min' => 'El valor :attribute debe ser mayor :min'
        ]);
        // * ARRAY DE ERRORES
        if ($validacion->fails()) {

            return response()->json($validacion->errors(), 404);
        }
        // * BUSCAR SI USUARIO YA SE ENCUENTRA REGISTRADO
        $credencial = crd::where('usuario', '=', base64_encode($request->get('usuario')))->get()->first();
        if (!$credencial) {                   //: CUANDO NO LOE ENCUENTRA REGISTRADO
            // * COMPARAR USUARIOS Y CLAVES
            if (!(strcmp($request->get('usuario'), $request->get('confirmar_usuario')) === 0)) {
                return response()->json(array("respuesta" => "Usuario no coinciden."), 404);
            }
            if (!(strcmp($request->get('clave'), $request->get('confirmar_clave')) === 0)) {
                return response()->json(array("respuesta" => "Clave no coinciden."), 404);
            }
            // * REGISTRAR NUEVAS CREDENCIALES
            $nuevaCredencial = new crd();
            $nuevaCredencial->fecha_registro = Carbon::now();
            $nuevaCredencial->usuario = base64_encode($request->get('usuario'));
            $nuevaCredencial->clave = base64_encode($request->get('clave'));
            $nuevaCredencial->save();

            return response()->json(array("respuesta" => "Registro exitoso."), 201);
        } else {
            return response()->json(array("respuesta" => "Usuario ya se encuentra registrado."), 404);
        }
    }
}
