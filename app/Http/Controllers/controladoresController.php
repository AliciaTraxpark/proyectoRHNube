<?php

namespace App\Http\Controllers;

use App\controladores;
use App\dispositivo_controlador;
use App\dispositivos;
use Illuminate\Http\Request;

class controladoresController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index(){
        $dispositivo=dispositivos::where('organi_id','=',session('sesionidorg'))
        ->where('dispo_estado','=',2)->get();
        return view('controladores.controladores',['dispositivo' => $dispositivo]);
    }
    public function store(Request $request){
        $controladores=new controladores();
        $controladores->cont_codigo=$request->codigoCon;
        $controladores->cont_nombres=$request->nombresCon;
        $controladores->cont_ApPaterno=$request->paternoCon;
        $controladores->cont_ApMaterno=$request->maternoCon;
        $controladores->cont_correo=$request->correoCon;
        $controladores->cont_estado=1;
        $controladores->organi_id=session('sesionidorg');
        $controladores->save();

        $idDispositi=$request->dispoCon;
        foreach($idDispositi as $idDispositis){
          $dispositivo_controlador=new dispositivo_controlador();
          $dispositivo_controlador->idDispositivos=$idDispositis;
          $dispositivo_controlador->idControladores=$controladores->idControladores;
          $dispositivo_controlador->organi_id=session('sesionidorg');
          $dispositivo_controlador->save();
        }

    }

    public function tablaControladores(){
        $controladores=controladores::where('controladores.organi_id','=',session('sesionidorg'))
         ->join('dispositivo_controlador as dc','controladores.idControladores','=','dc.idControladores')
        ->join('dispositivos as dis','dc.idDispositivos','=','dis.idDispositivos')
        ->select('controladores.idControladores','controladores.cont_codigo','controladores.cont_nombres',
        'controladores.cont_ApPaterno','controladores.cont_ApMaterno','controladores.cont_correo',
        'controladores.cont_estado')
        ->selectRaw('GROUP_CONCAT(dis.dispo_movil) as ids')
        ->groupBy('controladores.idControladores')
        ->get();

        return json_encode($controladores);


    }
    public function disposiControladores(Request $request){
        $idControlador=$request->idcontrolador;
        $dispositivo_controlador=dispositivo_controlador::where('dispositivo_controlador.organi_id','=',session('sesionidorg'))
        ->join('dispositivos as dis','dispositivo_controlador.idDispositivos','=','dis.idDispositivos')
        ->where('dispositivo_controlador.idControladores','=',$idControlador)

        ->get();
        return json_encode($dispositivo_controlador);
    }

}
