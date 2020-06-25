<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\organizacion;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_districts;
use App\User;
use App\usuario_organizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class registroEmpresaController extends Controller
{
    public function provincias($id)
    {
        return ubigeo_peru_provinces::where('departamento_id', $id)->get();
    }
    public function distritos($id)
    {
        return ubigeo_peru_districts::where('province_id', $id)->get();
    }
    public function index($user1)
    {

        $departamento = ubigeo_peru_departments::all();
        $provincia = ubigeo_peru_provinces::all();
        $distrito = ubigeo_peru_districts::all();
        return view('registro.registroEmpresa', ['departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito, 'userid' => $user1]);
    }

    public function registrarDatos(Request $request)
    {
        organizacion::insert($request->except(["_token"]));
    }

    public function create(Request $request)
    {
        $organizacion = new organizacion();
        $organizacion->organi_ruc = $request->get('ruc');
        $organizacion->organi_razonSocial = $request->get('razonSocial');
        $organizacion->organi_direccion = $request->get('direccion');
        $organizacion->organi_departamento = $request->get('departamento');
        $organizacion->organi_provincia = $request->get('provincia');
        $organizacion->organi_distrito = $request->get('distrito');
        $organizacion->organi_nempleados = $request->get('nempleados');
        $organizacion->organi_pagWeb = $request->get('pagWeb');
        $organizacion->organi_tipo = $request->get('tipo');
        $organizacion->save();
        $idorgani = $organizacion->organi_id;

        $usuario_organizacion = new usuario_organizacion();
        $usuario_organizacion->user_id = $request->get('iduser');
        $usuario_organizacion->organi_id = $idorgani;
        $usuario_organizacion->save();

        $data = DB::table('users as u')
        ->select('u.email','u.email_verified_at','confirmation_code')
        ->where('u.id','=',$request->get('iduser'))
        ->get();
        $datos = [];
        $datos["email"] = $data[0]->email;
        $datos["email_verified_at"] = $data[0]->email_verified_at;
        $datos["confirmation_code"] = $data[0]->confirmation_code;
        Mail::send('mails.confirmation_code', $datos, function ($message) use ($datos) {
            $message->to($datos['email'])->subject('Por favor confirma tu correo');
        });

        return Redirect::to('/')->with('mensaje', "Bien hecho, estas registrado!
        Te hemos enviado un correo de verificación.");
    }
}
