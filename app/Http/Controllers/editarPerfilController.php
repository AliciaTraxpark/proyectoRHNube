<?php

namespace App\Http\Controllers;

use App\organizacion;
use App\persona;
use App\User;
use App\usuario_organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class editarPerfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $persona = persona::where('perso_id', '=', $user->perso_id)->get()->first();
        $usuarioOrg = usuario_organizacion::where('user_id', '=', $user->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuarioOrg->organi_id)->get()->first();
        return view('editarPerfil', ['persona' => $persona, 'organizacion' => $organizacion]);
    }
    public function show()
    {
        $user = DB::table('users as u')
            ->join('persona as p', 'p.perso_id', '=', 'u.perso_id')
            ->join('usuario_organizacion as uo', 'uo.user_id', '=', 'u.id')
            ->join('organizacion as o', 'o.organi_id', '=', 'uo.organi_id')
            ->select(
                'u.id',
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'p.perso_direccion',
                'p.perso_fechaNacimiento',
                'p.perso_sexo',
                'o.organi_ruc',
                'o.organi_razonSocial',
                'o.organi_direccion',
                'o.organi_departamento',
                'o.organi_provincia',
                'o.organi_distrito',
                'o.organi_nempleados',
                'o.organi_pagWeb',
                'o.organi_tipo'
            )
            ->get()
            ->first();
        return response()->json($user, 200);
    }

    public function actualizarDP(Request $request)
    {
        $id = $request->get('id');
        $user = User::where('id', '=', $id)->get()->first();
        $persona = persona::where('perso_id', '=', $user->perso_id)->get()->first();
        $persona->perso_nombre = $request->get('objDatosPersonales')['nombre'];
        $persona->perso_fechaNacimiento = $request->get('objDatosPersonales')['fechaN'];
        $persona->perso_apPaterno = $request->get('objDatosPersonales')['apPaterno'];
        $persona->perso_direccion = $request->get('objDatosPersonales')['direccion'];
        $persona->perso_apMaterno = $request->get('objDatosPersonales')['apMaterno'];
        $persona->perso_sexo = $request->get('objDatosPersonales')['genero'];
        $persona->save();
        return response()->json($persona, 200);
    }

    public function actualizarDE(Request $request)
    {
        $id = $request->get('id');
        $user = User::where('id', '=', $id)->get()->first();
        $usuarioOrg = usuario_organizacion::where('user_id', '=', $user->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuarioOrg->organi_id)->get()->first();
        $organizacion->organi_razonSocial = $request->get('objDatosEmpresa')['razonSocial'];
        $organizacion->organi_direccion = $request->get('objDatosEmpresa')['direccion'];
        $organizacion->organi_nempleados = $request->get('objDatosEmpresa')['nempleados'];
        $organizacion->organi_pagWeb = $request->get('objDatosEmpresa')['pagWeb'];
        $organizacion->organi_tipo = $request->get('objDatosEmpresa')['tipo'];
        $organizacion->save();
        return response()->json($organizacion, 200);
    }
    public function actualizarFoto(Request $request)
    {
        $userId = Auth::user();
        $user = User::where('id', '=', $userId->id)->get()->first();
        if ($request->hasfile('file')) {
            $file = $request->file('file');
            $path = public_path() . '/fotosUser';
            $fileName = uniqid() . $file->getClientOriginalName();
            $file->move($path, $fileName);
            $user->foto = $fileName;
            $user->save();
            return json_encode(array('status' => true));
        }
    }
}
