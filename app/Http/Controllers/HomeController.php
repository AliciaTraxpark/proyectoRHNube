<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\eventos;
use App\calendario;
use App\usuario_organizacion;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(session('sesionidorg')==null || session('sesionidorg')=='null' ){
            return redirect('/elegirorganizacion');
        } else{
            //$calendario=calendario::all();
        $calendario = calendario::where('organi_id', '=', session('sesionidorg'))->get();
        //dd($calendario);
        if ($calendario->first()) {
            $variable = 1;
        } else {
            $variable = 0;
        }
        ////////////////////////

        $invitadod=DB::table('invitado')
        ->where('user_Invitado','=',Auth::user()->id)
        ->where('organi_id','=',session('sesionidorg'))
        ->get()->first();

        if($invitadod){
            if ($invitadod->dashboard==0){
                return redirect('/tareas');
            }
            else{
                return view('dashboard', ['variable' => $variable]);
            }
        }
        else{
            return view('dashboard', ['variable' => $variable]);
        }


        }




    }

    public function elegirEmpresa(){
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
            if($usuario_organizacion){
                if($usuario_organizacion->rol_id==4){
                    return redirect('/superadmin');
                 }
            }

         else{
            $organizacion = DB::table('organizacion as o')
            ->join('usuario_organizacion as uo', 'o.organi_id', '=', 'uo.organi_id')
            ->join('rol as r', 'uo.rol_id', '=', 'r.rol_id')
            ->where('uo.user_id','=',Auth::user()->id)
            ->get();
           /*  dd($organizacion); */
            return view('elegirEmpresa', ['organizacion' => $organizacion]);
         }


    }

    public function enviarIDorg(Request $request){
        $vars= $request->idorganiza;
        session(['sesionidorg' => $vars]);

      /*   return redirect(route('dashboard')); */
    }
}
