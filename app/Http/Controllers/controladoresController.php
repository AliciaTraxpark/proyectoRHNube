<?php

namespace App\Http\Controllers;

use App\controladores;
use App\dispositivo_controlador;
use App\dispositivos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class controladoresController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index(){
        if(session('sesionidorg')==null || session('sesionidorg')=='null' ){
            return redirect('/elegirorganizacion');
        } else{
        $dispositivo=dispositivos::where('organi_id','=',session('sesionidorg'))
        ->get();

        $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if( $invitadod->asistePuerta==1){
                        $permiso_invitado = DB::table('permiso_invitado')
                        ->where('idinvitado', '=', $invitadod->idinvitado)
                        ->get()->first();
                        return view('controladores.controladores',['dispositivo' => $dispositivo,
                        'verPuerta'=>$permiso_invitado->verPuerta,'agregarPuerta'=>$permiso_invitado->agregarPuerta,'modifPuerta'=>$permiso_invitado->modifPuerta]);
                    } else{
                          return redirect('/dashboard');
                    }
                   /*   */


                } else {
                    return view('controladores.controladores',['dispositivo' => $dispositivo]);
                }
            }
            else{
                return view('controladores.controladores',['dispositivo' => $dispositivo]);
            }
        }
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
        if($idDispositi){
            foreach($idDispositi as $idDispositis){
                $dispositivo_controlador=new dispositivo_controlador();
                $dispositivo_controlador->idDispositivos=$idDispositis;
                $dispositivo_controlador->idControladores=$controladores->idControladores;
                $dispositivo_controlador->organi_id=session('sesionidorg');
                $dispositivo_controlador->save();
              }

        }
    }

    public function tablaControladores(){
        $controladores=controladores::where('controladores.organi_id','=',session('sesionidorg'))
         ->leftJoin('dispositivo_controlador as dc','controladores.idControladores','=','dc.idControladores')
        ->leftJoin('dispositivos as dis','dc.idDispositivos','=','dis.idDispositivos')
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

    public function datosControEditar(Request $request){
        $controladores=controladores::where('controladores.organi_id','=',session('sesionidorg'))
        ->where('controladores.idControladores',$request->id)
        ->leftJoin('dispositivo_controlador as dc','controladores.idControladores','=','dc.idControladores')
       ->leftJoin('dispositivos as dis','dc.idDispositivos','=','dis.idDispositivos')
       ->select('controladores.idControladores','controladores.cont_codigo','controladores.cont_nombres',
       'controladores.cont_ApPaterno','controladores.cont_ApMaterno','controladores.cont_correo',
       'controladores.cont_estado')
       ->selectRaw('GROUP_CONCAT(dis.idDispositivos) as ids')
       ->groupBy('controladores.idControladores')
       ->get()->first();

       return $controladores;
    }

    public function controladUpdate(Request $request){
        $controladores = controladores::findOrFail($request->idcontr_ed);
        $controladores->cont_codigo=$request->codigoCon_ed;
        $controladores->cont_nombres=$request->nombresCon_ed;
        $controladores->cont_ApPaterno=$request->paternoCon_ed;
        $controladores->cont_ApMaterno=$request->maternoCon_ed;
        $controladores->cont_correo=$request->correoCon_ed;
        $controladores->save();

        $idDispositi=$request->dispoCon_ed;
        if($idDispositi){
            foreach($idDispositi as $idDispositis){

                $dispositivo_controlador=dispositivo_controlador::where('idDispositivos',$idDispositis)
                ->where('idControladores',$request->idcontr_ed)->where('organi_id',session('sesionidorg'))
                ->get()->first();
                if($dispositivo_controlador==null){
                    $dispositivo_controlador=new dispositivo_controlador();
                    $dispositivo_controlador->idDispositivos=$idDispositis;
                    $dispositivo_controlador->idControladores=$request->idcontr_ed;
                    $dispositivo_controlador->organi_id=session('sesionidorg');
                    $dispositivo_controlador->save();
                }
              }

              $dispositivo_controladorF=dispositivo_controlador::where('idControladores',$request->idcontr_ed)
              ->where('organi_id',session('sesionidorg'))
              ->pluck('idDispositivos');
              foreach($dispositivo_controladorF as $idsDisRegi){
                  if (in_array($idsDisRegi, $idDispositi)) {
                  /* dd('esta'); */}
                  else
                  {
                    $dispositivo_controlador=dispositivo_controlador::where('idDispositivos',$idsDisRegi)
                    ->where('idControladores',$request->idcontr_ed)->where('organi_id',session('sesionidorg'))
                    ->delete();
                      /* dd('no esta esta'); */
                  }
              }
        }
        else{
            $dispositivo_controlador=dispositivo_controlador::where('idControladores',$request->idcontr_ed)
            ->where('organi_id',session('sesionidorg'))
            ->delete();
        }


    }

}
