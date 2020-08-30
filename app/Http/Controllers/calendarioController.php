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
use Illuminate\Support\Facades\DB;

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
                    $eventos_usuario->save();
                }
            }

            $calendarioSel = calendario::where('organi_id', '=', session('sesionidorg'))->get();
            //FUNCIONA OK


            return view('calendario.calendario', ['pais' => $paises, 'calendario' => $calendarioSel]);
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
        return response()->json($id);
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
                    $eventos_usuario->save();
                }
            }

            $calendarioSel = calendario::where('organi_id', '=', session('sesionidorg'))->get();
            //FUNCIONA OK


            return view('calendario.calendarioMenu', ['pais' => $paises, 'calendario' => $calendarioSel]);
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
            $eventos_usuario->save();
        }

        return $calendarioR;

    }

    public function cargarcalendario(Request $request){
        $idcalendario=$request->idcalendario;
        $eventos_usuario=eventos_usuario::where('organi_id', '=', session('sesionidorg'))
        ->where('id_calendario','=',$idcalendario)
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
}
