<?php

namespace App\Http\Controllers;

use App\organizacion;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrganizacionesController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index(){
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

         if($usuario_organizacion->rol_id==4){
            return view('organizacionesSuperAdmin.organiSuper');

         } else{
            return redirect('/dashboard');
         }

    }

    public function listaOrganizaciones(){
        $organizaciones1 = DB::table('organizacion as or')
        ->leftJoin('empleado as e','or.organi_id','=','e.organi_id')
        ->leftJoin('usuario_organizacion as uso','or.organi_id','=','uso.organi_id')
        ->leftJoin('users as u','uso.user_id','=','u.id')
        ->leftJoin('persona as pe','u.perso_id','=','pe.perso_id')
        ->select('or.organi_id','or.organi_ruc','or.organi_tipo','or.organi_razonSocial',
        'or.created_at','or.organi_nempleados', DB::raw('IF(e.emple_id is null, 0, COUNT(DISTINCT e.emple_id)) as nemple'),'organi_estado')
        ->selectRaw('GROUP_CONCAT(DISTINCT uso.user_id) as users')
        ->selectRaw('GROUP_CONCAT(DISTINCT pe.perso_nombre," ",pe.perso_apPaterno," ",pe.perso_apMaterno) as nombres')
        ->selectRaw('GROUP_CONCAT( pe.perso_celular) as celular')
        ->selectRaw('GROUP_CONCAT( u.email) as correo')
        ->where('e.emple_id',null)
        ->groupBy('or.organi_id');
        $organizaciones = DB::table('organizacion as or')
        ->leftJoin('empleado as e','or.organi_id','=','e.organi_id')
        ->leftJoin('usuario_organizacion as uso','or.organi_id','=','uso.organi_id')
        ->leftJoin('users as u','uso.user_id','=','u.id')
        ->leftJoin('persona as pe','u.perso_id','=','pe.perso_id')
        ->select('or.organi_id','or.organi_ruc','or.organi_tipo','or.organi_razonSocial',
        'or.created_at','or.organi_nempleados', DB::raw(' COUNT( DISTINCT e.emple_id)  as nemple') ,'organi_estado')
        ->union($organizaciones1)
        ->selectRaw('GROUP_CONCAT(DISTINCT uso.user_id) as users')
        ->selectRaw('GROUP_CONCAT(DISTINCT pe.perso_nombre," ",pe.perso_apPaterno," ",pe.perso_apMaterno) as nombres')
        ->selectRaw('GROUP_CONCAT(DISTINCT pe.perso_celular) as celular')
        ->selectRaw('GROUP_CONCAT(DISTINCT u.email) as correo')
      /*
        ->orwhere(function($query)  {
            $query->where('e.emple_estado',1)

            ->select('or.organi_id','or.organi_ruc','or.organi_tipo','or.organi_razonSocial',
        'or.created_at','or.organi_nempleados', DB::raw('IF(e.emple_estado=1 , COUNT( DISTINCT e.emple_id  ) ,0 ) as nemple') ,'organi_estado');
            ;
        }) */
        ->groupBy('or.organi_id')

        ->get();


        return json_encode($organizaciones);
    }

    public function activacionOrg(Request $request){
        $idOrgani=$request->id;
        $estado=$request->b;
        $organizacion = organizacion::findOrFail($idOrgani);
       /*  $usuac=Auth::user()->id; */
        if($estado==1){
            $organizacion->organi_estado =1;
            $organizacion->save();
        } else{
            $organizacion->organi_estado =0;
            $organizacion->save();

           /*  $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=',$idOrgani)
            ->get()->pluck('user_id'); */

           /*  foreach( $usuario_organizacion as  $userIds){
            $userToLogout = User::find($userIds);
            Auth::setUser($userToLogout);
            Auth::logout();
            session()->forget('sesionidorg');
            session()->flush();
           }

           Auth::loginUsingId(2);
           return $usuario_organizacion; */
        }
    }

    public function superAdUsuario(Request $request){
        $idorgani=$request->idorgani;
        $organizaciones1 = DB::table('organizacion as or')
        ->join('usuario_organizacion as uso','or.organi_id','=','uso.organi_id')
        ->join('users as u','uso.user_id','=','u.id')
        ->join('persona as pe','u.perso_id','=','pe.perso_id')
        ->join('rol as r','uso.rol_id','=','r.rol_id')
        ->select('r.rol_nombre','u.updated_at','pe.perso_nombre','pe.perso_apPaterno','pe.perso_apMaterno','u.email',
        'perso_fechaNacimiento','perso_sexo','perso_celular','perso_direccion','r.rol_id')
        ->where('or.organi_id',$idorgani)
        ->get();

        return $organizaciones1;

    }
}
