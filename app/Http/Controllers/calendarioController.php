<?php

namespace App\Http\Controllers;

use App\eventos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ubigeo_peru_departments;
use App\paises;
use App\calendario;
use App\eventos_empleado;
use App\eventos_usuario;
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
                 foreach ($evento as $eventos) {
                    $eventos_usuario = new eventos_usuario();
                    $eventos_usuario->organi_id = session('sesionidorg');
                    $eventos_usuario->users_id = Auth::user()->id;
                    $eventos_usuario->title =$eventos->title;
                    $eventos_usuario->color =$eventos->color;
                    $eventos_usuario->textColor =$eventos->textColor;
                    $eventos_usuario->start =$eventos->start;
                    $eventos_usuario->end =$eventos->end;
                    $eventos_usuario->tipo =$eventos->tipo;
                    $eventos_usuario->id_calendario =$calendarioR->calen_id;
                    $eventos_usuario->laborable =$eventos->laborable;
                   /*  $eventos_usuario->backgroundColor =$calendarioR->backgroundColor; */
                    $eventos_usuario->save();
                }

                  ///


                 $dates = [];
                 $sundays = [];
                /*        para todos lo disd
                                while ($comienzo->lte($final)) {
                                $dates[] = $comienzo->copy()->format('Y-m-d');

                                    $comienzo->addDay();


                                } */

                 //para domigos
                 $oneDay     = 60*60*24;
                 for($i = strtotime($comienzo); $i <= strtotime($final); $i += $oneDay) {
                     $day = date('N', $i);
                     if($day == 7) {

                         $sundays[] = date('Y-m-d', $i);

                         $i += 6 * $oneDay;
                     }
                 }


                 foreach ($sundays as $dates2) {
                    $eventos_usuario2 = new eventos_usuario();
                    $eventos_usuario2->organi_id = session('sesionidorg');
                    $eventos_usuario2->users_id = Auth::user()->id;
                    $eventos_usuario2->title ='Descanso';
                    $eventos_usuario2->color ='#e6bdbd';
                    $eventos_usuario2->textColor =  '#504545';
                    $eventos_usuario2->start =$dates2;
                    $eventos_usuario2->tipo =1;
                    $eventos_usuario2->id_calendario =$calendarioR->calen_id;
                    $eventos_usuario2->laborable =0;

                    $eventos_usuario2->save();
                     }



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
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();

            $cargo=DB::table('cargo as cr')
            ->join('empleado as em', 'cr.cargo_id', '=', 'em.emple_cargo')
            ->select('cr.cargo_id as idcargo', 'cargo_descripcion as descripcion')
            ->groupBy('cr.cargo_id')
            ->get();
            $local=DB::table('local as lo')
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
                        'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga]);
                    }
                }

            else{
            return view('calendario.calendario', ['pais' => $paises, 'calendario' => $calendarioSel,
            'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi,'diaAnt' => $diaAnt,'empleado' => $empleado,
            'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga]);}
        } else {
            return redirect(route('principal'));
        }
    }

    public function show()
    {
        $eventos = DB::table('eventos')->select(['id', 'title', 'color', 'textColor', 'start', 'end', 'tipo']);

        $eventos_usuario = DB::table('eventos_usuario')
            ->select(['id', 'title', 'color', 'textColor', 'start', 'end', 'tipo'])
            ->where('organi_id', '=', session('sesionidorg'))
            ->union($eventos)
            ->get();
        return response()->json($eventos_usuario);
    }


    public function destroy(Request $request)
    {  $id=$request->id;
        //calendario::where('eventos_id',$id)->delete();
        $eventos_usuario = eventos_usuario::findOrFail($id);
        eventos_usuario::destroy($id);
        return response()->json($eventos_usuario);
    }
    public function indexMenu()
    {
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
                 foreach ($evento as $eventos) {
                    $eventos_usuario = new eventos_usuario();
                    $eventos_usuario->organi_id = session('sesionidorg');
                    $eventos_usuario->users_id = Auth::user()->id;
                    $eventos_usuario->title =$eventos->title;
                    $eventos_usuario->color =$eventos->color;
                    $eventos_usuario->textColor =$eventos->textColor;
                    $eventos_usuario->start =$eventos->start;
                    $eventos_usuario->end =$eventos->end;
                    $eventos_usuario->tipo =$eventos->tipo;
                    $eventos_usuario->id_calendario =$calendarioR->calen_id;
                    $eventos_usuario->laborable =$eventos->laborable;
                   /*  $eventos_usuario->backgroundColor =$calendarioR->backgroundColor; */
                    $eventos_usuario->save();
                }

                  ///


                 $dates = [];
                 $sundays = [];
                /*        para todos lo disd
                                while ($comienzo->lte($final)) {
                                $dates[] = $comienzo->copy()->format('Y-m-d');

                                    $comienzo->addDay();


                                } */

                 //para domigos
                 $oneDay     = 60*60*24;
                 for($i = strtotime($comienzo); $i <= strtotime($final); $i += $oneDay) {
                     $day = date('N', $i);
                     if($day == 7) {

                         $sundays[] = date('Y-m-d', $i);

                         $i += 6 * $oneDay;
                     }
                 }


                 foreach ($sundays as $dates2) {
                    $eventos_usuario2 = new eventos_usuario();
                    $eventos_usuario2->organi_id = session('sesionidorg');
                    $eventos_usuario2->users_id = Auth::user()->id;
                    $eventos_usuario2->title ='Descanso';
                    $eventos_usuario2->color ='#e6bdbd';
                    $eventos_usuario2->textColor =  '#504545';
                    $eventos_usuario2->start =$dates2;
                    $eventos_usuario2->tipo =1;
                    $eventos_usuario2->id_calendario =$calendarioR->calen_id;
                    $eventos_usuario2->laborable =0;

                    $eventos_usuario2->save();
                     }

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
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();

            $cargo=DB::table('cargo as cr')
            ->join('empleado as em', 'cr.cargo_id', '=', 'em.emple_cargo')
            ->select('cr.cargo_id as idcargo', 'cargo_descripcion as descripcion')
            ->groupBy('cr.cargo_id')
            ->get();
            $local=DB::table('local as lo')
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
                        return view('calendario.calendarioMenu', ['pais' => $paises, 'calendario' => $calendarioSel,
                        'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi,'diaAnt' => $diaAnt,'empleado' => $empleado,
                        'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga]);
                    }
                }

            else{
            return view('calendario.calendarioMenu', ['pais' => $paises, 'calendario' => $calendarioSel,
            'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi,'diaAnt' => $diaAnt,'empleado' => $empleado,
            'area'=>$area,'cargo'=>$cargo,'local'=>$local,'fechaOrga'=>$fechaOrga]);}
        } else {
            return redirect(route('principal'));
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

            foreach ($evento as $eventos) {
            $eventos_usuario = new eventos_usuario();
            $eventos_usuario->organi_id = session('sesionidorg');
            $eventos_usuario->users_id = Auth::user()->id;
            $eventos_usuario->title =$eventos->title;
            $eventos_usuario->color =$eventos->color;
            $eventos_usuario->textColor =$eventos->textColor;
            $eventos_usuario->start =$eventos->start;
            $eventos_usuario->end =$eventos->end;
            $eventos_usuario->tipo =$eventos->tipo;
            $eventos_usuario->id_calendario =$calendarioR->calen_id;
            $eventos_usuario->laborable =$eventos->laborable;
            $eventos_usuario->save();
        }

       $dates = [];
       $sundays = [];

       //para domigos
       $oneDay     = 60*60*24;
       for($i = strtotime($comienzo); $i <= strtotime($final); $i += $oneDay) {
           $day = date('N', $i);
           if($day == 7) {
               $sundays[] = date('Y-m-d', $i);
               $i += 6 * $oneDay;
           }
       }
       foreach ($sundays as $dates2) {
          $eventos_usuario2 = new eventos_usuario();
          $eventos_usuario2->organi_id = session('sesionidorg');
          $eventos_usuario2->users_id = Auth::user()->id;
          $eventos_usuario2->title ='Descanso';
          $eventos_usuario2->color ='#e6bdbd';
          $eventos_usuario2->textColor =  '#504545';
          $eventos_usuario2->start =$dates2;
          $eventos_usuario2->tipo =1;
          $eventos_usuario2->id_calendario =$calendarioR->calen_id;
          $eventos_usuario2->laborable =0;

          $eventos_usuario2->save();
           }

        return $calendarioR;

    }

    public function cargarcalendario(Request $request){
        $idcalendario=$request->idcalendario;
        $eventos_usuario=eventos_usuario::where('organi_id', '=', session('sesionidorg'))
        ->where('id_calendario','=',$idcalendario)
        ->select('id','start','end','title','color','textColor','tipo','laborable')
        ->get();
        return $eventos_usuario;
    }

    public function verificarID(Request $request){
        $idcalendario=$request->id_calendario;
        $eventos_empleado = DB::table('eventos_empleado')
        ->where('id_calendario', '=', $idcalendario)
        ->get();
        if ($eventos_empleado->isEmpty()) {
            return  0;
        } else{ return  1;}

    }
    public function copiarevenEmpleado(Request $request){
        $idcalendario=$request->id_calendario;
        $idevento=$request->idevento;
        $eventos_usuario = DB::table('eventos_usuario')
        ->where('id_calendario', '=', $idcalendario)
        ->where('id', '=',  $idevento)
        ->get()->first();

        $eventos_empleadoN = DB::table('eventos_empleado')
        ->where('id_calendario', '=', $idcalendario)
        ->groupBy('id_empleado')
        ->get();
        /* dd($eventos_usuario->title); */
        foreach($eventos_empleadoN as $eventos_empleadosN){
            $eventos_empleado = new eventos_empleado();
            $eventos_empleado->title =  $eventos_usuario->title;
            $eventos_empleado->color = $eventos_usuario->color;
            $eventos_empleado->textColor = $eventos_usuario->textColor;
            $eventos_empleado->start = $eventos_usuario->start;
            $eventos_empleado->end = $eventos_usuario->end;
            $eventos_empleado->id_empleado =$eventos_empleadosN->id_empleado;
            $eventos_empleado->tipo_ev = $eventos_usuario->tipo;
            $eventos_empleado->id_calendario = $eventos_usuario->id_calendario;
            $eventos_usuario->organi_id = session('sesionidorg');
            $eventos_empleado->laborable = $eventos_usuario->laborable;
            $eventos_empleado->save();

        }

    }

    public function registrarnuevoClonado(Request $request){
        $nombrecal=$request->nombrecal;
        $idcalenda=$request->idcalenda;
        $eventos_usuarioClon = eventos_usuario::where('id_calendario','=',$idcalenda)->get();
        $calendarioDup=calendario::where('calen_id','=',$idcalenda)->get()->first();
        $calendarioR = new calendario();
        $calendarioR->organi_id = session('sesionidorg');
        $calendarioR->users_id = Auth::user()->id;
        $calendarioR->calendario_nombre=$nombrecal;
        $calendarioR->fin_fecha= $calendarioDup->fin_fecha;
        $calendarioR->save();

            foreach ($eventos_usuarioClon as $eventos) {
            $eventos_usuario = new eventos_usuario();
            $eventos_usuario->organi_id = session('sesionidorg');
            $eventos_usuario->users_id = Auth::user()->id;
            $eventos_usuario->title =$eventos->title;
            $eventos_usuario->color =$eventos->color;
            $eventos_usuario->textColor =$eventos->textColor;
            $eventos_usuario->start =$eventos->start;
            $eventos_usuario->end =$eventos->end;
            $eventos_usuario->tipo =$eventos->tipo;
            $eventos_usuario->id_calendario =$calendarioR->calen_id;
            $eventos_usuario->laborable =$eventos->laborable;
            $eventos_usuario->save();
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

    $sundays = [];

       //para domigos
       $oneDay     = 60*60*24;
       for($i = strtotime($comienzo); $i <= strtotime($final); $i += $oneDay) {
           $day = date('N', $i);
           if($day == 7) {
               $sundays[] = date('Y-m-d', $i);
               $i += 6 * $oneDay;
           }
       }
       //////////////////////////////////////
       $añoCale=$añoFinc+1;
       $fechaCal=Carbon::create($añoCale.'-01-01');
       $calendario  = DB::table('calendario')
            ->where('calen_id', '=', $idcalenda)
               ->update(['fin_fecha' => $fechaCal]);
       /////////////////
       foreach ($sundays as $dates2) {
          $eventos_usuario2 = new eventos_usuario();
          $eventos_usuario2->organi_id = session('sesionidorg');
          $eventos_usuario2->users_id = Auth::user()->id;
          $eventos_usuario2->title ='Descanso';
          $eventos_usuario2->color ='#e6bdbd';
          $eventos_usuario2->textColor =  '#504545';
          $eventos_usuario2->start =$dates2;
          $eventos_usuario2->tipo =1;
          $eventos_usuario2->id_calendario =$idcalenda;
          $eventos_usuario2->laborable =0;
          $eventos_usuario2->save();
           }
  }

  public function listaEmplCa(Request $request){
    $idcalendar=$request->idcalendar;

    $empleados = DB::table('empleado as e')
    ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
    ->leftJoin('eventos_empleado as eve', 'e.emple_id', '=', 'eve.id_empleado')
    ->select(
        'e.emple_id',
        'p.perso_id',
        'p.perso_nombre',
        'p.perso_apPaterno',
        'p.perso_apMaterno',
        'eve.id_calendario as idcalendar'
    )
    ->where('eve.id_calendario', '=', $idcalendar)
    ->where('e.emple_estado', '=', 1)
    ->where('e.organi_id', '=', session('sesionidorg'))
    ->groupBy('e.emple_id')
    ->get();

    return json_encode($empleados);

  }

  public function asignarCalendario(Request $request){
    $idcalendario = $request->idcalenReg;
        $idempleado = $request->idemples;
        foreach($idempleado as $idempleados){
         $eventos_empleado = eventos_empleado::where('id_empleado', '=', $idempleados)
            ->get();

        if ($eventos_empleado->isEmpty()) {

            $eventos_usuario = eventos_usuario::where('organi_id', '=', session('sesionidorg'))
                ->where('id_calendario', '=', $idcalendario)->get();
            if ($eventos_usuario) {
                foreach ($eventos_usuario as $eventos_usuarios) {
                    $eventos_empleado_r = new eventos_empleado();
                    $eventos_empleado_r->id_empleado = $idempleados;
                    $eventos_empleado_r->title = $eventos_usuarios->title;
                    $eventos_empleado_r->color = $eventos_usuarios->color;
                    $eventos_empleado_r->textColor = $eventos_usuarios->textColor;
                    $eventos_empleado_r->start = $eventos_usuarios->start;
                    $eventos_empleado_r->end = $eventos_usuarios->end;
                    $eventos_empleado_r->tipo_ev = $eventos_usuarios->tipo;
                    $eventos_empleado_r->id_calendario = $idcalendario;
                    $eventos_empleado_r->laborable =0;
                    $eventos_empleado_r->save();
                }
            }
        }
        else{

            $eventos_empleadoRep = eventos_empleado::where('id_empleado', '=', $idempleados)
            ->where('id_calendario', '=', $idcalendario)
            ->get();
            if ($eventos_empleadoRep->isEmpty()) {
                DB::table('eventos_empleado')
            ->where('id_empleado', '=', $idempleados)
            ->delete();
            $eventos_usuario = eventos_usuario::where('organi_id', '=', session('sesionidorg'))
                ->where('id_calendario', '=', $idcalendario)->get();
            if ($eventos_usuario) {
                foreach ($eventos_usuario as $eventos_usuarios) {
                    $eventos_empleado_r = new eventos_empleado();
                    $eventos_empleado_r->id_empleado = $idempleados;
                    $eventos_empleado_r->title = $eventos_usuarios->title;
                    $eventos_empleado_r->color = $eventos_usuarios->color;
                    $eventos_empleado_r->textColor = $eventos_usuarios->textColor;
                    $eventos_empleado_r->start = $eventos_usuarios->start;
                    $eventos_empleado_r->end = $eventos_usuarios->end;
                    $eventos_empleado_r->tipo_ev = $eventos_usuarios->tipo;
                    $eventos_empleado_r->id_calendario = $idcalendario;
                    $eventos_empleado_r->laborable =0;
                    $eventos_empleado_r->save();
                }
            }

            }



        }
        }


  }

  public function empSeleccionados(Request $request){
    $idempleado = $request->ids;
    $arrayeve = collect();
    foreach($idempleado as $idempleados){
            $emps=    DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('eventos_empleado as eve', 'e.emple_id', '=', 'eve.id_empleado')
            ->leftJoin('calendario as ca', 'eve.id_calendario', '=', 'ca.calen_id')
            ->select(
                'e.emple_id',
                'p.perso_id',
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'eve.id_calendario as idcalendar',
                'ca.calendario_nombre'
            )
            ->where('e.emple_id', '=', $idempleados)
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->groupBy('e.emple_id')
            ->get();
            $arrayeve->push($emps);
    }
    return json_encode($arrayeve);

  }
}
