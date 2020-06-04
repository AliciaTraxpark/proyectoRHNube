<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\control;
use Illuminate\Support\Facades\DB;
use App\empleado;

class ControlController extends Controller
{
    public function index(){
        $empleado = DB::table('empleado as e')
        ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->select('e.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
        ->groupBy('p.perso_id')
        ->get();
        return view('tareas.tareas',['empleado'=>$empleado]);
    }

    public function store(Request $request){
        $control = new control();
        $control->Proyecto_Proye_id=$request->get('Proyecto_Proye_id');
        $control->fecha_i=$request->get('fecha_i');
        $control->fecha_f=$request->get('fecha_f');
        $control->hora_i=$request->get('hora_i');
        $control->hora_f=$request->get('hora_f');
        $control->Imag=$request->get('Imag');
        $control->save();

        return response()->json($control,200);

    }

    public function show(Request $request){
        $idempleado=$request->get('value');
        $control = DB::table('empleado as e')
            ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
            ->join('proyecto as p','p.Proye_id','=','Proyecto_Proye_id')
            ->join('control as c','c.Cont_id','=','p.Proye_id')
            ->select('c.hora_i','c.hora_f','c.Imag')
            ->where('e.emple_id','=',$idempleado)
            ->get();
            return response()->json($control,200);
    }
}
