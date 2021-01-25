<?php

namespace App\Http\Controllers;

use App\actividad_subactividad;
use App\marcacion_tareo;
use App\subactividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class subactividadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if ($invitadod->gestionActiv == 1) {
                        $permiso_invitado = DB::table('permiso_invitado')
                            ->where('idinvitado', '=', $invitadod->idinvitado)
                            ->get()->first();
                        return view('MantenedorActividades.subactividades', [
                            'agregarActi' => $permiso_invitado->agregarActi, 'modifActi' => $permiso_invitado->modifActi, 'bajaActi' => $permiso_invitado->bajaActi,
                        ]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('MantenedorActividades.subactividades');
                }
            } else {
                return view('MantenedorActividades.subactividades');
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
        /* OBTEMOS VALORES DE PARAMETRO  */
        $nombreSub = $request->nombreSub;
        $codigoSub = $request->codigoSub;
        $idActividad = $request->idActividad;
        $modoTareo = $request->tareosub;

        /* REGISTRAMOS SUBCTIVIDAD */
        $subactividad = new subactividad();
        $subactividad->subAct_nombre = $nombreSub;
        $subactividad->subAct_codigo = $codigoSub;
        $subactividad->estado = 1;
        $subactividad->modoTareo = $modoTareo;
        $subactividad->organi_id = session('sesionidorg');
        $subactividad->save();

        /* AHORA REGISTRAMOS LA  RELACION CON EL ID DE LA SUBACTIVIDAD QUE
        ACABAMOS DE REGISTRAR CON EL ID DE ACTIVIDAD QUE OBTUVIMOS DE FRONTED */

        $actividad_subactividad = new actividad_subactividad();
        $actividad_subactividad->Activi_id = $idActividad;
        $actividad_subactividad->subActividad = $subactividad->idsubActividad;
        $actividad_subactividad->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\subactividad  $subactividad
     * @return \Illuminate\Http\Response
     */
    public function show(subactividad $subactividad)
    {
        //

        $Subactividades = DB::table('subactividad as su')
            ->select(
                'su.idsubActividad',
                'su.subAct_nombre',
                'su.subAct_codigo',
                'su.estado',
                'su.modoTareo',
                'su.organi_id'
            )
            ->where('su.organi_id', '=', session('sesionidorg'))
            ->where('su.estado', '=', 1)
            ->groupBy('su.idsubActividad')
            ->get();

        /* VERIDICAMOS SI ESTA EN USO */
        foreach ($Subactividades as $SubactividadesM) {
            $marcacion_tareo = DB::table('marcacion_tareo as mt')
                ->where('mt.idsubActividad', '=', $SubactividadesM->idsubActividad)
                ->groupBy('mt.idmarcaciones_tareo')
                ->get();

            /*  SI ESTA EN USO*/
            if ($marcacion_tareo->isNotEmpty()) {
                $SubactividadesM->uso = 1;
            }
            /* SI NO ESTA EN USO */
            else {
                $SubactividadesM->uso = 0;
            }
        }

        // dd(DB::getQueryLog());
        return response()->json($Subactividades, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\subactividad  $subactividad
     * @return \Illuminate\Http\Response
     */
    public function edit( Request $request)
    {
        //
        $idSub = $request->get('idSub');
        $Subactividades = DB::table('subactividad as su')
        ->leftJoin('actividad_subactividad as asu','su.idsubActividad','=','asu.subActividad')
        ->select(
            'su.idsubActividad',
            'su.subAct_nombre',
            'su.subAct_codigo',
            'su.estado',
            'su.modoTareo',
            'su.organi_id',
            'asu.Activi_id'
        )
        ->where('su.organi_id', '=', session('sesionidorg'))
        ->where('su.estado', '=', 1)
        ->where('su.idsubActividad','=',$idSub)
        ->groupBy('su.idsubActividad')
        ->get();

    /* VERIDICAMOS SI ESTA EN USO */
    foreach ($Subactividades as $SubactividadesM) {
        $marcacion_tareo = DB::table('marcacion_tareo as mt')
            ->where('mt.idsubActividad', '=', $SubactividadesM->idsubActividad)
            ->groupBy('mt.idmarcaciones_tareo')
            ->get();

        /*  SI ESTA EN USO*/
        if ($marcacion_tareo->isNotEmpty()) {
            $SubactividadesM->uso = 1;
        }
        /* SI NO ESTA EN USO */
        else {
            $SubactividadesM->uso = 0;
        }
    }



    // dd(DB::getQueryLog());
    return response()->json($Subactividades->first(), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\subactividad  $subactividad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, subactividad $subactividad)
    {
        //

        $idSuactiv=$request->idSuactiv;
        $codigo=$request->codigo;
        $idActividad=$request->idActividad;
        $modoTareo=$request->modoTareo;

        $subactividad=subactividad::findOrFail($idSuactiv);
        $subactividad->subAct_codigo=$codigo;
        $subactividad->modoTareo=$modoTareo;
        $subactividad->save();

        $actividad_subactividad = actividad_subactividad::where('subActividad', '=', $idSuactiv)
        ->update(['Activi_id' => $idActividad]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\subactividad  $subactividad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $idSubactiv = $request->get('id');
        $subactividad = subactividad::findOrFail($idSubactiv);
        $marcacion_tareo = marcacion_tareo::where('idsubActividad', '=', $idSubactiv)
            ->get()->first();
        if ($marcacion_tareo) {
            return 1;
        } else {
            if ($subactividad) {
                $subactividad->estado = 0;
                $subactividad->save();

                $actividad_subactividad = actividad_subactividad::where('subActividad', '=', $idSubactiv)
                    ->get()->first();
                $actividad_subactividad->delete();
            }
            return response()->json($subactividad, 200);

        }

    }

    public function actividadesTareo(Request $request)
    {
        $actividades = DB::table('actividad as a')
            ->select('a.Activi_id as idActividad', 'a.Activi_Nombre as nombre')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.estado', '=', 1)
            ->where('a.eliminacion', '=', 1)
            ->where('a.modoTareo', '=', 1)
            ->get();

        return response()->json($actividades, 200);
    }
}
