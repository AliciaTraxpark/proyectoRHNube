<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\empleado;
use App\paises;
use App\ubigeo_peru_departments;
use Illuminate\Support\Facades\DB;
use App\temporal_eventos;
use App\horario_dias;
use App\horario;
use App\horario_empleado;
use Illuminate\Support\Facades\Auth;
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
        ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','e.emple_nDoc','p.perso_id','e.emple_id','he.empleado_emple_id')
        ->leftJoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
        ->whereNull('he.empleado_emple_id')
        ->get();
        return $empleados;

    }
    public function guardarEventos(Request $request){
        $pais=$request->pais;
        $departamento=$request->departamento;
        $datafecha=$request->fechasArray;
        $horas=$request->hora;
        $inicio=$request->inicio;
        $fin=$request->fin;
        foreach($datafecha as $datafechas){
        $temporal_eventos=new temporal_eventos();
        $temporal_eventos->title=$horas;
        $temporal_eventos->start=$datafechas;
        $temporal_eventos->color='#ffffff';
        $temporal_eventos->textColor='000000';
        $temporal_eventos->users_id=Auth::user()->id;
        $temporal_eventos->paises_id=$pais;
        $temporal_eventos->ubigeo_peru_departments_id=$departamento;
        $temporal_eventos->temp_horaI=$inicio;
        $temporal_eventos->temp_horaF=$fin;
        $temporal_eventos->save();
        }

    }
    public function eventos(){
        $eventos=DB::table('eventos')->select(['id','title' ,'color', 'textColor', 'start','end']);

        $eventos_usuario = DB::table('eventos_usuario')
        ->select(['id','title' ,'color', 'textColor', 'start','end'])
             ->where('Users_id','=',Auth::user()->id)
             ->where('evento_departamento','=',null)
             ->where('evento_pais','=',173)
                ->union($eventos);

        $temporal_eventos=DB::table('temporal_eventos')->select(['id','title' ,'color', 'textColor', 'start','end'])
        ->where('users_id','=',Auth::user()->id)
        ->union($eventos_usuario)
        ->get();

        return response()->json($temporal_eventos);
    }
    public function guardarEventosBD(Request $request){
        $idemps=$request->idemps;
        $sobretiempo=$request->sobretiempo;
        $tipHorario=$request->tipHorario;
        $descripcion=$request->descripcion;
        $toleranciaH=$request->toleranciaH;
        $temporal_evento=  temporal_eventos::where('users_id','=',Auth::user()->id)->get();
        $idasignar = collect();
        foreach($temporal_evento as $temporal_eventos)
        {   $horario_dias=new horario_dias();
            $horario_dias->title=$temporal_eventos->title;
            $horario_dias->start=$temporal_eventos->start;
            $horario_dias->color=$temporal_eventos->color;
            $horario_dias->textColor=$temporal_eventos->textColor;
            $horario_dias->users_id=$temporal_eventos->users_id;
            $horario_dias->paises_id=$temporal_eventos->paises_id;
            $horario_dias->ubigeo_peru_departments_id=$temporal_eventos->ubigeo_peru_departments_id;
            $horario_dias->horaI=$temporal_eventos->temp_horaI;
            $horario_dias->horaF= $temporal_eventos->temp_horaF;

            $horario_dias->save();
            //$contar=$temporal_eventos->count();
            //$idasignar=add($horario_dias->id);


            $idasignar->push($horario_dias->id);
 $temporal_evento->each->delete();
            //$idsh = $horario_dias->id;
            //return($idsh);
            //return($horario_dias->where('id','=',$horario_dias->id)->get());




        }

         //dd($idasignar);

  /*       $array = array();
        foreach( $idasignar as $t){

        $array[] = $t;

        }
        $horario_pe=$horario_dias->get(); */




        //return($horario_dias->where('id','=',$horario_dias->id)->get());
        $horario=new horario();
        $horario->horario_sobretiempo=$sobretiempo;
        $horario->horario_tipo=$tipHorario;
        $horario->horario_descripcion=$descripcion;
        $horario->horario_tolerancia=$toleranciaH;
        $horario->save();

       foreach($idemps as $idempleados){
            foreach($idasignar as $horariosdias){
            $horario_empleado=new horario_empleado();
            $horario_empleado->horario_horario_id=$horario->horario_id;
            $horario_empleado->empleado_emple_id=$idempleados;
            $horario_empleado->horario_dias_id=$horariosdias;
            $horario_empleado->save();
            }
        }

        }

        public function tablaHorario(){
            $tabla_empleado1 = DB::table('empleado as e')
                ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->leftJoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','c.cargo_descripcion',
                'a.area_descripcion','cc.centroC_descripcion','e.emple_id','he.horario_horario_id')
                ->distinct('e.emple_id')
                ->get();
                //dd($tabla_empleado);
            return view('horarios.tablaEmpleado',['tabla_empleado'=> $tabla_empleado1]);
        }

        public function verDataEmpleado(Request $request){
        $idsEm=$request->ids;
        $empleado = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','e.emple_nDoc','p.perso_id','e.emple_id','hd.paises_id','hd.ubigeo_peru_departments_id',
        'hor.horario_sobretiempo','hor.horario_tipo','hor.horario_descripcion','hor.horario_tolerancia')
        ->join('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
        ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
        ->join('horario as hor', 'he.horario_horario_id', '=', 'hor.horario_id')
        ->distinct('e.emple_id')
        ->where('emple_id','=',$idsEm)->get();

        return $empleado;
    }
    public function empleadoHorario(Request $request){
        $idEm=$request->ids;
        $eventos=DB::table('eventos')->select(['id','title' ,'color', 'textColor', 'start','end']);

        $eventos_usuario = DB::table('eventos_usuario')
        ->select(['id','title' ,'color', 'textColor', 'start','end'])
             ->where('Users_id','=',Auth::user()->id)
             ->where('evento_departamento','=',null)
             ->where('evento_pais','=',173)
                ->union($eventos);

        $horario_empleado=DB::table('horario_empleado as he')->select(['id','title' ,'color', 'textColor', 'start','end'])
        ->where('users_id','=',Auth::user()->id)
        ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
        ->where('he.empleado_emple_id','=',$idEm)
        ->union($eventos_usuario)
        ->get();

        return response()->json($horario_empleado);
    }

    }

