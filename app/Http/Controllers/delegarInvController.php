<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\empleado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
}
