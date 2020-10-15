<?php

namespace App\Http\Controllers;
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
        ->join('usuario_organizacion as uso','or.organi_id','=','uso.organi_id')
        ->join('users as u','uso.user_id','=','u.id')
        ->join('persona as pe','u.perso_id','=','pe.perso_id')
        ->select('or.organi_id','or.organi_ruc','or.organi_tipo','or.organi_razonSocial',
        'or.created_at','or.organi_nempleados', DB::raw('IF(e.emple_id is null, 0, COUNT(DISTINCT e.emple_id)) as nemple'))
        ->selectRaw('GROUP_CONCAT(DISTINCT uso.user_id) as users')
        ->selectRaw('GROUP_CONCAT(DISTINCT pe.perso_nombre," ",pe.perso_apPaterno," ",pe.perso_apMaterno,"<br>") as nombres')

        ->where('e.emple_id',null)
        ->groupBy('or.organi_id');
        $organizaciones = DB::table('organizacion as or')
        ->leftJoin('empleado as e','or.organi_id','=','e.organi_id')
        ->join('usuario_organizacion as uso','or.organi_id','=','uso.organi_id')
        ->join('users as u','uso.user_id','=','u.id')
        ->join('persona as pe','u.perso_id','=','pe.perso_id')
        ->select('or.organi_id','or.organi_ruc','or.organi_tipo','or.organi_razonSocial',
        'or.created_at','or.organi_nempleados', DB::raw('IF(e.emple_id is null, 0, COUNT( DISTINCT e.emple_id)) as nemple'))
        ->union($organizaciones1)
        ->selectRaw('GROUP_CONCAT(DISTINCT uso.user_id) as users')
        ->selectRaw('GROUP_CONCAT(DISTINCT pe.perso_nombre," ",pe.perso_apPaterno," ",pe.perso_apMaterno,"<br>") as nombres')

        ->where('e.emple_estado',1)
        ->groupBy('or.organi_id')

        ->get();


        return json_encode($organizaciones);
    }
}