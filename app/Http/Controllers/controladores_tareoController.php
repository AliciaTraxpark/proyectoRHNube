<?php

namespace App\Http\Controllers;

use App\controladores_tareo;
use App\dispositivos_tareo;
use App\dispositivo_controlador_tareo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class controladores_tareoController extends Controller
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
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $dispositivo = dispositivos_tareo::where('organi_id', '=', session('sesionidorg'))
                ->get();

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
               if ($invitadod->modoTareo == 1) {
                $permiso_invitado = DB::table('permiso_invitado')
                ->where('idinvitado', '=', $invitadod->idinvitado)
                ->get()->first();
                return view('ControladorTareo.controladoresT', ['dispositivo' => $dispositivo,
                'verModoTareo' => $permiso_invitado->verModoTareo, 'agregarModoTareo' => $permiso_invitado->agregarModoTareo,
                 'modifModoTareo' => $permiso_invitado->modifModoTareo]);
                } else {
                return redirect('/dashboard');
                }

                } else {
                    return view('ControladorTareo.controladoresT', ['dispositivo' => $dispositivo]);
                }
            } else {
                return view('ControladorTareo.controladoresT', ['dispositivo' => $dispositivo]);
            }
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
        $controladores = new controladores_tareo();
        $controladores->contrT_codigo = $request->codigoCon;
        $controladores->contrT_nombres = $request->nombresCon;
        $controladores->contrT_ApPaterno = $request->paternoCon;
        $controladores->contrT_ApMaterno = $request->maternoCon;
        $controladores->contrT_correo = $request->correoCon;
        $controladores->contrT_estado = 1;
        $controladores->organi_id = session('sesionidorg');
        $controladores->save();

        $idDispositi = $request->dispoCon;
        if ($idDispositi) {
            foreach ($idDispositi as $idDispositis) {
                $dispositivo_controlador = new dispositivo_controlador_tareo();
                $dispositivo_controlador->iddispositivos_tareo = $idDispositis;
                $dispositivo_controlador->idcontroladores_tareo = $controladores->idcontroladores_tareo;
                $dispositivo_controlador->organi_id = session('sesionidorg');
                $dispositivo_controlador->save();
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\controladores_tareo  $controladores_tareo
     * @return \Illuminate\Http\Response
     */
    public function show(controladores_tareo $controladores_tareo)
    {
        //
        $controladores = controladores_tareo::where('controladores_tareo.organi_id', '=', session('sesionidorg'))

            ->leftJoin('dispositivo_controlador_tareo as dc', 'controladores_tareo.idcontroladores_tareo', '=', 'dc.idcontroladores_tareo')

            ->leftJoin('dispositivos_tareo as dis', 'dc.iddispositivos_tareo', '=', 'dis.iddispositivos_tareo')

            ->select('controladores_tareo.idcontroladores_tareo', 'controladores_tareo.contrT_codigo',
                'controladores_tareo.contrT_nombres', 'controladores_tareo.contrT_ApPaterno',
                'controladores_tareo.contrT_ApMaterno', 'controladores_tareo.contrT_correo',
                'controladores_tareo.contrT_estado')
            ->selectRaw('GROUP_CONCAT(dis.dispoT_movil) as ids')
            ->groupBy('controladores_tareo.idcontroladores_tareo')
            ->get();

        return json_encode($controladores);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\controladores_tareo  $controladores_tareo
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
        $controladores = controladores_tareo::where('controladores_tareo.organi_id', '=', session('sesionidorg'))
            ->where('controladores_tareo.idcontroladores_tareo', $request->id)
            ->leftJoin('dispositivo_controlador_tareo as dc', 'controladores_tareo.idcontroladores_tareo', '=', 'dc.idcontroladores_tareo')
            ->leftJoin('dispositivos_tareo as dis', 'dc.iddispositivos_tareo', '=', 'dis.iddispositivos_tareo')
            ->select('controladores_tareo.idcontroladores_tareo', 'controladores_tareo.contrT_codigo', 'controladores_tareo.contrT_nombres',
                'controladores_tareo.contrT_ApPaterno', 'controladores_tareo.contrT_ApMaterno', 'controladores_tareo.contrT_correo',
                'controladores_tareo.contrT_estado')
            ->selectRaw('GROUP_CONCAT(dis.iddispositivos_tareo) as ids')
            ->groupBy('controladores_tareo.idcontroladores_tareo')
            ->get()->first();

        return $controladores;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\controladores_tareo  $controladores_tareo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, controladores_tareo $controladores_tareo)
    {
        //
        $controladores = controladores_tareo::findOrFail($request->idcontr_ed);
        $controladores->contrT_codigo = $request->codigoCon_ed;
        $controladores->contrT_nombres = $request->nombresCon_ed;
        $controladores->contrT_ApPaterno = $request->paternoCon_ed;
        $controladores->contrT_ApMaterno = $request->maternoCon_ed;
        $controladores->contrT_correo = $request->correoCon_ed;
        $controladores->save();

        $idDispositi = $request->dispoCon_ed;
        if ($idDispositi) {

            /* -------------RECORREMOS IDS DE DISPOSITIVOS QUE RECIBIMO------------------------- */
            foreach ($idDispositi as $idDispositis) {

                /* VERIFICAMOS SI EXISTE RELAACION ENTRE ESTE CONTROLADOR Y CADA UNO DE LOS DISPOSITIVOS */
                $dispositivo_controlador = dispositivo_controlador_tareo::where('iddispositivos_tareo', $idDispositis)
                    ->where('idcontroladores_tareo', $request->idcontr_ed)->where('organi_id', session('sesionidorg'))
                    ->get()->first();
                /* --------------------------------------------------------------------------------------- */

                /* SI NO HAY RELACION ENTONCES CREAMOS LA RELACION */
                if ($dispositivo_controlador == null) {
                    $dispositivo_controlador = new dispositivo_controlador_tareo();
                    $dispositivo_controlador->iddispositivos_tareo = $idDispositis;
                    $dispositivo_controlador->idcontroladores_tareo = $request->idcontr_ed;
                    $dispositivo_controlador->organi_id = session('sesionidorg');
                    $dispositivo_controlador->save();
                }
                /* ------------------------------------------------- */
            }
            /* ----------------------------------------------------------------------------------- */

            /*------------------- OBTENSMOS LOS ID DE LOS DISPOSITIVOS DE ESTE CONTROLADOR------------------------- */
            $dispositivo_controladorF = dispositivo_controlador_tareo::where('idcontroladores_tareo', $request->idcontr_ed)
                ->where('organi_id', session('sesionidorg'))
                ->pluck('iddispositivos_tareo');
            /* ---------------------------------------------------------------------------------------------------- */

            /* RECORREMOS  LOS ID DE DIPSITIVOS */
            foreach ($dispositivo_controladorF as $idsDisRegi) {

                /* VERIFICAMOS SI IDS DE DISPO ESTAN DENTRO DE IDS REGISTRADOS */
                if (in_array($idsDisRegi, $idDispositi)) {
                    /* dd('esta'); */}
                    else {
                        /* ELIMINAMOS LA RELACION SI NO ESTA */
                    $dispositivo_controlador = dispositivo_controlador_tareo::where('iddispositivos_tareo', $idsDisRegi)
                        ->where('idcontroladores_tareo', $request->idcontr_ed)->where('organi_id', session('sesionidorg'))
                        ->delete();
                    /* dd('no esta esta'); */
                }
            }
        } else {
            /* SI NO HAY DISPOSITIVOS */
            $dispositivo_controlador = dispositivo_controlador_tareo::where('idcontroladores_tareo', $request->idcontr_ed)
                ->where('organi_id', session('sesionidorg'))
                ->delete();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\controladores_tareo  $controladores_tareo
     * @return \Illuminate\Http\Response
     */
    public function destroy(controladores_tareo $controladores_tareo)
    {
        //
    }
}
