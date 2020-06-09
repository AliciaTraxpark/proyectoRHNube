<?php

namespace App\Http\Controllers;

use App\captura;
use Illuminate\Http\Request;
use App\control;
use Illuminate\Support\Facades\DB;
use App\empleado;
use App\envio;

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

    public function ReporteS(){
        $empleado = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
        ->join('proyecto as pr','pr.Proye_id','=','pe.Proyecto_Proye_id')
        ->join('envio as en','en.idEmpleado','=','e.emple_id')
        ->join('control as c','c.idEnvio','=','en.idEnvio')
        ->select('e.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','en.Total_Envio','c.Fecha_fin')
        ->orderBy('c.Fecha_fin','asc')
        ->orderBy('en.Total_Envio','desc')
        ->take(1)
        ->get();
        return view('tareas.reporteSemanal',['empleado'=>$empleado]);
    }
    public function store(Request $request){
        $envio = new envio();
        $envio->hora_Envio=$request->get('hora_Envio');
        $envio->Total_Envio=$request->get('Total_Envio');
        $envio->idEmpleado=$request->get('idEmpleado');
        $envio->save();
        $idEnvio=$envio->idEnvio;

        $captura = new captura();
        $captura->idEnvio=$idEnvio;
        $captura->estado=$request->get('estado');
        $captura->fecha_hora=$request->get('fecha_hora');
        $captura->imagen=$request->get('imagen');
        $captura->save();

        $control = new control();
        $control->Proyecto_Proye_id=$request->get('Proyecto_Proye_id');
        $control->fecha_ini=$request->get('fecha_ini');
        $control->Fecha_fin=$request->get('Fecha_fin');
        $control->hora_ini=$request->get('hora_ini');
        $control->hora_fin=$request->get('hora_fin');
        $control->idEnvio=$idEnvio;
        $control->save();

        return response()->json($control,200);

    }

    public function show(Request $request){
        $idempleado=$request->get('value');
        $control = DB::table('empleado as e')
            ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
            ->join('proyecto as p','p.Proye_id','=','pe.Proyecto_Proye_id')
            ->join('control as c','c.Proyecto_Proye_id','=','p.Proye_id')
            ->join('envio as en','en.idEnvio','=','c.idEnvio')
            ->join('captura as cp','cp.idEnvio','=','en.idEnvio')
            ->select('P.Proye_Nombre','c.hora_ini','c.hora_fin','cp.imagen','en.hora_Envio','c.Fecha_fin')
            ->where('e.emple_id','=',$idempleado)
            ->orderBy('c.Fecha_fin','asc')
            ->orderBy('c.hora_ini','asc')
            ->get();
            return response()->json($control,200);
    }
}
