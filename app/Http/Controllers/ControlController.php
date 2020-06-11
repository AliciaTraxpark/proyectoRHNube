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
        ->join('control as c','c.Proyecto_Proye_id','=','pr.Proye_id')
        ->join('envio as en',function($join){
            $join->on('en.idEnvio','=','c.idEnvio')
            ->on('en.idEmpleado','=','e.emple_id');
        })
        ->select('e.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','en.Total_Envio','c.Fecha_fin')
        ->groupBy('en.idEmpleado')
        ->get();
        return view('tareas.reporteSemanal',['empleado'=>$empleado]);
    }

    public function EmpleadoReporte(Request $request){
        $fecha = $request->get('fecha');
        $fechaF = explode("a",$fecha);

        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
            ->join('proyecto as pr','pr.Proye_id','=','pe.Proyecto_Proye_id')
            ->select('e.emple_id','p.perso_nombre as nombre','p.perso_apPaterno as apPaterno','p.perso_apMaterno as apMaterno')
            ->groupBy('e.emple_id')
            ->get();

        $horasTrabajadas = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
        ->join('proyecto as pr','pr.Proye_id','=','pe.Proyecto_Proye_id')
        ->leftJoin('control as c','c.Proyecto_Proye_id','=','pr.Proye_id')
        ->leftJoin('envio as en',function($join){
            $join->on('en.idEnvio','=','c.idEnvio')
            ->on('en.idEmpleado','=','e.emple_id');
        })
        ->select('e.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno',DB::raw('MAX(en.Total_Envio) as Total_Envio'),'c.Fecha_fin as fechaF')
        ->where('c.Fecha_fin','<=',$fechaF[1])
        ->where('c.Fecha_fin','>=',$fechaF[0])
        ->groupBy('c.Fecha_fin','e.emple_id')
        ->get();

        $respuesta = [];
        foreach($empleados as $empleado){
            array_push($respuesta,array("id"=>$empleado->emple_id,"nombre"=>$empleado->nombre,"apPaterno"=>$empleado->apPaterno,
            "apMaterno"=>$empleado->apMaterno,"horas"=>array(),"fechaF"=>array()));
        }
        for($i = 0; $i < sizeof($horasTrabajadas); $i++){
            for($j = 0; $j < sizeof($respuesta); $j++){
                if($respuesta[$j]["id"] == $horasTrabajadas[$i]->emple_id){
                    array_push($respuesta[$j]["horas"], $horasTrabajadas[$i]->Total_Envio);
                    array_push($respuesta[$j]["fechaF"],$horasTrabajadas[$i]->fechaF);
                }
            }
        }
        return response()->json($respuesta,200);
    }

    public function proyecto(Request $request){
        $idempleado=$request->get('value');
        $proyecto = DB::table('empleado as e')
            ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
            ->join('proyecto as p','p.Proye_id','=','pe.Proyecto_Proye_id')
            ->select('P.Proye_id','P.Proye_Nombre')
            ->where('e.emple_id','=',$idempleado)
            ->get();
        return response()->json($proyecto,200);
    }

    public function show(Request $request){
        $idempleado=$request->get('value');
        $fecha=$request->get('fecha');
        $proyecto=$request->get('proyecto');
        if($proyecto != ''){
            $control = DB::table('empleado as e')
            ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
            ->join('proyecto as p','p.Proye_id','=','pe.Proyecto_Proye_id')
            ->join('control as c','c.Proyecto_Proye_id','=','p.Proye_id')
            ->join('envio as en','en.idEnvio','=','c.idEnvio')
            ->join('captura as cp','cp.idEnvio','=','en.idEnvio')
            ->select('P.Proye_id','P.Proye_Nombre','c.hora_ini','c.hora_fin','cp.imagen','en.hora_Envio','c.Fecha_fin')
            ->where('e.emple_id','=',$idempleado)
            ->where('en.idEmpleado','=',$idempleado)
            ->where('c.Fecha_fin','=',$fecha)
            ->Where('P.Proye_id','=',$proyecto)
            ->orderBy('c.Fecha_fin','asc')
            ->orderBy('c.hora_ini','asc')
            ->get();
            return response()->json($control,200);
        }
        $control = DB::table('empleado as e')
            ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
            ->join('proyecto as p','p.Proye_id','=','pe.Proyecto_Proye_id')
            ->join('control as c','c.Proyecto_Proye_id','=','p.Proye_id')
            ->join('envio as en','en.idEnvio','=','c.idEnvio')
            ->join('captura as cp','cp.idEnvio','=','en.idEnvio')
            ->select('P.Proye_id','P.Proye_Nombre','c.hora_ini','c.hora_fin','cp.imagen','en.hora_Envio','c.Fecha_fin')
            ->where('e.emple_id','=',$idempleado)
            ->where('en.idEmpleado','=',$idempleado)
            ->where('c.Fecha_fin','=',$fecha)
            ->orderBy('c.Fecha_fin','asc')
            ->orderBy('c.hora_ini','asc')
            ->get();
            return response()->json($control,200);
    }
}
