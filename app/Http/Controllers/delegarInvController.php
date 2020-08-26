<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\empleado;
use App\persona;
use App\User;
use App\invitado;
use App\organizacion;
use App\invitado_empleado;
use App\usuario_organizacion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CorreoInvitado;
use App\Mail\CorreoMail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class delegarInvController extends Controller
{
    //
    public function index(){
        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->select(
                'e.emple_id',
                'p.perso_id',
                'p.perso_nombre',
                'e.emple_nDoc',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'a.area_descripcion',
                'e.emple_id',
                'a.area_id'
            )
            ->get();
            $area = DB::table('area as ar')
            ->join('empleado as em', 'ar.area_id', '=', 'em.emple_area')
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();
        return view('delegarInvitado.delegarControl',['empleado'=>$empleado,'area'=>$area]);
    }
    public function empleAreaIn(Request $request){
        $idarea=$request->idarea;
        $arrayem = collect();
        foreach($idarea as $idareas){
            $empleadosArea = DB::table('empleado')
        ->where('users_id', '=', Auth::user()->id)
        ->where('emple_area', '=', $idareas)
        ->where('emple_estado', '=', 1)
        ->get();
        $arrayem->push($empleadosArea);
        }
        return $arrayem->toArray();

    }

    public function registrarInvitado(Request $request){

        $emailInv=$request->emailInv;
        $idEmpleado=$request->idEmpleado;
        $idempusu=DB::table('usuario_organizacion')
        ->where('user_id', '=', Auth::user()->id)
        ->get()->first();
        $organi = organizacion::find( $idempusu->organi_id);
        $invitado = new invitado();
        $invitado->organi_id =  $organi->organi_id;
        $invitado->rol_id =3;
        $invitado->email_inv = $emailInv;
        $invitado->estado =0;
        $invitado->users_id =Auth::user()->id;
        $invitado->save();

        foreach($idEmpleado as $idEmpleados){
            $invitado_empleado = new invitado_empleado();
            $invitado_empleado->idinvitado = $invitado->idinvitado;
            $invitado_empleado->emple_id = $idEmpleados;
            $invitado_empleado->save();
        }
        Mail::to($emailInv)->queue(new CorreoInvitado($organi,$invitado));

    }

    public function vistaRegistroInv($idinEncr){

        $idInvit = Crypt::decrypt($idinEncr);
        return view('registro.registroInvitado',['idInvit'=>$idInvit]);
    }

    public function registroInvitado(Request $request){

        $idinvitado=$request->idinvita;
        //  registro persona y user
        $persona = new persona();
        $persona->perso_nombre = $request->get('nombres');
        $persona->perso_apPaterno = $request->get('apPaterno');
        $persona->perso_apMaterno = $request->get('apMaterno');
        $persona->perso_direccion = $request->get('direccion');
        $diaf = $request->get('dia_fecha');
        $mesf = $request->get('mes_fecha');
        $anof = $request->get('ano_fecha');

        $fechaN = $anof . "-" . $mesf . "-" . $diaf;
        $persona->perso_fechaNacimiento = $fechaN;

        $persona->perso_sexo = $request->get('sexo');

        $persona->save();
        $user_persona = $persona->perso_id;

        $data['confirmation_code'] = STR::random(25);

        $User = new User();
        $User->email = $request->get('email');
        $User->rol_id = 3;
        $User->perso_id = $user_persona;
        $User->user_estado = 1;
        $User->password = Hash::make($request->get('password'));
        $User->confirmation_code = $data['confirmation_code'];
        $User->save();

        //registro en organizacion
        $invitado = DB::table('invitado')
            ->where('idinvitado', '=',  $idinvitado)
            ->get()->first();
        $usuario_organizacion = new usuario_organizacion();
            $usuario_organizacion->user_id =$User->id;
            $usuario_organizacion->organi_id = $invitado->organi_id;
            $usuario_organizacion->save();

         //actualiza invitado
          
        //////////////





            $data = DB::table('users as u')
                ->select('u.email', 'u.email_verified_at', 'confirmation_code')
                ->where('u.id', '=', $User->id)
                ->get();
            $idPersona = DB::table('users as u')
                ->join('persona as p', 'u.perso_id', 'p.perso_id', 'p.')
                ->select('p.perso_id')
                ->where('u.id', '=', $User->id)
                ->get();
            $datos = [];
            $persona = [];
            $persona["id"] = $idPersona[0]->perso_id;
            $datos["email"] = $data[0]->email;
            $datos["email_verified_at"] = $data[0]->email_verified_at;
            $datos["confirmation_code"] = $data[0]->confirmation_code;
            $persona = persona::find($persona["id"]);
            $users = User::find($User->id);
            $organi = organizacion::find($invitado->organi_id);
            $correo = array($datos['email']);
            $datoNuevo = explode("@", $data[0]->email);


                Mail::to($correo)->queue(new CorreoMail($users, $persona, $organi));
                return Redirect::to('/')->with('mensaje', "Bien hecho, estas registrado! Te hemos enviado un correo de verificación.");




    }
}
