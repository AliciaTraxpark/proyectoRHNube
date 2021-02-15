<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\dispositivo_area;
use App\dispositivo_controlador;
use App\dispositivo_empleado;
use App\dispositivos;
use App\horario;
use App\marcacion_puerta;
use App\pausas_horario;
use App\tardanza;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use LengthException;
use Illuminate\Database\Eloquent\Collection;
use DateTime;

class TardanzasController extends Controller
{
///////////////////////////////////////////// CONTROL REMOTO //////////////////////////////////////////////////
    public function mostrarReporteTardanza()
    {
        if(session('sesionidorg')==NULL){
            return redirect('/');
        }

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

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
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }

        return view('tardanzas.reporteTardanzas', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
    }
    // CORREGIDO
    function getTardanzas($empleados, $fechaInicio, $fechaFin, $fechaHorarioI, $fechaHorarioF){

        $datos = new Collection();

        // CAPTURAS
        $capturas = DB::table('empleado as e')
        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
        ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
        ->select('p.perso_nombre', 'cp.hora_ini', 'e.emple_id', 'cp.idCaptura', 'c.cargo_descripcion', 'a.area_descripcion',
            DB::raw('DATE(cp.hora_ini) as diaM'),
            DB::raw('TIME(cp.hora_ini) as horaM'),
            DB::raw('0 as horario'),
            DB::raw('0 as Falta'),
            DB::raw('0 as dia')
        )
        ->where('e.organi_id', session('sesionidorg'))
        ->whereDate(DB::raw('DATE(cp.hora_ini)'), '>=',$fechaInicio)
        ->whereDate(DB::raw('DATE(cp.hora_ini)'), '<=',$fechaFin)
        ->orderBy('e.emple_id')
        ->get();

        // HORARIOS
        $horarios = DB::table('horario_empleado as he')
        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
        ->leftjoin('empleado as e', 'e.emple_id', '=', 'he.empleado_emple_id')
        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
        ->select('ho.horario_id', 'ho.horario_tolerancia', 'ho.horaI as horaH', 'ho.horaF', 'ho.horario_toleranciaF', 'e.emple_id', 'ho.horario_descripcion', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 
            DB::raw('DATE(hd.start) as diaH'),
            DB::raw('0 as HF'),
            DB::raw('0 as HI'),
            DB::raw('DATE(hd.start) as DP'),
            DB::raw('0 as horaM'),
            DB::raw('0 as diaM'),
            DB::raw('0 as marcacion'),
            DB::raw('0 as ver')
        )
        ->where('he.estado', '=', 1)
        ->where('ho.organi_id', '=', session('sesionidorg'))
        ->whereDate(DB::raw('DATE(hd.start)'), '>=',$fechaHorarioI)
        ->whereDate(DB::raw('DATE(hd.start)'), '<=',$fechaHorarioF)
        ->groupBy('hd.start', 'he.empleado_emple_id', 'ho.horaI')
        ->orderBy('he.empleado_emple_id')
        ->get();

        foreach ($horarios as $horario) {
            $horaEntrada = Carbon::parse($horario->horaH);
            $horaSalida = Carbon::parse($horario->horaF);
            $temp = Carbon::parse($horario->DP)->addDays(1);
            if($horaEntrada > $horaSalida){
                $horario->HF = "23:59:59";
                $horario->HI = "00:00:00";
                $horario->DP = $temp->year."-".$temp->month."-".$temp->day;
            }
        }

        /*      RECORREMOS CADA HORARIO        */
        foreach ($horarios as $horario) {
            $dia_horario = Carbon::parse($horario->diaH); // DÍA: 15
            $dia_horario2 = Carbon::parse($horario->DP); // SUMA UN DÍA AL HORARIO DÍA -> DÍA: 16
            $horario_entrada = Carbon::parse($horario->horaH)->subMinutes($horario->horario_tolerancia);
            $horario_salida = Carbon::parse($horario->horaF)->addMinutes($horario->horario_toleranciaF);

            foreach ($capturas as $captura) {
                $dia_marcacion = Carbon::parse($captura->diaM);
                $hora_marcacion = Carbon::parse($captura->horaM);
                // VERIFICAMOS QUE EL HORARIO LE PERTENEZCA AL EMPLEADO DE LA CAPTURA
                if($horario->emple_id == $captura->emple_id){
                    // HORARIO SAYAYIN
                    if($horario_entrada > $horario_salida){
                        $HF_temp = Carbon::parse($horario->HF);
                        $HI_temp = Carbon::parse($horario->HI);
                        // DIA DE MARCACIÓN ES IGUAL AL DIA DEL HORARIO
                        if($dia_marcacion->equalTo($dia_horario)){
                            // PERTENECE A LA PRIMERA MITAD DEL HORARIO ANTES DE FINALIZAR EL PRIMER DÍA (NORMAL)
                            if($hora_marcacion->greaterThanOrEqualTo($horario_entrada) && $hora_marcacion->lessThanOrEqualTo($HF_temp)){
                                if($captura->horario === 0){
                                    $captura->horario = $horario->horario_id;
                                    $captura->dia = $horario->diaH;
                                } else {
                                    $horario_temp = Carbon::parse($captura->horario);
                                    $diff1 = $horario_entrada->diffInSeconds($hora_marcacion); // NUEVO HORARIO
                                    $diff2 = $horario_temp->diffInSeconds($hora_marcacion); 
                                    if($diff1 < $diff2){
                                        $captura->horario = $horario->horario_id;
                                        $captura->dia = $horario->diaH;
                                    }
                                }
                                //$captura->Falta = $captura->Falta."|".$horario->horaH."|";
                            }
                        } else {
                            // DIA DE LA MARCACIÓN ES IGUAL A UN DÍA DESPUÉS DEL HORARIO
                            if($dia_marcacion->equalTo($dia_horario2)){
                                // PERTENECER A LA SEGUNDA MITAD DEL HORARIO, LO RESTANTE DEL SIGUIENTE DÍA (pendiente)
                                if($hora_marcacion->greaterThanOrEqualTo($HI_temp) && $hora_marcacion->lessThanOrEqualTo($horario_salida)){
                                    $captura->horario = $horario->horario_id;
                                    $captura->dia = $horario->diaH;
                                    //$captura->Falta = $captura->Falta."/".$horario->horaH."/";
                                }
                            }
                        }
                        //$captura->horario = $captura->horario."|".$horario->diaH."|";
                    } else {
                        // HORARIO NORMAL
                        if($dia_marcacion->equalTo($dia_horario)){
                            if ($hora_marcacion->greaterThanOrEqualTo($horario_entrada) && $horario_salida->greaterThanOrEqualTo($hora_marcacion)) {
                                if($captura->horario === 0){
                                    $captura->horario = $horario->horario_id;
                                    $captura->dia = $horario->diaH;
                                } else {
                                    $horario_temp = Carbon::parse($captura->horario);
                                    $diff1 = $horario_entrada->diffInSeconds($hora_marcacion); 
                                    $diff2 = $horario_temp->diffInSeconds($hora_marcacion); // NUEVO HORARIO
                                    if($diff1 < $diff2){
                                        $captura->horario = $horario->horario_id;
                                        $captura->dia = $horario->diaH;
                                    }
                                }
                            }
                            //$captura->Falta = $captura->Falta."#".$horario->horaH."#";
                        }
                        //$captura->horario = $captura->horario."/".$horario->diaH."/";
                    }
                    if($captura->horario != "0"){
                        $hora_horario_new = Carbon::parse($captura->horario);
                        if($hora_horario_new < $hora_marcacion){
                            $captura->Falta = "TARDANZA";
                        }
                    }
                }
            }
        }

        //$diff = $capturas->diffAssoc(['horario' => 0]);

        //dd($capturas);

        $capturas = $capturas->values();

        // HORARIOS TIENE TODOS LOS HORARIOS QUE TIENE UN EMPLEADO EN UN DÍA
        foreach($empleados as $empleado){
            foreach($horarios as $horario){
                if($empleado->emple_id == $horario->emple_id){
                    foreach ($capturas as $captura) {
                        // MISMO EMPLEADO, MISMO HORARIO Y MISMO DIA DE HORARIO
                        if($captura->emple_id == $horario->emple_id && $captura->horario == $horario->horario_id && $horario->diaH == $captura->diaM){
                            if($horario->horaM == 0){
                                $horario->horaM = $captura->horaM;
                                $horario->diaM = $captura->diaM;
                                $horario->marcacion = $captura->hora_ini;
                            } else {
                                $marca_temp = Carbon::parse($horario->horaM);
                                $marca_new = Carbon::parse($captura->horaM);
                                if($marca_temp > $marca_new){
                                    $horario->horaM = $captura->horaM;
                                    $horario->diaM = $captura->diaM;
                                    $horario->marcacion = $captura->hora_ini;
                                }
                            }
                        }
                    } 
                }
            }
        }

        $collection = new Collection;

        foreach($horarios as $horario){
            if($horario->horaM != 0){
                $collection->push($horario);
            }
        }

        return $collection;
    }

