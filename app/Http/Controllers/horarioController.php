<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\empleado;
use App\paises;
use App\ubigeo_peru_departments;
use Illuminate\Support\Facades\DB;
class horarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    //
    public function index(){
        $paises=paises::all();
        $departamento=ubigeo_peru_departments::all();

        return view('horarios.horarios',['pais'=>$paises,'departamento'=>$departamento]);
    }
    public function verEmpleado(Request $request){
        $idsEm=$request->ids;
        $empleado = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','e.emple_nDoc','p.perso_id','e.emple_id')
        ->whereIn('emple_id',explode(",",$idsEm))->get();

        return $empleado;
    }
    public function verTodEmpleado(Request $request){
        $empleados = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','e.emple_nDoc','p.perso_id','e.emple_id')
        ->get();
        return $empleados;
    }
    public function guardarEventos(Request $request){
        
    }
}
