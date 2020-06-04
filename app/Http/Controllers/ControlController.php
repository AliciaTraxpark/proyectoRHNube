<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\control;
use Illuminate\Support\Facades\DB;

class ControlController extends Controller
{
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
        $control = DB::table('control as c')
            ->select('c.hora_i','c.hora_f','c.Imag')
            ->get();
        return view('tareas.tareas',['control'=>$control]);
    }
}
