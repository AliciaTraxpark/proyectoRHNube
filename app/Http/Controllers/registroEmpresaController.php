<?php

namespace App\Http\Controllers;

use App\Mail\CorreoMail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\organizacion;
use App\persona;
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

    private function sendMessage($message, $recipients)
    {
        $account_sid = config('services.twilio.account_sid');
        $auth_token = config('services.twilio.password');
        $twilio_number = config('services.twilio.from');
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, ['body' => $message, 'from' => $twilio_number]);
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
            $organizacion->save();
            $idorgani = $organizacion->organi_id;

            $usuario_organizacion = new usuario_organizacion();
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
            $datoNuevo = explode("@", $data[0]->email);

            if (sizeof($datoNuevo) != 2) {
                $codigo = $request->get('iduser') . "c" . $idPersona[0]->perso_id;
                $codigoI = intval($codigo, 36);
                $mensaje = "RH SOLUTION \nCodigo de validacion\n" . $codigoI;
                $this->sendMessage($mensaje, $data[0]->email);
                return Redirect::to('/')->with('mensaje', "Bien hecho, estas registrado.!");
            } else {
                Mail::to($correo)->queue(new CorreoMail($users, $persona, $organi));
                return Redirect::to('/')->with('mensaje', "Bien hecho, estas registrado! Te hemos enviado un correo de verificación.");
            }
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