    function getTardanzasUbi($empleados, $fechaInicio, $fechaFin, $fechaHorarioI, $fechaHorarioF){

        $datos = new Collection();

        // CAPTURAS
        $capturas = DB::table('empleado as e')
        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
        ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
        ->select('p.perso_nombre', 'u.hora_ini', 'e.emple_id', 'u.id', 'c.cargo_descripcion', 'a.area_descripcion',
            DB::raw('DATE(u.hora_ini) as diaM'),
            DB::raw('TIME(u.hora_ini) as horaM'),
            DB::raw('0 as horario'),
            DB::raw('0 as Falta'),
            DB::raw('0 as dia')
        )
        ->where('e.organi_id', session('sesionidorg'))
        ->whereDate(DB::raw('DATE(u.hora_ini)'), '>=',$fechaInicio)
        ->whereDate(DB::raw('DATE(u.hora_ini)'), '<=',$fechaFin)
        ->orderBy('e.emple_id')
        ->get();

        // HORARIOS
        $horarios = DB::table('horario_empleado as he')
        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
        ->leftjoin('empleado as e', 'e.emple_id', '=', 'he.empleado_emple_id')
        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
        ->select('ho.horario_id', 'ho.horario_tolerancia', 'ho.horaI as horaH', 'ho.horaF', 'ho.horario_toleranciaF', 'e.emple_id', 'ho.horario_descripcion', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 
            DB::raw('DATE(hd.start) as diaH'),
            DB::raw('0 as HF'),
            DB::raw('0 as HI'),
            DB::raw('DATE(hd.start) as DP'),
            DB::raw('0 as horaM'),
            DB::raw('0 as diaM'),
            DB::raw('0 as marcacion'),
            DB::raw('0 as ver')
        )
        ->where('he.estado', '=', 1)
        ->where('ho.organi_id', '=', session('sesionidorg'))
        ->whereDate(DB::raw('DATE(hd.start)'), '>=',$fechaHorarioI)
        ->whereDate(DB::raw('DATE(hd.start)'), '<=',$fechaHorarioF)
        ->groupBy('hd.start', 'he.empleado_emple_id', 'ho.horaI')
        ->orderBy('he.empleado_emple_id')
        ->get();

        foreach ($horarios as $horario) {
            $horaEntrada = Carbon::parse($horario->horaH);
            $horaSalida = Carbon::parse($horario->horaF);
            $temp = Carbon::parse($horario->DP)->addDays(1);
            if($horaEntrada > $horaSalida){
                $horario->HF = "23:59:59";
                $horario->HI = "00:00:00";
                $horario->DP = $temp->year."-".$temp->month."-".$temp->day;
            }
        }

        /*      RECORREMOS CADA HORARIO        */
        foreach ($horarios as $horario) {
            $dia_horario = Carbon::parse($horario->diaH); // DÍA: 15
            $dia_horario2 = Carbon::parse($horario->DP); // SUMA UN DÍA AL HORARIO DÍA -> DÍA: 16
            $horario_entrada = Carbon::parse($horario->horaH)->subMinutes($horario->horario_tolerancia);
            $horario_salida = Carbon::parse($horario->horaF)->addMinutes($horario->horario_toleranciaF);

            foreach ($capturas as $captura) {
                $dia_marcacion = Carbon::parse($captura->diaM);
                $hora_marcacion = Carbon::parse($captura->horaM);
                // VERIFICAMOS QUE EL HORARIO LE PERTENEZCA AL EMPLEADO DE LA CAPTURA
                if($horario->emple_id == $captura->emple_id){
                    // HORARIO SAYAYIN
                    if($horario_entrada > $horario_salida){
                        $HF_temp = Carbon::parse($horario->HF);
                        $HI_temp = Carbon::parse($horario->HI);
                        // DIA DE MARCACIÓN ES IGUAL AL DIA DEL HORARIO
                        if($dia_marcacion->equalTo($dia_horario)){
                            // PERTENECE A LA PRIMERA MITAD DEL HORARIO ANTES DE FINALIZAR EL PRIMER DÍA (NORMAL)
                            if($hora_marcacion->greaterThanOrEqualTo($horario_entrada) && $hora_marcacion->lessThanOrEqualTo($HF_temp)){
                                if($captura->horario === 0){
                                    $captura->horario = $horario->horario_id;
                                    $captura->dia = $horario->diaH;
                                } else {
                                    $horario_temp = Carbon::parse($captura->horario);
                                    $diff1 = $horario_entrada->diffInSeconds($hora_marcacion); // NUEVO HORARIO
                                    $diff2 = $horario_temp->diffInSeconds($hora_marcacion); 
                                    if($diff1 < $diff2){
                                        $captura->horario = $horario->horario_id;
                                        $captura->dia = $horario->diaH;
                                    }
                                }
                                //$captura->Falta = $captura->Falta."|".$horario->horaH."|";
                            }
                        } else {
                            // DIA DE LA MARCACIÓN ES IGUAL A UN DÍA DESPUÉS DEL HORARIO
                            if($dia_marcacion->equalTo($dia_horario2)){
                                // PERTENECER A LA SEGUNDA MITAD DEL HORARIO, LO RESTANTE DEL SIGUIENTE DÍA (pendiente)
                                if($hora_marcacion->greaterThanOrEqualTo($HI_temp) && $hora_marcacion->lessThanOrEqualTo($horario_salida)){
                                    $captura->horario = $horario->horario_id;
                                    $captura->dia = $horario->diaH;
                                    //$captura->Falta = $captura->Falta."/".$horario->horaH."/";
                                }
                            }
                        }
                        //$captura->horario = $captura->horario."|".$horario->diaH."|";
                    } else {
                        // HORARIO NORMAL
                        if($dia_marcacion->equalTo($dia_horario)){
                            if ($hora_marcacion->greaterThanOrEqualTo($horario_entrada) && $horario_salida->greaterThanOrEqualTo($hora_marcacion)) {
                                if($captura->horario === 0){
                                    $captura->horario = $horario->horario_id;
                                    $captura->dia = $horario->diaH;
                                } else {
                                    $horario_temp = Carbon::parse($captura->horario);
                                    $diff1 = $horario_entrada->diffInSeconds($hora_marcacion); 
                                    $diff2 = $horario_temp->diffInSeconds($hora_marcacion); // NUEVO HORARIO
                                    if($diff1 < $diff2){
                                        $captura->horario = $horario->horario_id;
                                        $captura->dia = $horario->diaH;
                                    }
                                }
                            }
                            //$captura->Falta = $captura->Falta."#".$horario->horaH."#";
                        }
                        //$captura->horario = $captura->horario."/".$horario->diaH."/";
                    }
                    if($captura->horario != "0"){
                        $hora_horario_new = Carbon::parse($captura->horario);
                        if($hora_horario_new < $hora_marcacion){
                            $captura->Falta = "TARDANZA";
                        }
                    }
                }
            }
        }

        //$diff = $capturas->diffAssoc(['horario' => 0]);

        //dd($capturas);

        $capturas = $capturas->values();

        // HORARIOS TIENE TODOS LOS HORARIOS QUE TIENE UN EMPLEADO EN UN DÍA
        foreach($empleados as $empleado){
            foreach($horarios as $horario){
                if($empleado->emple_id == $horario->emple_id){
                    foreach ($capturas as $captura) {
                        // MISMO EMPLEADO, MISMO HORARIO Y MISMO DIA DE HORARIO
                        if($captura->emple_id == $horario->emple_id && $captura->horario == $horario->horario_id && $horario->diaH == $captura->diaM){
                            if($horario->horaM == 0){
                                $horario->horaM = $captura->horaM;
                                $horario->diaM = $captura->diaM;
                                $horario->marcacion = $captura->hora_ini;
                            } else {
                                $marca_temp = Carbon::parse($horario->horaM);
                                $marca_new = Carbon::parse($captura->horaM);
                                if($marca_temp > $marca_new){
                                    $horario->horaM = $captura->horaM;
                                    $horario->diaM = $captura->diaM;
                                    $horario->marcacion = $captura->hora_ini;
                                }
                            }
                        }
                    } 
                }
            }
        }

        $collection = new Collection;

        foreach($horarios as $horario){
            if($horario->horaM != 0){
                $collection->push($horario);
            }
        }

        return $collection;
    }

