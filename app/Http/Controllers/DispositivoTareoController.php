<?php

namespace App\Http\Controllers;

use App\dispositivos_tareo;
use App\dispositivo_controlador_tareo;
use App\controladores_tareo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\correoVinculacionTareo;

class DispositivoTareoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        //
        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('rol_id', '=', 3)
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();

        $controladoresTareo = DB::table('controladores_tareo')
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('contrT_estado', '=', 1)
            ->get();

        /* FILTRAMOS EMPLEADOS */
        if ($invitadod) {

            /*  */
            if ($invitadod->rol_id != 1) {
                /* AQUI VALIDAREMOS PERMISOS PARA INVITADO */
               if ($invitadod->modoTareo == 1) {
                $permiso_invitado = DB::table('permiso_invitado')
                ->where('idinvitado', '=', $invitadod->idinvitado)
                ->get()->first();
                return view('DispositivoTareo.dispositivosT', ['controladores' => $controladoresTareo,
                'verModoTareo' => $permiso_invitado->verModoTareo, 'agregarModoTareo' => $permiso_invitado->agregarModoTareo,
                'modifModoTareo' => $permiso_invitado->modifModoTareo
                ]);
                } else {
                return redirect('/dashboard');
                }
                /*   */
            } else {
                return view('DispositivoTareo.dispositivosT', ['controladores' => $controladoresTareo]);
            }
        } else {

            return view('DispositivoTareo.dispositivosT', ['controladores' => $controladoresTareo]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $idorEncrip = base64_encode(session('sesionidorg'));
        $codigo = STR::random(4).$idorEncrip;

        $dispositivos = new dispositivos_tareo();
        $dispositivos->tipo_dispositivo_id = 2;
        $dispositivos->dispoT_descripUbicacion = $request->descripccionUb;
        $dispositivos->dispoT_movil = $request->numeroM;
        $dispositivos->dispoT_tSincro = $request->tSincron;
        $dispositivos->dispoT_tMarca = $request->tMarcac;
        $dispositivos->dispoT_estadoActivo = 1;
        $dispositivos->dispoT_estado = 0;
        $dispositivos->organi_id = session('sesionidorg');
        $dispositivos->dispoT_Data = $request->tData;
        foreach ($request->lectura as $lectura) {
            if ($lectura == 1) {
                $dispositivos->dispoT_Manu = 1;
            }
            if ($lectura == 2) {
                $dispositivos->dispoT_Scan = 1;
            }

            if ($lectura == 3) {
                $dispositivos->dispoT_Cam = 1;
            }
        }

        $dispositivos->save();

        $contro = $request->idContro;
        if ($contro != null) {
            foreach ($contro as $contros) {
                $dispositivo_controlador = new dispositivo_controlador_tareo();
                $dispositivo_controlador->iddispositivos_tareo = $dispositivos->iddispositivos_tareo;
                $dispositivo_controlador->idcontroladores_tareo = $contros;
                $dispositivo_controlador->organi_id = session('sesionidorg');
                $dispositivo_controlador->save();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                // ENVIO DE C??DIGO POR CORREO
                $controlador = controladores_tareo::find($contros)->first();
                $organizacion = organizacion::find(session('sesionidorg'))->first();
                $email = $controlador->contrT_correo;
                $numero = $request->numeroM;
                $envio = Mail::to($email)->queue(new correoVinculacionTareo($codigo, $numero, $organizacion));
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            }
        }

        if ($request->smsCh == 1) {
            $dispositivosAc = dispositivos_tareo::findOrFail($dispositivos->iddispositivos_tareo);
            $dispositivosAc->dispoT_estado = 1;
            $dispositivosAc->dispoT_codigo = $codigo;
            $dispositivosAc->save();

            $nroCel = substr($dispositivosAc->dispoT_movil, 2);

            $mensaje = "Dispositivo " . $nroCel . " registrado en RH nube - Modo Tareo, tu codigo es " . $codigo . " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube.modotareo";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '{
                   "apiKey":2308,
                   "country":"PE",
                   "dial":38383,
                   "message":"' . $mensaje . '",
                   "msisdns":[' . $dispositivosAc->dispoT_movil . '],
                   "tag":"tag-prueba"
                }',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: zAS+nYnqJ+zX8KBr05ojMufSWuo=",
                    "Cache-Control: no-cache",
                ),
            ));
            $err = curl_error($curl);
            $response = curl_exec($curl);
            if($response==false){
                return 'Hubo un error en el servidor de mensajeria, no se pudo enviar el SMS';
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\dispositivos_tareo  $dispositivos_tareo
     * @return \Illuminate\Http\Response
     */
    public function show(dispositivos_tareo $dispositivos_tareo)
    {
        $dispositivos = dispositivos_tareo::where('organi_id', '=', session('sesionidorg'))
            ->where('tipo_dispositivo_id', '=', 2)->get();

        return json_encode($dispositivos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\dispositivos_tareo  $dispositivos_tareo
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
        $idDispo = $request->id;
        $dispositivo = dispositivos_tareo::where('dispositivos_tareo.organi_id', '=', session('sesionidorg'))

            ->where('dispositivos_tareo.iddispositivos_tareo', $idDispo)
            ->select(
                'dispositivos_tareo.iddispositivos_tareo',
                'tipo_dispositivo_id',
                'dispoT_descripUbicacion',
                'dispoT_movil',
                'dispoT_tSincro',
                'dispoT_tMarca',
                'dispoT_Data',
                'dispoT_Manu',
                'dispoT_Scan',
                'dispoT_Cam',
                'dispoT_codigo'
            )->get();

            foreach( $dispositivo as  $dispositivos){
                $disposit_controlador = DB::table('dispositivo_controlador_tareo as dc')
                        ->select('idcontroladores_tareo')
                        ->where('dc.iddispositivos_tareo', '=', $dispositivos->iddispositivos_tareo)
                        ->get();

                 $dispositivos->idConts =  $disposit_controlador;

            }

        return [$dispositivo[0]];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\dispositivos_tareo  $dispositivos_tareo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, dispositivos_tareo $dispositivos_tareo)
    {
        //
        /* ---------------ACTUALIZO DATOS DE DISPOSITIV TAREO---------------- */
        $dispositivos = dispositivos_tareo::findOrFail($request->idDisposEd_ed);
        $dispositivos->dispoT_descripUbicacion = $request->descripccionUb_ed;
        $dispositivos->dispoT_movil = $request->numeroM_ed;
        $dispositivos->dispoT_tSincro = $request->tSincron_ed;
        $dispositivos->dispoT_tMarca = $request->tMarca_ed;
        $dispositivos->dispoT_Data = $request->tData_ed;
        $lectura=$request->lectura_ed;


        //*ASIGNANDO
        if(in_array("1", $lectura)){

                $dispositivos->dispoT_Manu = 1;


        }
        else{
            $dispositivos->dispoT_Manu = 0;
        }

        if(in_array("2", $lectura)){

            $dispositivos->dispoT_Scan = 1;
        }
        else{
            $dispositivos->dispoT_Scan = 0;
        }

        if(in_array("3", $lectura)){

                $dispositivos->dispoT_Cam = 1;
        }
        else{
            $dispositivos->dispoT_Cam = 0;
        }

        $dispositivos->save();
        /* -------------------------------------------------------------------- */

        $idcont_id = $request->idcont_id;

        /* VERIFICAMOS SI HAY CONTROLADORES CON ESTE DISPOSITIVO */
        $dispositivo_contAnt = dispositivo_controlador_tareo::where('iddispositivos_tareo', '=', $request->idDisposEd_ed)
            ->get();
        /* ----------------------------------------------------- */

        /*---------------------- SI HAY DATOS------------------  */
        if ($dispositivo_contAnt->isNotEmpty()) {
            /* ------------------------------------------- */
            if ($idcont_id != null) {
            foreach ($idcont_id as $idconts) {
                $estado = false;
                for ($index = 0; $index < sizeof($dispositivo_contAnt); $index++) {
                    /* SI TENEMOS REGISTRADO LOS ID DE CONTROLADORES QUE EQUEREMOS INSERTAR */

                    if ($dispositivo_contAnt[$index]->idcontroladores_tareo == $idconts) {
                        $estado = true;

                    }
                }
                if ($estado == false) {
                    /* SI NO COINCIDE LOS ID REGISTRADOS CON LOS NUEVOS ENTONCES REGISTRAMOS NUEVOS CONTROLS */
                    $dispositivos_controladorReg = new dispositivo_controlador_tareo();
                    $dispositivos_controladorReg->iddispositivos_tareo = $request->idDisposEd_ed;
                    $dispositivos_controladorReg->idcontroladores_tareo = $idconts;
                    $dispositivos_controladorReg->organi_id = session('sesionidorg');
                    $dispositivos_controladorReg->save();
                }

            }
            }

            /* COMPARAMOS LOS REGISTRADOS CON LA LISTA DE CONTROLADORES PARA DESCARTAR LOS QUE YA NO TIENEN*/
            foreach ($dispositivo_contAnt as $idcontDis) {
                $estadoEReg = false;

                if ($idcont_id != null) {
                foreach ($idcont_id as $idcos) {
                    if ($idcontDis->idcontroladores_tareo == $idcos) {
                        $estadoEReg = true;
                    }
                }
            }
                if ($estadoEReg == false) {

                    $borrarDispo = dispositivo_controlador_tareo::where('iddispositivos_tareo', '=', $request->idDisposEd_ed)
                        ->where('idcontroladores_tareo', '=', $idcontDis->idcontroladores_tareo)
                        ->where('organi_id', '=', session('sesionidorg'))->get();
                    if ($borrarDispo) {
                        $borrarDispo->each->delete();
                    }

                }

            }
            /* ------------------------------------------- */

        } else {
            /* SI ESTA VACIO REGISTRAMOS TODOS LOS DATOS DE FRONTED */
            if ($idcont_id != null) {
                foreach ($idcont_id as $contros) {
                    $dispositivo_controlador = new dispositivo_controlador_tareo();
                    $dispositivo_controlador->iddispositivos_tareo = $request->idDisposEd_ed;
                    $dispositivo_controlador->idcontroladores_tareo = $contros;
                    $dispositivo_controlador->organi_id = session('sesionidorg');
                    $dispositivo_controlador->save();
                }
            }
            /* ---------------------------------------------------- */
        }
        /* ----------------------------------------------------- */
        /* $borrarDispo = dispositivo_controlador_tareo::where('iddispositivos_tareo', '=', $request->idDisposEd_ed)
    ->where('organi_id', '=', session('sesionidorg'))->get();
    if ($borrarDispo) {
    $borrarDispo->each->delete();
    } */

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\dispositivos_tareo  $dispositivos_tareo
     * @return \Illuminate\Http\Response
     */
    public function destroy(dispositivos_tareo $dispositivos_tareo)
    {
        //
    }

    public function comprobarMovil(Request $request)
    {

        $dispositivos = dispositivos_tareo::where('dispoT_movil', '=', $request->numeroM)
        ->where('organi_id','=',session('sesionidorg'))->get()->first();

        if ($dispositivos != null) {
            return 1;
        } else {
            return 0;
        }
    }

    public function enviarmensaje(Request $request)
    {
        $idorEncrip = base64_encode(session('sesionidorg'));
        $codigo = STR::random(4).$idorEncrip;
        $dispositivosAc = dispositivos_tareo::findOrFail($request->idDis);
        $dispositivosAc->dispoT_estado = 1;
        $dispositivosAc->dispoT_codigo = $codigo;
        $dispositivosAc->save();
        $nroCel = substr($dispositivosAc->dispoT_movil, 2);

        $mensaje = "Dispositivo " . $nroCel . " registrado en RH nube - Modo Tareo, tu codigo es " . $codigo . " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube.modotareo";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
               "apiKey":2308,
               "country":"PE",
               "dial":38383,
               "message":"' . $mensaje . '",
               "msisdns":[' . $dispositivosAc->dispoT_movil . '],
               "tag":"tag-prueba"
            }',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: zAS+nYnqJ+zX8KBr05ojMufSWuo=",
                "Cache-Control: no-cache",
            ),
        ));
        $err = curl_error($curl);
        $response = curl_exec($curl);
        if($response==false){
            return 'Hubo un error en el servidor de mensajeria, no se pudo enviar el SMS';
        }
    }
    public function reenviarmensaje(Request $request)
    {

        $dispositivosAc = dispositivos_tareo::findOrFail($request->idDis);
        $codigo = $dispositivosAc->dispoT_codigo;
        $nroCel = substr($dispositivosAc->dispoT_movil, 2);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // ENVIO DE C??DIGO POR CORREO
        $dispoControls = dispositivo_controlador_tareo::where('iddispositivos_tareo', '=', $request->idDis)->get();
        foreach ($dispoControls as $dispoControl) {
            $controlador = controladores_tareo::find($dispoControl->idcontroladores_tareo)->first();
            $organizacion = organizacion::find(session('sesionidorg'))->first();
            $email = $controlador->contrT_correo;
            $numero = $request->numeroM;
            $envio = Mail::to($email)->queue(new correoVinculacionTareo($codigo, $numero, $organizacion));
        }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $mensaje = "Dispositivo " . $nroCel . " registrado en RH nube - Modo Tareo, tu codigo es " . $codigo . " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube.modotareo";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
               "apiKey":2308,
               "country":"PE",
               "dial":38383,
               "message":"' . $mensaje . '",
               "msisdns":[' . $dispositivosAc->dispoT_movil . '],
               "tag":"tag-prueba"
            }',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: zAS+nYnqJ+zX8KBr05ojMufSWuo=",
                "Cache-Control: no-cache",
            ),
        ));
        $err = curl_error($curl);
        $response = curl_exec($curl);

       if($response==false){
           return 'Hubo un error en el servidor de mensajeria, no se pudo enviar el SMS';
       }
    }

    public function desactivarDisposi(Request $request)
    {

        $dispositivos = dispositivos_tareo::findOrFail($request->idDisDesac);
        $dispositivos->dispoT_estadoActivo = 0;
        $dispositivos->save();
    }

    public function activarDisposi(Request $request)
    {

        $dispositivos = dispositivos_tareo::findOrFail($request->idDisAct);
        $dispositivos->dispoT_estadoActivo = 1;
        $dispositivos->save();
    }
}
