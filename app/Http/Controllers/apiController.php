<?php

namespace App\Http\Controllers;

use App\actividad;
use App\captura;
use App\control;
use App\envio;
use App\proyecto;
use App\proyecto_empleado;
use App\tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class apiController extends Controller
{
    public function api(){
        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','c.cargo_descripcion',
            'a.area_descripcion','cc.centroC_descripcion','e.emple_id')
            ->get();
            return $empleado;
    }

    public function logueoEmpleado(Request $request){
       $pass = DB::table('empleado as e')
        ->select('e.emple_pasword')
        ->where('e.emple_nDoc','=',$request->get('emple_nDoc'))
        ->get();

        if(count($pass)==0)  return response()->json(null,404);
        //if(password_verify($request->get("emple_pasword"),$pass[0]->emple_pasword)){
        if(Hash::check($request->get("emple_pasword"), $pass[0]->emple_pasword)){
            $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
            ->select('e.emple_id',DB::raw('CONCAT(p.perso_nombre ," ", p.perso_apPaterno, " ", p.perso_apMaterno) AS nombre'),'pe.proye_empleado_id','e.emple_estado')
            ->where('e.emple_nDoc','=',$request->get('emple_nDoc'))
            ->get();
            $factory = JWTFactory::customClaims([
                'sub' => env('API_ID'),
            ]);
            $payload = $factory->make();
            $token = JWTAuth::encode($payload);
            return response()->json(array('data' => $empleado, 'token' => $token->get()),200);
        }else{
            return response()->json(null,403);
        }
    }

    public function apiTarea(Request $request){
        $Proye_id = $request['Proye_id'];
        $proyecto = proyecto::where('Proye_id',$Proye_id)->first();
        if($proyecto){
            $tarea = new tarea();
            $tarea->Tarea_Nombre=$request['Tarea_Nombre'];
            $tarea->Proyecto_Proye_id=$Proye_id;
            $tarea->empleado_emple_id=$request['emple_id'];
            $tarea->save();
            $Tarea_Tarea_id=$tarea->Tarea_id;
            if($request['Activi_Nombre'] != ""){
                $actividad = new actividad();
                $actividad->Activi_Nombre=$request['Activi_Nombre'];
                $actividad->Tarea_Tarea_id=$Tarea_Tarea_id;
                $actividad->empleado_emple_id=$request['emple_id'];
                $actividad->save();
                return response()->json([$tarea,$actividad],200);
            }
            return response()->json($tarea,200);
        }

        return response()->json($proyecto,400);
    }

    public function apiActividad(Request $request){
        $actividad = new actividad();
        $actividad->Activi_Nombre=$request['Activi_Nombre'];
        $actividad->Tarea_Tarea_id=$request['Tarea_Tarea_id'];
        $actividad->empleado_emple_id=$request['emple_id'];
        $actividad->save();
        return response()->json($actividad,200);
    }

    public function editarApiTarea(Request $request){
        $Tarea_id = $request['Tarea_id'];
        $Activi_id = $request['Activi_id'];
        $tarea = tarea::where('Tarea_id',$Tarea_id)->first();
        if($tarea){
            $tarea->Tarea_Nombre=$request['Tarea_Nombre'];
            if($request['Activi_id'] != ''){
                $actividad = actividad::where('Activi_id',$Activi_id)->first();
                if($actividad){
                    $actividad->Activi_Nombre=$request['Activi_Nombre'];
                    $actividad->save();
                    return response()->json($actividad,200);
                }
            }
            $tarea->save();
            return response()->json($tarea,200);
        }
        return response()->json($tarea,400);
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
        if($request->get('Tarea_Tarea_id') != ''){
            $control->Tarea_Tarea_id=$request->get('Tarea_Tarea_id');
        }
        if($request->get('Actividad_Activi_id') != ''){
            $control->Actividad_Activi_id=$request->get('Actividad_Activi_id');
        }
        $control->save();

        return response()->json($control,200);

    }

    public function selectProyecto(Request $request){
        $empleado = $request->get('emple_id');
        $proyecto = $request->get('proye_id');

        $proyecto_empleado = proyecto_empleado::where('Proye_empleado_id',$empleado,'Proyecto_Proye_id',$proyecto)->first();

        if($proyecto_empleado){
            $datos = DB::table('empleado as e')
                ->join('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
                ->join('proyecto as pr','pr.Proye_id','=','pe.Proyecto_Proye_id')
                ->leftJoin('tarea as t','t.Proyecto_Proye_id','=','pr.Proye_id')
                ->leftJoin('actividad as ac','a.Tarea_Tarea_id','=','t.Tarea_id')
                ->select('pr.Proye_Nombre','t.Tarea_Nombre','ac.Activi_Nombre')
                ->get();
            return response()->json($datos,200);
        }
    }
}
