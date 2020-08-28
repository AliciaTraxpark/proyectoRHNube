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

        //$calendario=calendario::all();
        $calendario = calendario::where('users_id', '=', Auth::user()->id)->get();
        //dd($calendario);
        if ($calendario->first()) {
            $variable = 1;
        } else {
            $variable = 0;
        }

        return view('dashboard', ['variable' => $variable]);
    }

    public function elegirEmpresa(){

        $organizacion = DB::table('organizacion as o')
        ->join('usuario_organizacion as uo', 'o.organi_id', '=', 'uo.organi_id')
        ->join('rol as r', 'uo.rol_id', '=', 'r.rol_id')
        ->where('uo.user_id','=',Auth::user()->id)
        ->get();
       /*  dd($organizacion); */
        return view('elegirEmpresa', ['organizacion' => $organizacion]);

    }

    public function enviarIDorg(Request $request){
        $vars= $request->idorganiza;
        session(['sesionidorg' => $vars]);
      /*   return redirect(route('dashboard')); */
    }
}
