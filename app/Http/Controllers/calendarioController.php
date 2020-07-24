<?php

namespace App\Http\Controllers;

use App\eventos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ubigeo_peru_departments;
use App\paises;
use App\calendario;
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
            $calendario = calendario::where('users_id', '=', Auth::user()->id)->get();
            if ($calendario->first()) {
            } else {
                //copiar tabla
                $evento = eventos::all();

                $calendarioR = new calendario();
                $calendarioR->users_id = Auth::user()->id;
                $calendarioR->calendario_nombre='Perú';
                $calendarioR->save();
                 foreach ($evento as $eventos) {
                    $eventos_usuario = new eventos_usuario();

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

            $calendarioSel = calendario::where('users_id', '=', Auth::user()->id)->get();
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

            $calendarioR->save();
        }
    }
    public function show()
    {
        $eventos = DB::table('eventos')->select(['id', 'title', 'color', 'textColor', 'start', 'end', 'tipo']);

        $eventos_usuario = DB::table('eventos_usuario')
            ->select(['id', 'title', 'color', 'textColor', 'start', 'end', 'tipo'])
            ->where('Users_id', '=', Auth::user()->id)

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
            $calendario = calendario::where('users_id', '=', Auth::user()->id)->get();
            if ($calendario->first()) {
            } else {
                //copiar tabla
                $evento = eventos::all();

                $calendarioR = new calendario();
                $calendarioR->users_id = Auth::user()->id;
                $calendarioR->calendario_nombre='Perú';
                $calendarioR->save();
                 foreach ($evento as $eventos) {
                    $eventos_usuario = new eventos_usuario();

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

            $calendarioSel = calendario::where('users_id', '=', Auth::user()->id)->get();
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
        $calendarioR->users_id = Auth::user()->id;
        $calendarioR->calendario_nombre=$nombrecal;
        $calendarioR->save();

            foreach ($evento as $eventos) {
            $eventos_usuario = new eventos_usuario();

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
        $eventos_usuario=eventos_usuario::where('users_id','=',Auth::user()->id)
        ->where('id_calendario','=',$idcalendario)
        ->get();
        return $eventos_usuario;
    }
}
