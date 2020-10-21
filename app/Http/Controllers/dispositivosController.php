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

        return view('Dispositivos.dispositivos');
    }
    public function store(Request $request){

      /*   dd($request->tData,$request->lectura); */
        $codigo=STR::random(4);

        $dispositivos=new dispositivos();
        $dispositivos->dispo_descripUbicacion=$request->descripccionUb;
        $dispositivos->dispo_movil=$request->numeroM;
        $dispositivos->dispo_tSincro=$request->tSincron;
        $dispositivos->dispo_tMarca=$request->tMarcac;
        $dispositivos->dispo_estado=0;
        $dispositivos->organi_id=session('sesionidorg');
        $dispositivos->dispo_Data=$request->tData;
        foreach($request->lectura as $lectura){
            if($lectura==1){
                $dispositivos->dispo_Manu=1;
            }
           /*  else{
                $dispositivos->dispo_Manu=0;

            } */

            if($lectura==2){
                $dispositivos->dispo_Scan=1;
            }
           /*  else{
                $dispositivos->dispo_Scan=0;
            } */

            if($lectura==3){
                $dispositivos->dispo_Cam=1;
            }
           /*  else{
                $dispositivos->dispo_Cam=0;
            } */

        }

        /* if($request->lectura[0] || $request->lectura[1] ||$request->lectura[2]){
             dd($request->lectura.Length);
        } */

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
    return view('Dispositivos.reporteDis',['organizacion'=>$nombreOrga]);
 }

 public function reporteTabla(Request $request){
     $fechaR=$request->fecha;
    /*  dd($fechaR); */
      $fecha=Carbon::create($fechaR);
      $año= $fecha->year;
      $mes= $fecha->month;
      $dia= $fecha->day;
      $ndia= $dia+1;
     $marcaciones=DB::table('marcacion_movil as marcm')
     ->select('marcm.marcaMov_id','emple_nDoc','perso_nombre','perso_apPaterno','perso_apMaterno',
     'cargo_descripcion' )
     ->leftJoin('empleado as e','marcm.marcaMov_emple_id','=','e.emple_id')
     ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
     ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
     ->selectRaw('GROUP_CONCAT(IF(marcaMov_fecha is null,0,marcaMov_fecha)) as entrada')
     ->selectRaw('GROUP_CONCAT(IF(marcaMov_salida is null,0,marcaMov_salida)) as final')
     ->groupBy('marcm.marcaMov_emple_id')
    /*   ->whereYear('marcaMov_fecha',$año)
        ->whereMonth('marcaMov_fecha',$mes)
        ->whereDay('marcaMov_fecha',$dia) */
        ->whereDate('marcaMov_fecha',$fecha)
        ->orwhere(function($query) use ($fecha) {
            $query->where('marcaMov_fecha', null)
            ->whereDate('marcaMov_salida',$fecha);
        })

     ->where('marcm.organi_id','=',session('sesionidorg'))->get() ;

     /* $marcaciones1=$marcaciones->addSelect(DB::raw('(select marc2.marcaMov_fecha from marcacion_movil as marc2
      where marc2.marcaMov_tipo=0 and marcm.marcaMov_emple_id=marc2.marcaMov_emple_id and
      YEAR(marc2.marcaMov_fecha)= '.$año.' and MONTH(marc2.marcaMov_fecha)='.$mes.'
      and( DAY(marc2.marcaMov_fecha)='.$dia.' or DAY(marc2.marcaMov_fecha)='.$ndia.' )) as final' ))
     ->get(); */


     return json_encode($marcaciones);

 }
}
