<?php

namespace App\Http\Controllers;

use App\dispositivos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
class dispositivosController extends Controller
{
    //
    public function index(){

        return view('Dispositivos.dispositivos');
    }
    public function store(Request $request){
        $codigo=STR::random(4);

        $dispositivos=new dispositivos();
        $dispositivos->dispo_descripUbicacion=$request->descripccionUb;
        $dispositivos->dispo_movil=$request->numeroM;
        $dispositivos->dispo_tSincro=$request->tSincron;
        $dispositivos->dispo_tMarca=$request->tMarcac;
       $dispositivos->dispo_codigo=$codigo;
        $dispositivos->dispo_estado=0;
        $dispositivos->organi_id=session('sesionidorg');
        $dispositivos->save();

        if($request->smsCh==1){
            $dispositivosAc = dispositivos::findOrFail($dispositivos->idDispositivos);
            $dispositivosAc->dispo_estado=1;
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
          dd($err);
        }


    }
    public function enviarmensaje(){
        $codigo = "12";
        $codigoI = intval($codigo, 36);
       $mensaje = "RH nube - Codigo de validacion " . $codigoI;
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
               "msisdns":[51968009336],
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
      dd($err);
                   /*   if ($err) {
                        return 0;
                    } else {
                        return 1;
                    } */
    }

    public function tablaDisposit(){
        $dispositivos=dispositivos::where('organi_id','=',session('sesionidorg'))->get();
        return json_encode($dispositivos);
    }
}