    public function cargarReporteTardanzas(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $empleadoL = $request->idemp;

        /*      OBTENEMOS TODOS LOS EMPLEADOS SEGÚN EL USUARIO (ADMIN O INVITADO)        */
        if ($empleadoL == 0) {
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();
            }
        } else {
            // EMPLEADO > 0
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id' )
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->orderBy('e.emple_id')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->orderBy('e.emple_id')
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->get();
            }
        }
        /*      FIN DE OBETENER EMPLEADOS    */
        
        /* RANGO DE FECHA */
        $emple_id = $request->idemp;
        $fecha1 = $request->fecha1;
        $fechaR = Carbon::create($fecha1);
        $fecha2 = $request->fecha2;
        $fechaF = Carbon::create($fecha2);

        $fechaTempoInicio = Carbon::create($fecha1)->subDays(1);
        $fechaTempI = $fechaTempoInicio->year."-".$fechaTempoInicio->month."-".$fechaTempoInicio->day;
        $fechaTempoFin = Carbon::create($fecha2)->addDays(1);
        $fechaTempF = $fechaTempoFin->year."-".$fechaTempoFin->month."-".$fechaTempoFin->day;

        $empleados1 = $this->getTardanzas($empleados, $fechaTempI, $fechaTempF, $fecha1, $fecha2);
        $empleados = $empleados1->sortBy('emple_id')->values();

        /* COLECCIÓN QUE SE ENVIA AL JS */
        $datos = new Collection();
        /* VARIABLES PARA LA COMPARACIÓN */
        $cantTardanzas = 0;
        $tiempoTardanza = 0;
        $len = count($empleados);
        $i = 0;
        $employee = 0;

        //dd($empleados);

        foreach($empleados as $key => $empleado){
            //if($empleado->marcacion != 0){
                $marcacion = Carbon::parse($empleado->marcacion);
                $diaHorario = Carbon::create($empleado->diaH);
                $horaHorario = Carbon::parse($empleado->horaH);
                $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
                $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado->horario_tolerancia);
                /*  CAPTURA DENTRO DEL RANGO DE FECHAS  */
                if($fechaF->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR) && $empleado->marcacion != 0){
                    if($i == 0){
                        //$datos->push("-------------1-------------");
                        //$datos->push($empleado->emple_id);
                        $employee = $empleado->emple_id;
                    }

                    if($employee != $empleado->emple_id && $cantTardanzas > 0 ){
                        //$datos->push("-------------2-------------");
                        //$datos->push($empleado->emple_id);
                        $datos->push($obj);
                        $cantTardanzas = 0;
                        $tiempoTardanza = 0;
                        $employee = $empleado->emple_id;
                    }
                    /*  COMPRUEBA SI HAY TARDANZA    */
                    if ($marcacion->greaterThan($horario_tolerancia) == TRUE) {
                        //$datos->push("-------------3-------------");
                        //$datos->push($empleado->emple_id);
                        $diffS = $marcacion->DiffInSeconds($horario);
                        $tiempoTardanza += $diffS;
                        $cantTardanzas += 1;              

                        $obj = (object) array(
                            'area_descripcion' => strlen($empleado->area) > 0 ? $empleado->area : "--",
                            'cargo_descripcion' => strlen($empleado->cargo) > 0 ? $empleado->cargo : "--",
                            'emple_id' => $empleado->emple_id, //*
                            'emple_code' => strlen($empleado->codigo) > 0 ? $empleado->codigo : $empleado->documento,
                            'emple_nDoc' => $empleado->documento,
                            'entradaModif' => $empleado->marcacion,//*
                            'horario' => $empleado->horario_descripcion,//*
                            'idhorario' => $empleado->horario_id,//*
                            'cantTardanzas' => $cantTardanzas,
                            'tiempoTardanzas' => gmdate('H:i:s', $tiempoTardanza),
                            'organi_id' => session('sesionidorg'),
                            'organi_razonSocial' => $usuario_organizacion->razonSocial,
                            'organi_direccion' => $usuario_organizacion->direccion,
                            'organi_ruc' => $usuario_organizacion->ruc,
                            'fecha' => now()->format('d-m-Y H:i:s'),
                            'fechaD' => $fecha1,
                            'fechaH' => $fecha2,
                            'perso_apMaterno' => $empleado->apMaterno,
                            'perso_apPaterno' => $empleado->apPaterno,
                            'perso_nombre' => $empleado->nombre
                        );
                    }

                    $i++;
                }

                if($key == $len - 1 && $cantTardanzas > 0){
                    //$datos->push("-------------4-------------");
                    //$datos->push($empleado->emple_id);
                    $datos->push($obj);
                    $cantTardanzas = 0;
                    $tiempoTardanza = 0;
                } 
           // }
        }

        //dd($datos);

        return response()->json($datos, 200);
    }

    // MATRIZ
    public function mostrarMatrizTardanzas()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
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

                        return view('tardanzas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('tardanzas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                }
            } else {
                return view('tardanzas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
            }
        }
    }

    public function cargarMatrizTardanzas(Request $request)
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
                    ->select('e.emple_id')
                    ->where('e.emple_estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->get();
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
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                    } else {
                        if ($selector == "Área") {
                            $empleados = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                        } else {
                            if ($selector == "Local") {
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
                        ->select('e.emple_id')
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();
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
                    
                }
            }
        }

        $date1 = new DateTime($fechaF[0]);
        $date2 = new DateTime($fechaF[1]);

        $fechaTempoInicio = Carbon::create($fechaF[0])->subDays(1);
        $fechaTempI = $fechaTempoInicio->year."-".$fechaTempoInicio->month."-".$fechaTempoInicio->day;
        $fechaTempoFin = Carbon::create($fechaF[1])->addDays(1);
        $fechaTempF = $fechaTempoFin->year."-".$fechaTempoFin->month."-".$fechaTempoFin->day;

        $empleados = $this->getTardanzas($empleados, $fechaTempI, $fechaTempF, $date1, $date2);
        $empleados = $empleados->sortBy('emple_id')->values();
        
        $datos = new Collection();

        if (isset($empleados)) {
            $sql = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)), DAY(DATE(cp.hora_ini)) ), if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            // DB::enableQueryLog();

            $date1 = new DateTime($fechaF[0]);
            $date2 = new DateTime($fechaF[1]);
            $diff = $date1->diff($date2);
            //Array
            $horas = array();
            $dias = array();

            for ($i = 0; $i <= $diff->days; $i++) {
                array_push($horas, 0);
                $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                array_push($dias, date('Y-m-j', $dia));
            }
            $contEmpleados = 0;
            $sumTardanza = 0;
            $tiempoTardanza = 0;
            $employee = "";
            $len = $empleados->count();
            $i = 0;
            $fechaF1 = Carbon::parse($date2);
            $fechaR2 = Carbon::parse($date1);

            foreach ($empleados as $empleado) {
                $marcacion = Carbon::parse($empleado->marcacion);
                $diaHorario = Carbon::create($empleado->diaH);
                $horaHorario = Carbon::parse($empleado->horaH);
                $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
                $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado->horario_tolerancia);
                if($fechaF1->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR2) && $empleado->marcacion != 0){
                    if ($i == 0) {
                        $employee = $empleado->emple_id;
                    }

                    if($employee != $empleado->emple_id && $sumTardanza > 0){
                        $employee = $empleado->emple_id;
                        $datos->push($obj);
                        $sumTardanza = 0;
                        $tiempoTardanza = 0;
                        for ($i = 0; $i <= $diff->days; $i++) {
                            $horas[$i] = 0;
                        }
                    }

                    if ($marcacion->greaterThan($horario_tolerancia) == TRUE){
                        $diffS = $marcacion->DiffInSeconds($horario);
                        $tiempoTardanza += $diffS;
                        $sumTardanza += 1;
                        $horas[$diaHorario->day-1] += 1;
                    }

                    $obj = (object) array(
                        "emple_id" => $empleado->emple_id, 
                        "nombre" => $empleado->nombre, 
                        "apPaterno" => $empleado->apPaterno,
                        "apMaterno" => $empleado->apMaterno, 
                        "horas" => $horas, 
                        "fechaF" => $dias, 
                        "totalTardanza" => gmdate('H:i:s', $tiempoTardanza),
                        "cantidadTardanza" => $sumTardanza,
                        "ruc" => $usuario_organizacion->ruc, 
                        "razonSocial" => $usuario_organizacion->razonSocial, 
                        "direccion" => $usuario_organizacion->direccion,
                        "codigo" => strlen($empleado->codigo) > 0 ? $empleado->codigo : $empleado->documento, 
                        "documento" => $empleado->documento, 
                        "fecha" => now()->format('d-m-Y H:i:s'), 
                        "fechaD" => $fechaF[0], 
                        "fechaH" => $fechaF[1]
                    );
                    $i++;
                }
                if($contEmpleados == $len - 1 && $sumTardanza > 0){
                    $datos->push($obj);
                    $sumTardanza = 0;
                    $tiempoTardanza = 0;
                    for ($i = 0; $i <= $diff->days; $i++) {
                        $horas[$i] = 0;
                    }
                }
                $contEmpleados++;
            }
        }

        return response()->json($datos, 200);
    }

    public function selectMatrizTardanzas(Request $request)
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

    ///////////////////////////////////////////// CONTROL EN RUTA //////////////////////////////////////////////////
    public function mostrarReporteTardanzaRuta()
    {
        if(session('sesionidorg')==NULL){
            return redirect('/');
        }

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

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
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }

        return view('tardanzas.reporteTardanzasRuta', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
    }

    public function cargarReporteTardanzasRuta(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $empleadoL = $request->idemp;

        /*      OBTENEMOS TODOS LOS EMPLEADOS SEGÚN EL USUARIO (ADMIN O INVITADO)        */
        if ($empleadoL == 0) {
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados_ubi = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();

                    $empleados = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados_ubi = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->get();

                        $empleados_cap = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->get();
                    } else {
                        $empleados_ubi = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();

                        $empleados_cap = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados_ubi = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();

                $empleados_cap = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();
            }
        } else {
            // EMPLEADO > 0
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados_ubi = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->get();

                        $empleados_cap = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->get();
                    } else {
                        $empleados_ubi = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();

                        $empleados_cap = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados_ubi = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();

                $empleados_cap = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();
            }
        }
        /*      FIN DE OBETENER EMPLEADOS    */

        /* RANGO DE FECHA */
        $emple_id = $request->idemp;
        $fecha1 = $request->fecha1;
        $fechaR = Carbon::create($fecha1);
        $fecha2 = $request->fecha2;
        $fechaF = Carbon::create($fecha2);

        $fechaTempoInicio = Carbon::create($fecha1)->subDays(1);
        $fechaTempI = $fechaTempoInicio->year."-".$fechaTempoInicio->month."-".$fechaTempoInicio->day;
        $fechaTempoFin = Carbon::create($fecha2)->addDays(1);
        $fechaTempF = $fechaTempoFin->year."-".$fechaTempoFin->month."-".$fechaTempoFin->day;

        $empleados_ubi = $this->getTardanzasUbi($empleados_ubi, $fechaTempI, $fechaTempF, $fecha1, $fecha2);
        $empleados_cap = $this->getTardanzas($empleados_cap, $fechaTempI, $fechaTempF, $fecha1, $fecha2);

        $empleados = new Collection();
        foreach($empleados_cap as $empleado_cap){
           foreach($empleados_ubi as $empleado_ubi){
                if($empleado_cap->emple_id == $empleado_ubi->emple_id && $empleado_cap->horario_descripcion == $empleado_ubi->horario_descripcion && $empleado_cap->diaH == $empleado_ubi->diaH){
                    if($empleado_cap->horaM < $empleado_ubi->horaM){
                        $empleados->push($empleado_cap);
                    } else {
                        $empleados->push($empleado_ubi);
                    }
                    $empleado_ubi->ver = 1;
                    $empleado_cap->ver = 1;
                }
            }
        }

        foreach($empleados_cap as $empleado_cap){
            if($empleado_cap->ver == 0){
                $empleados->push($empleado_cap);
            }
        }

        foreach($empleados_ubi as $empleado_ubi){
            if($empleado_ubi->ver == 0){
                $empleados->push($empleado_ubi);
            }
        }

        $empleados = $empleados->sortBy('emple_id');
        $empleados->values()->all();

        //dd($empleados);

        /* COLECCIÓN QUE SE ENVIA AL JS */
        $datos = new Collection();
        /* VARIABLES PARA LA COMPARACIÓN */
        $cantTardanzas = 0;
        $tiempoTardanza = 0;
        $len = $empleados->count();
        $i = 0;
        $employee = 0;
        $contEmpleados = 0;

        foreach($empleados as $empleado){
            $marcacion = Carbon::parse($empleado->marcacion);
            $diaHorario = Carbon::create($empleado->diaH);
            $horaHorario = Carbon::parse($empleado->horaH);
            $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
            $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado->horario_tolerancia);
            /*  CAPTURA DENTRO DEL RANGO DE FECHAS  */
            if($fechaF->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR)){
                if($i == 0){
                    //$datos->push("-------------1-------------");
                    $employee = $empleado->emple_id;
                }

                if($employee != $empleado->emple_id && $cantTardanzas > 0){
                    //$datos->push("-------------2-------------");
                    $datos->push($obj);
                    $cantTardanzas = 0;
                    $tiempoTardanza = 0;
                    $employee = $empleado->emple_id;
                }
                /*  COMPRUEBA SI HAY TARDANZA    */
                if ($marcacion->greaterThan($horario_tolerancia) == TRUE) {
                    //$datos->push("-------------3-------------");
                    $diffS = $marcacion->DiffInSeconds($horario);
                    $tiempoTardanza += $diffS;
                    $cantTardanzas += 1;
                    $area = strlen($empleado->area) > 0 ? $empleado->area : "--";
                    $cargo = strlen($empleado->cargo) > 0 ? $empleado->cargo : "--";
                    $codigo = strlen($empleado->codigo) > 0 ? $empleado->codigo : $empleado->documento;
                    $documento = $empleado->documento;
                    $marcacion = $empleado->marcacion;
                    $horario_descripcion = $empleado->horario_descripcion;
                    $horario_id = $empleado->horario_id;
                    $emple_id = $empleado->emple_id;

                    $obj = (object) array(
                        'area_descripcion' => $area,
                        'cargo_descripcion' => $cargo,
                        'emple_id' => $emple_id,
                        'emple_code' => $codigo,
                        'emple_nDoc' => $documento,
                        'entradaModif' => $marcacion,
                        'horario' => $horario_descripcion,
                        'idhorario' => $horario_id,
                        'cantTardanzas' => $cantTardanzas,
                        'tiempoTardanzas' => gmdate('H:i:s', $tiempoTardanza),
                        'organi_id' => session('sesionidorg'),
                        'organi_razonSocial' => $usuario_organizacion->razonSocial,
                        'organi_direccion' => $usuario_organizacion->direccion,
                        'organi_ruc' => $usuario_organizacion->ruc,
                        'fecha' => now()->format('d-m-Y H:i:s'),
                        'fechaD' => $fecha1,
                        'fechaH' => $fecha2,
                        'perso_apMaterno' => $empleado->apMaterno,
                        'perso_apPaterno' => $empleado->apPaterno,
                        'perso_nombre' => $empleado->nombre
                    );
                }

                $i++;
            }

            if($contEmpleados == $len - 1 && $cantTardanzas > 0){
                //$datos->push("-------------4-------------");
                $datos->push($obj);
                $cantTardanzas = 0;
                $tiempoTardanza = 0;
            }
            $contEmpleados++;
        }

        return response()->json($datos, 200);
    }
    // MATRIZ
    public function mostrarMatrizTardanzasRuta()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
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

                        return view('tardanzas.reporteMatrizTardanzasRuta', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('tardanzas.reporteMatrizTardanzasRuta', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                }
            } else {
                return view('tardanzas.reporteMatrizTardanzasRuta', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
            }
        }
    }

    public function cargarMatrizTardanzasRuta(Request $request)
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
                    $empleados_cap = DB::table('empleado as e')
                        ->select('e.emple_id')
                        ->where('e.emple_estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();

                    $empleados_ubi = DB::table('empleado as e')
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
                        $empleados_ubi = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();

                        $empleados_cap = DB::table('empleado as e')
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
                        $empleados_ubi = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->get();

                        $empleados_cap = DB::table('empleado as e')
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
                $empleados_ubi = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->where('e.emple_estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->get();

                $empleados_cap = DB::table('empleado as e')
                    ->select('e.emple_id')
                    ->where('e.emple_estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->get();
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
                            $empleados_ubi = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();

                            $empleados_cap = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                        } else {
                            if($selector == "Cargo"){
                                $empleados_ubi = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();

                            $empleados_cap = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados_ubi = DB::table('empleado as e')
                                        ->select('e.emple_id')
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->get();

                                    $empleados_cap = DB::table('empleado as e')
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
                                $empleados_ubi = DB::table('empleado as e')
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

                                $empleados_cap = DB::table('empleado as e')
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
                                    $empleados_ubi = DB::table('empleado as e')
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

                                $empleados_cap = DB::table('empleado as e')
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
                                        $empleados_ubi = DB::table('empleado as e')
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

                                        $empleados_cap = DB::table('empleado as e')
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
                                $empleados_ubi = DB::table('empleado as e')
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

                                $empleados_cap = DB::table('empleado as e')
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
                                    $empleados_ubi = DB::table('empleado as e')
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

                                    $empleados_cap = DB::table('empleado as e')
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
                                        $empleados_ubi = DB::table('empleado as e')
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

                                        $empleados_cap = DB::table('empleado as e')
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
                        $empleados_ubi = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();

                        $empleados_cap = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                    } else {
                        if ($selector == "Área") {
                            $empleados_ubi = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();

                            $empleados_cap = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                        } else {
                            if ($selector == "Local") {
                                $empleados_ubi = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_local', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();

                                $empleados_cap = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_local', $area)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
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
                        $empleados_ubi = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();

                        $empleados_cap = DB::table('empleado as e')
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
                            $empleados_ubi = DB::table('empleado as e')
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

                            $empleados_cap = DB::table('empleado as e')
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
                            $empleados_ubi = DB::table('empleado as e')
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

                            $empleados_cap = DB::table('empleado as e')
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
                    $empleados_ubi = DB::table('empleado as e')
                        ->select('e.emple_id')
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();

                    $empleados_cap = DB::table('empleado as e')
                        ->select('e.emple_id')
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();
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
                            $empleados_ubi = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();

                            $empleados_cap = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                        } else {
                            if($selector == "Cargo"){
                                $empleados_ubi = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();

                                $empleados_cap = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados_ubi = DB::table('empleado as e')
                                    ->select('e.emple_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_local', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->get();

                                    $empleados_cap = DB::table('empleado as e')
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
                                $empleados_ubi = DB::table('empleado as e')
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

                                $empleados_cap = DB::table('empleado as e')
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
                                    $empleados_ubi = DB::table('empleado as e')
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

                                    $empleados_cap = DB::table('empleado as e')
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
                                        $empleados_ubi = DB::table('empleado as e')
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

                                        $empleados_cap = DB::table('empleado as e')
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
                                $empleados_ubi = DB::table('empleado as e')
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

                                $empleados_cap = DB::table('empleado as e')
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
                                    $empleados_ubi = DB::table('empleado as e')
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

                                    $empleados_cap = DB::table('empleado as e')
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
                                        $empleados_ubi = DB::table('empleado as e')
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

                                        $empleados_cap = DB::table('empleado as e')
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
                        $empleados_ubi = DB::table('empleado as e')
                        ->select('e.emple_id')
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();

                        $empleados_cap = DB::table('empleado as e')
                        ->select('e.emple_id')
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();

                    } else {
                        if($selector == "Cargo"){
                            $empleados_ubi = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();

                            $empleados_cap = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                        } else {
                            if($selector == "Local"){
                                $empleados_ubi = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_local', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->get();

                                $empleados_cap = DB::table('empleado as e')
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
                    
                }
            }
        }

        $date1 = new DateTime($fechaF[0]);
        $date2 = new DateTime($fechaF[1]);

        $fechaTempoInicio = Carbon::create($fechaF[0])->subDays(1);
        $fechaTempI = $fechaTempoInicio->year."-".$fechaTempoInicio->month."-".$fechaTempoInicio->day;
        $fechaTempoFin = Carbon::create($fechaF[1])->addDays(1);
        $fechaTempF = $fechaTempoFin->year."-".$fechaTempoFin->month."-".$fechaTempoFin->day;

        $empleados_cap = $this->getTardanzas($empleados_cap, $fechaTempI, $fechaTempF, $date1, $date2);
        $empleados_ubi = $this->getTardanzasUbi($empleados_ubi, $fechaTempI, $fechaTempF, $date1, $date2);
        $datos = new Collection();

        $empleados = new Collection();
        foreach($empleados_cap as $empleado_cap){
           foreach($empleados_ubi as $empleado_ubi){
                if($empleado_cap->emple_id == $empleado_ubi->emple_id && $empleado_cap->horario_descripcion == $empleado_ubi->horario_descripcion && $empleado_cap->diaH == $empleado_ubi->diaH){
                    if($empleado_cap->horaM < $empleado_ubi->horaM){
                        $empleados->push($empleado_cap);
                    } else {
                        $empleados->push($empleado_ubi);
                    }
                    $empleado_ubi->ver = 1;
                    $empleado_cap->ver = 1;
                }
            }
        }

        foreach($empleados_cap as $empleado_cap){
            if($empleado_cap->ver == 0){
                $empleados->push($empleado_cap);
            }
        }

        foreach($empleados_ubi as $empleado_ubi){
            if($empleado_ubi->ver == 0){
                $empleados->push($empleado_ubi);
            }
        }

        $empleados = $empleados->sortBy('emple_id');
        $empleados->values()->all();

        //dd($empleados);

        if (isset($empleados)) {
            $sql = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)), DAY(DATE(cp.hora_ini)) ), if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            // DB::enableQueryLog();

            $diff = $date1->diff($date2);
            //Array
            $horas = array();
            $dias = array();

            for ($i = 0; $i <= $diff->days; $i++) {
                array_push($horas, 0);
                $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                array_push($dias, date('Y-m-j', $dia));
            }
            $contEmpleados = 0;
            $sumTardanza = 0;
            $tiempoTardanza = 0;
            $employee = "";
            $len = $empleados->count();
            $i = 0;
            $fechaF1 = Carbon::parse($date2);
            $fechaR2 = Carbon::parse($date1);

            foreach ($empleados as $empleado) {
                $marcacion = Carbon::parse($empleado->marcacion);
                $diaHorario = Carbon::create($empleado->diaH);
                $horaHorario = Carbon::parse($empleado->horaH);
                $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
                $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado->horario_tolerancia);
                if($fechaF1->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR2)){
                    if ($i == 0) {
                        $employee = $empleado->emple_id;
                    }

                    if($employee != $empleado->emple_id && $sumTardanza > 0){
                        $employee = $empleado->emple_id;
                        $datos->push($obj);
                        $sumTardanza = 0;
                        $tiempoTardanza = 0;
                        for ($i = 0; $i <= $diff->days; $i++) {
                            $horas[$i] = 0;
                        }
                    }

                    if ($marcacion->greaterThan($horario_tolerancia) == TRUE){
                        $diffS = $marcacion->DiffInSeconds($horario);
                        $tiempoTardanza += $diffS;
                        $sumTardanza += 1;
                        $horas[$diaHorario->day-1] += 1;
                    }

                    $obj = (object) array(
                        "emple_id" => $empleado->emple_id, 
                        "nombre" => $empleado->nombre, 
                        "apPaterno" => $empleado->apPaterno,
                        "apMaterno" => $empleado->apMaterno, 
                        "horas" => $horas, 
                        "fechaF" => $dias, 
                        "totalTardanza" => gmdate('H:i:s', $tiempoTardanza),
                        "cantidadTardanza" => $sumTardanza,
                        "ruc" => $usuario_organizacion->ruc, 
                        "razonSocial" => $usuario_organizacion->razonSocial, 
                        "direccion" => $usuario_organizacion->direccion,
                        "codigo" => strlen($empleado->codigo) > 0 ? $empleado->codigo : $empleado->documento, 
                        "documento" => $empleado->documento, 
                        "fecha" => now()->format('d-m-Y H:i:s'), 
                        "fechaD" => $fechaF[0], 
                        "fechaH" => $fechaF[1]
                    );
                    $i++;
                }
                if($contEmpleados == $len - 1 && $sumTardanza > 0){
                    $datos->push($obj);
                    $sumTardanza = 0;
                    $tiempoTardanza = 0;
                    for ($i = 0; $i <= $diff->days; $i++) {
                        $horas[$i] = 0;
                    }
                }
                $contEmpleados++;
            }
        }

        return response()->json($datos, 200);
    }

    public function selectMatrizTardanzasRuta(Request $request)
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

    ///////////////////////////////////////////// CONTROL EN PUERTA //////////////////////////////////////////////////

    public function mostrarReporteTardanzaPuerta()
    {
        if(session('sesionidorg')==NULL){
            return redirect('/');
        }

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

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
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }

        return view('tardanzas.reporteTardanzasPuerta', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
    }

    public function cargarReporteTardanzasPuerta(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $empleadoL = $request->idemp;

        /*      OBTENEMOS TODOS LOS EMPLEADOS SEGÚN EL USUARIO (ADMIN O INVITADO)        */
        if ($empleadoL == 0) {
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $request->fecha1)
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $request->fecha2)
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                            ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->whereDate(DB::raw('DATE(hd.start)'), '>=', $request->fecha1)
                            ->whereDate(DB::raw('DATE(hd.start)'), '<=', $request->fecha2)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                            ->get();

                    } else {
                        $empleados = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->where('invi.estado', '=', 1)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $request->fecha1)
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $request->fecha2)
                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $request->fecha1)
                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $request->fecha2)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                    ->get();
            }
        } else {
            // EMPLEADO > 0
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->where('e.emple_id', '=', $empleadoL)
                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $request->fecha1)
                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $request->fecha2)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                            ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->whereDate(DB::raw('DATE(hd.start)'), '>=', $request->fecha1)
                            ->whereDate(DB::raw('DATE(hd.start)'), '<=', $request->fecha2)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                            ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->whereDate(DB::raw('DATE(hd.start)'), '>=', $request->fecha1)
                            ->whereDate(DB::raw('DATE(hd.start)'), '<=', $request->fecha2)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $request->fecha1)
                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $request->fecha2)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                    ->get();
            }
        }
        /*      FIN DE OBETENER EMPLEADOS    */
        
        //dd($empleados);

        /* RANGO DE FECHA */
        $emple_id = $request->idemp;
        $fecha1 = $request->fecha1;
        $fechaR = Carbon::create($fecha1);
        $fecha2 = $request->fecha2;
        $fechaF = Carbon::create($fecha2);

        /* COLECCIÓN QUE SE ENVIA AL JS */
        $datos = new Collection();
        /* VARIABLES PARA LA COMPARACIÓN */
        $cantTardanzas = 0;
        $tiempoTardanza = 0;
        $len = count($empleados);
        $i = 0;
        $employee = 0;

        //dd($empleados);

        foreach($empleados as $key => $empleado){
            //if($empleado->marcacion != 0){
                $marcacion = Carbon::parse($empleado->marcacion);
                $diaHorario = Carbon::create($empleado->diaH);
                $horaHorario = Carbon::parse($empleado->horaH);
                $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
                $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado->horario_tolerancia);
                /*  CAPTURA DENTRO DEL RANGO DE FECHAS  */
                if($fechaF->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR)){
                    if($i == 0){
                        //$datos->push("-------------1-------------");
                        //$datos->push($empleado->emple_id);
                        $employee = $empleado->emple_id;
                    }

                    if($employee != $empleado->emple_id && $cantTardanzas > 0 ){
                        //$datos->push("-------------2-------------");
                        //$datos->push($empleado->emple_id);
                        $datos->push($obj);
                        $cantTardanzas = 0;
                        $tiempoTardanza = 0;
                        $employee = $empleado->emple_id;
                    }
                    /*  COMPRUEBA SI HAY TARDANZA    */
                    if ($marcacion->greaterThan($horario_tolerancia) == TRUE) {
                        //$datos->push("-------------3-------------");
                        //$datos->push($empleado->emple_id);
                        $diffS = $marcacion->DiffInSeconds($horario);
                        $tiempoTardanza += $diffS;
                        $cantTardanzas += 1;              

                        $obj = (object) array(
                            'area_descripcion' => strlen($empleado->area) > 0 ? $empleado->area : "--",
                            'cargo_descripcion' => strlen($empleado->cargo) > 0 ? $empleado->cargo : "--",
                            'emple_id' => $empleado->emple_id, //*
                            'emple_code' => strlen($empleado->codigo) > 0 ? $empleado->codigo : $empleado->documento,
                            'emple_nDoc' => $empleado->documento,
                            'entradaModif' => $empleado->marcacion,//*
                            'horario' => $empleado->horario_descripcion,//*
                            'idhorario' => $empleado->horario_id,//*
                            'cantTardanzas' => $cantTardanzas,
                            'tiempoTardanzas' => gmdate('H:i:s', $tiempoTardanza),
                            'organi_id' => session('sesionidorg'),
                            'organi_razonSocial' => $usuario_organizacion->razonSocial,
                            'organi_direccion' => $usuario_organizacion->direccion,
                            'organi_ruc' => $usuario_organizacion->ruc,
                            'fecha' => now()->format('d-m-Y H:i:s'),
                            'fechaD' => $fecha1,
                            'fechaH' => $fecha2,
                            'perso_apMaterno' => $empleado->apMaterno,
                            'perso_apPaterno' => $empleado->apPaterno,
                            'perso_nombre' => $empleado->nombre
                        );
                    }

                    $i++;
                }

                if($key == $len - 1 && $cantTardanzas > 0){
                    //$datos->push("-------------4-------------");
                    //$datos->push($empleado->emple_id);
                    $datos->push($obj);
                    $cantTardanzas = 0;
                    $tiempoTardanza = 0;
                } 
           // }
        }

        //dd($datos);

        return response()->json($datos, 200);
    }

    // MATRIZ
    public function mostrarMatrizTardanzasPuerta()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
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

                        return view('tardanzas.reporteMatrizTardanzasPuerta', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('tardanzas.reporteMatrizTardanzasPuerta', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                }
            } else {
                return view('tardanzas.reporteMatrizTardanzasPuerta', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
            }
        }
    }

    public function cargarMatrizTardanzasPuerta(Request $request)
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
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();

                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();

                    } else {
                        $empleados = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->where('invi.estado', '=', 1)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();
                    }
                }
            } else {
                $empleados = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                    ->get();
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
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                >whereIn('e.emple_area', $area)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();
                        } else {
                            if($selector == "Cargo"){
                            $empleados = DB::table('empleado as e')
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados = DB::table('empleado as e')
                                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                        )
                                        ->where('he.estado', '=', 1)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
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
                                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                    )
                                    ->where('he.estado', '=', 1)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereIn('e.emple_area', $area)
                                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                    ->get();

                            } else {
                                if($selector == "Cargo"){
                                $empleados = DB::table('empleado as e')
                                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                    )
                                    ->where('he.estado', '=', 1)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                    ->get();

                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                            ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                            ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                            ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                            ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                                DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                                DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                                DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                            )
                                            ->where('he.estado', '=', 1)
                                            ->orderBy('e.emple_id')
                                            ->where('e.organi_id', '=', session('sesionidorg'))
                                            ->where('e.emple_estado', '=', 1)
                                            ->where('invi.estado', '=', 1)
                                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                            ->whereIn('e.emple_local', $area)
                                            ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                            ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                            ->get();
                                    }
                                }
                            }
                        } else {
                            if($selector == "Área"){
                            $empleados = DB::table('empleado as e')
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_area', $area)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();


                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                    )
                                    ->where('he.estado', '=', 1)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                    ->get();

                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                        )
                                        ->where('he.estado', '=', 1)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->where('e.emple_estado', '=', 1)
                                        ->where('invi.estado', '=', 1)
                                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                        ->whereIn('e.emple_local', $area)
                                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                        ->get();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ($selector == "Cargo") {
                        $empleados = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_cargo', $area)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();
                    } else {
                        if ($selector == "Área") {
                            $empleados = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                            ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                            ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                            ->get();
                        } else {
                            if ($selector == "Local") {
                                $empleados = DB::table('empleado as e')
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_local', $area)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();
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
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();

                    } else {
                        $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                            ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                            ->where('invem.area_id', '=', null)
                            ->where('invem.emple_id', '!=', null)
                            ->get()->first();
                        if ($invitado_empleadoIn != null) {
                            $empleados = DB::table('empleado as e')
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();
                        } else {
                            $empleados = DB::table('empleado as e')
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();
                        }
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();
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
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                            ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                            ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                            ->get();
                        } else {
                            if($selector == "Cargo"){
                                $empleados = DB::table('empleado as e')
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                    )
                                    ->where('he.estado', '=', 1)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_local', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
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
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();
                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                    )
                                    ->where('he.estado', '=', 1)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                    ->get();

                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                        )
                                        ->where('he.estado', '=', 1)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->whereIn('e.emple_id', $empleadoL)
                                        ->where('invi.estado', '=', 1)
                                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                        ->get();

                                    }
                                }
                            }
                        } else {
                            if($selector == "Área"){
                                $empleados = DB::table('empleado as e')
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->where('invi.estado', '=', 1)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();

                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                    ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                    ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                        DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                        DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                        DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                    )
                                    ->where('he.estado', '=', 1)
                                    ->orderBy('e.emple_id')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                    ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                    ->get();

                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                        )
                                        ->where('he.estado', '=', 1)
                                        ->orderBy('e.emple_id')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->whereIn('e.emple_id', $empleadoL)
                                        ->where('invi.estado', '=', 1)
                                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                        ->get();

                                    }
                                }
                            }
                        }
                    }
                } else {
                    if($selector == "Área"){
                        $empleados = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                        ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                        ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                        ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                            DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                            DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                            DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                        )
                        ->where('he.estado', '=', 1)
                        ->orderBy('e.emple_id')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                        ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                        ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                        ->get();

                    } else {
                        if($selector == "Cargo"){
                            $empleados = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                            ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                            ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                            ->get();

                        } else {
                            if($selector == "Local"){
                                $empleados = DB::table('empleado as e')
                                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                                ->join('horario_empleado as he', 'he.horarioEmp_id', '=', 'mp.horarioEmp_id')
                                ->join('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                                ->select('e.emple_id', 'e.emple_codigo as codigo', 'e.emple_nDoc as documento', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'ho.horaI as horaH', 'hd.start as diaH', 'c.cargo_descripcion as cargo', 'a.area_descripcion as area', 'ho.horario_tolerancia', 'ho.horario_descripcion', 'ho.horario_id', 'mp.marcaMov_fecha as marcacion',
                                    DB::raw('DATE(mp.marcaMov_fecha) as diaM'),
                                    DB::raw('TIME(mp.marcaMov_fecha) as horaM'),
                                    DB::raw('MIN(TIME(mp.marcaMov_fecha)) as minMarcacion')
                                )
                                ->where('he.estado', '=', 1)
                                ->orderBy('e.emple_id')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_local', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->whereDate(DB::raw('DATE(hd.start)'), '>=', $fechaF[0])
                                ->whereDate(DB::raw('DATE(hd.start)'), '<=', $fechaF[1])
                                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'ho.horario_id')
                                ->get();
                            }
                        }
                    }
                    
                }
            }
        }
        
        $datos = new Collection();

        if (isset($empleados)) {
            $sql = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)), DAY(DATE(cp.hora_ini)) ), if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            // DB::enableQueryLog();

            $date1 = new DateTime($fechaF[0]);
            $date2 = new DateTime($fechaF[1]);
            $diff = $date1->diff($date2);
            //Array
            $horas = array();
            $dias = array();

            for ($i = 0; $i <= $diff->days; $i++) {
                array_push($horas, 0);
                $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                array_push($dias, date('Y-m-j', $dia));
            }
            $contEmpleados = 0;
            $sumTardanza = 0;
            $tiempoTardanza = 0;
            $employee = "";
            $len = $empleados->count();
            $i = 0;
            $fechaF1 = Carbon::parse($date2);
            $fechaR2 = Carbon::parse($date1);

            foreach ($empleados as $empleado) {
                $marcacion = Carbon::parse($empleado->marcacion);
                $diaHorario = Carbon::create($empleado->diaH);
                $horaHorario = Carbon::parse($empleado->horaH);
                $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
                $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado->horario_tolerancia);
                if($fechaF1->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR2) && $empleado->marcacion != 0){
                    if ($i == 0) {
                        $employee = $empleado->emple_id;
                    }

                    if($employee != $empleado->emple_id && $sumTardanza > 0){
                        $employee = $empleado->emple_id;
                        $datos->push($obj);
                        $sumTardanza = 0;
                        $tiempoTardanza = 0;
                        for ($i = 0; $i <= $diff->days; $i++) {
                            $horas[$i] = 0;
                        }
                    }

                    if ($marcacion->greaterThan($horario_tolerancia) == TRUE){
                        $diffS = $marcacion->DiffInSeconds($horario);
                        $tiempoTardanza += $diffS;
                        $sumTardanza += 1;
                        $horas[$diaHorario->day-1] += 1;
                    }

                    $obj = (object) array(
                        "emple_id" => $empleado->emple_id, 
                        "nombre" => $empleado->nombre, 
                        "apPaterno" => $empleado->apPaterno,
                        "apMaterno" => $empleado->apMaterno, 
                        "horas" => $horas, 
                        "fechaF" => $dias, 
                        "totalTardanza" => gmdate('H:i:s', $tiempoTardanza),
                        "cantidadTardanza" => $sumTardanza,
                        "ruc" => $usuario_organizacion->ruc, 
                        "razonSocial" => $usuario_organizacion->razonSocial, 
                        "direccion" => $usuario_organizacion->direccion,
                        "codigo" => strlen($empleado->codigo) > 0 ? $empleado->codigo : $empleado->documento, 
                        "documento" => $empleado->documento, 
                        "fecha" => now()->format('d-m-Y H:i:s'), 
                        "fechaD" => $fechaF[0], 
                        "fechaH" => $fechaF[1]
                    );
                    $i++;
                }
                if($contEmpleados == $len - 1 && $sumTardanza > 0){
                    $datos->push($obj);
                    $sumTardanza = 0;
                    $tiempoTardanza = 0;
                    for ($i = 0; $i <= $diff->days; $i++) {
                        $horas[$i] = 0;
                    }
                }
                $contEmpleados++;
            }
        }

        return response()->json($datos, 200);
    }

    public function selectMatrizTardanzasPuerta(Request $request)
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



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
