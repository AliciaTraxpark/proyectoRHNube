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

                $calendarioR = new calendario();
                $calendarioR->organi_id = session('sesionidorg');
                $calendarioR->users_id = Auth::user()->id;
                $calendarioR->calendario_nombre='Perú';
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
                  $today = Carbon::now();
                  $año=$today->year;
                  $mes=$today->month;

                  $comienzo=Carbon::create($año.'-'.$mes.'-01');
                  $final=Carbon::create($año.'-12-31');

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
            $fechaEnvi=$fechaOrga2->format('Y-m-d');
            $fechaEnviFi=$fechaOrga3->format('Y-m-d');
                if($invitadod){
                    if ($invitadod->rol_id!=1){
                        return redirect('/dashboard');
                    }
                    else{
                        return view('calendario.calendario', ['pais' => $paises, 'calendario' => $calendarioSel,
                        'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi]);
                    }
                }

            else{
            return view('calendario.calendario', ['pais' => $paises, 'calendario' => $calendarioSel,
            'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi]);}
        } else {
            return redirect(route('principal'));
        }
    }
    public function store(Request $request)
    {

        $evento = eventos::all();
        $calendario = calendario::all();

        foreach ($evento as $eventos) {
            $calendarioR = new calendario();
            $calendarioR->users_id = Auth::user()->id;
            $calendarioR->eventos_id = $eventos->id;
            $calendarioR->organi_id = session('sesionidorg');
            $calendarioR->save();
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

                $calendarioR = new calendario();
                $calendarioR->organi_id = session('sesionidorg');
                $calendarioR->users_id = Auth::user()->id;
                $calendarioR->calendario_nombre='Perú';
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
                  $today = Carbon::now();
                  $año=$today->year;
                  $mes=$today->month;

                  $comienzo=Carbon::create($año.'-'.$mes.'-01');
                  $final=Carbon::create($año.'-12-31');

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
            $fechaEnvi=$fechaOrga2->format('Y-m-d');
            $fechaEnviFi=$fechaOrga3->format('Y-m-d');
                if($invitadod){
                    if ($invitadod->rol_id!=1){
                        return redirect('/dashboard');
                    }
                    else{
                        return view('calendario.calendarioMenu', ['pais' => $paises, 'calendario' => $calendarioSel,
                        'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi]);
                    }
                }

            else{
            return view('calendario.calendarioMenu', ['pais' => $paises, 'calendario' => $calendarioSel,
            'fechaEnvi' => $fechaEnvi,'fechaEnviFi' => $fechaEnviFi]);}
        } else {
            return redirect(route('principal'));
        }
    }

    public function registrarnuevo(Request $request){
        $nombrecal=$request->nombrecal;

        $evento = eventos::all();

        $calendarioR = new calendario();
        $calendarioR->organi_id = session('sesionidorg');
        $calendarioR->users_id = Auth::user()->id;
        $calendarioR->calendario_nombre=$nombrecal;
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
        $today = Carbon::now();
        $año=$today->year;
        $mes=$today->month;

        $comienzo=Carbon::create($año.'-'.$mes.'-01');
        $final=Carbon::create($año.'-12-31');
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
            $eventos_empleado->save();

        }

    }

    public function registrarnuevoClonado(Request $request){
        $nombrecal=$request->nombrecal;
        $idcalenda=$request->idcalenda;
        $eventos_usuarioClon = eventos_usuario::where('id_calendario','=',$idcalenda)->get();

        $calendarioR = new calendario();
        $calendarioR->organi_id = session('sesionidorg');
        $calendarioR->users_id = Auth::user()->id;
        $calendarioR->calendario_nombre=$nombrecal;
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

  
}
