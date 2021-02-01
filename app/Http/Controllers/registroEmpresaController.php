<?php

namespace App\Http\Controllers;

use App\condicion_pago;
use App\Mail\CorreoMail;
use App\Mail\NuevoUsuarioMail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\organizacion;
use App\persona;
use App\tipo_contrato;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_districts;
use App\User;
use App\usuario_organizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

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

        $users = Crypt::decrypt($user1);
        $departamento = ubigeo_peru_departments::all();
        $provincia = ubigeo_peru_provinces::all();
        $distrito = ubigeo_peru_districts::all();
        return view('registro.registroEmpresa', ['departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito, 'userid' => $users]);
    }

    public function registrarDatos(Request $request)
    {
        organizacion::insert($request->except(["_token"]));
    }

    public function create(Request $request)
    {
        $rucorganizacion = DB::table('organizacion as o')
            ->where('o.organi_ruc', '=', $request->get('ruc'))
            ->get()->first();

        if ($rucorganizacion == null) {
            if ($request->get('inputOrgani') != null) {
                $organi = $request->get('inputOrgani');
            } else {
                $organi = $request->get('tipo');
            }
            $organizacion = new organizacion();
            $organizacion->organi_ruc = $request->get('ruc');
            $organizacion->organi_razonSocial = $request->get('razonSocial');
            $organizacion->organi_direccion = $request->get('direccion');
            $organizacion->organi_departamento = $request->get('departamento');
            $organizacion->organi_provincia = $request->get('provincia');
            $organizacion->organi_distrito = $request->get('distrito');
            $organizacion->organi_nempleados = $request->get('nempleados');
            $organizacion->organi_pagWeb = $request->get('pagWeb');
            $organizacion->organi_tipo = $organi;
            $organizacion->organi_estado = 1;
            $organizacion->save();
            $idorgani = $organizacion->organi_id;

            //
            $condicion_pago = new condicion_pago();
            $condicion_pago->condicion = 'Mensual';
            $condicion_pago->organi_id = $idorgani;
            $condicion_pago->save();

            //
            $condicion_pago1 = new condicion_pago();
            $condicion_pago1->condicion = 'Quincenal';
            $condicion_pago1->organi_id = $idorgani;
            $condicion_pago1->save();

            // TIPOS DE CONTRATO
            $arrayContrato = [
                'Freelance',
                'Part time',
                'Planilla',
                'Planilla - Contrato de emergencia',
                'Planilla - Contrato de suplencia',
                'Planilla - Contrato de temporada',
                'Planilla - Contrato intermitente',
                'Planilla - Contrato ocasional',
                'Planilla - Contrato por inicio o incremento de actividad',
                'Planilla - Contrato por necesidad de mercado',
                'Planilla - Contrato por obra determinada o servicio específico',
                'Planilla - Contrato por reconversión empresarial',
                'Por servicio'
            ];
            foreach ($arrayContrato as $c) {
                $contrato = new tipo_contrato();
                $contrato->contrato_descripcion = $c;
                $contrato->organi_id =  $idorgani;
                $contrato->save();
            }
            // 
            $usuario_organizacion = new usuario_organizacion();
            $usuario_organizacion->rol_id = 1;
            $usuario_organizacion->user_id = $request->get('iduser');
            $usuario_organizacion->organi_id = $idorgani;
            $usuario_organizacion->save();

            $data = DB::table('users as u')
                ->select('u.email', 'u.email_verified_at', 'confirmation_code')
                ->where('u.id', '=', $request->get('iduser'))
                ->get();
            $idPersona = DB::table('users as u')
                ->join('persona as p', 'u.perso_id', 'p.perso_id', 'p.')
                ->select('p.perso_id')
                ->where('u.id', '=', $request->get('iduser'))
                ->get();
            $datos = [];
            $persona = [];
            $persona["id"] = $idPersona[0]->perso_id;
            $datos["email"] = $data[0]->email;
            $datos["email_verified_at"] = $data[0]->email_verified_at;
            $datos["confirmation_code"] = $data[0]->confirmation_code;
            $persona = persona::find($persona["id"]);
            $users = User::find($request->get('iduser'));
            $organi = organizacion::find($idorgani);
            $correo = array($datos['email']);
            $correoU = 'info@rhnube.com.pe';
            /////////////////////////////////
            $comusuario_organizacion = usuario_organizacion::where('user_id', '=', $users->id)->count();
            if ($comusuario_organizacion > 1) {
                Mail::to($correoU)->queue(new NuevoUsuarioMail($persona, $organi, $users));
                return Redirect::to('/')->with('mensaje', "Nueva organizacion creada exitosamente!");
            } else {
                Mail::to($correo)->queue(new CorreoMail($users, $persona, $organi));
                Mail::to($correoU)->queue(new NuevoUsuarioMail($persona, $organi, $users));
                return Redirect::to('/')->with('mensaje', "Bien hecho, estas registrado! Te hemos enviado un correo de verificación.");
            }
            ////////////////////////////

        } else {
            return redirect()->back()->with('errors', 'RUC/ID ya se encuentra registrado')->withInput();
        }
    }
    public function busquedaRuc(Request $request)
    {
        $rucEmpresa = $request->get('consulta');

        $organizacion = DB::table('organizacion as o')
            ->where('o.organi_ruc', '=', $rucEmpresa)
            ->get()->first();

        if ($organizacion == null) {
            return 0;
        } else {
            return 1;
        }
    }
}
