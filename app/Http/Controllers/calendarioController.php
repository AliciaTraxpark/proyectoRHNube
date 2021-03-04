<?php

namespace App\Http\Controllers;

use App\eventos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ubigeo_peru_departments;
use App\paises;
use App\calendario;
use App\calendario_empleado;
use App\eventos_empleado;
use App\eventos_calendario;
use App\incidencia_dias;
use App\organizacion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class calendarioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    //
    public function index()
    {
        if(session('sesionidorg')==null || session('sesionidorg')=='null' ){
            return redirect('/elegirorganizacion');
        } else{

        if (Auth::check()) {
            $paises = paises::all();
            $departamento = ubigeo_peru_departments::all();
            $calendario = calendario::where('organi_id', '=', session('sesionidorg'))->get();
            if ($calendario->first()) {
            } else {
                //copiar tabla
                $evento = eventos::all();

                $today = Carbon::now();
                $año=$today->year;
                $mes=$today->month;
                $añoFinc=$año+1;
                $comienzo=Carbon::create($año.'-'.$mes.'-01');
                $fincale=Carbon::create($añoFinc.'-01-01');
                $final=Carbon::create($año.'-12-31');

                $calendarioR = new calendario();
                $calendarioR->organi_id = session('sesionidorg');
                $calendarioR->users_id = Auth::user()->id;
                $calendarioR->calendario_nombre='Perú';
                $calendarioR->fin_fecha=$fincale;
                $calendarioR->save();


                  ///




            }

            $calendarioSel = calendario::where('organi_id', '=', session('sesionidorg'))->get();
            //FUNCIONA OK

                 $invitadod=DB::table('invitado')
                ->where('user_Invitado','=',Auth::user()->id)
                ->where('organi_id','=',session('sesionidorg'))
                ->get()->first();

                $organizacion=organizacion::where('organi_id','=',session('sesionidorg'))
            ->get() ->first();
            /* dd($organizacion->created_at->format('Y-m-d')); */
            $fechaOrga=Carbon::create($organizacion->created_at->format('Y-m-d'));
            $mesOrga=$fechaOrga->month;
            $yearOrga=$fechaOrga->year;
            $fechaOrga2=Carbon::create($yearOrga.'-'.$mesOrga.'-01');
            $añonn=$yearOrga+1;
            $fechaOrga3=Carbon::create($añonn.'-01-01');
            $fechaEnvi=$fechaOrga2->format('d/m/Y');
            $fechaEnviJS=$fechaOrga2->format('Y-m-d');
            $fechaEnviFi=$fechaOrga3->format('Y-m-d');
            $diaAnt=Carbon::create($fechaEnviFi)->subDays(1)->format('Y-m-d');
            //////////////////////////////////////
            $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->groupBy('e.emple_id')
            ->get();

            $area = DB::table('area as ar')
            ->join('empleado as em', 'ar.area_id', '=', 'em.emple_area')
            ->where('ar.organi_id','=',session('sesionidorg'))
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();

            $cargo=DB::table('cargo as cr')
            ->where('cr.organi_id','=',session('sesionidorg'))
            ->join('empleado as em', 'cr.cargo_id', '=', 'em.emple_cargo')
            ->select('cr.cargo_id as idcargo', 'cargo_descripcion as descripcion')
            ->groupBy('cr.cargo_id')
            ->get();
            $local=DB::table('local as lo')
            ->where('lo.organi_id','=',session('sesionidorg'))
            ->join('empleado as em', 'lo.local_id', '=', 'em.emple_local')
            ->select('lo.local_id as idlocal', 'local_descripcion as descripcion')
            ->groupBy('lo.local_id')
            ->get();
            ////////////////////////////////////////
                if($invitadod){
                    if ($invitadod->rol_id!=1){
                        return redirect('/dashboard');
                    }
                    else{
                        return view('calendario.calendario', ['pais' => $paises, 'calendario' => $calendarioSel,
                        'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi,'diaAnt' => $diaAnt,'empleado' => $empleado,
                        'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga,'fechaEnviJS' => $fechaEnviJS]);
                    }
                }

            else{
            return view('calendario.calendario', ['pais' => $paises, 'calendario' => $calendarioSel,
            'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi,'diaAnt' => $diaAnt,'empleado' => $empleado,
            'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga,'fechaEnviJS' => $fechaEnviJS]);}
        } else {
            return redirect(route('principal'));
        }
    }
    }




    public function destroy(Request $request)
    {  $id=$request->id;
        //calendario::where('eventos_id',$id)->delete();
        $eventos_calendario = eventos_calendario::findOrFail($id);
        eventos_calendario::destroy($id);
        return response()->json($eventos_calendario);
    }
    public function indexMenu()
    {
        if(session('sesionidorg')==null || session('sesionidorg')=='null' ){
            return redirect('/elegirorganizacion');
        } else{
        if (Auth::check()) {
            $paises = paises::all();
            $departamento = ubigeo_peru_departments::all();
            $calendario = calendario::where('organi_id', '=', session('sesionidorg'))->get();
            if ($calendario->first()) {
            } else {
                //copiar tabla
                $evento = eventos::all();

                $today = Carbon::now();
                $año=$today->year;
                $mes=$today->month;
                $añoFinc=$año+1;
                $comienzo=Carbon::create($año.'-'.$mes.'-01');
                $fincale=Carbon::create($añoFinc.'-01-01');
                $final=Carbon::create($año.'-12-31');

                $calendarioR = new calendario();
                $calendarioR->organi_id = session('sesionidorg');
                $calendarioR->users_id = Auth::user()->id;
                $calendarioR->calendario_nombre='Perú';
                $calendarioR->fin_fecha=$fincale;
                $calendarioR->save();


                  ///


            }
            $calendarioSel = calendario::where('organi_id', '=', session('sesionidorg'))->get();
            //FUNCIONA OK


            $invitadod=DB::table('invitado')
            ->where('user_Invitado','=',Auth::user()->id)
            ->where('organi_id','=',session('sesionidorg'))
            ->get()->first();

            $organizacion=organizacion::where('organi_id','=',session('sesionidorg'))
            ->get() ->first();
            /* dd($organizacion->created_at->format('Y-m-d')); */
            $fechaOrga=Carbon::create($organizacion->created_at->format('Y-m-d'));
            $mesOrga=$fechaOrga->month;
            $yearOrga=$fechaOrga->year;
            $fechaOrga2=Carbon::create($yearOrga.'-'.$mesOrga.'-01');
            $añonn=$yearOrga+1;
            $fechaOrga3=Carbon::create($añonn.'-01-01');
            $fechaEnvi=$fechaOrga2->format('d/m/Y');
            $fechaEnviJS=$fechaOrga2->format('Y-m-d');
            $fechaEnviFi=$fechaOrga3->format('Y-m-d');

            $diaAnt=Carbon::create($fechaEnviFi)->subDays(1)->format('Y-m-d');
            //////////////////////////////////////
            $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->groupBy('e.emple_id')
            ->get();

            $area = DB::table('area as ar')
            ->join('empleado as em', 'ar.area_id', '=', 'em.emple_area')
            ->where('ar.organi_id','=',session('sesionidorg'))
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();

            $cargo=DB::table('cargo as cr')
            ->where('cr.organi_id','=',session('sesionidorg'))
            ->join('empleado as em', 'cr.cargo_id', '=', 'em.emple_cargo')
            ->select('cr.cargo_id as idcargo', 'cargo_descripcion as descripcion')
            ->groupBy('cr.cargo_id')
            ->get();
            $local=DB::table('local as lo')
            ->where('lo.organi_id','=',session('sesionidorg'))
            ->join('empleado as em', 'lo.local_id', '=', 'em.emple_local')
            ->select('lo.local_id as idlocal', 'local_descripcion as descripcion')
            ->groupBy('lo.local_id')
            ->get();
            ////////////////////////////////////////
                if($invitadod){
                    if ($invitadod->rol_id!=1){

                        if($invitadod->gestCalendario==1){
                            return view('calendario.calendarioMenu', ['pais' => $paises, 'calendario' => $calendarioSel,
                        'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi,'diaAnt' => $diaAnt,'empleado' => $empleado,
                        'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga,'fechaEnviJS' => $fechaEnviJS]);
                        }
                        else{
                            return redirect('/dashboard');
                        }

                    }
                    else{
                        return view('calendario.calendarioMenu', ['pais' => $paises, 'calendario' => $calendarioSel,
                        'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi,'diaAnt' => $diaAnt,'empleado' => $empleado,
                        'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga,'fechaEnviJS' => $fechaEnviJS]);
                    }
                }

            else{
            return view('calendario.calendarioMenu', ['pais' => $paises, 'calendario' => $calendarioSel,
            'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi,'diaAnt' => $diaAnt,'empleado' => $empleado,
            'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga,'fechaEnviJS' => $fechaEnviJS]);}
        } else {
            return redirect(route('principal'));
        }
    }
    }

    public function registrarnuevo(Request $request){
        $nombrecal=$request->nombrecal;

        $evento = eventos::all();

        $today = Carbon::now();
        $año=$today->year;
        $mes=$today->month;
        $añoFinc=$año+1;
        $comienzo=Carbon::create($año.'-'.$mes.'-01');
        $fincale=Carbon::create($añoFinc.'-01-01');
        $final=Carbon::create($año.'-12-31');

        $calendarioR = new calendario();
        $calendarioR->organi_id = session('sesionidorg');
        $calendarioR->users_id = Auth::user()->id;
        $calendarioR->calendario_nombre=$nombrecal;
        $calendarioR->fin_fecha=$fincale;
        $calendarioR->save();





        return $calendarioR;

    }

    public function cargarcalendario(Request $request){
        $idcalendario=$request->idcalendario;
        $eventos_calendario=eventos_calendario::where('eventos_calendario.organi_id', '=', session('sesionidorg'))
        ->leftJoin('incidencias','eventos_calendario.inciden_id','=','incidencias.inciden_id')
        ->where('id_calendario','=',$idcalendario)
        ->select('id','start','end','inciden_descripcion as title','color','textColor','laborable')
        ->get();
        return $eventos_calendario;
    }

    public function verificarID(Request $request){
        $idcalendario=$request->id_calendario;
        $calendario_empleado = DB::table('calendario_empleado')
        ->where('calen_id', '=', $idcalendario)
        ->get();
        if ($calendario_empleado->isEmpty()) {
            return  0;
        } else{ return  1;}

    }
    public function copiarevenEmpleado(Request $request){
        $idcalendario=$request->id_calendario;
        $idevento=$request->idevento;
        $eventos_calendario = DB::table('eventos_calendario')
        ->where('id_calendario', '=', $idcalendario)
        ->where('id', '=',  $idevento)
        ->get()->first();

        $eventos_empleadoN = DB::table('calendario_empleado')
        ->where('calen_id', '=', $idcalendario)
        ->groupBy('emple_id')
        ->get();
        /* dd($eventos_calendario->title); */
        foreach($eventos_empleadoN as $eventos_empleadosN){
            $incidencia_dias = new incidencia_dias();
            $incidencia_dias->id_incidencia = $eventos_calendario->inciden_id;
            $incidencia_dias->inciden_dias_fechaI = $eventos_calendario->start;
            $incidencia_dias->inciden_dias_fechaF = $eventos_calendario->end;
            $incidencia_dias->id_empleado = $eventos_empleadosN->emple_id;
            $incidencia_dias->laborable =$eventos_calendario->laborable;
            $incidencia_dias->save();

        }

    }

    public function registrarnuevoClonado(Request $request){
        $nombrecal=$request->nombrecal;
        $idcalenda=$request->idcalenda;
        $allValsAños=$request->allValsAños;
        foreach($allValsAños as $allValsAñosx){
           $eventos_calendarioClon = eventos_calendario::where('id_calendario','=',$idcalenda)->whereYear('start',$allValsAñosx)->get();

        }
       $añomax=max($allValsAños)+1;
       $finNew=Carbon::create($añomax.'-01-01');
        $calendarioDup=calendario::where('calen_id','=',$idcalenda)->get()->first();
        $calendarioR = new calendario();
        $calendarioR->organi_id = session('sesionidorg');
        $calendarioR->users_id = Auth::user()->id;
        $calendarioR->calendario_nombre=$nombrecal;
        $calendarioR->fin_fecha= $finNew;
        $calendarioR->save();

            foreach ($eventos_calendarioClon as $eventos) {
            $eventos_calendario = new eventos_calendario();
            $eventos_calendario->organi_id = session('sesionidorg');
            $eventos_calendario->users_id = Auth::user()->id;
            $eventos_calendario->color =$eventos->color;
            $eventos_calendario->textColor =$eventos->textColor;
            $eventos_calendario->start =$eventos->start;
            $eventos_calendario->end =$eventos->end;
            $eventos_calendario->id_calendario =$calendarioR->calen_id;
            $eventos_calendario->laborable =$eventos->laborable;
            $eventos_calendario->inciden_id =$eventos->inciden_id;
            $eventos_calendario->save();
        }


        return $calendarioR;

    }

    public function mostrarFCalend(Request $request){
        $idcale=$request->idcale;
        $calendarioF=calendario::where('calen_id','=',$idcale)
        ->get()->first();
        $finff=Carbon::create($calendarioF->fin_fecha);

        $fechafin=$finff->format('Y-m-d');

        return $fechafin;
    }

  public function añadirFinCalenda(Request $request){
    $calendfEd=$request->añoFed;
    $idcalenda=$request->calendfEd;

    $añoFinc=$calendfEd;
    $comienzo=Carbon::create($añoFinc.'-01-01');

    $final=Carbon::create($añoFinc.'-12-31');

       ////////////////////////////////////
       $añoCale=$añoFinc+1;
       $fechaCal=Carbon::create($añoCale.'-01-01');
       $calendario  = DB::table('calendario')
            ->where('calen_id', '=', $idcalenda)
               ->update(['fin_fecha' => $fechaCal]);
       /////////////////

       $eventosA=eventos::whereYear('start','=',2020)->get();

  }

  public function listaEmplCa(Request $request){
    $idcalendar=$request->idcalendar;

    $empleados = DB::table('empleado as e')
    ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
    ->leftJoin('calendario_empleado as ce', 'e.emple_id', '=', 'ce.emple_id')
    ->select(
        'e.emple_id',
        'p.perso_id',
        'p.perso_nombre',
        'p.perso_apPaterno',
        'p.perso_apMaterno',
        'ce.calen_id as idcalendar'
    )
    ->where('ce.calen_id', '=', $idcalendar)
    ->where('e.emple_estado', '=', 1)
    ->where('e.organi_id', '=', session('sesionidorg'))
    ->groupBy('e.emple_id')
    ->get();

    return json_encode($empleados);

  }

  public function asignarCalendario(Request $request){
    $idcalendario = $request->idcalenReg;
    $idempleado = $request->idemples;

        //*recorro empleados a asignar
        foreach($idempleado as $idempleados){

         //*verifico si tiene calendario asignado
         $calendario_empleado = calendario_empleado::where('emple_id', '=', $idempleados)
            ->get();

        //*si no tiene calendario asiggnado
        if ($calendario_empleado->isEmpty()) {

            //*asignamos
            $calendario_empleado=new calendario_empleado();
            $calendario_empleado->emple_id=$idempleados;
            $calendario_empleado->calen_id=$idcalendario;
            $calendario_empleado->save();

            //*verifico eventos de calendario
            $eventos_calendario = eventos_calendario::where('organi_id', '=', session('sesionidorg'))
                ->where('id_calendario', '=', $idcalendario)->get();

            if ($eventos_calendario) {
                foreach ($eventos_calendario as $eventos_calendarios) {
                    $incidencia_dias = new incidencia_dias();
                    $incidencia_dias->id_incidencia = $eventos_calendarios->inciden_id;
                    $incidencia_dias->inciden_dias_fechaI = $eventos_calendarios->start;
                    $incidencia_dias->inciden_dias_fechaF = $eventos_calendarios->end;
                    $incidencia_dias->id_empleado = $idempleados;
                    $incidencia_dias->laborable =$eventos_calendarios->laborable;
                    $incidencia_dias->save();
                }
            }
        }
        else{

            //*verifico si el empleado tiene calendario asignado
            $eventos_empleadoRep =  calendario_empleado::where('emple_id', '=', $idempleados)
            ->where('calen_id', '=', $idcalendario)
            ->get();

            //*si no tiene  calendario
            if ($eventos_empleadoRep->isEmpty()) {

                //*verifico si existe empleado con cualquier calendario
                $eventos_empleadoExist =  calendario_empleado::where('emple_id', '=', $idempleados)
                ->get()->first();

                if($eventos_empleadoExist){
                     //*actualizo su calendario
                    $calendario_empleado=calendario_empleado::find( $eventos_empleadoExist->idcalendario_empleado);
                    $calendario_empleado->calen_id=$idcalendario;
                    $calendario_empleado->save();

                }

                //*elimino sus incidencias de empleado
                DB::table('incidencia_dias')
                ->where('id_empleado', '=', $idempleados)
                ->delete();

                 //*verifico eventos de calendario
                $eventos_calendario = eventos_calendario::where('organi_id', '=', session('sesionidorg'))
                ->where('id_calendario', '=', $idcalendario)->get();

                if ($eventos_calendario) {
                    foreach ($eventos_calendario as $eventos_calendarios) {
                        $incidencia_dias = new incidencia_dias();
                        $incidencia_dias->id_incidencia = $eventos_calendarios->inciden_id;
                        $incidencia_dias->inciden_dias_fechaI = $eventos_calendarios->start;
                        $incidencia_dias->inciden_dias_fechaF = $eventos_calendarios->end;
                        $incidencia_dias->id_empleado = $idempleados;
                        $incidencia_dias->laborable =$eventos_calendarios->laborable;
                        $incidencia_dias->save();
                    }
                }

             } else{

             }



        }
        }


  }

  public function empSeleccionados(Request $request){

    $idempleado = $request->ids;
    $arrayeve = collect();
    if($idempleado){
        foreach($idempleado as $idempleados){
            $emps=    DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('calendario_empleado as ce', 'e.emple_id', '=', 'ce.emple_id')
            ->leftJoin('calendario as ca', 'ce.calen_id', '=', 'ca.calen_id')
            ->select(
                'e.emple_id',
                'p.perso_id',
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'ce.calen_id as idcalendar',
                'ca.calendario_nombre'
            )
            ->where('e.emple_id', '=', $idempleados)
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->groupBy('e.emple_id')
            ->get();
            $arrayeve->push($emps);
    }
    }

    return json_encode($arrayeve);

  }

  public function yearCale(Request $request){
      $idcale=$request->idcale;
      $calendario = calendario::where('calen_id', '=', $idcale)
                ->get()->first();

      $fechaOrga=Carbon::create($calendario->created_at->format('Y-m-d'));
      $inicio=$fechaOrga->year;
      $fechamenosdi=Carbon::create($calendario->fin_fecha)->subDays(1);
       $fechafin=Carbon::create($fechamenosdi->format('Y-m-d'));
      $fin=$fechafin->year;

      $arrayfec = collect();

      for ($i=$inicio; $i <$fin+1 ; $i++) {
           $i;
           $arrayfec->push($i);
      }
      return $arrayfec;

  }

  public function agregarSelectFeriado(Request $request){

    //obtener tipo de incidencia
    $tipo_incidencia=DB::table('tipo_incidencia')
    ->where('organi_id','=',session('sesionidorg'))
    ->where('tipoInc_descripcion','=','Feriado')
    ->get()->first();

    $incidencias=DB::table('incidencias')
    ->where('organi_id','=',session('sesionidorg'))
    ->where('idtipo_incidencia','=',$tipo_incidencia->idtipo_incidencia)
    ->where('estado','=',1)
    ->get();

    return ($incidencias);

  }

  public function agregarSelectDescanso(Request $request){

    //obtener tipo de incidencia
    $tipo_incidencia=DB::table('tipo_incidencia')
    ->where('organi_id','=',session('sesionidorg'))
    ->where('tipoInc_descripcion','=','Descanso')
    ->get()->first();

    $incidencias=DB::table('incidencias')
    ->where('organi_id','=',session('sesionidorg'))
    ->where('idtipo_incidencia','=',$tipo_incidencia->idtipo_incidencia)
    ->where('estado','=',1)
    ->get();

    return ($incidencias);

  }
}
