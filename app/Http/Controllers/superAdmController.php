<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class superAdmController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function indexDashboard(){
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

         if($usuario_organizacion->rol_id==4){
            return view('MenuSuperAdmin.dashboard');

         } else{
            return redirect('/dashboard');
         }

    }

    public function datosOrgani(Request $request){
        $organizacion = DB::table('organizacion as or')
        ->leftJoin('empleado as e','or.organi_id','=','e.organi_id')
        ->select('or.organi_razonSocial as category','or.organi_nempleados as first', DB::raw('IF(e.emple_id is null, 0, COUNT(e.emple_id)) as second'))
        ->where('e.emple_id',null)
        ->groupBy('or.organi_id');
            $organizacion1 = DB::table('organizacion as or')
            ->leftJoin('empleado as e','or.organi_id','=','e.organi_id')
            ->select('or.organi_razonSocial as category','or.organi_nempleados as first', DB::raw('IF(e.emple_id is null, 0, COUNT(e.emple_id)) as second'))
            ->where('e.emple_estado',1)
            ->groupBy('or.organi_id')
            ->union($organizacion)
            ->paginate(7);

            return $organizacion1;

    }
}
