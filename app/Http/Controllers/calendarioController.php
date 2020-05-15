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
        $this->middleware('auth');
    }
    //
    public function index(){
        if (Auth::check()) {
            $paises=paises::all();
            $departamento=ubigeo_peru_departments::all();
            $calendario=calendario::where('users_id','=',Auth::user()->id)->get();
            if($calendario->first() )  {}
            else{
            //copiar tabla
            $evento=eventos::all();


            foreach($evento as $eventos)
            {   $calendarioR=new calendario();
                $calendarioR->users_id=Auth::user()->id;
                $calendarioR->eventos_id=$eventos->id;
                $calendarioR->calen_pais=173;
                $calendarioR->save();
            }}
            //FUNCIONA OK


            return view ('calendario.calendario',['pais'=>$paises,'departamento'=>$departamento]);
        }
        else{
            return redirect(route('principal'));
        }
    }
    public function store(Request $request){
        //para insertar a calendario general
    /*   $datosEvento=request()->except(['_method']);
      eventos::insert($datosEvento); */
      $evento=eventos::all();
      $calendario=calendario::all();

      foreach($evento as $eventos)
      {   $calendarioR=new calendario();
          $calendarioR->users_id=Auth::user()->id;
          $calendarioR->eventos_id=$eventos->id;
          $calendarioR->calen_departamento=$request->get('departamento');

          $calendarioR->save();
      }


    }
    public function show(){
        $eventos=DB::table('eventos')->select(['id','title' ,'color', 'textColor', 'start','end','tipo']);

        $eventos_usuario = DB::table('eventos_usuario')
        ->select(['id','title' ,'color', 'textColor', 'start','end','tipo'])
             ->where('Users_id','=',Auth::user()->id)
             ->where('evento_departamento','=',null)
             ->where('evento_pais','=',173)
                ->union($eventos)
                ->get();
        return response()->json($eventos_usuario);
    }
     public function showDep(Request $request){

        $pais=$request->get('pais');
        $depa=$request->get('departamento');

        if($pais==173){
        $eventos=DB::table('eventos')->select(['id','title' ,'color', 'textColor', 'start','end','tipo']);

        $eventos_usuario1 = DB::table('eventos_usuario')
        ->select(['id','title' ,'color', 'textColor', 'start','end','tipo'])
             ->where('Users_id','=',Auth::user()->id)
             ->where('evento_departamento','=',$depa)
             ->where('evento_pais','=',173)
                ->union($eventos)

                ->get();
                    return response()->json($eventos_usuario1);}


        else {
            $eventos_usuario1 = DB::table('eventos_usuario')
            ->select(['id','title' ,'color', 'textColor', 'start','end','tipo'])
                 ->where('Users_id','=',Auth::user()->id)
                 ->where('evento_pais','=',$pais)

                    ->get();
                        return response()->json($eventos_usuario1);

        }
    }
    public function showDepconfirmar(Request $request){
        $pais=$request->get('pais');
        $depa=$request->get('departamento');
        $existencia = DB::table('calendario')
        ->select('users_id','calen_departamento')
        ->where('users_id', '=',Auth::user()->id)
        ->where('calen_departamento','=',$depa)
        ->where('calen_pais','=',$pais)

        ->get();
                if(count($existencia) >= 1) {

                    return (1);
                } else{
                    $calendario=new calendario();
                    $calendario->users_id=Auth::user()->id;
                    $calendario->calen_departamento=$depa;
                    $calendario->calen_pais=$pais;
                    $calendario->save();
                }
    }

    public function destroy($id){
        //calendario::where('eventos_id',$id)->delete();
        $eventos_usuario=eventos_usuario::findOrFail($id);
        eventos_usuario::destroy($id);
        return response()->json($id);
    }
}
