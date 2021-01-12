<?php

namespace App\Http\Controllers;

use App\dispositivos;
use Illuminate\Http\Request;
use App\organizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class biometricoController extends Controller
{
    //
    public function vistaReporte()
    {
        $organizacion = organizacion::all('organi_id', 'organi_razonSocial');
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion) {
            if ($usuario_organizacion->rol_id == 4) {
                return view('Biometrico.reportePersonalizado', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacion->rol_id]);
            }
        } else {
            if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
                return redirect('/elegirorganizacion');
            } else {
                $usuario_organizacionR = DB::table('usuario_organizacion as uso')
                    ->where('uso.organi_id', '=', session('sesionidorg'))
                    ->where('uso.user_id', '=', Auth::user()->id)
                    ->get()->first();
                return view('Biometrico.reportePersonalizado', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacionR->rol_id]);
            }
        }
    }

    public function dispoStoreBiometrico( Request $request){
        $dispositivos = new dispositivos();
        $dispositivos->tipoDispositivo = 3;
        $dispositivos->dispo_codigo = $request->serieBio;
        $dispositivos->dispo_movil = $request->ippuerto;

        $dispositivos->dispo_estadoActivo = 1;
        $dispositivos->dispo_estado = 0;
        $dispositivos->organi_id = session('sesionidorg');

        $dispositivos->save();

    }
}
