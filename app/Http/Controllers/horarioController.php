<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\empleado;
use Illuminate\Support\Facades\DB;
class horarioController extends Controller
{
    //
    public function index(){
        return view('horarios.horarios');
    }
    public function verEmpleado(Request $request){
        $idsEm=$request->ids;
        $empleado = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','e.emple_nDoc','p.perso_id')
        ->whereIn('emple_id',explode(",",$idsEm))->get();

        return $empleado;
    }
}
