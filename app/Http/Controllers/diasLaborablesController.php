<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\empleado;
use App\eventos_empleado;
use App\incidencias;
use App\incidencia_dias;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class diasLaborablesController extends Controller
{
    //
    public function indexMenu(){
        $empleado = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->where('e.emple_estado', '=', 1)
        ->where('users_id','=',Auth::user()->id)
        ->get();
        return View('horarios.diasLaborales',['empleado'=>$empleado]);
    }
    public function storeCalendario(Request $request){

        $idempleado=$request->idempleado;
        $arrayeve = collect();
        foreach($idempleado as $idempleados){
            $eventos_empleado = new eventos_empleado();
            $eventos_empleado->title =$request->get('title');
            $eventos_empleado->color =$request->get('color');
            $eventos_empleado->textColor =$request->get('textColor');
            $eventos_empleado->start =$request->get('start');
            $eventos_empleado->end =$request->get('end');
            $eventos_empleado->tipo_ev =$request->get('tipo');
            $eventos_empleado->id_empleado =$idempleados;
            $eventos_empleado->save();
            $arrayeve->push($eventos_empleado->evEmpleado_id);
        }
        return $arrayeve ;
    }
    public function eliminarBD(Request $request){
        $ideve= $request->ideve;

          $eventos_empleado = eventos_empleado::whereIn('evEmpleado_id', explode(",",$ideve))->get();
          $eventos_empleado->each->delete();

    }
    public function diasIncidempleado(Request $request)
    {
        $idempleado=$request->get('idempleado');
        $incidencia = new incidencias();
        $incidencia->inciden_descripcion =  $request->get('title');
        $incidencia->inciden_descuento = $request->get('descuentoI');
        $incidencia->inciden_hora =  $request->get('horaIn');
        $incidencia->users_id = Auth::user()->id;
        $incidencia->save();
        foreach($idempleado as $idempleados){
        $incidencia_dias = new incidencia_dias();
        $incidencia_dias->id_incidencia = $incidencia->inciden_id;
        $incidencia_dias->inciden_dias_fechaI = $request->get('start');
        $incidencia_dias->inciden_dias_fechaF = $request->get('end');
        $incidencia_dias->id_empleado = $idempleados;
        $incidencia_dias->save();
        }

        return $incidencia;

    }
}
