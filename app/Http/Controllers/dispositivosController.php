<?php

namespace App\Http\Controllers;

use App\dispositivos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use LengthException;

class dispositivosController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index(){
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
                    return view('Dispositivos.dispositivos', [
                        'verPuerta'=>$permiso_invitado->verPuerta,'agregarPuerta'=>$permiso_invitado->agregarPuerta,'modifPuerta'=>$permiso_invitado->modifPuerta
                    ]);
                    } else{
                          return redirect('/dashboard');
                    }
                   /*   */


                } else {
                    return view('Dispositivos.dispositivos');
                }
            }
            else{
                return view('Dispositivos.dispositivos');
            }

    }
    public function store(Request $request){

      /*   dd($request->tData,$request->lectura); */
        $codigo=STR::random(4);

        $dispositivos=new dispositivos();
        $dispositivos->dispo_descripUbicacion=$request->descripccionUb;
        $dispositivos->dispo_movil=$request->numeroM;
        $dispositivos->dispo_tSincro=$request->tSincron;
        $dispositivos->dispo_tMarca=$request->tMarcac;
        $dispositivos->dispo_estadoActivo=1;
        $dispositivos->dispo_estado=0;
        $dispositivos->organi_id=session('sesionidorg');
        $dispositivos->dispo_Data=$request->tData;
        foreach($request->lectura as $lectura){
            if($lectura==1){
                $dispositivos->dispo_Manu=1;
            }
            if($lectura==2){
                $dispositivos->dispo_Scan=1;
            }

            if($lectura==3){
                $dispositivos->dispo_Cam=1;
            }
        }

        $dispositivos->save();

        if($request->smsCh==1){
            $dispositivosAc = dispositivos::findOrFail($dispositivos->idDispositivos);
            $dispositivosAc->dispo_estado=1;
            $dispositivosAc->dispo_codigo=$codigo;
            $dispositivosAc->save();

           $mensaje = "RH nube - Codigo de validacion " . $codigo;
           $curl = curl_init();
           curl_setopt_array($curl, array(
               CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_FOLLOWLOCATION => TRUE,
               CURLOPT_SSL_VERIFYPEER => false,
               CURLOPT_CUSTOMREQUEST => "POST",
               CURLOPT_POSTFIELDS => '{
                   "apiKey":2308,
                   "country":"PE",
                   "dial":38383,
                   "message":"' . $mensaje . '",
                   "msisdns":[' .  $dispositivosAc->dispo_movil . '],
                   "tag":"tag-prueba"
                }',
               CURLOPT_HTTPHEADER => array(
                   "Content-Type: application/json",
                   "Authorization:67p7e5ONkalvrKLDQh3RaONgSFs=",
                   "Cache-Control: no-cache"
               ),
           ));
           $err = curl_error($curl);
           $response = curl_exec($curl);

        }


    }
    public function enviarmensaje(Request $request){
        $codigo=STR::random(4);
        $dispositivosAc = dispositivos::findOrFail($request->idDis);
        $dispositivosAc->dispo_estado=1;
        $dispositivosAc->dispo_codigo=$codigo;
        $dispositivosAc->save();
       $mensaje = "RH nube - Codigo de validacion " . $codigo;
       $curl = curl_init();
       curl_setopt_array($curl, array(
           CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_FOLLOWLOCATION => TRUE,
           CURLOPT_SSL_VERIFYPEER => false,
           CURLOPT_CUSTOMREQUEST => "POST",
           CURLOPT_POSTFIELDS => '{
               "apiKey":2308,
               "country":"PE",
               "dial":38383,
               "message":"' . $mensaje . '",
               "msisdns":[' .  $dispositivosAc->dispo_movil . '],
               "tag":"tag-prueba"
            }',
           CURLOPT_HTTPHEADER => array(
               "Content-Type: application/json",
               "Authorization:67p7e5ONkalvrKLDQh3RaONgSFs=",
               "Cache-Control: no-cache"
           ),
       ));
       $err = curl_error($curl);
       $response = curl_exec($curl);


    }

    public function reenviarmensaje(Request $request){

        $dispositivosAc = dispositivos::findOrFail($request->idDis);
        $codigo=$dispositivosAc->dispo_codigo;
       $mensaje = "RH nube - Codigo de validacion " . $codigo;
       $curl = curl_init();
       curl_setopt_array($curl, array(
           CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_FOLLOWLOCATION => TRUE,
           CURLOPT_SSL_VERIFYPEER => false,
           CURLOPT_CUSTOMREQUEST => "POST",
           CURLOPT_POSTFIELDS => '{
               "apiKey":2308,
               "country":"PE",
               "dial":38383,
               "message":"' . $mensaje . '",
               "msisdns":[' .  $dispositivosAc->dispo_movil . '],
               "tag":"tag-prueba"
            }',
           CURLOPT_HTTPHEADER => array(
               "Content-Type: application/json",
               "Authorization:67p7e5ONkalvrKLDQh3RaONgSFs=",
               "Cache-Control: no-cache"
           ),
       ));
       $err = curl_error($curl);
       $response = curl_exec($curl);


    }

    public function tablaDisposit(){
        $dispositivos=dispositivos::where('organi_id','=',session('sesionidorg'))->get();
        return json_encode($dispositivos);
    }

    public function comprobarMovil(Request $request){

        $dispositivos=dispositivos::where('dispo_movil','=',$request->numeroM)->get()->first();

        if($dispositivos!= null){
            return 1;
        }
        else{
            return 0;
        }
    }

    public function reporteMarcaciones(){
        $organizacion=DB::table('organizacion')
        ->where('organi_id','=',session('sesionidorg'))
        ->get()->first();
        $nombreOrga= $organizacion->organi_razonSocial;
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
           /*  ->where('e.emple_estado', '=', 1)    */

            ->get();
    return view('Dispositivos.reporteDis',['organizacion'=>$nombreOrga,'empleado'=>$empleados]);
 }

 public function reporteTabla(Request $request){
     $fechaR=$request->fecha;
    /*  dd($fechaR); */
     $idemp=$request->idemp;
      $fecha=Carbon::create($fechaR);
      $año= $fecha->year;
      $mes= $fecha->month;
      $dia= $fecha->day;
      $ndia= $dia+1;
      if($idemp==0 || $idemp==' '){
        $marcaciones=DB::table('marcacion_movil as marcm')
        ->select('e.emple_id','marcm.marcaMov_id','emple_nDoc','perso_nombre','perso_apPaterno','perso_apMaterno',
        'cargo_descripcion' ,'marcm.organi_id')
        ->leftJoin('empleado as e','marcm.marcaMov_emple_id','=','e.emple_id')
        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
        ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->where('marcm.organi_id','=',session('sesionidorg'))

        /*      */
        ->selectRaw('GROUP_CONCAT(IF(marcaMov_fecha is null,0,marcaMov_fecha) ORDER BY marcm.marcaMov_id DESC) as entrada ')
        ->selectRaw('GROUP_CONCAT(IF(marcaMov_salida is null,0,marcaMov_salida) ORDER BY marcm.marcaMov_id DESC)  as final  ')
        ->groupBy('marcm.marcaMov_emple_id')
           ->whereDate('marcaMov_fecha',$fecha)
           ->orwhere(function($query) use ($fecha) {
               $query->where('marcaMov_fecha', null)
               ->whereDate('marcaMov_salida',$fecha)

               ->where('marcm.organi_id','=',session('sesionidorg'));
           })
       ->get() ;
      } else{
        $marcaciones=DB::table('marcacion_movil as marcm')
        ->select('e.emple_id','marcm.marcaMov_id','emple_nDoc','perso_nombre','perso_apPaterno','perso_apMaterno',
        'cargo_descripcion' ,'marcm.organi_id')
        ->leftJoin('empleado as e','marcm.marcaMov_emple_id','=','e.emple_id')
        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
        ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->where('marcm.organi_id','=',session('sesionidorg'))
        ->where('e.emple_id',$idemp)
        /*      */
        ->selectRaw('GROUP_CONCAT(IF(marcaMov_fecha is null,0,marcaMov_fecha) ORDER BY marcm.marcaMov_id DESC) as entrada ')
        ->selectRaw('GROUP_CONCAT(IF(marcaMov_salida is null,0,marcaMov_salida) ORDER BY marcm.marcaMov_id DESC)  as final  ')
        ->groupBy('marcm.marcaMov_emple_id')
           ->whereDate('marcaMov_fecha',$fecha)
           ->orwhere(function($query) use ($fecha,$idemp) {
               $query->where('marcaMov_fecha', null)
               ->whereDate('marcaMov_salida',$fecha)
               ->where('e.emple_id',$idemp)
               ->where('marcm.organi_id','=',session('sesionidorg'));
           })
       ->get() ;
      }





     return json_encode($marcaciones);

 }

 public function datosDispoEditar(Request $request){
     $idDispo=$request->id;
     $dispositivo=dispositivos::where('organi_id','=',session('sesionidorg'))
     ->where('idDispositivos',$idDispo)->get()->first();
        return $dispositivo;
 }

 public function actualizarDispos(Request $request){
    $dispositivos = dispositivos::findOrFail($request->idDisposEd_ed);
    $dispositivos->dispo_descripUbicacion=$request->descripccionUb_ed;
    $dispositivos->dispo_movil=$request->numeroM_ed;
    $dispositivos->dispo_tSincro=$request->tSincron_ed;
    $dispositivos->dispo_tMarca=$request->tMarca_ed;
    $dispositivos->dispo_Data=$request->tData_ed;
    foreach($request->lectura_ed as $lectura){
        if($lectura==1){
            $dispositivos->dispo_Manu=1;
        }
        if($lectura==2){
            $dispositivos->dispo_Scan=1;
        }

        if($lectura==3){
            $dispositivos->dispo_Cam=1;
        }
    }

    $dispositivos->save();

 }

 public function desactivarDisposi(Request $request){

    $dispositivos = dispositivos::findOrFail($request->idDisDesac);
    $dispositivos->dispo_estadoActivo=0;
    $dispositivos->save();
 }

 public function activarDisposi(Request $request){

    $dispositivos = dispositivos::findOrFail($request->idDisAct);
    $dispositivos->dispo_estadoActivo=1;
    $dispositivos->save();
 }
}
