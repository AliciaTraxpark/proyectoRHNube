<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\empleado;
use App\persona;
use App\User;
use App\invitado;
use App\organizacion;
use App\invitado_empleado;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CorreoInvitado;
use Illuminate\Support\Facades\Crypt;
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

       /*  $persona = new persona();
        $persona->perso_nombre = $nombreInv;
        $persona->perso_apPaterno = $apPaInv;
        $persona->perso_apMaterno =$apMaInv;
        $persona->save();
        $user_persona = $persona->perso_id;

        $data['confirmation_code'] = STR::random(25);
       ////////////
       $clave=STR::random(9);
       ////////////
        $User = new User();
        $User->email = $emailInv;
        $User->rol_id = 3;
        $User->perso_id = $user_persona;
        $User->user_estado = 0;
        $User->password = Hash::make($clave);
        $User->confirmation_code = $data['confirmation_code'];
        $User->save();
        $id = $User->id;
/////////////////////////////////////*/
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
      /*   $user = User::where('confirmation_code', $code)->first();

        if (!$user)
            return redirect('/');

        $user->email_verified_at = Carbon::now();
        $user->confirmation_code = null;
        $user->save(); */
        $idInvit = Crypt::decrypt($idinEncr);
        return view('registro.registroInvitado',['idInvit'=>$idInvit]);
    }
}
