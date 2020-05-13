<?php

namespace App\Http\Controllers;
use App\eventos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ubigeo_peru_departments;
use App\paises;
use App\calendario;
use Illuminate\Support\Facades\DB;
class calendarioController extends Controller
{
    //
    public function index(){
        if (Auth::check()) {
            $paises=paises::all();
            $departamento=ubigeo_peru_departments::all();
            //copiar tabla
            $evento=eventos::all();
            $calendario=calendario::all();

            foreach($evento as $eventos)
            {   $calendarioR=new calendario();
                $calendarioR->users_id=Auth::user()->id;
                $calendarioR->eventos_id=$eventos->id;
                $calendarioR->save();
            }
            //FUNCIONA OK


            return view ('calendario.calendario',['pais'=>$paises,'departamento'=>$departamento]);
        }
        else{
            return redirect(route('principal'));
        }
    }
    public function store(Request $request){
        //para insertar a calendario general
      $datosEvento=request()->except(['_method']);
      eventos::insert($datosEvento);
    }
    public function show(){
        $eventos=DB::table('eventos')->select([ 'title' ,'color', 'textColor', 'start','end','tipo']);

        $eventos_usuario = DB::table('eventos_usuario')
        ->select([ 'title' ,'color', 'textColor', 'start','end','tipo'])
             ->where('Users_id','=',Auth::user()->id)
                ->union($eventos)
                ->get();
        return response()->json($eventos_usuario);
    }
}
