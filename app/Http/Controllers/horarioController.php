<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\empleado;
use App\eventos_usuario;
use App\eventos;
use App\paises;
use App\ubigeo_peru_departments;
use Illuminate\Support\Facades\DB;
use App\temporal_eventos;
use App\horario_dias;
use App\horario;
use App\horario_empleado;
use App\incidencias;
use App\incidencia_dias;
use App\User;
use App\eventos_empleado;
use Illuminate\Support\Facades\Auth;

class horarioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    //
    public function index()
    {
        $paises = paises::all();
        $departamento = ubigeo_peru_departments::all();
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();
        $horario=horario::where('user_id', '=', Auth::user()->id)->get();
        $horarion=DB::table('horario as h')
        ->leftJoin('horario_empleado as he','h.horario_id','=','he.horario_horario_id')
        ->where('h.user_id', '=', Auth::user()->id)
        ->whereNull('he.horario_horario_id')
        ->get();
        $area=DB::table('area')
        ->select('area_id as idarea', 'area_descripcion as descripcion')
        ->get();
        $cargo=DB::table('cargo')
        ->select('cargo_id as idcargo', 'cargo_descripcion as descripcion')
        ->get();
        $local=DB::table('local')
        ->select('local_id as idlocal', 'local_descripcion as descripcion')
        ->get();
        return view('horarios.horarios', ['pais' => $paises, 'departamento' => $departamento, 'empleado' => $empleado,'horario'=>$horario,'horarion'=>$horarion,
        'area'=>$area,'cargo'=>$cargo,'local'=>$local]);
    }
    public function verTodEmpleado(Request $request)
    {
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id', 'he.empleado_emple_id')
            ->leftJoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
            // ->whereNull('he.empleado_emple_id')
            ->distinct('e.emple_id')
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();
        return $empleados;
    }
    public function guardarEventos(Request $request)
    {
        $pais = $request->pais;
        $departamento = $request->departamento;
        $datafecha = $request->fechasArray;
        $horas = $request->hora;
        $inicio = $request->inicio;
        $fin = $request->fin;
        $idhorar = $request->idhorar;
        $arrayeve = collect();
        foreach ($datafecha as $datafechas) {
            $temporal_eventos = new temporal_eventos();
            $temporal_eventos->title = $horas;
            $temporal_eventos->start = $datafechas;
            $temporal_eventos->color = '#ffffff';
            $temporal_eventos->textColor = '111111';
            $temporal_eventos->users_id = Auth::user()->id;
            $temporal_eventos->paises_id = $pais;
            $temporal_eventos->ubigeo_peru_departments_id = $departamento;
            $temporal_eventos->temp_horaI = $inicio;
            $temporal_eventos->temp_horaF = $fin;
            $temporal_eventos->id_horario = $idhorar;
            $temporal_eventos->save();
            $arrayeve->push($temporal_eventos);
        }


        $temp = DB::table('temporal_eventos as te')
        ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
       /*  return  response()->json($arrayeve); */
       return  response()->json($temp);
    }
    public function eventos()
    {
        $temporal_eventos = DB::table('temporal_eventos')->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();

        return response()->json($temporal_eventos);
    }
    public function guardarHorarioBD(Request $request)
    {
        $sobretiempo = $request->sobretiempo;

        $descripcion = $request->descripcion;
        $toleranciaH = $request->toleranciaH;
        $inicio = $request->inicio;
        $fin = $request->fin;

        $horario = new horario();
        $horario->horario_sobretiempo = $sobretiempo;

        $horario->horario_descripcion = $descripcion;
        $horario->horario_tolerancia = $toleranciaH;
        $horario->horaI = $inicio;
        $horario->horaF = $fin;
        $horario->user_id = Auth::user()->id;
        $horario->save();
        return $horario;

    }

    public function tablaHorario()
    {   $horario=horario::where('user_id', '=', Auth::user()->id)
        ->leftJoin('horario_empleado as he', 'horario.horario_id', '=', 'he.horario_horario_id')
        ->groupBy('horario.horario_id')
        ->get();

       return view('horarios.tablaHorario',['horario'=>$horario]);
    }

    public function verDataEmpleado(Request $request)
    {
        $idsEm = $request->ids;
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select(
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'e.emple_nDoc',
                'p.perso_id',
                'e.emple_id',
                'hd.paises_id',
                'hd.ubigeo_peru_departments_id',
                'hor.horario_sobretiempo',
                'hor.horario_tipo',
                'hor.horario_descripcion',
                'hor.horario_tolerancia',
                'e.emple_nDoc',
                'e.emple_Correo',
                'e.emple_celular',
                'a.area_descripcion',
                'c.cargo_descripcion',
                'cc.centroC_descripcion',
                'lo.local_descripcion'
            )
            ->leftJoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
            ->leftJoin('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->leftJoin('horario as hor', 'he.horario_horario_id', '=', 'hor.horario_id')
            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->leftJoin('local as lo', 'e.emple_local', '=', 'lo.local_id')

            ->distinct('e.emple_id')
            ->where('emple_id', '=', $idsEm)->get();
        if (count($empleado) >= 1) {

            $iddepar = $empleado[0]->ubigeo_peru_departments_id;
            //
            $eventos_empleado = DB::table('eventos_empleado')
            ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('id_empleado', '=', $idsEm);

            $horario_empleado = DB::table('horario_empleado as he')->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
               /*  ->where('users_id', '=', Auth::user()->id) */
                ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                ->where('he.empleado_emple_id', '=', $idsEm)
                ->union($eventos_empleado);


            $incidencias = DB::table('incidencias as i')
                ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_descuento as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
                ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
                ->where('i.emple_id', '=', $idsEm)
                ->union($horario_empleado)
                ->get();



                $horarioEmpleado= DB::table('horario_empleado as he')
                -> select ('hd.title','h.horaI','h.horaF', 'h.horaF','he.empleado_emple_id')
                ->where('users_id', '=', Auth::user()->id)
                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                ->where('he.empleado_emple_id', '=', $idsEm)
                ->groupBy('h.horario_id')
                ->get();
                if($horarioEmpleado){
                    return [$empleado, $incidencias,$horarioEmpleado];
                }
                else{return [$empleado, $incidencias,0];}

        } /* else {
            $eventos1 = DB::table('eventos')->select(['id', 'title', 'color', 'textColor', 'start', 'end']);

            $eventos_usuario1 = DB::table('eventos_usuario')
                ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
                ->where('Users_id', '=', Auth::user()->id)
                ->where('evento_departamento', '=', null)
                ->where('evento_pais', '=', 173)
                ->union($eventos1)
                ->get();
            return [$eventos_usuario1, $eventos_usuario1];
        } */
    }
    public function vaciartemporal()
    {
        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id)->delete();
    }


    public function empleadosIncidencia(Request $request)
    {
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id', 'he.empleado_emple_id')
            ->join('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
            ->distinct('e.emple_id')
            ->get();
        return $empleados;
    }

    public function registrarIncidencia(Request $request)
    {
        $idempl = $request->idempleadoI;

        $inc_dias = new incidencia_dias();
        $inc_dias->inciden_dias_fechaI = $request->fechaI;
        $inc_dias->inciden_dias_fechaF = $request->fechaF;
        $inc_dias->inciden_dias_hora = $request->horaIn;
        $inc_dias->save();

        foreach ($idempl as $idempls) {
            $incidencia = new incidencias();
            $incidencia->inciden_descripcion = $request->descripcionI;
            $incidencia->inciden_descuento = $request->descuentoI;
            $incidencia->inciden_dias_id = $inc_dias->inciden_dias_id;
            $incidencia->emple_id = $idempls;
            $incidencia->save();
        }
    }
    public function indexMenu()
    {
        $paises = paises::all();
        $departamento = ubigeo_peru_departments::all();
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();
            $horario=horario::where('user_id', '=', Auth::user()->id)->get();
            $horarion=DB::table('horario as h')
        ->leftJoin('horario_empleado as he','h.horario_id','=','he.horario_horario_id')
        ->where('h.user_id', '=', Auth::user()->id)
        ->whereNull('he.horario_horario_id')
        ->get();
        $area=DB::table('area')
        ->select('area_id as idarea', 'area_descripcion as descripcion')
        ->get();
        $cargo=DB::table('cargo')
        ->select('cargo_id as idcargo', 'cargo_descripcion as descripcion')
        ->get();
        $local=DB::table('local')
        ->select('local_id as idlocal', 'local_descripcion as descripcion')
        ->get();

        return view('horarios.horarioMenu', ['pais' => $paises, 'departamento' => $departamento, 'empleado' => $empleado,'horario'=>$horario,'horarion'=>$horarion,
        'area'=>$area,'cargo'=>$cargo,'local'=>$local]);
    }

    public function eliminarHora(Request $request)
    {
        $idHora = $request->idHora;

            $temporal_evento = temporal_eventos::where('id', '=', $idHora)->delete();


    }
    public function cambiarEstado(Request $request)
    {

        $user = User::where('id', '=', Auth::user()->id)
            ->update(['user_estado' => 1]);
    }

    public function storeDescanso(Request $request)
    {
        //

        $temporal_eventos = new temporal_eventos();
        $temporal_eventos->title = $request->get('title');
        $temporal_eventos->color = '#e5e5e5';
        $temporal_eventos->textColor = '#3f51b5';
        $temporal_eventos->start = $request->get('start');
        $temporal_eventos->end = $request->get('end');
        $temporal_eventos->paises_id = $request->get('pais');
        $temporal_eventos->ubigeo_peru_departments_id = $request->get('departamento');
        $temporal_eventos->users_id = Auth::user()->id;
        $temporal_eventos->save();

        $temp = DB::table('temporal_eventos as te')
        ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
       /*  return  response()->json($arrayeve); */
       return  response()->json($temp);

    }
    public function storeLabor(Request $request)
    {
        //
        $temporal_eventos =[];
        $temporal_eventos = new temporal_eventos();
        $temporal_eventos->title = $request->get('title');
        $temporal_eventos->color = '#dfe6f2';
        $temporal_eventos->textColor = '#0b1b29';
        $temporal_eventos->start = $request->get('start');
        $temporal_eventos->end = $request->get('end');
        $temporal_eventos->paises_id = $request->get('pais');
        $temporal_eventos->ubigeo_peru_departments_id = $request->get('departamento');
        $temporal_eventos->users_id = Auth::user()->id;
        $temporal_eventos->save();

        $temp = DB::table('temporal_eventos as te')
        ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
       /*  return  response()->json($arrayeve); */
       return  response()->json($temp);
    }
    public function storeNoLabor(Request $request)
    {
        //
        $temporal_eventos =[];
        $temporal_eventos = new temporal_eventos();
        $temporal_eventos->title = $request->get('title');
        $temporal_eventos->color = '#a34141';
        $temporal_eventos->textColor = '#fff7f7';
        $temporal_eventos->start = $request->get('start');
        $temporal_eventos->end = $request->get('end');
        $temporal_eventos->paises_id = $request->get('pais');
        $temporal_eventos->ubigeo_peru_departments_id = $request->get('departamento');
        $temporal_eventos->users_id = Auth::user()->id;
        $temporal_eventos->save();

        $temp = DB::table('temporal_eventos as te')
        ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
       /*  return  response()->json($arrayeve); */
       return  response()->json($temp);
    }

    public function storeIncidencia(Request $request)
    {

        $temporal_eventos = new temporal_eventos();
        $temporal_eventos->title = $request->get('title');
        $temporal_eventos->color = '#9E9E9E';
        $temporal_eventos->textColor = '#313131';
        $temporal_eventos->start = $request->get('start');
        $temporal_eventos->end = $request->get('end');
        $temporal_eventos->paises_id = $request->get('pais');
        $temporal_eventos->ubigeo_peru_departments_id = $request->get('departamento');
        $temporal_eventos->temp_horaI = $request->get('horaIn');
        $temporal_eventos->temp_horaF = $request->get('descuentoI');
        $temporal_eventos->users_id = Auth::user()->id;
        $temporal_eventos->save();

        $temp = DB::table('temporal_eventos as te')
        ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
       /*  return  response()->json($arrayeve); */
       return  response()->json($temp);
    }

    public function guardarHorarioC(Request $request){
        $idemps = $request->idemps;
        $temporal_eventoH =  temporal_eventos::where('users_id', '=', Auth::user()->id)
        ->where('id_horario', '!=', null)->where('temp_horaF', '=', null)->get();
        $idasignar = collect();
        $idhors=collect();
        if($temporal_eventoH){
            foreach ($temporal_eventoH as $temporal_eventosH) {
                $horario_dias = new horario_dias();
                $horario_dias->title = $temporal_eventosH->title;
                $horario_dias->start = $temporal_eventosH->start;
                $horario_dias->end = $temporal_eventosH->end;
                $horario_dias->color = $temporal_eventosH->color;
                $horario_dias->textColor = $temporal_eventosH->textColor;
                $horario_dias->users_id = $temporal_eventosH->users_id;

                $horario_dias->save();

             foreach ($idemps as $idempleados) {
                   $horario_empleado = new horario_empleado();
                    $horario_empleado->horario_horario_id =  $temporal_eventosH->id_horario;
                    $horario_empleado->empleado_emple_id = $idempleados;
                    $horario_empleado->horario_dias_id = $horario_dias->id;
                    $horario_empleado->save();
            }}
        }
        $temporal_evento= temporal_eventos::where('users_id', '=', Auth::user()->id)
        ->where('id_horario', '=', null)->where('temp_horaF', '=', null)->where('textColor', '!=', '#0b1b29')
        ->where('textColor', '!=', '#fff7f7')->get();
        if($temporal_evento){
            $empleadoN = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->distinct('e.emple_id')

                        ->join('eventos_empleado as ve', 'e.emple_id', '=', 've.id_empleado')
                        ->pluck('e.emple_id');
                        $integerIDs = array_map('intval',$idemps);

                        $nu=Arr::flatten($empleadoN);
                        $filtro = array_diff( $integerIDs,$nu);

                        $nuev=Arr::flatten($filtro);
                     //dd($nu,$integerIDs,$nuev);


                foreach ($nuev as $nuevs) {

            foreach ($temporal_evento as $temporal_eventos) {


                        $eventos_empleado=new eventos_empleado();
                        $eventos_empleado->title=$temporal_eventos->title;
                        $eventos_empleado->color=$temporal_eventos->color;
                        $eventos_empleado->textColor=$temporal_eventos->textColor;
                        $eventos_empleado->start=$temporal_eventos->start;
                        $eventos_empleado->end=$temporal_eventos->end;
                        $eventos_empleado->paises_id=$temporal_eventos->paises_id;
                        $eventos_empleado->ubigeo_peru_departments_id= $temporal_eventos->ubigeo_peru_departments_id;
                        $eventos_empleado->id_empleado=$nuevs;
                        /* if($temporal_eventos->color='#a34141'){
                        $eventos_empleado->tipo_ev=0;
                        } else{ $eventos_empleado->tipo_ev=1;} */
                        /* if($temporal_eventos->color='#4673a0'){
                            $eventos_empleado->tipo_ev=1;
                        }

                        if($temporal_eventos->color='#e6bdbd'){
                            $eventos_empleado->tipo_ev=2;
                        }

                        if($temporal_eventos->color='#dfe6f2'){
                            $eventos_empleado->tipo_ev=3;
                        } */
                        $eventos_empleado->save();


                    }


            }
        }

    $temporal_eventotextc= temporal_eventos::where('users_id', '=', Auth::user()->id)
    ->where('id_horario', '=', null)->where('temp_horaF', '=', null)->where('textColor', '=', '#0b1b29')
    ->orWhere('textColor', '=', '#fff7f7')->get();
     if($temporal_eventotextc){
         foreach( $temporal_eventotextc as $temporal_eventotextcs ){
            foreach ($idemps as $idempleados){
                $eventos_empleado=new eventos_empleado();
                        $eventos_empleado->title=$temporal_eventotextcs->title;
                        $eventos_empleado->color=$temporal_eventotextcs->color;
                        $eventos_empleado->textColor=$temporal_eventotextcs->textColor;
                        $eventos_empleado->start=$temporal_eventotextcs->start;
                        $eventos_empleado->end=$temporal_eventotextcs->end;
                        $eventos_empleado->paises_id=$temporal_eventotextcs->paises_id;
                        $eventos_empleado->ubigeo_peru_departments_id= $temporal_eventotextcs->ubigeo_peru_departments_id;
                        $eventos_empleado->id_empleado=$idempleados;
                        $eventos_empleado->save();


             }
         }
     }

    $temporal_eventoInc=  temporal_eventos::where('users_id', '=', Auth::user()->id)
    ->where('temp_horaF', '!=', null)->get();
     if($temporal_eventoInc){
        foreach ($temporal_eventoInc as $temporal_eventoIncs) {
        $inc_dias = new incidencia_dias();
        $inc_dias->inciden_dias_fechaI = $temporal_eventoIncs->start;
        $inc_dias->inciden_dias_fechaF = $temporal_eventoIncs->end;
        $inc_dias->inciden_dias_hora = $temporal_eventoIncs->temp_horaI;
        $inc_dias->save();

        foreach ($idemps as $idempleados) {
        $incidencia = new incidencias();
        $incidencia->inciden_descripcion =  $temporal_eventoIncs->title;
        $incidencia->inciden_descuento = $temporal_eventoIncs->temp_horaF;
        $incidencia->inciden_dias_id = $inc_dias->inciden_dias_id;
        $incidencia->emple_id = $idempleados;
        $incidencia->save();
        }

        }

    }
    }

    public function vaciarhor(){
        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id)
        ->where('id_horario', '!=', null)->where('temp_horaF', '=', null)->delete();
        $temporal_evento=temporal_eventos::where('users_id', '=', Auth::user()->id)->get();
        return $temporal_evento;
    }
    public function vaciardl(){
        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id)
        ->where('color', '=', '#dfe6f2')->where('textColor', '=', '#0b1b29')->delete();
        $temporal_evento=temporal_eventos::where('users_id', '=', Auth::user()->id)->get();
        return $temporal_evento;
    }
    public function vaciarndl(){
        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id)
        ->where('color', '=','#a34141')->delete();
        $temporal_evento=temporal_eventos::where('users_id', '=', Auth::user()->id)->get();
        return $temporal_evento;
    }


    public function eliminareventosHorario(Request $request){
        $idevento=$request->idevento;
        //primero ver que data llega a ver empleado
    }

    public function eliminarHorarBD(Request $request)
    {    $ide = $request->ide;
        $idHora = $request->idHora;
        $textcolor=$request->textcolor;
        if($textcolor=='111111')
       { $horario_empleado = horario_empleado::where('horario_dias_id', '=', $idHora)->get();
        $nhor=count($horario_empleado);

          if($nhor==1){
            $horario_empleado0 = horario_empleado::where('horario_dias_id', '=', $idHora)->where('empleado_emple_id', '=', $ide)->delete();
            $horario_dias=horario_dias::where('id','=',$idHora)->delete();
          }else if($nhor>1){
            $horario_empleado1 = horario_empleado::where('horario_dias_id', '=', $idHora)->where('empleado_emple_id', '=', $ide)->delete();

          }}
           else{
            $eventos_empleado=eventos_empleado::where('evEmpleado_id','=',$idHora)->delete();

          }



    }

    public function eliminarIncidBD(Request $request){
        $ide = $request->ide;
        $idHora = $request->idHora;
        $incidencias=incidencias::where('inciden_id','=',$idHora)->delete();

    }

    public function storeIncidenciaEmpleado(Request $request){

        $start = $request->start;
        $title = $request->title;
        $end = $request->end;
        $horaIn = $request->horaIn;
        $idempl = $request->idempl;
        $descuentoI=$request->descuentoI;

        $inc_dias = new incidencia_dias();
        $inc_dias->inciden_dias_fechaI =$start;
        $inc_dias->inciden_dias_fechaF = $end;
        $inc_dias->inciden_dias_hora = $horaIn;
        $inc_dias->save();

        $incidencia = new incidencias();
        $incidencia->inciden_descripcion =  $title;
        $incidencia->inciden_descuento = $descuentoI;
        $incidencia->inciden_dias_id = $inc_dias->inciden_dias_id;
        $incidencia->emple_id = $idempl;
        $incidencia->save();

        $eventos_empleado = DB::table('eventos_empleado')
        ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
        ->where('id_empleado', '=', $idempl);

        $horario_empleado = DB::table('horario_empleado as he')->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.empleado_emple_id', '=', $idempl)
            ->union($eventos_empleado);


        $incidencias = DB::table('incidencias as i')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_descuento as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
            ->where('i.emple_id', '=', $idempl)
            ->union($horario_empleado)
            ->get();
            return($incidencias);

    }

    public function storeHorarioEmBD(Request $request){
        $idempl = $request->idempl;
        $datafecha = $request->fechasArray;
        $horario = $request->hora;
        $idhorar = $request->idhorar;

        foreach($datafecha as $datafechas)
        {
            $horario_dias = new horario_dias();
            $horario_dias->title = $horario;
            $horario_dias->start = $datafechas;
            $horario_dias->color = '#ffffff';
            $horario_dias->textColor = '111111';
            $horario_dias->users_id = Auth::user()->id;
            $horario_dias->paises_id = 173;
            $horario_dias->save();
            $horario_empleado = new horario_empleado();
            $horario_empleado->horario_horario_id =  $idhorar;
            $horario_empleado->empleado_emple_id = $idempl;
            $horario_empleado->horario_dias_id = $horario_dias->id;
            $horario_empleado->save();

        }

        $eventos_empleado = DB::table('eventos_empleado')
        ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
        ->where('id_empleado', '=', $idempl);

        $horario_empleado = DB::table('horario_empleado as he')->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.empleado_emple_id', '=', $idempl)
            ->union($eventos_empleado);


        $incidencias = DB::table('incidencias as i')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_descuento as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
            ->where('i.emple_id', '=', $idempl)
            ->union($horario_empleado)
            ->get();
            return($incidencias);

    }

    public function storeLaborHorarioBD(Request $request)
    {
        $title=$request->title;
        $start=$request->start;
        $end=$request->end;
        $idempl=$request->idempl;
        //

        $eventos_empleado=new eventos_empleado();
        $eventos_empleado->title=$title;
        $eventos_empleado->color='#dfe6f2';
        $eventos_empleado->textColor='#0b1b29';
        $eventos_empleado->start=$start;
        $eventos_empleado->end=$end;
        $eventos_empleado->paises_id=173;
        $eventos_empleado->id_empleado=$idempl;
        $eventos_empleado->save();

        $eventos_empleado1 = DB::table('eventos_empleado')
        ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
        ->where('id_empleado', '=', $idempl);

        $horario_empleado = DB::table('horario_empleado as he')->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.empleado_emple_id', '=', $idempl)
            ->union($eventos_empleado1);


        $incidencias = DB::table('incidencias as i')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_descuento as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
            ->where('i.emple_id', '=', $idempl)
            ->union($horario_empleado)
            ->get();
            return($incidencias);

    }

    public function storeNoLaborHorarioBD(Request $request)
    {
        $title=$request->title;
        $start=$request->start;
        $end=$request->end;
        $idempl=$request->idempl;
        //

        $eventos_empleado=new eventos_empleado();
        $eventos_empleado->title=$title;
        $eventos_empleado->color= '#a34141';
        $eventos_empleado->textColor='#fff7f7';
        $eventos_empleado->start=$start;
        $eventos_empleado->end=$end;
        $eventos_empleado->paises_id=173;
        $eventos_empleado->id_empleado=$idempl;
        $eventos_empleado->save();

        $eventos_empleado1 = DB::table('eventos_empleado')
        ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
        ->where('id_empleado', '=', $idempl);

        $horario_empleado = DB::table('horario_empleado as he')->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.empleado_emple_id', '=', $idempl)
            ->union($eventos_empleado1);


        $incidencias = DB::table('incidencias as i')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_descuento as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
            ->where('i.emple_id', '=', $idempl)
            ->union($horario_empleado)
            ->get();
            return($incidencias);

    }

    public function incidenciatemporal(){
        $incidencias=  temporal_eventos::where('users_id', '=', Auth::user()->id)
        ->where('temp_horaF', '!=', null)->get();
        return($incidencias);

    }
    public function eliminarinctempotal(Request $request){
        $idinc = $request->idinc;

        $temporal_evento = temporal_eventos::where('id', '=', $idinc)->delete();
        $temporal=  temporal_eventos::where('users_id', '=', Auth::user()->id)
        ->get();
        return($temporal);

    }

    public function verDatahorario(Request $request){
        $idsedit=$request->idsedit;
    $horario=horario::where('user_id', '=', Auth::user()->id)
    ->where('horario_id', '=',$idsedit)
    ->get();
    return $horario;
    }

    public function actualizarhorarioed(Request $request){
        $idhorario=$request->idhorario;

        $sobretiempo=$request->sobretiempo;
        $descried=$request->descried;
        $toleed=$request->toleed;
        $horaIed=$request->horaIed;
        $horaFed=$request->horaFed;

        $horario = horario::where('horario_id', '=',$idhorario)
        ->update(['horario_sobretiempo' => $sobretiempo,
        'horario_descripcion' =>$descried,'horario_tolerancia' =>$toleed,'horaI' => $horaIed,
        'horaF' => $horaFed]);


       $horarion=DB::table('horario as h')
        ->leftJoin('horario_empleado as he','h.horario_id','=','he.horario_horario_id')
        ->where('h.user_id', '=', Auth::user()->id)
        ->get();

        return($horarion);

    }

    public function verificarID(Request $request){
        $idhorario=$request->idhorario;
        $horarion=DB::table('horario as h')
        ->leftJoin('horario_empleado as he','h.horario_id','=','he.horario_horario_id')
        ->where('h.user_id', '=', Auth::user()->id)
        ->where('h.horario_id', '=', $idhorario)
        ->get();
        if($horarion[0]->horario_horario_id!=null){
            return 1;
        } else{return 0;}


    }

    public function eliminarHorario(Request $request){
        $idhorario=$request->idhorario;
        $horario = horario::where('horario_id', '=', $idhorario)->delete();

    }

    public function empleArea(Request $request){
        $idarea=$request->idarea;
        $empleadosArea = DB::table('empleado')
        ->where('users_id', '=', Auth::user()->id)
        ->where('emple_area', '=', $idarea)
        ->get();
        return $empleadosArea;
    }

    public function empleCargo(Request $request){
        $idcargo=$request->idcargo;
       /*  dd($idcargo);  */
        $empleadosCargo = DB::table('empleado')
        ->where('users_id', '=', Auth::user()->id)
        ->where('emple_cargo', '=', $idcargo)
        ->get();
        return $empleadosCargo;
    }

   public function empleLocal(Request $request){
        $idlocal=$request->idlocal;
        $empleadosidLocal = DB::table('empleado')
        ->where('users_id', '=', Auth::user()->id)
        ->where('emple_local', '=', $idlocal)
        ->get();
        return $empleadosidLocal;
    }
}


