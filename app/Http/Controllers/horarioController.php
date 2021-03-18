<?php

namespace App\Http\Controllers;

use App\eventos;
use App\eventos_empleado;
use App\eventos_calendario;
use App\historial_horarioempleado;
use App\horario;
use App\horario_dias;
use App\horario_empleado;
use App\incidencias;
use App\incidencia_dias;
use App\paises;
use App\pausas_horario;
use App\temporal_eventos;
use App\ubigeo_peru_departments;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\organizacion;
use DateTime;
class horarioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    //
    public function index()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {

            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('calendario_empleado as eve', 'e.emple_id', '=', 'eve.emple_id')
                ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('eve.emple_id', '!=', null)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
            $horario = horario::where('organi_id', '=', session('sesionidorg'))->get();
            $horarion = DB::table('horario as h')
                ->leftJoin('horario_empleado as he', 'h.horario_id', '=', 'he.horario_horario_id')
                ->where('h.organi_id', '=', session('sesionidorg'))
                ->whereNull('he.horario_horario_id')
                ->get();

            //*AREA CUADNO TIENE ASIGNADO EMPLEADOS
            $area = DB::table('area as ar')
            ->join('empleado as em', 'ar.area_id', '=', 'em.emple_area')
            ->where('ar.organi_id','=',session('sesionidorg'))
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();

            //*CARGO CUADNO TIENE ASIGNADO EMPLEADO
            $cargo = DB::table('cargo as c')
            ->join('empleado as em', 'c.cargo_id', '=', 'em.emple_cargo')
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->select('cargo_id as idcargo',
                'cargo_descripcion as descripcion')
            ->groupBy('c.cargo_id')
            ->get();

            //*LOCAL CUADNO TIENE ASIGNADO EMPLEADO
            $local = DB::table('local as l')
            ->join('empleado as em', 'l.local_id', '=', 'em.emple_local')
            ->where('l.organi_id', '=', session('sesionidorg'))
            ->select('local_id as idlocal',
               'local_descripcion as descripcion')
            ->groupBy('l.local_id')
            ->get();

            //*NIVEL CUADNO TIENE ASIGNADO EMPLEADO
            $nivel = DB::table('nivel as n')
            ->join('empleado as em', 'n.nivel_id', '=', 'em.emple_nivel')
            ->where('n.organi_id', '=', session('sesionidorg'))
            ->select('nivel_id as idnivel',
               'nivel_descripcion as descripcion')
            ->groupBy('n.nivel_id')
            ->get();

            //*CENTRO DE COSTOS CUANDO TIENE ASIGNADO EMPLEADOS
            $centroc = DB::table('centro_costo as cc')
            ->join('centrocosto_empleado as ce', 'cc.centroC_id', '=', 'ce.idCentro')
            ->where('cc.organi_id', '=', session('sesionidorg'))
            ->where('ce.estado', '=', 1)
            ->select('cc.centroC_id as idcentro',
               'cc.centroC_descripcion as descripcion')
            ->groupBy('cc.centroC_id')
            ->get();

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            //*reglas
            $reglasHExtras=DB::table('reglas_horasextras')
            ->where('organi_id','=', session('sesionidorg'))
            ->where('idTipoRegla','=',1)
            ->get();

            //*reglas nocturnas
            $reglasHExtrasNocturno=DB::table('reglas_horasextras')
            ->where('organi_id','=', session('sesionidorg'))
            ->where('tipo_regla','=','Nocturno')
            ->orderBy('idTipoRegla','DESC')
            ->get();

            //*tipo iincidencia
            $tipo_incidencia=DB::table('tipo_incidencia')
            ->where('organi_id','=',session('sesionidorg'))
            ->where('sistema','=',0)
            ->orderBy('tipoInc_descripcion','DESC')
            ->get();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    return redirect('/dashboard');
                } else {
                    return view('horarios.horarios', [
                         'empleado' => $empleado, 'horario' => $horario, 'horarion' => $horarion,
                        'area' => $area, 'cargo' => $cargo, 'local' => $local, 'nivel'=>$nivel,
                        'centroc'=>$centroc, 'reglasHExtras'=>$reglasHExtras,'tipo_incidencia'=>$tipo_incidencia,
                        'reglasHExtrasNocturno'=>$reglasHExtrasNocturno

                    ]);
                }
            } else {
                return view('horarios.horarios', [
                    'empleado' => $empleado, 'horario' => $horario, 'horarion' => $horarion,
                    'area' => $area, 'cargo' => $cargo, 'local' => $local,  'nivel'=>$nivel,
                    'centroc'=>$centroc, 'reglasHExtras'=>$reglasHExtras, 'tipo_incidencia'=>$tipo_incidencia,
                    'reglasHExtrasNocturno'=>$reglasHExtrasNocturno,

                ]);
            }
        }
    }
    public function verTodEmpleado(Request $request)
    {
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id', 'he.empleado_emple_id')
            ->leftJoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
        // ->whereNull('he.empleado_emple_id')
            ->distinct('e.emple_id')
            ->where('he.estado', '=', 1)
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();
        return $empleados;
    }
    public function guardarEventos(Request $request)
    {

        $datafecha = $request->fechasArray;
        $horas = $request->hora;
        $inicio = $request->inicio;
        $fin = $request->fin;
        $idhorar = $request->idhorar;
        $fueraHora = $request->fueraHora;
        $horaC = $request->horaC;
        $horaA = $request->horaA;
        $nHoraAdic = $request->nHoraAdic;
        $arrayrep = collect();
        $arrayeve = collect();

        foreach ($datafecha as $datafechas) {
            $tempre = temporal_eventos::where('users_id', '=', Auth::user()->id)
                ->where('start', '=', $datafechas)
                ->where('id_horario', '=', $idhorar)
                ->where('incidencia_id', '=',null)
                ->get()->first();
            if ($tempre) {
                $startArre = $tempre->start;
                $arrayrep->push($startArre);
            }
        }

        $datos = Arr::flatten($arrayrep);

        //DIFERENCIA ARRAYS
        $datafecha2 = array_values(array_diff($datafecha, $datos));

        //* PARA COMPARAR QUE NO ESTE DENTRO DE HORARIO QUE NO SE CRUCEN
        $horarioEmpleado = horario::where('horario_id', $idhorar)->first();
        $horaInicialF = Carbon::parse($horarioEmpleado->horaI)->subMinutes($horarioEmpleado->horario_tolerancia);
        $horaFinalF = Carbon::parse($horarioEmpleado->horaF)->addMinutes($horarioEmpleado->horario_toleranciaF);
        $arrayHDentro = collect();

        //*
        foreach ($datafecha as $datafechas) {
            $horarioDentro = temporal_eventos::select(['title', 'color', 'textColor', 'start', 'end', 'horaI',
                'horaF', 'borderColor', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF'])
                ->join('horario as h', 'temporal_eventos.id_horario', '=', 'h.horario_id')
                ->where('start', '=', $datafechas)
                ->where('users_id', '=', Auth::user()->id)
                ->where('incidencia_id', '=',null)
                ->get();
            if ($horarioDentro) {
                foreach ($horarioDentro as $horarioDentros) {
                    $horaIDentro = Carbon::parse($horarioDentros->horaI)->subMinutes($horarioDentros->toleranciaI);
                    $horaFDentro = Carbon::parse($horarioDentros->horaF)->addMinutes($horarioDentros->toleranciaF);

                    if ($horaInicialF->gt($horaIDentro) && $horaFinalF->lt($horaFDentro) && $horaInicialF->lt($horaFDentro)) {
                        $startArreD = carbon::create($horarioDentros->start);
                        $arrayHDentro->push($startArreD->format('Y-m-d'));
                    } elseif (($horaInicialF->gt($horaIDentro) && $horaInicialF->lt($horaFDentro)) || ($horaFinalF->gt($horaIDentro) && $horaFinalF->lt($horaFDentro))) {
                        $startArreD = carbon::create($horarioDentros->start);
                        $arrayHDentro->push($startArreD->format('Y-m-d'));
                    } elseif ($horaInicialF == $horaIDentro || $horaFinalF == $horaFDentro) {
                        $startArreD = carbon::create($horarioDentros->start);
                        $arrayHDentro->push($startArreD->format('Y-m-d'));
                    } elseif ($horaIDentro->gt($horaInicialF) && $horaFDentro->lt($horaFinalF)) {
                        $startArreD = carbon::create($horarioDentros->start);
                        $arrayHDentro->push($startArreD->format('Y-m-d'));
                    }
                }
            }
        }

        //* SACANDO HORARIOS QUE SE CRUCEN
        $datosDentroN = Arr::flatten($arrayHDentro);
        $datafecha3 = array_values(array_diff($datafecha2, $datosDentroN));
        //

        foreach ($datafecha3 as $datafechas) {

            $temporal_eventos = new temporal_eventos();
            $temporal_eventos->title = $horas;
            $temporal_eventos->start = $datafechas;
            $temporal_eventos->color = '#e2e2e2';
            $temporal_eventos->textColor = '111111';
            $temporal_eventos->users_id = Auth::user()->id;

            $temporal_eventos->temp_horaI = $inicio;
            $temporal_eventos->temp_horaF = $fin;
            $temporal_eventos->id_horario = $idhorar;
            $temporal_eventos->fuera_horario = $fueraHora;
            $temporal_eventos->horarioComp = $horaC;
            $temporal_eventos->horaAdic = $horaA;
            if ($fueraHora == 1) {
                $temporal_eventos->borderColor = '#5369f8';
            }
            $temporal_eventos->nHoraAdic = $nHoraAdic;
            $temporal_eventos->save();
            $arrayeve->push($temporal_eventos);
        }
        $datafechaValida = array_values(array_diff($datafecha, $datafecha3));
        if ($datafechaValida != null || $datafechaValida != []) {
            return 'Ya existe un horario asignado en este rango de horas, revise horario con tolerancia y vuelva a intentar.';
        } else {
            return 'Horario asignado';
        }
    }
    public function eventos()
    {
        $temporal_eventos = DB::table('temporal_eventos')->select([
            'id', 'title', 'textColor', 'start', 'end', 'color', 'horaI',
            'horaF', 'borderColor', 'horaAdic', 'id_horario', 'horasObliga', 'nHoraAdic',
        ])
            ->leftJoin('horario as h', 'temporal_eventos.id_horario', '=', 'h.horario_id')
            ->where('users_id', '=', Auth::user()->id)
            ->get();

        foreach ($temporal_eventos as $tab) {
            $pausas_horario = DB::table('pausas_horario as pauh')
                ->select('idpausas_horario', 'pausH_descripcion', 'pausH_Inicio', 'pausH_Fin', 'pauh.horario_id')
                ->where('pauh.horario_id', '=', $tab->id_horario)
                ->distinct('pauh.idpausas_horario')
                ->get();

            $tab->pausas = $pausas_horario;
        }

        return response()->json($temporal_eventos);
    }
    public function guardarHorarioBD(Request $request)
    {

        $tardanza = $request->tardanza;
        $descripcion = $request->descripcion;
        $toleranciaH = $request->toleranciaH;
        $inicio = $request->inicio;
        $fin = $request->fin;
        $toleranciaF = $request->toleranciaF;
        $horaOblig = $request->horaOblig;

        $horario = new horario();

        $horario->horario_descripcion = $descripcion;
        $horario->horario_tolerancia = $toleranciaH;
        $horario->horaI = $inicio;
        $horario->horaF = $fin;
        $horario->user_id = Auth::user()->id;
        $horario->organi_id = session('sesionidorg');
        $horario->horario_toleranciaF = $toleranciaF;
        $horario->horasObliga = $horaOblig;
        $horario->save();

        $descPausa = $request->get('descPausa');
        $IniPausa = $request->get('pausaInicio');
        $FinPausa = $request->get('finPausa');
        if ($descPausa) {

            if ($descPausa != null || $descPausa != '') {
                for ($i = 0; $i < sizeof($descPausa); $i++) {
                    if ($descPausa[$i] != null) {
                        $pausas_horario = new pausas_horario();
                        $pausas_horario->pausH_descripcion = $descPausa[$i];
                        $pausas_horario->pausH_Inicio = $IniPausa[$i];
                        $pausas_horario->pausH_Fin = $FinPausa[$i];
                        $pausas_horario->horario_id = $horario->horario_id;
                        $pausas_horario->save();
                    }
                }
            }
        }

        return $horario;
    }



    public function verDataEmpleado(Request $request)
    {
        $idsEm = $request->ids;
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select(
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'e.emple_nDoc',
                'p.perso_id',
                'e.emple_id',
                'hd.ubigeo_peru_departments_id',
                'hor.horario_tipo',
                'hor.horario_descripcion',
                'hor.horario_tolerancia',
                'e.emple_nDoc',
                'e.emple_Correo',
                'e.emple_celular',
                'a.area_descripcion',
                'c.cargo_descripcion',
                'cc.centroC_descripcion',
                'lo.local_descripcion'
            )
            ->leftJoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
            ->leftJoin('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->leftJoin('horario as hor', 'he.horario_horario_id', '=', 'hor.horario_id')
            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centrocosto_empleado as ce', 'e.emple_id', '=', 'ce.idEmpleado')
            ->leftJoin('centro_costo as cc', 'cc.centroC_id', '=', 'ce.idCentro')
            ->leftJoin('local as lo', 'e.emple_local', '=', 'lo.local_id')
            ->where('e.emple_estado', '=', 1)
            ->where('cc.estado', '=', 1)
            ->where('ce.estado', '=', 1)
            ->distinct('e.emple_id')
            ->where('he.estado', '=', 1)
            ->where('emple_id', '=', $idsEm)->get();
        if (count($empleado) >= 1) {

            $iddepar = $empleado[0]->ubigeo_peru_departments_id;
            //
            $eventos_empleado = DB::table('eventos_empleado')
                ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
                ->where('id_empleado', '=', $idsEm);

            $horario_empleado = DB::table('horario_empleado as he')
                ->select(['id', 'h.horario_descripcion as title', 'color', 'textColor', 'start', 'end'])
            /*  ->where('users_id', '=', Auth::user()->id) */
                ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                ->where('he.empleado_emple_id', '=', $idsEm)
                ->where('he.estado', '=', 1)
                ->union($eventos_empleado);

            $incidencias = DB::table('incidencias as i')
                ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_pagado as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
                ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
                ->where('i.emple_id', '=', $idsEm)
                ->union($horario_empleado)
                ->get();

            $horarioEmpleado = DB::table('horario_empleado as he')
                ->select('h.horario_descripcion as title', 'h.horaI', 'h.horaF', 'h.horaF', 'he.empleado_emple_id')
                ->where('he.organi_id', '=', session('sesionidorg'))
                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                ->where('he.empleado_emple_id', '=', $idsEm)
                ->where('he.estado', '=', 1)
                ->groupBy('h.horario_id')
                ->get();
            if ($horarioEmpleado) {
                return [$empleado, $incidencias, $horarioEmpleado];
            } else {
                return [$empleado, $incidencias, 0];
            }
        } /* else {
    $eventos1 = DB::table('eventos')->select(['id', 'title', 'color', 'textColor', 'start', 'end']);

    $eventos_calendario1 = DB::table('eventos_calendario')
    ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
    ->where('Users_id', '=', Auth::user()->id)
    ->where('evento_departamento', '=', null)
    ->where('evento_pais', '=', 173)
    ->union($eventos1)
    ->get();
    return [$eventos_calendario1, $eventos_calendario1];
    } */
    }
    public function vaciartemporal()
    {
        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id)->delete();
    }

    public function empleadosIncidencia(Request $request)
    {
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id', 'he.empleado_emple_id')
            ->join('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
            ->distinct('e.emple_id')
            ->where('he.estado', '=', 1)
            ->where('e.emple_estado', '=', 1)
            ->get();
        return $empleados;
    }

    public function registrarIncidencia(Request $request)
    {
        $idempl = $request->idempleadoI;

        $inc_dias = new incidencia_dias();
        $inc_dias->inciden_dias_fechaI = $request->fechaI;
        $inc_dias->inciden_dias_fechaF = $request->fechaF;
        $inc_dias->inciden_dias_hora = $request->horaIn;
        $inc_dias->save();

        foreach ($idempl as $idempls) {
            $incidencia = new incidencias();
            $incidencia->inciden_descripcion = $request->descripcionI;
            $incidencia->inciden_pagado = $request->descuentoI;
            $incidencia->inciden_dias_id = $inc_dias->inciden_dias_id;
            $incidencia->emple_id = $idempls;
            $incidencia->save();
        }
    }
    public function indexMenu()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {

            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('calendario_empleado as eve', 'e.emple_id', '=', 'eve.emple_id')
                ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('eve.emple_id', '!=', null)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
            $horario = horario::where('organi_id', '=', session('sesionidorg'))->get();
            $horarion = DB::table('horario as h')
                ->leftJoin('horario_empleado as he', 'h.horario_id', '=', 'he.horario_horario_id')
                ->where('he.estado', '=', 1)
                ->where('h.organi_id', '=', session('sesionidorg'))
                ->whereNull('he.horario_horario_id')
                ->get();
            //*AREA CUADNO TIENE ASIGNADO EMPLEADOS
            $area = DB::table('area as ar')
            ->join('empleado as em', 'ar.area_id', '=', 'em.emple_area')
            ->where('ar.organi_id','=',session('sesionidorg'))
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();

            //*CARGO CUADNO TIENE ASIGNADO EMPLEADO
            $cargo = DB::table('cargo as c')
            ->join('empleado as em', 'c.cargo_id', '=', 'em.emple_cargo')
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->select('cargo_id as idcargo',
                'cargo_descripcion as descripcion')
            ->groupBy('c.cargo_id')
            ->get();

            //*LOCAL CUADNO TIENE ASIGNADO EMPLEADO
            $local = DB::table('local as l')
            ->join('empleado as em', 'l.local_id', '=', 'em.emple_local')
            ->where('l.organi_id', '=', session('sesionidorg'))
            ->select('local_id as idlocal',
               'local_descripcion as descripcion')
            ->groupBy('l.local_id')
            ->get();

            //*NIVEL CUADNO TIENE ASIGNADO EMPLEADO
            $nivel = DB::table('nivel as n')
            ->join('empleado as em', 'n.nivel_id', '=', 'em.emple_nivel')
            ->where('n.organi_id', '=', session('sesionidorg'))
            ->select('nivel_id as idnivel',
               'nivel_descripcion as descripcion')
            ->groupBy('n.nivel_id')
            ->get();

            //*CENTRO DE COSTOS CUANDO TIENE ASIGNADO EMPLEADOS
            $centroc = DB::table('centro_costo as cc')
            ->join('centrocosto_empleado as ce', 'cc.centroC_id', '=', 'ce.idCentro')
            ->where('cc.organi_id', '=', session('sesionidorg'))
            ->where('ce.estado', '=', 1)
            ->select('cc.centroC_id as idcentro',
               'cc.centroC_descripcion as descripcion')
            ->groupBy('cc.centroC_id')
            ->get();

            //*reglas
            $reglasHExtras=DB::table('reglas_horasextras')
            ->where('organi_id','=', session('sesionidorg'))
            ->where('idTipoRegla','=',1)
            ->get();

            //*reglas nocturnas
            $reglasHExtrasNocturno=DB::table('reglas_horasextras')
            ->where('organi_id','=', session('sesionidorg'))
            ->where('tipo_regla','=','Nocturno')
            ->orderBy('idTipoRegla','DESC')
            ->get();

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            //*tipo iincidencia
            $tipo_incidencia=DB::table('tipo_incidencia')
            ->where('organi_id','=',session('sesionidorg'))
            ->where('sistema','=',0)
            ->orderBy('tipoInc_descripcion','DESC')
            ->get();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    return redirect('/dashboard');
                } else {
                    return view('horarios.horarioMenu', [
                         'empleado' => $empleado, 'horario' => $horario, 'horarion' => $horarion,
                        'area' => $area, 'cargo' => $cargo, 'local' => $local,'nivel'=>$nivel,
                        'centroc'=>$centroc, 'reglasHExtras'=>$reglasHExtras, 'tipo_incidencia'=>$tipo_incidencia,
                        'reglasHExtrasNocturno'=>$reglasHExtrasNocturno
                    ]);
                }
            } else {
                return view('horarios.horarioMenu', [
                     'empleado' => $empleado, 'horario' => $horario, 'horarion' => $horarion,
                    'area' => $area, 'cargo' => $cargo, 'local' => $local,'nivel'=>$nivel,
                    'centroc'=>$centroc, 'reglasHExtras'=>$reglasHExtras, 'tipo_incidencia'=>$tipo_incidencia,
                    'reglasHExtrasNocturno'=>$reglasHExtrasNocturno
                ]);
            }
        }
    }

    public function eliminarHora(Request $request)
    {
        $idHora = $request->idHora;
        $tipoEliminar = $request->tipoEliminar;

        if ($tipoEliminar == 0) {
            $temporal_evento = temporal_eventos::where('id', '=', $idHora)->delete();
        } else {
            $horario_Empleado = horario_empleado::findOrfail($idHora);
            $horario_Empleado->estado = 0;
            $horario_Empleado->save();

        }

    }
    public function eliminarIncidenciaHorario(Request $request)
    {
        $idHora = $request->idHora;
        $nuevaIncidencia = $request->nuevaIncidencia;

        if ($nuevaIncidencia == 1) {
            $temporal_evento = temporal_eventos::where('id', '=', $idHora)->delete();
        } else {
            $incidencias = incidencia_dias::findOrfail($idHora);
            $incidencias->delete();

        }

    }
    public function cambiarEstado(Request $request)
    {

        $user = User::where('id', '=', Auth::user()->id)
            ->update(['user_estado' => 1]);

            $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))
            ->update(['organi_menu' => 1]);
    }

    public function storeDescanso(Request $request)
    {
        //

        $temporal_eventos = new temporal_eventos();
        $temporal_eventos->title = $request->get('title');
        $temporal_eventos->color = '#e5e5e5';
        $temporal_eventos->textColor = '#3f51b5';
        $temporal_eventos->start = $request->get('start');
        $temporal_eventos->end = $request->get('end');

        $temporal_eventos->users_id = Auth::user()->id;
        $temporal_eventos->save();

        $temp = DB::table('temporal_eventos as te')
            ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
        /*  return  response()->json($arrayeve); */
        return response()->json($temp);
    }
    public function storeLabor(Request $request)
    {
        //
        $temporal_eventos = [];
        $temporal_eventos = new temporal_eventos();
        $temporal_eventos->title = $request->get('title');
        $temporal_eventos->color = '#dfe6f2';
        $temporal_eventos->textColor = '#0b1b29';
        $temporal_eventos->start = $request->get('start');
        $temporal_eventos->end = $request->get('end');

        $temporal_eventos->users_id = Auth::user()->id;
        $temporal_eventos->save();

        $temp = DB::table('temporal_eventos as te')
            ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
        /*  return  response()->json($arrayeve); */
        return response()->json($temp);
    }
    public function storeNoLabor(Request $request)
    {
        //
        $temporal_eventos = [];
        $temporal_eventos = new temporal_eventos();
        $temporal_eventos->title = $request->get('title');
        $temporal_eventos->color = '#a34141';
        $temporal_eventos->textColor = '#fff7f7';
        $temporal_eventos->start = $request->get('start');
        $temporal_eventos->end = $request->get('end');

        $temporal_eventos->users_id = Auth::user()->id;
        $temporal_eventos->save();

        $temp = DB::table('temporal_eventos as te')
            ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
        /*  return  response()->json($arrayeve); */
        return response()->json($temp);
    }

    public function storeIncidencia(Request $request)
    {

        $temporal_eventos = new temporal_eventos();
        $temporal_eventos->title = $request->get('title');
        $temporal_eventos->color = '#9E9E9E';
        $temporal_eventos->textColor = '#313131';
        $temporal_eventos->start = $request->get('start');
        $temporal_eventos->end = $request->get('end');

        $temporal_eventos->temp_horaI = $request->get('horaIn');
        $temporal_eventos->temp_horaF = $request->get('descuentoI');
        $temporal_eventos->users_id = Auth::user()->id;
        $temporal_eventos->save();

        $temp = DB::table('temporal_eventos as te')
            ->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('users_id', '=', Auth::user()->id)
            ->get();
        /*  return  response()->json($arrayeve); */
        return response()->json($temp);
    }

    public function guardarHorarioC(Request $request)
    {
        $idemps = $request->idemps;

        //*PARA HORARIOS TEMPORALES
        $temporal_eventoH = temporal_eventos::where('users_id', '=', Auth::user()->id)
        ->where('incidencia_id', '=',null) ->where('id_horario', '!=', null)->where('temp_horaF', '=', null)->get();
        $idasignar = collect();
        $idhors = collect();
        if ($temporal_eventoH->isNotEmpty()) {
            foreach ($temporal_eventoH as $temporal_eventosH) {
                $horario_dias = new horario_dias();
                $horario_dias->title = $temporal_eventosH->title;
                $horario_dias->start = $temporal_eventosH->start;
                $horario_dias->end = $temporal_eventosH->end;
                $horario_dias->color = '#ffffff';
                $horario_dias->textColor = $temporal_eventosH->textColor;
                $horario_dias->users_id = $temporal_eventosH->users_id;
                $horario_dias->organi_id = session('sesionidorg');
                $horario_dias->save();

                /////////////////////////////////////COMPARAR SI ESTA DENTRO DE RANGO

                $horarioEmpleado = horario::where('horario_id', $temporal_eventosH->id_horario)->first();
                $horaInicialF = Carbon::parse($horarioEmpleado->horaI)->subMinutes($horarioEmpleado->horario_tolerancia);
                $horaFinalF = Carbon::parse($horarioEmpleado->horaF)->addMinutes($horarioEmpleado->horario_toleranciaF);
                $arrayHDentro = collect();
                ////////////////////////////////////
                foreach ($idemps as $idempsva) {
                    $horarioDentro = horario_empleado::select(['horario_empleado.horarioEmp_id as id', 'h.horario_descripcion as title', 'color', 'textColor',
                        'start', 'end', 'horaI', 'horaF', 'borderColor', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF'])
                        ->join('horario as h', 'horario_empleado.horario_horario_id', '=', 'h.horario_id')
                        ->join('horario_dias as hd', 'horario_empleado.horario_dias_id', '=', 'hd.id')
                        ->where('start', '=', $temporal_eventosH->start)
                    /* ->where('h.horaI', '=', $idhorar)
                    ->where('h.horaF', '=', $idhorar) */
                        ->where('horario_empleado.empleado_emple_id', '=', $idempsva)
                        ->where('horario_empleado.estado', '=', 1)
                        ->get();
                    if ($horarioDentro) {
                        foreach ($horarioDentro as $horarioDentros) {
                            $horaIDentro = Carbon::parse($horarioDentros->horaI)->subMinutes($horarioDentros->toleranciaI);
                            $horaFDentro = Carbon::parse($horarioDentros->horaF)->addMinutes($horarioDentros->toleranciaF);

                            if ($horaInicialF->gt($horaIDentro) && $horaFinalF->lt($horaFDentro) && $horaInicialF->lt($horaFDentro)) {
                                $startArreD = carbon::create($horarioDentros->start);
                                $arrayHDentro->push($idempsva);
                            } elseif (($horaInicialF->gt($horaIDentro) && $horaInicialF->lt($horaFDentro)) || ($horaFinalF->gt($horaIDentro) && $horaFinalF->lt($horaFDentro))) {
                                $startArreD = carbon::create($horarioDentros->start);
                                $arrayHDentro->push($idempsva);
                            } elseif ($horaInicialF == $horaIDentro || $horaFinalF == $horaFDentro) {
                                $startArreD = carbon::create($horarioDentros->start);
                                $arrayHDentro->push($idempsva);
                            } elseif ($horaIDentro->gt($horaInicialF) && $horaFDentro->lt($horaFinalF)) {
                                $startArreD = carbon::create($horarioDentros->start);
                                $arrayHDentro->push($idempsva);
                            }
                        }
                    }
                }
                $datosDentroN = Arr::flatten($arrayHDentro);
                $idemps3 = array_values(array_diff($idemps, $datosDentroN));
                /////////////////////////////////////
                foreach ($idemps3 as $idempleados) {
                    $horario_empleado = new horario_empleado();
                    $horario_empleado->horario_horario_id = $temporal_eventosH->id_horario;
                    $horario_empleado->empleado_emple_id = $idempleados;
                    $horario_empleado->horario_dias_id = $horario_dias->id;
                    $horario_empleado->fuera_horario = $temporal_eventosH->fuera_horario;
                    $horario_empleado->horarioComp = $temporal_eventosH->horarioComp;
                    $horario_empleado->horaAdic = $temporal_eventosH->horaAdic;
                    $horario_empleado->nHoraAdic = $temporal_eventosH->nHoraAdic;
                    $horario_empleado->estado = 1;
                    if ($temporal_eventosH->fuera_horario == 1) {
                        $horario_empleado->borderColor = $temporal_eventosH->borderColor;
                    }
                    $horario_empleado->save();

                    /*---- REGISTRAR HISTORIAL DE CAMBIO -------------------*/
                    /*------ SE REGISTRA SI EL CAMBIO O REGISTRO EN EL HORARIO ES EL DIA ACTUAL--- */
                    /* OBTENEMOS DIA ACTUAL */
                    $fechaHoy = Carbon::now('America/Lima');
                    $diaActual = $fechaHoy->isoFormat('YYYY-MM-DD');
                    /* --------------------------------------------- */
                    /* OBTENEMOS DIA DE HORARIO */
                    $fechaHoy1 = Carbon::create($temporal_eventosH->start);
                    $diaHorario = $fechaHoy1->isoFormat('YYYY-MM-DD');
                    /* --------------------------------------------- */
                    if ($diaHorario == $diaActual) {
                        /* SI LAS FECHAS SON IGUALES */
                        $historial_horarioE = new historial_horarioempleado();
                        $historial_horarioE->horarioEmp_id = $horario_empleado->horarioEmp_id;
                        $historial_horarioE->fechaCambio = $fechaHoy;
                        $historial_horarioE->estadohorarioEmp = 1;
                        $historial_horarioE->save();
                    }

                    /* ------------------------------- */
                }
            }
        }

        //***************************INCIDENCIAS REGISTRAR****************************** */

        // EVENTOS TEMPORALES QUE SON INCIDENCIAS
        $temporal_eventoIncid = temporal_eventos::where('users_id', '=', Auth::user()->id)
        ->where('incidencia_id', '!=',null) ->where('id_horario', '=', null)->get();

        //SI ENCONTRAMOS  INCIDENCIAS
        if($temporal_eventoIncid->isNotEmpty()){

            //BUSCAMOS INCIDENCIAS CON LAS MISMAS FECHA PRA EMPLEADOS SELECCIONADOS, SI SON IGUALES
            //NO REGISTRAMOS DE NUEVO

            //*BUSCAMOS INCIDENCIAS IGAULES , SI HAY YA NO REGISTRAMOS
            foreach($temporal_eventoIncid as $temporal_eventoIncid){

                foreach($idemps as $idempInci){

                    $incidenciasBuscar=DB::table('incidencia_dias')
                    ->where('inciden_dias_fechaI', '=', $temporal_eventoIncid->start)
                    ->where('id_empleado', '=', $idempInci)
                    ->where('id_incidencia', '=', $temporal_eventoIncid->incidencia_id)
                    ->get()->first();

                    //*SI NO ENCONTRAMOS ENTONCES REGISTRAMOS
                    if(!$incidenciasBuscar){
                        $inc_dias = new incidencia_dias();
                        $inc_dias->id_incidencia = $temporal_eventoIncid->incidencia_id;
                        $inc_dias->	inciden_dias_fechaI = $temporal_eventoIncid->start;
                        $inc_dias->id_empleado = $idempInci;
                        $inc_dias->laborable=0;
                        $inc_dias->save();
                    }


                }

            }


        }

        //************************---------FIN INCIDENCIAS---------------**************** */


        //*EMPLEADOS CON HORARIOS REPETIDOS
        $empleadosMostrar = collect();
        if ($temporal_eventoH->isNotEmpty()) {
            foreach ($datosDentroN as $datosDentroNs) {
                $empleadosTabla = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->select('e.emple_id', 'e.emple_nDoc', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                    ->where('e.emple_id', '=', $datosDentroNs)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->get();
                $empleadosMostrar->push($empleadosTabla);
            }
        }

        $datosEmpleadosSH = Arr::flatten($empleadosMostrar);
        return $datosEmpleadosSH;
    }

    public function vaciarhor(Request $request)
    {

        $temporal_evento = temporal_eventos::where('users_id', '=', Auth::user()->id)->get();

        $empleados = $request->empleados;
        $inicio =  Carbon::create($request->inicio);
        $fin = Carbon::create($request->fin);

        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id) ->where('incidencia_id', '=',null)
            ->where('id_horario', '!=', null)->where('temp_horaF', '=', null)
            ->whereBetween(DB::raw('DATE(start)'), [$inicio, $fin]) ->delete();

        if($empleados){
            foreach ($empleados as $empleado) {

            DB::table('horario_empleado as he')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                ->whereBetween(DB::raw('DATE(hd.start)'), [$inicio, $fin])
                ->where('he.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_id', '=', $empleado)
                ->update(['he.estado' => 0]);
            }
        }
    }
    public function vaciarIncid(Request $request)
    {

        $empleados = $request->empleados;
        $inicio =  Carbon::create($request->inicio);
        $fin = Carbon::create($request->fin);
        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id) ->where('incidencia_id', '!=',null)
        ->where('id_horario', '=', null)->whereBetween(DB::raw('DATE(start)'), [$inicio, $fin])->delete();
        if($empleados){
            foreach ($empleados as $empleado) {

                $incidencias = DB::table('incidencia_dias as idi')
                ->select('idi.inciden_dias_id as idIncidencia', 'i.inciden_descripcion as title', 'i.inciden_pagado as pagado', 'i.inciden_codigo',
                 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end','p.perso_nombre as nombre','tipoI.tipoInc_descripcion',
                 'idi.id_empleado as idempleado'

                )
                ->leftJoin('incidencias as i', 'idi.id_incidencia', '=', 'i.inciden_id')
                ->join('empleado as e', 'idi.id_empleado', '=', 'e.emple_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('tipo_incidencia as tipoI','i.idtipo_incidencia','=','tipoI.idtipo_incidencia')
                ->where('idi.id_empleado', '=', $empleado)
                ->whereBetween(DB::raw('DATE(idi.inciden_dias_fechaI)'), [$inicio, $fin])
                ->delete();

            }
        }

    }
    public function vaciardl()
    {
        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id)
            ->where('color', '=', '#dfe6f2')->where('textColor', '=', '#0b1b29')->delete();
        $temporal_evento = temporal_eventos::where('users_id', '=', Auth::user()->id)->get();
        return $temporal_evento;
    }
    public function vaciarndl()
    {
        DB::table('temporal_eventos')->where('users_id', '=', Auth::user()->id)
            ->where('color', '=', '#a34141')->delete();
        $temporal_evento = temporal_eventos::where('users_id', '=', Auth::user()->id)->get();
        return $temporal_evento;
    }

    public function eliminareventosHorario(Request $request)
    {
        $idevento = $request->idevento;
        //primero ver que data llega a ver empleado
    }

    public function eliminarHorarBD(Request $request)
    {
        $ide = $request->ide;
        $idHora = $request->idHora;
        $textcolor = $request->textcolor;
        if ($textcolor == '111111') {
            $horario_empleado = horario_empleado::where('horario_dias_id', '=', $idHora)
                ->where('horario_empleado.estado', '=', 1)->get();
            $nhor = count($horario_empleado);

            if ($nhor == 1) {
                $horario_empleado0 = horario_empleado::where('horario_dias_id', '=', $idHora)
                    ->where('empleado_emple_id', '=', $ide)->delete();
                $horario_dias = horario_dias::where('id', '=', $idHora)->delete();
            } else if ($nhor > 1) {
                $horario_empleado1 = horario_empleado::where('horario_dias_id', '=', $idHora)->where('empleado_emple_id', '=', $ide)->delete();
            }
        } else {
            $eventos_empleado = eventos_empleado::where('evEmpleado_id', '=', $idHora)->delete();
        }
    }

    public function eliminarIncidBD(Request $request)
    {
        $ide = $request->ide;
        $idHora = $request->idHora;
        $incidencias = incidencias::where('inciden_id', '=', $idHora)->delete();
    }

    public function storeIncidenciaEmpleado(Request $request)
    {

        $start = $request->start;
        $title = $request->title;
        $end = $request->end;
        $horaIn = $request->horaIn;
        $idempl = $request->idempl;
        $descuentoI = $request->descuentoI;

        $inc_dias = new incidencia_dias();
        $inc_dias->inciden_dias_fechaI = $start;
        $inc_dias->inciden_dias_fechaF = $end;
        $inc_dias->inciden_dias_hora = $horaIn;
        $inc_dias->save();

        $incidencia = new incidencias();
        $incidencia->inciden_descripcion = $title;
        $incidencia->inciden_pagado = $descuentoI;
        $incidencia->inciden_dias_id = $inc_dias->inciden_dias_id;
        $incidencia->emple_id = $idempl;
        $incidencia->save();

        $eventos_empleado = DB::table('eventos_empleado')
            ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('id_empleado', '=', $idempl);

        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['id', 'h.horario_descripcion as title', 'color', 'textColor', 'start', 'end'])
            ->where('he.organi_id', '=', session('sesionidorg'))
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.empleado_emple_id', '=', $idempl)
            ->where('he.estado', '=', 1)
            ->union($eventos_empleado);

        $incidencias = DB::table('incidencias as i')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_pagado as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
            ->where('i.emple_id', '=', $idempl)
            ->union($horario_empleado)
            ->get();
        return ($incidencias);
    }

    public function storeHorarioEmBD(Request $request)
    {
        $idempl = $request->idempl;
        $datafecha = $request->fechasArray;
        $horario = $request->hora;
        $idhorar = $request->idhorar;

        foreach ($datafecha as $datafechas) {
            $horario_dias = new horario_dias();
            $horario_dias->title = $horario;
            $horario_dias->start = $datafechas;
            $horario_dias->color = '#ffffff';
            $horario_dias->textColor = '111111';
            $horario_dias->users_id = Auth::user()->id;
            $horario_dias->organi_id = session('sesionidorg');

            $horario_dias->save();
            $horario_empleado = new horario_empleado();
            $horario_empleado->horario_horario_id = $idhorar;
            $horario_empleado->empleado_emple_id = $idempl;
            $horario_empleado->horario_dias_id = $horario_dias->id;
            $horario_empleado->save();
        }



        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['id', 'h.horario_descripcion as title', 'color', 'textColor', 'start', 'end'])
            ->where('he.organi_id', '=', session('sesionidorg'))
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.empleado_emple_id', '=', $idempl)
            ->where('he.estado', '=', 1);


        $incidencias = DB::table('incidencias as i')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_pagado as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
            ->where('i.emple_id', '=', $idempl)
            ->union($horario_empleado)
            ->get();
        return ($incidencias);
    }

    public function storeLaborHorarioBD(Request $request)
    {
        $title = $request->title;
        $start = $request->start;
        $end = $request->end;
        $idempl = $request->idempl;
        //

        $eventos_empleado = new eventos_empleado();
        $eventos_empleado->title = $title;
        $eventos_empleado->color = '#dfe6f2';
        $eventos_empleado->textColor = '#0b1b29';
        $eventos_empleado->start = $start;
        $eventos_empleado->end = $end;

        $eventos_empleado->id_empleado = $idempl;
        $eventos_empleado->laborable = 0;
        $eventos_empleado->save();

        $eventos_empleado1 = DB::table('eventos_empleado')
            ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('id_empleado', '=', $idempl);

        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['id', 'h.horario_descripcion as title', 'color', 'textColor', 'start', 'end'])
            ->where('he.organi_id', '=', session('sesionidorg'))
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->where('he.empleado_emple_id', '=', $idempl)
            ->where('he.estado', '=', 1)
            ->union($eventos_empleado1);

        $incidencias = DB::table('incidencias as i')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_pagado as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
            ->where('i.emple_id', '=', $idempl)
            ->union($horario_empleado)
            ->get();
        return ($incidencias);
    }

    public function storeNoLaborHorarioBD(Request $request)
    {
        $title = $request->title;
        $start = $request->start;
        $end = $request->end;
        $idempl = $request->idempl;
        //

        $eventos_empleado = new eventos_empleado();
        $eventos_empleado->title = $title;
        $eventos_empleado->color = '#a34141';
        $eventos_empleado->textColor = '#fff7f7';
        $eventos_empleado->start = $start;
        $eventos_empleado->end = $end;

        $eventos_empleado->id_empleado = $idempl;
        $eventos_empleado->laborable = 0;
        $eventos_empleado->save();

        $eventos_empleado1 = DB::table('eventos_empleado')
            ->select(['evEmpleado_id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('id_empleado', '=', $idempl);

        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['id', 'h.horario_descripcion as title', 'color', 'textColor', 'start', 'end'])
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->where('he.organi_id', '=', session('sesionidorg'))
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.empleado_emple_id', '=', $idempl)
            ->where('he.estado', '=', 1)
            ->union($eventos_empleado1);

        $incidencias = DB::table('incidencias as i')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_pagado as color', 'idi.inciden_dias_hora as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_dias_id', '=', 'idi.inciden_dias_id')
            ->where('i.emple_id', '=', $idempl)
            ->union($horario_empleado)
            ->get();
        return ($incidencias);
    }

    public function incidenciatemporal()
    {
        $incidencias = temporal_eventos::where('users_id', '=', Auth::user()->id)
            ->where('temp_horaF', '!=', null)->get();
        return ($incidencias);
    }
    public function eliminarinctempotal(Request $request)
    {
        $idinc = $request->idinc;

        $temporal_evento = temporal_eventos::where('id', '=', $idinc)->delete();
        $temporal = temporal_eventos::where('users_id', '=', Auth::user()->id)
            ->get();
        return ($temporal);
    }

    public function verDatahorario(Request $request)
    {
        $idsedit = $request->get('id');
        $horario = horario::where('organi_id', '=', session('sesionidorg'))
            ->where('horario_id', '=', $idsedit)
            ->get();
        $pausas_horario = pausas_horario::where('horario_id', '=', $idsedit)->get();

        return response()->json(array("horario" => $horario, "pausas" => $pausas_horario), 200);
    }

    public function actualizarhorarioed(Request $request)
    {
        $idhorario = $request->idhorario;
        $descried = $request->descried;
        $toleed = $request->toleed;
        $horaIed = $request->horaIed;
        $horaFed = $request->horaFed;
        $toleranciaFed = $request->toleranciaFed;
        $horaObed = $request->horaObed;
        ////////////////////////

        ///////////////////////

        $horario = horario::where('horario_id', '=', $idhorario)
            ->update([
                'horario_descripcion' => $descried, 'horario_tolerancia' => $toleed, 'horaI' => $horaIed,
                'horaF' => $horaFed, 'horario_toleranciaF' => $toleranciaFed, 'horasObliga' => $horaObed,
            ]);

        $horarion = horario::where('organi_id', '=', session('sesionidorg'))->get();

        $descPausa = $request->get('descPausa_ed');
        $IniPausa = $request->get('pausaInicio_ed');
        $FinPausa = $request->get('finPausa_ed');

        //comprobar si existe pausas

        $pausas_horarioComprobar = DB::table('pausas_horario')
            ->where('horario_id', $idhorario)->get();

        if ($pausas_horarioComprobar->isEmpty()) {
            if ($descPausa) {

                if ($descPausa != null || $descPausa != '') {
                    for ($i = 0; $i < sizeof($descPausa); $i++) {
                        if ($descPausa[$i] != null) {
                            $pausas_horario = new pausas_horario();
                            $pausas_horario->pausH_descripcion = $descPausa[$i];
                            $pausas_horario->pausH_Inicio = $IniPausa[$i];
                            $pausas_horario->pausH_Fin = $FinPausa[$i];
                            $pausas_horario->horario_id = $idhorario;
                            $pausas_horario->save();
                        }
                    }
                }
            }
        } else {
            //ACTUALIZAR PAUSAS YA REGISTRADAS
            $idpausasReg = $request->get('ID_edReg');
            $descPausaReg = $request->get('descPausa_edReg');
            $IniPausaReg = $request->get('pausaInicio_edReg');
            $FinPausaReg = $request->get('finPausa_edReg');
            if ($idpausasReg) {

                if ($idpausasReg != null || $idpausasReg != '') {
                    for ($i = 0; $i < sizeof($idpausasReg); $i++) {
                        if ($idpausasReg[$i] != null) {
                            $pausas_horarioReg = pausas_horario::findOrFail($idpausasReg[$i]);
                            $pausas_horarioReg->pausH_descripcion = $descPausaReg[$i];
                            $pausas_horarioReg->pausH_Inicio = $IniPausaReg[$i];
                            $pausas_horarioReg->pausH_Fin = $FinPausaReg[$i];
                            $pausas_horarioReg->horario_id = $idhorario;
                            $pausas_horarioReg->save();
                        }
                    }
                }
            }
        }
        $descPausaRN = $request->get('descPausa_edRN');
        $IniPausaRN = $request->get('pausaInicio_edRN');
        $FinPausaRN = $request->get('finPausa_edRN');
        if ($descPausaRN) {

            if ($descPausaRN != null || $descPausaRN != '') {
                for ($i = 0; $i < sizeof($descPausaRN); $i++) {
                    if ($descPausaRN[$i] != null) {
                        $pausas_horarioRN = new pausas_horario();
                        $pausas_horarioRN->pausH_descripcion = $descPausaRN[$i];
                        $pausas_horarioRN->pausH_Inicio = $IniPausaRN[$i];
                        $pausas_horarioRN->pausH_Fin = $FinPausaRN[$i];
                        $pausas_horarioRN->horario_id = $idhorario;
                        $pausas_horarioRN->save();
                    }
                }
            }
        }

        return ($horarion);
    }

    public function verificarID(Request $request)
    {
        $idhorario = $request->idhorario;
        $horarion = DB::table('horario as h')
            ->leftJoin('horario_empleado as he', 'h.horario_id', '=', 'he.horario_horario_id')
            ->where('h.organi_id', '=', session('sesionidorg'))
            ->where('h.horario_id', '=', $idhorario)
        /*  ->where('he.estado', '=', 1) */
            ->get();
        if ($horarion[0]->horario_horario_id != null) {
            return 1;
        } else {
            return 0;
        }
    }

    public function eliminarHorario(Request $request)
    {
        $idhorario = $request->idhorario;
        $pausas_horario = pausas_horario::where('horario_id', '=', $idhorario)->delete();
        $horario = horario::where('horario_id', '=', $idhorario)->delete();
    }

    public function empleArea(Request $request)
    {
        $idarea = $request->idarea;
        $empleadosArea = DB::table('empleado')
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('emple_area', '=', $idarea)
            ->where('emple_estado', '=', 1)
            ->get();
        return $empleadosArea;
    }

    public function empleCargo(Request $request)
    {
        $idcargo = $request->idcargo;
        /*  dd($idcargo);  */
        $empleadosCargo = DB::table('empleado')
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('emple_cargo', '=', $idcargo)
            ->where('emple_estado', '=', 1)
            ->get();
        return $empleadosCargo;
    }

    public function empleLocal(Request $request)
    {
        $idlocal = $request->idlocal;
        $empleadosidLocal = DB::table('empleado')
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('emple_local', '=', $idlocal)
            ->where('emple_estado', '=', 1)
            ->get();
        return $empleadosidLocal;
    }

    public function empleNivel(Request $request)
    {
        $idnivel = $request->idnivel;
        $empleadosidNivel = DB::table('empleado')
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('emple_nivel', '=', $idnivel)
            ->where('emple_estado', '=', 1)
            ->get();
        return $empleadosidNivel;
    }

    public function empleCentroc(Request $request)
    {
        $idcentro = $request->idcentro;
        $empleadoscentro = DB::table('empleado')
            ->join('centrocosto_empleado as ccemple','empleado.emple_id','=','ccemple.idEmpleado')
            ->where('ccemple.estado', '=', 1)
            ->where('empleado.organi_id', '=', session('sesionidorg'))
            ->where('ccemple.idCentro', '=', $idcentro)
            ->where('empleado.emple_estado', '=', 1)
            ->groupBy('empleado.emple_id')
            ->select('empleado.emple_id')
            ->get();
        return $empleadoscentro;
    }

    public function copiarferiados(Request $request)
    {
        $evento = eventos::all();
        if ($evento) {
            foreach ($evento as $eventos) {
                $temporal_eventos = new temporal_eventos();

                $temporal_eventos->title = $eventos->title;
                $temporal_eventos->color = $eventos->color;
                $temporal_eventos->textColor = $eventos->textColor;
                $temporal_eventos->start = $eventos->start;
                $temporal_eventos->end = $eventos->end;
                $temporal_eventos->users_id = Auth::user()->id;
                $temporal_eventos->save();
            }
        }
    }
    public function borrarferiados(Request $request)
    {
        $temporal_eventoH = temporal_eventos::where('users_id', '=', Auth::user()->id)
            ->where('color', '=', '#e6bdbd')->delete();
    }

    public function horarioListar()
    {
        $numero = 0;
        $horario = horario::where('horario.organi_id', '=', session('sesionidorg'))
            ->leftJoin('horario_empleado as he', 'horario.horario_id', '=', 'he.horario_horario_id')
            ->groupBy('horario.horario_id')
            ->get();
        return json_encode($horario);
    }

    public function eliminarPausasEnEditar(Request $request)
    {
        $idhorario = $request->valorHorario;
        DB::table('pausas_horario')->where('horario_id', '=', $idhorario)->delete();
    }
    public function eliminarPausaHorario(Request $request)
    {
        $idpausa = $request->idpausa;
        DB::table('pausas_horario')->where('idpausas_horario', '=', $idpausa)->delete();
    }

    // * REGISTRAR NUEVO HORARIO
    public function guardarNuevoHorario(Request $request)
    {
        $horario = new horario();
        $horario->horario_descripcion = $request->get('descripcion');
        $horario->horario_tolerancia = $request->get('toleranciaI');
        $horario->horaI = $request->get('horaInicio');
        $horario->horaF = $request->get('horaFin');
        $horario->user_id = Auth::user()->id;
        $horario->organi_id = session('sesionidorg');
        $horario->horario_toleranciaF = $request->get('toleranciaF');
        $horario->horasObliga = $request->get('horasO');
        $horario->tiempoMingreso = $request->get('tmIngreso');
        $horario->tiempoMsalida = $request->get('tmSalida');
        $horario->idreglas_horasExtras = $request->get('idReglaHExtras');
        $horario->idreglas_horasExtrasNoct = $request->get('idReglaHExtrasNoc');
        $horario->save();

        $idHorario = $horario->horario_id;
        // * PAUSAS
        if (!is_null($request->get('pausas'))) {
            foreach ($request->get('pausas') as $pausa) {
                if (!is_null($pausa["descripcion"]) && !is_null($pausa["inicioPausa"]) && !is_null($pausa["finPausa"])) {
                    $pausaH = new pausas_horario();
                    $pausaH->pausH_descripcion = $pausa["descripcion"];
                    $pausaH->pausH_Inicio = $pausa["inicioPausa"];
                    $pausaH->pausH_Fin = $pausa["finPausa"];
                    $pausaH->horario_id = $idHorario;
                    $pausaH->tolerancia_inicio = $pausa["toleranciaI"];
                    $pausaH->tolerancia_fin = $pausa["toleranciaF"];
                    $pausaH->inactivar = $pausa["inactivar"];
                    $pausaH->descontar = $pausa["descontar"];
                    $pausaH->save();
                }
            }
        }

        return response()->json($idHorario, 200);
    }

    // * OBTENER PAUSAS DE HORARIO
    public function pausasHorario(Request $request)
    {
        $id = $request->get('id');
        $pausasHorario = pausas_horario::where('horario_id', '=', $id)->get();

        return response()->json($pausasHorario, 200);
    }

    // * EDITAR HORARIO
    public function editarHorario(Request $request)
    {
        // * HORARIO
        $horario = horario::findOrFail($request->get('id'));
        $horario->horario_descripcion = $request->get('descripcion');
        $horario->horario_tolerancia = $request->get('toleranciaI');
        $horario->horaI = $request->get('horaInicio');
        $horario->horaF = $request->get('horaFin');
        $horario->horario_toleranciaF = $request->get('toleranciaF');
        $horario->horasObliga = $request->get('horasO');
        $horario->tiempoMingreso = $request->get('tmIngreso');
        $horario->tiempoMsalida = $request->get('tmSalida');
        $horario->idreglas_horasExtras = $request->get('idReglaHExtras');
        $horario->idreglas_horasExtrasNoct = $request->get('idReglaHExtrasNoc');
        $horario->save();

        $idHorario = $horario->horario_id;

        // * PAUSAS
        if (!is_null($request->get('pausas'))) {
            foreach ($request->get('pausas') as $pausa) {
                $pausaHorario = pausas_horario::where('idpausas_horario', '=', $pausa["id"])->get()->first();
                // * SI SE ENCUENTRA REGISTRADO LA PAUSA
                if ($pausaHorario) {
                    // * COMPARAR DATOS SI ESTA VACIO
                    if (!is_null($pausa["descripcion"]) && !is_null($pausa["inicioPausa"]) && !is_null($pausa["finPausa"])) {
                        $pausaHorario->pausH_descripcion = $pausa["descripcion"];
                        $pausaHorario->pausH_Inicio = $pausa["inicioPausa"];
                        $pausaHorario->pausH_Fin = $pausa["finPausa"];
                        $pausaHorario->tolerancia_inicio = $pausa["toleranciaI"];
                        $pausaHorario->tolerancia_fin = $pausa["toleranciaF"];
                        $pausaHorario->inactivar = $pausa["inactivar"];
                        $pausaHorario->descontar = $pausa["descontar"];
                        $pausaHorario->save();
                    } else {
                        // * ELIMINAR PAUSA HORARIO
                        $pausaHorario->delete();
                    }
                } else {
                    // * COMPARAR DATOS SI ESTA VACIO
                    if (!is_null($pausa["descripcion"]) && !is_null($pausa["inicioPausa"]) && !is_null($pausa["finPausa"])) {
                        $nuevoPausa = new pausas_horario();
                        $nuevoPausa->pausH_descripcion = $pausa["descripcion"];
                        $nuevoPausa->pausH_Inicio = $pausa["inicioPausa"];
                        $nuevoPausa->pausH_Fin = $pausa["finPausa"];
                        $nuevoPausa->tolerancia_inicio = $pausa["toleranciaI"];
                        $nuevoPausa->tolerancia_fin = $pausa["toleranciaF"];
                        $nuevoPausa->inactivar = $pausa["inactivar"];
                        $nuevoPausa->descontar = $pausa["descontar"];
                        $nuevoPausa->horario_id = $idHorario;
                        $nuevoPausa->save();
                    }
                }
            }
        } else {
            $pausasHorario = pausas_horario::where('horario_id', '=', $idHorario)->get();
            // * ELIMINAR PAUSAS DEL HORARIO
            foreach ($pausasHorario as $pausa) {
                $pausa->delete();
            }
        }

        return response()->json($idHorario, 200);
    }

    public function obtenerHorarios()
    {
        $horario = horario::select('horario_id', 'horario_descripcion', 'horaI', 'horaF')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get();

        return response()->json($horario, 200);
    }

    //*ACTUALIZAR CONFIGURACION DE HORARIO EN REGISTRAR EMPLEADO
    public function actualizarConfigHorario(Request $request)
    {

        //*VALOR DE PARAMETROS
        $idHoraEmp = $request->idHoraEmp;
        $fueraHorario = $request->fueraHorario;
        $permiteHadicional = $request->permiteHadicional;
        $nHorasAdic = $request->nHorasAdic;
        $tipHorarioC = $request->tipHorarioC;

        //*ACTUALIZANDO CUANDO ES HORARIO NO REGISTRADO
        if ($tipHorarioC == 0) {
            $horario_empleado = temporal_eventos::findOrfail($idHoraEmp);
            if ($fueraHorario == 1) {
                $horario_empleado->borderColor = '#5369f8';
            } else {
                $horario_empleado->borderColor = null;
            }
            $horario_empleado->fuera_horario = $fueraHorario;
            $horario_empleado->horaAdic = $permiteHadicional;
            $horario_empleado->nHoraAdic = $nHorasAdic;
            $horario_empleado->save();
        } else {
            //*ACTUALIZANDO CUANDO ES HORARIO REGISTRADO
            $horario_empleado = horario_empleado::findOrfail($idHoraEmp);
            if ($fueraHorario == 1) {
                $horario_empleado->borderColor = '#5369f8';
            } else {
                $horario_empleado->borderColor = null;
            }
            $horario_empleado->fuera_horario = $fueraHorario;
            $horario_empleado->horaAdic = $permiteHadicional;
            $horario_empleado->nHoraAdic = $nHorasAdic;
            $horario_empleado->save();
        }

    }

    public function horarioNuevo()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {

            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('calendario_empleado as eve', 'e.emple_id', '=', 'eve.emple_id')
                ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('eve.emple_id', '!=', null)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
            $horario = horario::where('organi_id', '=', session('sesionidorg'))->get();
            $horarion = DB::table('horario as h')
                ->leftJoin('horario_empleado as he', 'h.horario_id', '=', 'he.horario_horario_id')
                ->where('he.estado', '=', 1)
                ->where('h.organi_id', '=', session('sesionidorg'))
                ->whereNull('he.horario_horario_id')
                ->get();
            $area = DB::table('area')->where('organi_id', '=', session('sesionidorg'))
                ->select('area_id as idarea', 'area_descripcion as descripcion')
                ->get();
            $cargo = DB::table('cargo')
                ->where('organi_id', '=', session('sesionidorg'))
                ->select('cargo_id as idcargo', 'cargo_descripcion as descripcion')
                ->get();
            $local = DB::table('local')
                ->where('organi_id', '=', session('sesionidorg'))
                ->select('local_id as idlocal', 'local_descripcion as descripcion')
                ->get();

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    return redirect('/dashboard');
                } else {
                    return view('horarios.horarioMenuNuevo', [
                        'empleado' => $empleado, 'horario' => $horario, 'horarion' => $horarion,
                        'area' => $area, 'cargo' => $cargo, 'local' => $local,
                    ]);
                }
            } else {
                return view('horarios.horarioMenuNuevo', [
                    'empleado' => $empleado, 'horario' => $horario, 'horarion' => $horarion,
                    'area' => $area, 'cargo' => $cargo, 'local' => $local,
                ]);
            }
        }
    }
    public function horariosEmpleado(Request $request)
    {
        $idcalendario = $request->idcalendario;
        $idempleado = $request->idempleado;

        $eventos_empleado = eventos_empleado::where('id_empleado', '=', $idempleado)
            ->get();

        if ($eventos_empleado->isEmpty()) {
            $eventos_calendario = eventos_calendario::where('organi_id', '=', session('sesionidorg'))
                ->where('id_calendario', '=', $idcalendario)->get();
            if ($eventos_calendario) {
                foreach ($eventos_calendario as $eventos_calendarios) {
                    $eventos_empleado_r = new eventos_empleado();
                    $eventos_empleado_r->id_empleado = $idempleado;
                    $eventos_empleado_r->title = $eventos_calendarios->title;
                    $eventos_empleado_r->color = $eventos_calendarios->color;
                    $eventos_empleado_r->textColor = $eventos_calendarios->textColor;
                    $eventos_empleado_r->start = $eventos_calendarios->start;
                    $eventos_empleado_r->end = $eventos_calendarios->end;
                    $eventos_empleado_r->tipo_ev = $eventos_calendarios->tipo;
                    $eventos_empleado_r->id_calendario = $idcalendario;
                    $eventos_empleado_r->laborable = 0;
                    $eventos_empleado_r->save();
                }
            }
        }

        /*  dd($horario_empleado); */

        $incidencias = DB::table('incidencias as i')
            ->select(
                'idi.inciden_dias_id as id', 'i.inciden_descripcion as title', 'i.inciden_pagado as color', 'i.inciden_descripcion as textColor',
                'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end', DB::raw('IF(i.inciden_codigo is null, "--" , i.inciden_codigo) as horaI'),  'tip.tipoInc_descripcion as horaF',
                 DB::raw('IF(inciden_pagado=1, "Si" , "No") as borderColor'),
                  'laborable', 'i.inciden_descripcion as horaAdic', 'i.inciden_descripcion as idhorario',
                   'i.inciden_descripcion as horasObliga', 'i.inciden_descripcion as nHoraAdic'
            )
            ->join('incidencia_dias as idi', 'i.inciden_id', '=', 'idi.id_incidencia')
            ->join('tipo_incidencia as tip', 'i.idtipo_incidencia', '=', 'tip.idtipo_incidencia')
            ->where('idi.id_empleado', '=', $idempleado);
        /*   ->union($horario_empleado); */


        /*   $horario_empleado ->union($eventos_empleado); */
        /* -------HORARIOS QUE NO ESTAN GUARDADOS------ */
        $temporal_eventosHO = DB::table('temporal_eventos')->select([
            'id', 'title', 'color', 'textColor', 'start', 'end', 'horaI',
            'horaF', 'borderColor', 'color as laborable', 'horaAdic', 'id_horario as idhorario', 'horasObliga', 'nHoraAdic',
        ])
            ->leftJoin('horario as h', 'temporal_eventos.id_horario', '=', 'h.horario_id')
            ->where('users_id', '=', Auth::user()->id)
            ->where('id_horario', '!=', null)
            ->where('incidencia_id', '=', null)
            ->union($incidencias);

         /* -------INCIDENCIAS QUE NO ESTAN GUARDADOS------ */
         $temporal_eventosIN = DB::table('temporal_eventos')->select([
            'id', 'title', 'color', 'textColor', 'start', 'end', DB::raw('IF(i.inciden_codigo is null, "--" , i.inciden_codigo) as horaI'),
            'tip.tipoInc_descripcion as horaF',  DB::raw('IF(inciden_pagado=1, "Si" , "No") as borderColor'), 'color as laborable', 'color as horaAdic', 'id_horario as idhorario', 'color as horasObliga', 'nHoraAdic',
        ])
            ->leftJoin('incidencias as i', 'temporal_eventos.incidencia_id', '=', 'i.inciden_id')
            ->join('incidencia_dias as idi', 'i.inciden_id', '=', 'idi.id_incidencia')
            ->join('tipo_incidencia as tip', 'i.idtipo_incidencia', '=', 'tip.idtipo_incidencia')
            ->where('temporal_eventos.users_id', '=', Auth::user()->id)
            ->where('id_horario', '=', null)
            ->where('incidencia_id', '!=', null)
            ->union($temporal_eventosHO);
        /* -------------------------------------------- */
        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['he.horarioEmp_id as id', 'h.horario_descripcion as title', 'color', 'textColor', 'start', 'end', 'horaI', 'horaF', 'borderColor', 'laborable', 'horaAdic', 'h.horario_id as idhorario', 'horasObliga', 'nHoraAdic'])
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.estado', '=', 1)
            ->where('he.empleado_emple_id', '=', $idempleado)
            ->union($temporal_eventosIN)
            ->get();

        foreach ($horario_empleado as $tab) {
            $pausas_horario = DB::table('pausas_horario as pauh')
                ->select('idpausas_horario', 'pausH_descripcion', 'pausH_Inicio', 'pausH_Fin', 'pauh.horario_id')
                ->where('pauh.horario_id', '=', $tab->idhorario)
                ->distinct('pauh.idpausas_horario')
                ->get();

            $tab->pausas = $pausas_horario;
        }

        //*EXISTE EVENTOS TEMPORALES
        $temporal_eventos = DB::table('temporal_eventos')
            ->where('users_id', '=', Auth::user()->id)->get();

        if($temporal_eventos->isNotEmpty()){
            $datosTemporales=1;

        } else{
            $datosTemporales=0;
        }

        return [$horario_empleado,$datosTemporales];
    }

    public function horariosVariosEmps(Request $request)
    {
        $idcalendario = $request->idcalendario;
        $idempleados = $request->idempleado;

        $horarioEmpleado = new Collection();
        $incidenciasU = new Collection();

        /*   foreach($idempleados as $idempleado){
        $incidencias2 = DB::table('incidencias as i')
        ->select([
        'idi.inciden_dias_id as id', 'i.inciden_descripcion as title', 'i.inciden_pagado as color', 'i.inciden_pagado as textColor',
        'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end', 'i.inciden_descripcion as horaI', 'i.inciden_descripcion as horaF', 'i.inciden_descripcion as borderColor', 'laborable',
        'i.inciden_descripcion as horaAdic', 'i.inciden_descripcion as idhorario', 'i.inciden_descripcion as horasObliga', 'i.inciden_descripcion as nHoraAdic',
        ])
        ->join('incidencia_dias as idi', 'i.inciden_id', '=', 'idi.id_incidencia')
        ->where('idi.id_empleado', '=', $idempleado)->get();
        $horarioEmpleado->push($incidencias2);

        } */

        /* foreach($idempleados as $idempleado){
        $eventos_empleado2 = DB::table('eventos_empleado')
        ->select([
        'evEmpleado_id as id', 'title', 'color', 'textColor', 'start', 'end', 'title as horaI', 'title as horaF', 'title as borderColor', 'laborable',
        'title as horaAdic', 'start as idhorario', 'start as horasObliga', 'start as nHoraAdic',
        ])
        ->where('id_empleado', '=', $idempleado)
        ->get();
        $horarioEmpleado->push($eventos_empleado2);
        } */

        /*   $horario_empleado ->union($eventos_empleado); */
        /* -------HORARIOS QUE NO ESTAN GUARDADOS------ */
        $temporal_eventos = DB::table('temporal_eventos')->select([
            'id', 'title', 'color', 'textColor', 'start', 'end', 'horaI',
            'horaF', 'borderColor', 'color as laborable', 'horaAdic', 'id_horario as idhorario', 'horasObliga', 'nHoraAdic',
        ])
            ->leftJoin('horario as h', 'temporal_eventos.id_horario', '=', 'h.horario_id')
            ->where('users_id', '=', Auth::user()->id)
            ->get();

        $horarioEmpleado->push($temporal_eventos);
        /* -------------------------------------------- */
        foreach ($idempleados as $idempleado) {



            $horario_empleado = DB::table('horario_empleado as he')
                ->select(['he.horarioEmp_id as id', 'h.horario_descripcion as title', 'color', 'textColor',DB::raw('DATE(start) as start') , 'end', 'horaI', 'horaF', 'borderColor', 'h.horario_id as idhorario','laborable'])
                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                ->where('he.estado', '=', 1)
                ->where('he.empleado_emple_id', '=', $idempleado)

                ->get();

            foreach ($horario_empleado as $tab) {

                $pausas_horario = DB::table('pausas_horario as pauh')
                    ->select('idpausas_horario', 'pausH_descripcion', 'pausH_Inicio', 'pausH_Fin', 'pauh.horario_id')
                    ->where('pauh.horario_id', '=', $tab->idhorario)
                    ->distinct('pauh.idpausas_horario')
                    ->get();

                $tab->pausas = $pausas_horario;


            }

            $horarioEmpleado->push($horario_empleado);

            $incidencias = DB::table('incidencia_dias as idi')
            ->select(['i.inciden_id as id', 'i.inciden_descripcion as title', 'i.inciden_pagado as color', 'i.inciden_pagado as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end',
            'i.inciden_descripcion as horaI', 'i.inciden_descripcion as horaF', 'i.inciden_descripcion as borderColor', 'i.inciden_id as idhorario','laborable'])
            ->leftJoin('incidencias as i', 'idi.id_incidencia', '=', 'i.inciden_id')
            ->where('idi.id_empleado', '=', $idempleado)
            ->get();
            $incidenciasU->push($incidencias);
        }
        //convertimos collection a plano y sacamos los unico dias de horario
        $horariosEs = $horarioEmpleado->collapse()->unique('start');

         //convertimos collection a plano y sacamos los unico dias de incidencias
         $Incid = $incidenciasU->collapse()->unique('start');


        //unimos incidencias con horarios
        $unimos = $horariosEs->merge($Incid);

        foreach ($unimos as $asig) {
            if($asig->laborable==1){
                $asig->title = 'Programado';
                $asig->borderColor = '';
                $asig->color = '#fff27d';
                $asig->textColor = '#000';

            } else{
                if($asig->laborable=='0'){
                  $asig->title = 'Incidencia';
                $asig->borderColor = '';
                $asig->color = '#fff0f0';
                $asig->textColor = '#bd6767';
                }

            }

        }
        //*EXISTE EVENTOS TEMPORALES
        $temporal_eventos = DB::table('temporal_eventos')
            ->where('users_id', '=', Auth::user()->id)->get();

        if($temporal_eventos->isNotEmpty()){
            $datosTemporales=1;

        } else{
            $datosTemporales=0;
        }
        return[$unimos->values()->all(),$datosTemporales];
    }

    public function datosHorarioEmpleado(Request $request)
    {

        //*obtengo paramentros
        $diadeHorario = $request->diadeHorario;
        $empleados = $request->empleados;

        //*formateamos fecha
        $fecha = Carbon::create($diadeHorario);
        $fechaHorario = $fecha->isoFormat('YYYY-MM-DD');

        //*declaramos collection para guardar horarios de empeado
        $dataHorario = new Collection();
        //*recorremos empleados seleccionnados
        foreach ($empleados as $empleado) {

            $empleadoHorario = DB::table('horario_empleado as he')
            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select('e.emple_id as idempleado',
                     'p.perso_nombre as nombre',
                    DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_id', '=', $empleado)
                ->whereDate('hd.start', '=', $fechaHorario)
                ->where('he.estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();

            //*obtenr solo de empleado con horario
            if ($empleadoHorario->isNotEmpty()) {
                $dataHorario->push($empleadoHorario);
            }

        }

        $dataHorario=$dataHorario->collapse();
        $dataHorarioF=$dataHorario->sortBy('apellidos',SORT_LOCALE_STRING)->values()->toArray();



            foreach($dataHorarioF as $dataHorarios){
                //*buscamos su horario para el dia de hoy
                $horarioEmpleado = DB::table('horario_empleado as he')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp',
                    'h.horario_id', 'h.horario_descripcion', 'h.horaI', 'h.horaF',
                    'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario', 'h.horasObliga',
                    DB::raw("IF(he.fuera_horario=1,'Si' , 'No') as fueraHorario"),
                    DB::raw("IF(he.nHoraAdic is null, 0 , nHoraAdic) as horaAdicional")
                    )
                    ->whereDate('hd.start', '=', $fechaHorario)
                ->where('he.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_id', '=', $dataHorarios->idempleado)
                ->get();

                $dataHorarios->horario= $horarioEmpleado;

            }

        return ($dataHorarioF);

    }

    //*ELIMINAR HORARIO EMPLEADOS MASIVAMENTE
    public function elimarhoraEmps(Request $request)
    {
        $diadeHorario = $request->diadeHorario;
        $empleados = $request->empleados;
        $idsHoraEmps = $request->valoresCheck;
        foreach ($idsHoraEmps as $idsHoraEmp) {
            $horario_Empleado = horario_empleado::findOrfail($idsHoraEmp);
            $horario_Empleado->estado = 0;
            $horario_Empleado->save();

        }

        //*LLAMNDO DATOS DE NUEVO***********************************************
        //*formateamos fecha
        $fecha = Carbon::create($diadeHorario);
        $fechaHorario = $fecha->isoFormat('YYYY-MM-DD');

        //*declaramos collection para guardar horarios de empeado
        $dataHorario = new Collection();
        //*recorremos empleados seleccionnados
        foreach ($empleados as $empleado) {

            //*buscamos su horario para el dia de hoy
            $horarioEmpleado = DB::table('horario_empleado as he')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp',
                    'h.horario_id', 'h.horario_descripcion', 'h.horaI', 'h.horaF',
                    'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario', 'p.perso_nombre as nombre', 'h.horasObliga',
                    DB::raw("IF(he.fuera_horario=1,'Si' , 'No') as fueraHorario"),
                    DB::raw("IF(he.nHoraAdic is null, 0 , nHoraAdic) as horaAdicional"),
                    DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'))
                    ->whereDate('hd.start', '=', $fechaHorario)
                ->where('he.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_id', '=', $empleado)
                ->get();

            //*obtenr solo de empleado con horario
            if ($horarioEmpleado->isNotEmpty()) {
                $dataHorario->push($horarioEmpleado);
            }

        }
        if ($dataHorario) {
            return ($dataHorario);
        } else {
            return ('0');
        }

    }

    //*FUNCION PARA CLONAR HORARIOS DE EMPLEADO***************
    public function clonarHorarios(Request $request)
    {

        //**recibir paramentros
        $empleadosaClonar = $request->empleadosaClonar;
        $empleadoCLonar = $request->empleadoCLonar;
        $diaI = Carbon::create($request->diaI);
        $diaF = Carbon::create($request->diaF);
        $asigNuevo = $request->asigNuevo;
        $reempExistente = $request->reempExistente;

        //*OBTENGO HORARIOS DEL EMPLEADO A CLONAR******
        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['he.horarioEmp_id as id', 'he.horario_horario_id', 'he.horario_dias_id', 'he.fuera_horario',
                'textColor', 'start', 'end', 'he.estado', 'borderColor', 'laborable', 'horaAdic', 'h.horario_id as idhorario',
                'horasObliga', 'nHoraAdic', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                'horaI', 'horaF'])
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.estado', '=', 1)
            ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
            ->where('he.empleado_emple_id', '=', $empleadoCLonar)
            ->get();

        if ($horario_empleado->isNotEmpty()) {
            foreach ($horario_empleado as $horario_empleadoDia) {
                /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
                $fecha = Carbon::create($horario_empleadoDia->start);
                $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                $despues = $fecha->addDays(1);
                $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                if (Carbon::parse($horario_empleadoDia->horaF)->lt(Carbon::parse($horario_empleadoDia->horaI))) {

                    $horario_empleadoDia->horaI = $fechaHoy . " " . $horario_empleadoDia->horaI;
                    $horario_empleadoDia->horaF = $fechaMan . " " . $horario_empleadoDia->horaF;
                } else {
                    $horario_empleadoDia->horaI = $fechaHoy . " " . $horario_empleadoDia->horaI;
                    $horario_empleadoDia->horaF = $fechaHoy . " " . $horario_empleadoDia->horaF;
                }

            }
        }

        //******************************************* */

        //*verifico si hay horarios encontrado******************

        //*COPIO O REEMPLAZO HORARIOS A LOS EMPLEADOS SELECCIONADOS***************************
        //RECORRO LOS HORARIOS A CLOONAR y empleados
        $conRepeticion = 0;
        foreach ($empleadosaClonar as $empleadosCl) {
            $empleadoCruce=0;
            //*si reemplaza primero borro los otros horarios que tenga en esas fechas
            if ($reempExistente == 1) {
                $horario_empleadoBorrar = DB::table('horario_empleado as he')
                    ->select(['he.horarioEmp_id as id', 'he.horario_horario_id', 'he.horario_dias_id', 'color', 'textColor', 'start', 'end', 'horaI', 'horaF', 'borderColor', 'laborable', 'horaAdic', 'h.horario_id as idhorario', 'horasObliga', 'nHoraAdic'])
                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                    ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                    ->where('he.estado', '=', 1)
                    ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
                    ->where('he.empleado_emple_id', '=', $empleadosCl)
                    ->update(['he.estado' => 0]);
                if ($horario_empleado->isNotEmpty()) {
                    foreach ($horario_empleado as $horario_empleados) {

                        $horarioClonando = new horario_empleado();
                        $horarioClonando->horario_horario_id = $horario_empleados->horario_horario_id;
                        $horarioClonando->empleado_emple_id = $empleadosCl;
                        $horarioClonando->horario_dias_id = $horario_empleados->horario_dias_id;
                        $horarioClonando->fuera_horario = $horario_empleados->fuera_horario;
                        $horarioClonando->borderColor = $horario_empleados->borderColor;
                        $horarioClonando->laborable = $horario_empleados->laborable;
                        $horarioClonando->horaAdic = $horario_empleados->horaAdic;
                        $horarioClonando->nHoraAdic = $horario_empleados->nHoraAdic;
                        $horarioClonando->estado = $horario_empleados->estado;
                        $horarioClonando->save();

                    }
                }
              /*   return 1; */

            } else {

                //*OBTENEOS HORARIO DE EMPLEADO
                $horario_empleadoVer = DB::table('horario_empleado as he')
                    ->select(['he.horarioEmp_id as id', 'he.horario_horario_id', 'he.horario_dias_id',
                        'color', 'textColor', 'start', 'end', 'horaI', 'horaF', 'borderColor', 'laborable',
                        'horaAdic', 'h.horario_id as idhorario', 'horasObliga', 'nHoraAdic', 'h.horario_tolerancia as toleranciaI',
                        'h.horario_toleranciaF as toleranciaF'])
                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                    ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                    ->where('he.estado', '=', 1)
                    ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
                    ->where('he.empleado_emple_id', '=', $empleadosCl)
                    ->get();



                //*SI ENCONTRAMOS HORARIOS DE EMPLEADO
                if ($horario_empleadoVer->isNotEmpty()) {

                    //OBTENEMOS DIAS
                    foreach ($horario_empleadoVer as $horario_empleadoVerDi) {
                        /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
                        $fecha = Carbon::create($horario_empleadoVerDi->start);
                        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                        $despues = $fecha->addDays(1);
                        $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                        if (Carbon::parse($horario_empleadoVerDi->horaF)->lt(Carbon::parse($horario_empleadoVerDi->horaI))) {

                            $horario_empleadoVerDi->horaI = $fechaHoy . " " . $horario_empleadoVerDi->horaI;
                            $horario_empleadoVerDi->horaF = $fechaMan . " " . $horario_empleadoVerDi->horaF;
                        } else {
                            $horario_empleadoVerDi->horaI = $fechaHoy . " " . $horario_empleadoVerDi->horaI;
                            $horario_empleadoVerDi->horaF = $fechaHoy . " " . $horario_empleadoVerDi->horaF;
                        }

                    }
                    foreach ($horario_empleadoVer as $horario_empleadoVerH) {

                        //*OBTENEMSO HORAR INICIAL Y FINAL CON TOLERANCIAS DE HORARIOS DE EMPLEADO
                        $horaIEmp = Carbon::parse($horario_empleadoVerH->horaI)->subMinutes($horario_empleadoVerH->toleranciaI);
                        $horaFEmp = Carbon::parse($horario_empleadoVerH->horaF)->addMinutes($horario_empleadoVerH->toleranciaF);

                        if ($horario_empleado->isNotEmpty()) {
                            foreach ($horario_empleado as $key => $horario_empleados) {

                                //*OBTENEMSO HORAR INICIAL Y FINAL CON TOLERANCIAS DE HORARIOS A CLONAR
                                $horaIClonar = Carbon::parse($horario_empleados->horaI)->subMinutes($horario_empleados->toleranciaI);
                                $horaFClonar = Carbon::parse($horario_empleados->horaF)->addMinutes($horario_empleados->toleranciaF);

                                //*VALIDAMOS QUE NO SE CRUZEN
                                if ($horaIEmp->gt($horaIClonar) && $horaFEmp->lt($horaFClonar) && $horaIEmp->lt($horaFClonar)) {
                                    $conRepeticion = 1;
                                    $empleadoCruce=1;


                                } elseif (($horaIEmp->gt($horaIClonar) && $horaIEmp->lt($horaFClonar)) || ($horaFEmp->gt($horaIClonar) && $horaFEmp->lt($horaFClonar))) {
                                    $conRepeticion = 1;
                                    $empleadoCruce=1;

                                } elseif ($horaIEmp == $horaIClonar || $horaFEmp == $horaFClonar) {
                                    $conRepeticion = 1;
                                    $empleadoCruce=1;

                                } elseif ($horaIClonar->gt($horaIEmp) && $horaFClonar->lt($horaFEmp)) {
                                    $conRepeticion = 1;
                                    $empleadoCruce=1;

                                }

                            }
                        }
                    }

                    if ($empleadoCruce == 0) {
                        if ($horario_empleado->isNotEmpty()) {
                            foreach ($horario_empleado as $horario_empleados) {

                                $horarioClonando = new horario_empleado();
                                $horarioClonando->horario_horario_id = $horario_empleados->horario_horario_id;
                                $horarioClonando->empleado_emple_id = $empleadosCl;
                                $horarioClonando->horario_dias_id = $horario_empleados->horario_dias_id;
                                $horarioClonando->fuera_horario = $horario_empleados->fuera_horario;
                                $horarioClonando->borderColor = $horario_empleados->borderColor;
                                $horarioClonando->laborable = $horario_empleados->laborable;
                                $horarioClonando->horaAdic = $horario_empleados->horaAdic;
                                $horarioClonando->nHoraAdic = $horario_empleados->nHoraAdic;
                                $horarioClonando->estado = $horario_empleados->estado;
                                $horarioClonando->save();

                            }
                        }


                    } else {
                        //* si $conRepeticion==1
                        //* no se guarda datos
                       /*  return 0; */
                    }

                } else {

                    //* SI NO TIENE HORARIOS SOLO COPIAMOS LOS CLONADOS
                    if ($horario_empleado->isNotEmpty()) {
                        foreach ($horario_empleado as $horario_empleados) {

                            $horarioClonando = new horario_empleado();
                            $horarioClonando->horario_horario_id = $horario_empleados->horario_horario_id;
                            $horarioClonando->empleado_emple_id = $empleadosCl;
                            $horarioClonando->horario_dias_id = $horario_empleados->horario_dias_id;
                            $horarioClonando->fuera_horario = $horario_empleados->fuera_horario;
                            $horarioClonando->borderColor = $horario_empleados->borderColor;
                            $horarioClonando->laborable = $horario_empleados->laborable;
                            $horarioClonando->horaAdic = $horario_empleados->horaAdic;
                            $horarioClonando->nHoraAdic = $horario_empleados->nHoraAdic;
                            $horarioClonando->estado = $horario_empleados->estado;
                            $horarioClonando->save();

                        }
                    }

                   /*  return 1; */
                }

            }

        }
        if($asigNuevo==1){
          if($conRepeticion==1){
            return 0;
        } else{
            return 1;
        }
        } else{
            return 1;
        }

    }
     //*FUNCION PARA CLONAR INCIDENCIAS DE EMPLEADO***************
     public function clonarIncidencias(Request $request)
     {

         //**recibir paramentros
         $empleadosaClonar = $request->empleadosaClonar;
         $empleadoCLonar = $request->empleadoCLonar;
         $diaI = Carbon::create($request->diaI);
         $diaF = Carbon::create($request->diaF);
         $asigNuevo = $request->asigNuevo;
         $reempExistente = $request->reempExistente;

         //*OBTENGO INCIDENCIAS DEL EMPLEADO A CLONAR******
            $incidencia_empleado = DB::table('incidencia_dias as idi')
            ->select('idi.id_empleado as idempleado', 'idi.inciden_dias_fechaI','idi.id_incidencia'
            )
            ->join('empleado as e', 'idi.id_empleado', '=', 'e.emple_id')
            ->where('idi.id_empleado', '=', $empleadoCLonar)
            ->whereBetween(DB::raw('DATE(idi.inciden_dias_fechaI)'), [$diaI, $diaF])
            ->get();

            //*RECORREMOS EMPLEADOOS A LOS QUE VAMOS A CLONAR INCIDENCIAS
            foreach ($empleadosaClonar as $empleadosCl) {
                //*si reemplaza primero borro los otros incidencias que tenga en esas fechas
                if ($reempExistente == 1) {

                    //*BORRANDO INCIDENCIAS POR EMPLEADO
                    $incidencia_empleadoElim = DB::table('incidencia_dias as idi')
                            ->select('idi.id_empleado as idempleado', 'idi.inciden_dias_fechaI'
                            )
                            ->join('empleado as e', 'idi.id_empleado', '=', 'e.emple_id')
                            ->where('idi.id_empleado', '=',$empleadosCl)
                            ->whereBetween(DB::raw('DATE(idi.inciden_dias_fechaI)'), [$diaI, $diaF])
                            ->delete();


                    //*AHORA COPIAMOS LAS INCIDENCIAS DE EMPLEADO
                    if ($incidencia_empleado->isNotEmpty()) {
                        foreach($incidencia_empleado as $incidencia_empleados){
                            $inc_dias = new incidencia_dias();
                            $inc_dias->id_incidencia = $incidencia_empleados->id_incidencia;
                            $inc_dias->	inciden_dias_fechaI = $incidencia_empleados->inciden_dias_fechaI;
                            $inc_dias->id_empleado = $empleadosCl;
                            $inc_dias->laborable=0;
                            $inc_dias->save();

                        }

                    }
                } else{
                    //*SE COPIA NADA MAS LAS INCIDENCIAS

                    //PRIMERO VERIFICAREMOS QUE NO SE REPITAN LAS INCIDENCIAS ANTERIORES DE EMPLEADOS A LAS PNUEVAS A AGREGAR
                     //*OBTENEOS INCIDENCIA DE EMPLEADO
                     $incidencia_empleadoOb = DB::table('incidencia_dias as idi')
                            ->select('idi.id_empleado as idempleado', 'idi.inciden_dias_fechaI','idi.id_incidencia'
                            )
                            ->join('empleado as e', 'idi.id_empleado', '=', 'e.emple_id')
                            ->where('idi.id_empleado', '=',$empleadosCl)
                            ->whereBetween(DB::raw('DATE(idi.inciden_dias_fechaI)'), [$diaI, $diaF])
                            ->get();

                    //*SI ENCONTRAMOS INCIDENCIAS DE EMPLEADO VERIFICAR QUE NO SE REPITA
                    if ($incidencia_empleadoOb->isNotEmpty()){
                        foreach($incidencia_empleadoOb as $incidenciasO){
                            if ($incidencia_empleado->isNotEmpty()) {
                                foreach ($incidencia_empleado as $key => $incidencia_empleados) {
                                    if($incidenciasO->id_incidencia==$incidencia_empleados->id_incidencia &&
                                     $incidenciasO->inciden_dias_fechaI==$incidencia_empleados->inciden_dias_fechaI ){
                                        $incidencia_empleado->pull($key);
                                    }
                                }
                            }
                        }
                        //* COPIAMOS LAS OTRAS INCIDE
                        if ($incidencia_empleado->isNotEmpty()) {
                            foreach($incidencia_empleado as $incidencia_empleados){
                                $inc_dias = new incidencia_dias();
                                $inc_dias->id_incidencia = $incidencia_empleados->id_incidencia;
                                $inc_dias->	inciden_dias_fechaI = $incidencia_empleados->inciden_dias_fechaI;
                                $inc_dias->id_empleado = $empleadosCl;
                                $inc_dias->laborable=0;
                                $inc_dias->save();

                            }

                        }

                    } else{
                        //* SI NO TIENE INCIDENCIAS SOLO COPIAMOS
                        if ($incidencia_empleado->isNotEmpty()) {
                            foreach($incidencia_empleado as $incidencia_empleados){
                                $inc_dias = new incidencia_dias();
                                $inc_dias->id_incidencia = $incidencia_empleados->id_incidencia;
                                $inc_dias->	inciden_dias_fechaI = $incidencia_empleados->inciden_dias_fechaI;
                                $inc_dias->id_empleado = $empleadosCl;
                                $inc_dias->laborable=0;
                                $inc_dias->save();

                            }

                        }
                    }
                }
            }


         //******************************************* */




     }

    //*FUNCION PARA CONFIRMAR REEMPLAZAR Y OMITIR HORARIOS
    public function reemplazarHorariosClonacion(Request $request)
    {

        //**recibir paramentros
        $empleadosaClonar = $request->empleadosaClonar;
        $empleadoCLonar = $request->empleadoCLonar;
        $diaI = Carbon::create($request->diaI);
        $diaF = Carbon::create($request->diaF);

        //*OBTENGO HORARIOS DEL EMPLEADO A CLONAR******
        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['he.horarioEmp_id as id', 'he.horario_horario_id', 'he.horario_dias_id', 'he.fuera_horario',
                'textColor', 'start', 'end', 'he.estado', 'borderColor', 'laborable', 'horaAdic', 'h.horario_id as idhorario',
                'horasObliga', 'nHoraAdic', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                'horaI', 'horaF'])
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.estado', '=', 1)
            ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
            ->where('he.empleado_emple_id', '=', $empleadoCLonar)
            ->get();

        //*OBTENEMOS FECHAS Y HORA
        if ($horario_empleado->isNotEmpty()) {
            foreach ($horario_empleado as $horario_empleadoDia) {
                /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
                $fecha = Carbon::create($horario_empleadoDia->start);
                $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                $despues = $fecha->addDays(1);
                $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                if (Carbon::parse($horario_empleadoDia->horaF)->lt(Carbon::parse($horario_empleadoDia->horaI))) {

                    $horario_empleadoDia->horaI = $fechaHoy . " " . $horario_empleadoDia->horaI;
                    $horario_empleadoDia->horaF = $fechaMan . " " . $horario_empleadoDia->horaF;
                } else {
                    $horario_empleadoDia->horaI = $fechaHoy . " " . $horario_empleadoDia->horaI;
                    $horario_empleadoDia->horaF = $fechaHoy . " " . $horario_empleadoDia->horaF;
                }

            }
        }

        //******************************************* */

        //*verifico si hay horarios encontrado******************

        //*COPIO O REEMPLAZO HORARIOS A LOS EMPLEADOS SELECCIONADOS***************************
        //RECORRO LOS HORARIOS A CLOONAR y empleados
        foreach ($empleadosaClonar as $empleadosCl) {

            //*si reemplaza primero borro los otros horarios que tenga en esas fechas

            //*OBTENEOS HORARIO DE EMPLEADO
            $horario_empleadoVer = DB::table('horario_empleado as he')
                ->select(['he.horarioEmp_id as id', 'he.horario_horario_id', 'he.horario_dias_id',
                    'color', 'textColor', 'start', 'end', 'horaI', 'horaF', 'borderColor', 'laborable',
                    'horaAdic', 'h.horario_id as idhorario', 'horasObliga', 'nHoraAdic', 'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF'])
                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                ->where('he.estado', '=', 1)
                ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
                ->where('he.empleado_emple_id', '=', $empleadosCl)
                ->get();

            //*SI ENCONTRAMOS HORARIOS DE EMPLEADO
            if ($horario_empleadoVer->isNotEmpty()) {

                //OBTENEMOS DIAS
                foreach ($horario_empleadoVer as $horario_empleadoVerDi) {
                    /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
                    $fecha = Carbon::create($horario_empleadoVerDi->start);
                    $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                    $despues = $fecha->addDays(1);
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                    if (Carbon::parse($horario_empleadoVerDi->horaF)->lt(Carbon::parse($horario_empleadoVerDi->horaI))) {

                        $horario_empleadoVerDi->horaI = $fechaHoy . " " . $horario_empleadoVerDi->horaI;
                        $horario_empleadoVerDi->horaF = $fechaMan . " " . $horario_empleadoVerDi->horaF;
                    } else {
                        $horario_empleadoVerDi->horaI = $fechaHoy . " " . $horario_empleadoVerDi->horaI;
                        $horario_empleadoVerDi->horaF = $fechaHoy . " " . $horario_empleadoVerDi->horaF;
                    }

                }
                foreach ($horario_empleadoVer as $key => $horario_empleadoVerH) {

                    //*OBTENEMSO HORAR INICIAL Y FINAL CON TOLERANCIAS DE HORARIOS DE EMPLEADO
                    $horaIEmp = Carbon::parse($horario_empleadoVerH->horaI)->subMinutes($horario_empleadoVerH->toleranciaI);
                    $horaFEmp = Carbon::parse($horario_empleadoVerH->horaF)->addMinutes($horario_empleadoVerH->toleranciaF);

                    if ($horario_empleado->isNotEmpty()) {
                        foreach ($horario_empleado as $horario_empleados) {

                            //*OBTENEMSO HORAR INICIAL Y FINAL CON TOLERANCIAS DE HORARIOS A CLONAR
                            $horaIClonar = Carbon::parse($horario_empleados->horaI)->subMinutes($horario_empleados->toleranciaI);
                            $horaFClonar = Carbon::parse($horario_empleados->horaF)->addMinutes($horario_empleados->toleranciaF);

                            //*VALIDAMOS QUE NO SE CRUZEN
                            if ($horaIEmp->gt($horaIClonar) && $horaFEmp->lt($horaFClonar) && $horaIEmp->lt($horaFClonar)) {

                                //*SI CRUZA CON HORARIO YA REGISTRADO, QUITAMOS EL HORARIO REGISTRADO
                                $horario_empleadoBorrar = DB::table('horario_empleado as he')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                                    ->where('he.estado', '=', 1)
                                    ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
                                    ->where('he.horarioEmp_id', '=', $horario_empleadoVer[$key]->id)
                                    ->update(['he.estado' => 0]);

                            } elseif (($horaIEmp->gt($horaIClonar) && $horaIEmp->lt($horaFClonar)) || ($horaFEmp->gt($horaIClonar) && $horaFEmp->lt($horaFClonar))) {

                                //*SI CRUZA CON HORARIO YA REGISTRADO, QUITAMOS EL HORARIO REGISTRADO
                                $horario_empleadoBorrar = DB::table('horario_empleado as he')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                                    ->where('he.estado', '=', 1)
                                    ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
                                    ->where('he.horarioEmp_id', '=', $horario_empleadoVer[$key]->id)
                                    ->update(['he.estado' => 0]);

                            } elseif ($horaIEmp == $horaIClonar || $horaFEmp == $horaFClonar) {

                                //*SI CRUZA CON HORARIO YA REGISTRADO, QUITAMOS EL HORARIO REGISTRADO
                                $horario_empleadoBorrar = DB::table('horario_empleado as he')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                                    ->where('he.estado', '=', 1)
                                    ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
                                    ->where('he.horarioEmp_id', '=', $horario_empleadoVer[$key]->id)
                                    ->update(['he.estado' => 0]);

                            } elseif ($horaIClonar->gt($horaIEmp) && $horaFClonar->lt($horaFEmp)) {

                                //*SI CRUZA CON HORARIO YA REGISTRADO, QUITAMOS EL HORARIO REGISTRADO
                                $horario_empleadoBorrar = DB::table('horario_empleado as he')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                                    ->where('he.estado', '=', 1)
                                    ->whereBetween(DB::raw('DATE(hd.start)'), [$diaI, $diaF])
                                    ->where('he.horarioEmp_id', '=', $horario_empleadoVer[$key]->id)
                                    ->update(['he.estado' => 0]);

                            }

                        }
                    }
                }

                if ($horario_empleado->isNotEmpty()) {
                    foreach ($horario_empleado as $horario_empleados) {

                        $horarioClonando = new horario_empleado();
                        $horarioClonando->horario_horario_id = $horario_empleados->horario_horario_id;
                        $horarioClonando->empleado_emple_id = $empleadosCl;
                        $horarioClonando->horario_dias_id = $horario_empleados->horario_dias_id;
                        $horarioClonando->fuera_horario = $horario_empleados->fuera_horario;
                        $horarioClonando->borderColor = $horario_empleados->borderColor;
                        $horarioClonando->laborable = $horario_empleados->laborable;
                        $horarioClonando->horaAdic = $horario_empleados->horaAdic;
                        $horarioClonando->nHoraAdic = $horario_empleados->nHoraAdic;
                        $horarioClonando->estado = $horario_empleados->estado;
                        $horarioClonando->save();

                    }
                }

            } else {

                //* SI NO TIENE HORARIOS SOLO COPIAMOS LOS CLONADOS
                if ($horario_empleado->isNotEmpty()) {
                    foreach ($horario_empleado as $horario_empleados) {

                        $horarioClonando = new horario_empleado();
                        $horarioClonando->horario_horario_id = $horario_empleados->horario_horario_id;
                        $horarioClonando->empleado_emple_id = $empleadosCl;
                        $horarioClonando->horario_dias_id = $horario_empleados->horario_dias_id;
                        $horarioClonando->fuera_horario = $horario_empleados->fuera_horario;
                        $horarioClonando->borderColor = $horario_empleados->borderColor;
                        $horarioClonando->laborable = $horario_empleados->laborable;
                        $horarioClonando->horaAdic = $horario_empleados->horaAdic;
                        $horarioClonando->nHoraAdic = $horario_empleados->nHoraAdic;
                        $horarioClonando->estado = $horario_empleados->estado;
                        $horarioClonando->save();

                    }
                }

            }

        }

    }

    public function Incidenciasxtipo(Request $request){

        $tipoInci=$request->tipoInci;
         //*obtenemos si es de sistema el tipo


         //*VERIFICAMOS SI ES TIPO INCIDENCIA PORQUE SE DEBE JALAR INCIDENCIA Y SISTEMA********
         $tipoInciOrg=DB::table('tipo_incidencia')
         ->where('tipoInc_descripcion','=','Incidencia')
         ->where('organi_id','=', session('sesionidorg'))
         ->get()->first();

         $tipoInciOrgSis=DB::table('tipo_incidencia')
         ->where('tipoInc_descripcion','=','De sistema')
         ->where('organi_id','=', session('sesionidorg'))
         ->get()->first();

         if($tipoInci==$tipoInciOrg->idtipo_incidencia){

             //*OBTENEMOS INCIDENCIAS CON EL TIPO QUE ELEGIMOS
            $incidenciaTipo=DB::table('incidencias')
            ->where('organi_id','=',session('sesionidorg'))
            ->where('idtipo_incidencia','=',$tipoInci)
            ->orWhere('idtipo_incidencia','=', $tipoInciOrgSis->idtipo_incidencia)
            ->where('estado', '=', 1)
            ->get();

         } else{

             //*OBTENEMOS INCIDENCIAS CON EL TIPO QUE ELEGIMOS
            $incidenciaTipo=DB::table('incidencias')
            ->where('organi_id','=',session('sesionidorg'))
            ->where('idtipo_incidencia','=',$tipoInci)
            ->where('estado', '=', 1)
            ->get();
         }

         //************************************************************************************

        //VERIFICAMOS SI ES DE SISTEMA, SI ES DE SISTEMA EN TIPO Y INCIDENCIA ENTONCES NO LLEVAMOS A SELECT
        $tipoSistema=DB::table('tipo_incidencia')
        ->where('tipoInc_descripcion','=','De sistema')
        ->where('organi_id','=', session('sesionidorg'))
        ->get()->first();

        foreach($incidenciaTipo as $key => $incidenciaTipos){
            if($incidenciaTipos->idtipo_incidencia==$tipoSistema->idtipo_incidencia){
                $incidenciaTipos->tipoSistema=1;
            } else{
                $incidenciaTipos->tipoSistema=0;
            }

            if($incidenciaTipos->sistema==1 && $incidenciaTipos->tipoSistema==1){

                //si los 2 lados son de sistema y tipo sistema lo sacamos: fatas, tardanza justificaciones
                $incidenciaTipo->pull($key);

            }
        }

        return response()->json($incidenciaTipo);
    }


    public function registrarInciTemp(Request $request){

        //*PARAMETROS
        $idIncidencia=$request->idIncidencia;
        $datafecha=$request->fechasArray;
        $nombreIncidencia=$request->nombreIncidencia;

        $repetidos=0;
        //*OBTENEMOS INCIDENCIAS TEMPORALES NO REPETIDAS
        foreach ($datafecha as $key=> $datafechas) {

            //obtenemos temporal eventos que sean iguales a los q tenemos
            $tempre = temporal_eventos::where('users_id', '=', Auth::user()->id)
                ->where('start', '=', $datafechas)
                ->where('id_horario', '=', null)
                ->where('incidencia_id', '=', $idIncidencia)
                ->get()->first();

                if ($tempre) {
                    $repetidos=1;
                    // si existe incidencia igual la sacamos
                    Arr::pull($datafecha, $key);

                }
        }

        //*REGISTRAR INCIDENCIA TEMPORAL
        foreach ($datafecha as $datafechas) {
            $temporal_eventos = new temporal_eventos();
            $temporal_eventos->title = $nombreIncidencia;
            $temporal_eventos->color = '#9E9E9E';
            $temporal_eventos->textColor = '#313131';
            $temporal_eventos->start = $datafechas;
            $temporal_eventos->incidencia_id = $idIncidencia;
            $temporal_eventos->users_id = Auth::user()->id;
            $temporal_eventos->save();
        }

        return($repetidos);

    }

    public function datosIncidenciaEmpleado(Request $request)
    {

        //*obtengo paramentros
        $diadeIncidencia = $request->diadeIncidencia;
        $empleados = $request->empleados;

        //*formateamos fecha
        $fecha = Carbon::create($diadeIncidencia);
        $fechaIncidencia = $fecha->isoFormat('YYYY-MM-DD');

        //*declaramos collection para guardar horarios de empeado
        $dataIncidencia = new Collection();
        //*recorremos empleados seleccionnados
        foreach ($empleados as $empleado) {


            $EmpleadoConInc = DB::table('incidencia_dias as idi')
            ->select('p.perso_nombre as nombre',
             'idi.id_empleado as idempleado',
             DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos')
            )
            ->join('empleado as e', 'idi.id_empleado', '=', 'e.emple_id')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->where('idi.id_empleado', '=', $empleado)
            ->whereDate('idi.inciden_dias_fechaI', '=', $fechaIncidencia)
            ->groupBy('e.emple_id')
            ->get();


            //*obtenr solo de empleado con horario
            if ($EmpleadoConInc->isNotEmpty()) {
                $dataIncidencia->push($EmpleadoConInc);
            }

        }

        //*ORDENAMOS POR APELLIDOS Y APLANAMOS
        $dataIncidencia=$dataIncidencia->collapse();
        $dataIncidenciaf=$dataIncidencia->sortBy('apellidos',SORT_LOCALE_STRING)->values()->toArray();

        foreach($dataIncidenciaf as $dataIncidencias){

            //*buscamos su horario para el dia de hoy
            $incidencias = DB::table('incidencia_dias as idi')
            ->select('idi.inciden_dias_id as idIncidencia', 'i.inciden_descripcion as title', 'i.inciden_pagado as pagado', 'i.inciden_codigo',
            'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end','tipoI.tipoInc_descripcion',
            'idi.id_empleado as idempleado'
            )
            ->leftJoin('incidencias as i', 'idi.id_incidencia', '=', 'i.inciden_id')
            ->join('empleado as e', 'idi.id_empleado', '=', 'e.emple_id')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('tipo_incidencia as tipoI','i.idtipo_incidencia','=','tipoI.idtipo_incidencia')
            ->where('idi.id_empleado', '=', $dataIncidencias->idempleado)
            ->whereDate('idi.inciden_dias_fechaI', '=', $fechaIncidencia)
            ->get();

            $dataIncidencias->incidencias= $incidencias;
        }

        return ($dataIncidenciaf);

    }

    //*ELIMINAR HORARIO EMPLEADOS MASIVAMENTE
    public function elimarIncidiEmps(Request $request)
    {
        $diadeIncidencia = $request->diadeIncidencia;
        $empleados = $request->empleados;
        $idsIncEmps = $request->valoresCheck;
        foreach ($idsIncEmps as $idsIncEmp) {
            $incidencia_diasBorrar = incidencia_dias::findOrfail($idsIncEmp);
            $incidencia_diasBorrar->delete();

        }

        //*****LLAMNDO DATOS DE NUEVO PARA QUE ME SIRVA SI TENGO DATOSO O NO*********
        //*formateamos fecha
        $fecha = Carbon::create($diadeIncidencia);
        $fechaIncid = $fecha->isoFormat('YYYY-MM-DD');

        //*declaramos collection para guardar horarios de empeado
        $dataIncidencia = new Collection();
        //*recorremos empleados seleccionnados
        foreach ($empleados as $empleado) {

                $incidencias = DB::table('incidencia_dias as idi')
                ->select('idi.inciden_dias_id as idIncidencia', 'i.inciden_descripcion as title', 'i.inciden_pagado as pagado', 'i.inciden_codigo',
                 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end','p.perso_nombre as nombre','tipoI.tipoInc_descripcion',
                 'idi.id_empleado as idempleado',
                 DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos')

                )
                ->leftJoin('incidencias as i', 'idi.id_incidencia', '=', 'i.inciden_id')
                ->join('empleado as e', 'idi.id_empleado', '=', 'e.emple_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('tipo_incidencia as tipoI','i.idtipo_incidencia','=','tipoI.idtipo_incidencia')
                ->where('idi.id_empleado', '=', $empleado)
                ->where(DB::raw('DATE(idi.inciden_dias_fechaI)'), '=', $fechaIncid)
                ->get();

            //*obtenr solo de empleado con horario
            if ($incidencias->isNotEmpty()) {
                $dataIncidencia->push($incidencias);
            }

        }
        if ($dataIncidencia) {
            return ($dataIncidencia);
        } else {
            return ('0');
        }

    }

    //*api para el tipo deregla modificar
    public function cambiartiporegla(){

        //*PRIMERO VACEAMO LOS CAMPO HORARIOS CON TIPO REGLA*******
        $horarios=DB::table('horario')->get();
        foreach($horarios as $horario){
            $horarioACtualizar=horario::find($horario->horario_id);
            $horarioACtualizar->idreglas_horasExtras=null;
            $horarioACtualizar->idreglas_horasExtrasNoct=null;
            $horarioACtualizar->save();
        }
        //*************************************************** */

        //*CAMBIAMOS PARA NORMAL TIPO1
        $tiposRegla=DB::table('reglas_horasextras')
        ->where('idTipoRegla','=',1) ->update(['reglas_descripcion' => 'Horas extras(25% y 35%)',
        'lleno25'=>0,'lleno35'=> 1,'lleno100'=> 2]);

        //*CAMBIAMOS PARA NORMAL TIPO2
        $tiposRegla=DB::table('reglas_horasextras')
        ->where('idTipoRegla','=',2) ->update(['tipo_regla'=> 'Nocturno','reglas_descripcion' => 'Horas extras(35%)',
        'lleno25'=>2,'lleno35'=> 1,'lleno100'=> 2]);

        //*CAMBIAMOS PARA NORMAL TIPO3
        $tiposRegla=DB::table('reglas_horasextras')
        ->where('idTipoRegla','=',3) ->update(['reglas_descripcion' => 'Horas extras(25% y 35%)',
        'lleno25'=>0,'lleno35'=> 1,'lleno100'=> 2]);

         //*ELIMINAMOS TIPO DE REGLA 4Y 5
         $tiposRegla=DB::table('reglas_horasextras')
         ->where('idTipoRegla','=',4) ->orWhere('idTipoRegla','=',5)
         ->delete();


         //*SETEANDO TIPO REGLA POR ORGANIZACION
         $organizaciones=DB::table('organizacion')->get();
         foreach($organizaciones as $organizacion){

            //*obtenemos tipo regla1 de cada organizacion
            $tiposReglaOrgN=DB::table('reglas_horasextras')
            ->where('idTipoRegla','=',1)
            ->where('organi_id','=',$organizacion->organi_id)
            ->get()->first();

            //*obtenemos tipo regla3 noct de cada organizacion
            $tiposReglaOrgNoct=DB::table('reglas_horasextras')
            ->where('idTipoRegla','=',3)
            ->where('organi_id','=',$organizacion->organi_id)
            ->get()->first();


            //*OBTENEMOS TODOS LOS HORARIOS DE CADA ORGANIZACION
            $horariosOrgan=DB::table('horario')
            ->where('organi_id','=',$organizacion->organi_id)->get();

            //*CAMBIAMOS REGLAS PARA CADA HORARIO
            foreach($horariosOrgan as $horariosOrg){

                $horarioACtualizar=horario::find($horariosOrg->horario_id);
                $horarioACtualizar->idreglas_horasExtras=$tiposReglaOrgN->idreglas_horasExtras;
                $horarioACtualizar->idreglas_horasExtrasNoct=$tiposReglaOrgNoct->idreglas_horasExtras;
                $horarioACtualizar->save();
            }

         }

         return('ejecutado con exito, no ejecutar otra vez');

    }
    public function mostrarReporteHorarios()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $organizacion = DB::table('organizacion')->select('organi_razonSocial')->where('organi_id', '=', session('sesionidorg'))->first();

            $usuario_organizacion = DB::table('usuario_organizacion as uso')
                ->where('uso.organi_id', '=', session('sesionidorg'))
                ->where('uso.user_id', '=', Auth::user()->id)
                ->get()->first();
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                if ($invitado->verTodosEmps == 1) {
                    //* EMPLEADO
                    $empleado = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_id')
                        ->get();

                    //? AREA
                    $areas =  DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->select(
                            'a.area_id',
                            'a.area_descripcion'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_area')
                        ->get();

                    $cargos =  DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('cargo as a', 'e.emple_cargo', '=', 'a.cargo_id')
                        ->select(
                            'a.cargo_id as idcargo',
                            'a.cargo_descripcion as descripcion'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_cargo')
                        ->get();

                    $locales =  DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('local as a', 'e.emple_local', '=', 'a.local_id')
                        ->select(
                            'a.local_id as idlocal',
                            'a.local_descripcion as descripcion'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_cargo')
                        ->get();
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();

                    if ($invitado_empleadoIn != null) {
                        //* EMPLEADO
                        $empleado = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();

                        //? AREA
                        $areas =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->select(
                                'a.area_id',
                                'a.area_descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_area')
                            ->get();

                        $cargos =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('cargo as a', 'e.emple_cargo', '=', 'a.cargo_id')
                            ->select(
                                'a.cargo_id as idcargo',
                                'a.cargo_descripcion as descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_cargo')
                            ->get();

                        $locales =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('local as a', 'e.emple_local', '=', 'a.local_id')
                            ->select(
                                'a.local_id as idlocal',
                                'a.local_descripcion as descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_cargo')
                            ->get();
                    } else {
                        //* EMPLEADO
                        $empleado = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();

                        //? AREA
                        $areas =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->select(
                                'a.area_id',
                                'a.area_descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_area')
                            ->get();

                        $cargos =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('cargo as a', 'e.emple_cargo', '=', 'a.cargo_id')
                            ->select(
                                'a.cargo_id as idcargo',
                                'a.cargo_descripcion as descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_cargo')
                            ->get();

                        $locales =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('local as a', 'e.emple_local', '=', 'a.local_id')
                            ->select(
                                'a.local_id as idlocal',
                                'a.local_descripcion as descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_cargo')
                            ->get();


                    }
                }
            } else {
                // * EMPLEADO
                $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_id')
                    ->get();
                //? AREA
                $areas =  DB::table('empleado as e')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->select(
                        'a.area_id',
                        'a.area_descripcion'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_area')
                    ->get();

                $cargos =  DB::table('empleado as e')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->join('cargo as a', 'e.emple_cargo', '=', 'a.cargo_id')
                    ->select(
                        'a.cargo_id as idcargo',
                        'a.cargo_descripcion as descripcion'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_cargo')
                    ->get();

                $locales =  DB::table('empleado as e')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->join('local as a', 'e.emple_local', '=', 'a.local_id')
                    ->select(
                        'a.local_id as idlocal',
                        'a.local_descripcion as descripcion'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_cargo')
                    ->get();
            }

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if ($invitadod->modoCR == 1) {

                        return view('horarios.matrizHorarios', ['organizacion' => $organizacion->organi_razonSocial, 'empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('horarios.matrizHorarios', ['organizacion' => $organizacion->organi_razonSocial, 'empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                }
            } else {
                return view('horarios.matrizHorarios', ['organizacion' => $organizacion->organi_razonSocial, 'empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
            }
        }
    }

    public function cargarReporteHorarios(Request $request){
        if($request->area == "" && $request->empleado == ""){
            $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
            ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
            ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
            ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
            ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
            ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
            ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
            ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
            ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                DB::raw('DATE(hd.start) as DP')
            )
            ->where('e.emple_estado', '=', 1)
            ->where(function ($query) {
                $query->where('he.estado', '=', 1)
                    ->orWhereNull('he.estado');
            })
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();
        } else {
            // area y empleados tienen valor
            if($request->area != "" && $request->empleado != ""){
                if($request->selector == "Área"){
                    $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                    ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                    ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                    ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                    ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                    ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                    ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                        DB::raw('DATE(hd.start) as DP')
                    )
                    ->where('e.emple_estado', '=', 1)
                    ->where(function ($query) {
                        $query->where('he.estado', '=', 1)
                            ->orWhereNull('he.estado');
                    })
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_area', '=', $request->area)
                    ->where('e.emple_id', '=', $request->empleado)
                    ->get();
                } else {
                    if($request->selector == "Cargo"){
                        $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                        ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                        ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                        ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                        ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                        ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                            DB::raw('DATE(hd.start) as DP')
                        )
                        ->where('e.emple_estado', '=', 1)
                        ->where(function ($query) {
                            $query->where('he.estado', '=', 1)
                                ->orWhereNull('he.estado');
                        })
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_cargo', '=', $request->area)
                        ->where('e.emple_id', '=', $request->empleado)
                        ->get();
                    } else {
                        if($request->selector == "Local"){
                            $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                            ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                            ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                            ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                            ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                            ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                            ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                            ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                            ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                            ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                                DB::raw('DATE(hd.start) as DP')
                            )
                            ->where('e.emple_estado', '=', 1)
                            ->where(function ($query) {
                                $query->where('he.estado', '=', 1)
                                    ->orWhereNull('he.estado');
                            })
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_local', '=', $request->area)
                            ->where('e.emple_id', '=', $request->empleado)
                            ->get();
                        }
                    }
                }
                
            } else {
                if($request->area != ""){
                    if($request->selector == "Área"){
                        $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                        ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                        ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                        ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                        ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                        ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                            DB::raw('DATE(hd.start) as DP')
                        )
                        ->where('e.emple_estado', '=', 1)
                        ->where(function ($query) {
                            $query->where('he.estado', '=', 1)
                                ->orWhereNull('he.estado');
                        })
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_area', '=', $request->area)
                        ->get();
                    } else {
                        if($request->selector == "Cargo"){
                            $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                            ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                            ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                            ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                            ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                            ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                            ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                            ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                            ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                            ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                                DB::raw('DATE(hd.start) as DP')
                            )
                            ->where('e.emple_estado', '=', 1)
                            ->where(function ($query) {
                                $query->where('he.estado', '=', 1)
                                    ->orWhereNull('he.estado');
                            })
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_cargo', '=', $request->area)
                            ->get();
                        } else {
                            if($request->selector == "Local"){
                                $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                                ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                                ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                                ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                                ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                                ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                                ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                                    DB::raw('DATE(hd.start) as DP')
                                )
                                ->where('e.emple_estado', '=', 1)
                                ->where(function ($query) {
                                    $query->where('he.estado', '=', 1)
                                        ->orWhereNull('he.estado');
                                })
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_local', '=', $request->area)
                                ->get();
                            }
                        }
                    }
                }

                if($request->empleado != ""){
                    $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                    ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                    ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                    ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                    ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                    ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                    ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                        DB::raw('DATE(hd.start) as DP')
                    )
                    ->where('e.emple_estado', '=', 1)
                    ->where(function ($query) {
                        $query->where('he.estado', '=', 1)
                            ->orWhereNull('he.estado');
                    })
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_id', '=', $request->empleado)
                    ->get();
                }
            }
        }
        
        $empleados = $empleados->groupBy('perso_id')->values();
        //dd($empleados);

        $date1 = new DateTime($request->fecha1);
        $date2 = new DateTime($request->fecha2);
        $diff = $date1->diff($date2);
        $dias = array();

        for ($i = 0; $i <= $diff->days; $i++) {
            $dia = strtotime('+' . $i . 'day', strtotime($request->fecha1));
            array_push($dias, date('Y-m-j', $dia));
        }

        $contEmpleados = $empleados->count();
        $empleados = $empleados->concat($dias);
        $empleados = $empleados->push($contEmpleados);

        return response()->json($empleados, 200);
    }

    public function selectMatrizHorarios(Request $request)
    {
        $area = $request->get('area');
        $selector = $request->selector;
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if (is_null($area) === true) {
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();

                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_id')
                        ->get();
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();
                    }
                }
            } else {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_id')
                    ->get();
            }
            return response()->json($empleados, 200);
        } else {
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();

                if ($invitado->verTodosEmps == 1) {
                    if($selector == "Área"){
                        $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->groupBy('e.emple_id')
                        ->get();
                    } else {
                        if($selector == "Cargo"){
                            $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                        } else {
                            if ($selector == "Local") {
                                $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_local', $area)
                                ->groupBy('e.emple_id')
                                ->get();
                            }
                        }
                    }
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
                        if($selector == "Área"){
                            $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->whereIn('e.emple_area', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                        } else {
                            if($selector == "Cargo"){
                                $empleados = DB::table('empleado as e')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_cargo', $area)
                                ->groupBy('e.emple_id')
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereIn('e.emple_local', $area)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                }
                            }
                        }
                    } else {
                        if($selector == "Área"){
                            $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->whereIn('e.emple_area', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                        } else {
                            if($selector == "Cargo"){
                                $empleados = DB::table('empleado as e')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_cargo', $area)
                                ->groupBy('e.emple_id')
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereIn('e.emple_local', $area)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                }
                            }
                        }

                    }
                }
            } else {
                if($selector == "Área"){
                    $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->whereIn('e.emple_area', $area)
                    ->groupBy('e.emple_id')
                    ->get();
                } else {
                    if($selector == "Cargo"){
                        $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_cargo', $area)
                        ->groupBy('e.emple_id')
                        ->get();
                    } else {
                        if($selector == "Local"){
                            $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_local', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                        }
                    }
                }
            }

            return response()->json($empleados, 200);
        }
    }

    public function cargarMatrizHorario(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $fecha = $request->get('fecha');
        $fechaF = explode("a", $fecha);
        $area = $request->get('area');
        $empleadoL = $request->get('empleadoL');
        $selector = $request->selector;

        // AREA -> NULL && EMPLEADO -> NULL
        if (is_null($area) === true && is_null($empleadoL) === true) {
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();

                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                        ->select('e.emple_id')
                        ->where('e.emple_estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();

                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();
                    }
                }
            } else {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->join('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                    ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->leftjoin('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                    ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                    ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                    ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                    ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal','o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                        DB::raw('DATE(hd.start) as DP')
                    )
                    ->where('e.emple_estado', '=', 1)
                    ->where('he.estado', '=', 1)
                    ->where(function ($query) {
                        $query->where('he.estado', '=', 1)
                              ->orWhereNull('he.estado');
                    })
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->get();
                    $empleados = $empleados->groupBy('perso_id')->values();
                    //dd($empleados);

                    $date1 = new DateTime($fechaF[0]);
                    $date2 = new DateTime($fechaF[1]);
                    $diff = $date1->diff($date2);
                    $dias = array();

                    for ($i = 0; $i <= $diff->days; $i++) {
                        $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                        array_push($dias, date('Y-m-j', $dia));
                    }

                    $contEmpleados = $empleados->count();
                    $empleados = $empleados->concat($dias);
                    $empleados = $empleados->push($contEmpleados);
            }
        } else {
            if (is_null($area) === false && is_null($empleadoL) === true) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();

                    if ($invitado->verTodosEmps == 1) {
                        if($selector == "Área"){
                            $empleados = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                        } else {
                            if($selector == "Cargo"){
                                $empleados = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados = DB::table('empleado as e')
                                        ->select('e.emple_id')
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->get();
                                }
                            }
                        }
                    } else {
                        $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                            ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                            ->where('invem.area_id', '=', null)
                            ->where('invem.emple_id', '!=', null)
                            ->get()->first();
                        if ($invitado_empleadoIn != null) {
                            if($selector == "Área"){
                                $empleados = DB::table('empleado as e')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereIn('e.emple_area', $area)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->get();
                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->get();
                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                            ->select('e.emple_id')
                                            ->where('e.emple_estado', '=', 1)
                                            ->where('invi.estado', '=', 1)
                                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                            ->whereIn('e.emple_local', $area)
                                            ->orderBy('e.emple_id')
                                            ->where('e.organi_id', '=', session('sesionidorg'))
                                            ->get();
                                    }
                                }
                            }
                        } else {
                            if($selector == "Área"){
                                $empleados = DB::table('empleado as e')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_area', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();

                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->get();
                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                        ->select('e.emple_id')
                                        ->where('e.emple_estado', '=', 1)
                                        ->where('invi.estado', '=', 1)
                                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                        ->whereIn('e.emple_local', $area)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->get();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ($selector == "Cargo") {
                            $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->join('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                                ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                                ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                                ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                                ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                                ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                                    DB::raw('DATE(hd.start) as DP')
                                )
                                ->where('e.emple_estado', '=', 1)
                                ->where('he.estado', '=', 1)
                                ->where(function ($query) {
                                    $query->where('he.estado', '=', 1)
                                          ->orWhereNull('he.estado');
                                })
                                ->whereIn('e.emple_cargo', $area)
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                                $empleados = $empleados->groupBy('perso_id')->values();
                                //dd($empleados);

                                $date1 = new DateTime($fechaF[0]);
                                $date2 = new DateTime($fechaF[1]);
                                $diff = $date1->diff($date2);
                                $dias = array();

                                for ($i = 0; $i <= $diff->days; $i++) {
                                    $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                                    array_push($dias, date('Y-m-j', $dia));
                                }

                                $contEmpleados = $empleados->count();
                                $empleados = $empleados->concat($dias);
                                $empleados = $empleados->push($contEmpleados);
                    } else {
                        if ($selector == "Área") {
                            $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                                ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                                ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                                ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                                ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                                ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                                ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                                    DB::raw('DATE(hd.start) as DP')
                                )
                                ->where('e.emple_estado', '=', 1)
                                ->where('he.estado', '=', 1)
                                ->where(function ($query) {
                                    $query->where('he.estado', '=', 1)
                                          ->orWhereNull('he.estado');
                                })
                                ->whereIn('e.emple_area', $area)
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                                $empleados = $empleados->groupBy('perso_id')->values();
                                //dd($empleados);

                                $date1 = new DateTime($fechaF[0]);
                                $date2 = new DateTime($fechaF[1]);
                                $diff = $date1->diff($date2);
                                $dias = array();

                                for ($i = 0; $i <= $diff->days; $i++) {
                                    $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                                    array_push($dias, date('Y-m-j', $dia));
                                }

                                $contEmpleados = $empleados->count();
                                $empleados = $empleados->concat($dias);
                                $empleados = $empleados->push($contEmpleados);
                        } else {
                            if ($selector == "Local") {
                                $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                                ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                                ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                                ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                                ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                                ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                                ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                                    DB::raw('DATE(hd.start) as DP')
                                )
                                ->where('e.emple_estado', '=', 1)
                                ->where('he.estado', '=', 1)
                                ->where(function ($query) {
                                    $query->where('he.estado', '=', 1)
                                          ->orWhereNull('he.estado');
                                })
                                ->whereIn('e.emple_local', $area)
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                                $empleados = $empleados->groupBy('perso_id')->values();
                                //dd($empleados);

                                $date1 = new DateTime($fechaF[0]);
                                $date2 = new DateTime($fechaF[1]);
                                $diff = $date1->diff($date2);
                                $dias = array();

                                for ($i = 0; $i <= $diff->days; $i++) {
                                    $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                                    array_push($dias, date('Y-m-j', $dia));
                                }

                                $contEmpleados = $empleados->count();
                                $empleados = $empleados->concat($dias);
                                $empleados = $empleados->push($contEmpleados);
                            }
                        }
                    }
                }
            }
            if (is_null($area) === true && is_null($empleadoL) === false) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();

                    if ($invitado->verTodosEmps == 1) {
                        $empleados = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                    } else {
                        $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                            ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                            ->where('invem.area_id', '=', null)
                            ->where('invem.emple_id', '!=', null)
                            ->get()->first();
                        if ($invitado_empleadoIn != null) {
                            $empleados = DB::table('empleado as e')
                                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                        } else {
                            $empleados = DB::table('empleado as e')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                        }
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                        ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                        ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                        ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                        ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                        ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                            DB::raw('DATE(hd.start) as DP')
                        )
                        ->where('e.emple_estado', '=', 1)
                        ->where('he.estado', '=', 1)
                        ->where(function ($query) {
                            $query->where('he.estado', '=', 1)
                                  ->orWhereNull('he.estado');
                        })
                        ->whereIn('e.emple_id', $empleadoL)
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();
                        $empleados = $empleados->groupBy('perso_id')->values();
                        //dd($empleados);

                        $date1 = new DateTime($fechaF[0]);
                        $date2 = new DateTime($fechaF[1]);
                        $diff = $date1->diff($date2);
                        $dias = array();

                        for ($i = 0; $i <= $diff->days; $i++) {
                            $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                            array_push($dias, date('Y-m-j', $dia));
                        }

                        $contEmpleados = $empleados->count();
                        $empleados = $empleados->concat($dias);
                        $empleados = $empleados->push($contEmpleados);
                }
            }
            if (is_null($area) === false && is_null($empleadoL) === false) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();

                    if ($invitado->verTodosEmps == 1) {
                        if($selector == "Área"){
                            $empleados = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                        } else {
                            if($selector == "Cargo"){
                                $empleados = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados = DB::table('empleado as e')
                                    ->select('e.emple_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_local', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->get();
                                }
                            }
                        }

                    } else {
                        $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                            ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                            ->where('invem.area_id', '=', null)
                            ->where('invem.emple_id', '!=', null)
                            ->get()->first();
                        if ($invitado_empleadoIn != null) {
                            if($selector == "Área"){
                                $empleados = DB::table('empleado as e')
                                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->get();
                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                        ->select('e.emple_id')
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->whereIn('e.emple_id', $empleadoL)
                                        ->where('invi.estado', '=', 1)
                                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->get();
                                    }
                                }
                            }
                        } else {
                            if($selector == "Área"){
                                $empleados = DB::table('empleado as e')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->get();
                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                        ->select('e.emple_id')
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->whereIn('e.emple_id', $empleadoL)
                                        ->where('invi.estado', '=', 1)
                                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->get();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if($selector == "Área"){
                        $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                        ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                        ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                        ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                        ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                        ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                            DB::raw('DATE(hd.start) as DP')
                        )
                        ->where('e.emple_estado', '=', 1)
                        ->where('he.estado', '=', 1)
                        ->where(function ($query) {
                            $query->where('he.estado', '=', 1)
                                  ->orWhereNull('he.estado');
                        })
                        ->whereIn('e.emple_area', $area)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();
                        $empleados = $empleados->groupBy('perso_id')->values();
                        //dd($empleados);

                        $date1 = new DateTime($fechaF[0]);
                        $date2 = new DateTime($fechaF[1]);
                        $diff = $date1->diff($date2);
                        $dias = array();

                        for ($i = 0; $i <= $diff->days; $i++) {
                            $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                            array_push($dias, date('Y-m-j', $dia));
                        }

                        $contEmpleados = $empleados->count();
                        $empleados = $empleados->concat($dias);
                        $empleados = $empleados->push($contEmpleados);

                    } else {
                        if($selector == "Cargo"){
                            $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                                ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                                ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                                ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                                ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                                ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                                ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                                    DB::raw('DATE(hd.start) as DP')
                                )
                                ->where('e.emple_estado', '=', 1)
                                ->where('he.estado', '=', 1)
                                ->where(function ($query) {
                                    $query->where('he.estado', '=', 1)
                                          ->orWhereNull('he.estado');
                                })
                                ->whereIn('e.emple_cargo', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                                $empleados = $empleados->groupBy('perso_id')->values();
                                //dd($empleados);

                                $date1 = new DateTime($fechaF[0]);
                                $date2 = new DateTime($fechaF[1]);
                                $diff = $date1->diff($date2);
                                $dias = array();

                                for ($i = 0; $i <= $diff->days; $i++) {
                                    $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                                    array_push($dias, date('Y-m-j', $dia));
                                }

                                $contEmpleados = $empleados->count();
                                $empleados = $empleados->concat($dias);
                                $empleados = $empleados->push($contEmpleados);
                        } else {
                            if($selector == "Local"){
                                $empleados = DB::table('empleado as e')
                                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                    ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                                    ->leftjoin('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
                                    ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                    ->leftjoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                    ->leftjoin('local as l', 'l.local_id', '=', 'e.emple_local')
                                    ->leftjoin('nivel as n', 'n.nivel_id', '=', 'e.emple_nivel')
                                    ->leftjoin('centrocosto_empleado as cce', 'cce.idEmpleado', '=', 'e.emple_id')
                                    ->leftjoin('centro_costo as cc', 'cc.centroC_id', '=', 'cce.idCentro')
                                    ->select('e.emple_Correo', 'ho.horario_descripcion as horario', 'ho.horaI', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'p.perso_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'a.area_descripcion as area', 'c.cargo_descripcion as cargo', 'l.local_descripcion as local', 'n.nivel_descripcion as nivel', 'cc.centroC_descripcion', 'ho.horasObliga as horasObligadas', 'ho.horaI as horaInicio', 'ho.horaF as horaFinal', 'o.organi_ruc', 'organi_razonSocial', 'o.organi_direccion',
                                        DB::raw('DATE(hd.start) as DP')
                                    )
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('he.estado', '=', 1)
                                    ->where(function ($query) {
                                        $query->where('he.estado', '=', 1)
                                              ->orWhereNull('he.estado');
                                    })
                                    ->whereIn('e.emple_local', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->get();
                                    $empleados = $empleados->groupBy('perso_id')->values();
                                    //dd($empleados);

                                    $date1 = new DateTime($fechaF[0]);
                                    $date2 = new DateTime($fechaF[1]);
                                    $diff = $date1->diff($date2);
                                    $dias = array();

                                    for ($i = 0; $i <= $diff->days; $i++) {
                                        $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                                        array_push($dias, date('Y-m-j', $dia));
                                    }

                                    $contEmpleados = $empleados->count();
                                    $empleados = $empleados->concat($dias);
                                    $empleados = $empleados->push($contEmpleados);
                            }
                        }
                    }

                }
            }
        }

        return response()->json($empleados, 200);
    }

}
