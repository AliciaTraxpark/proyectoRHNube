<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 *  FUNCIONES PRA HORARIOS
 *  SE USA FH COMO CODIGO DE HELPER
 */

//**  FUNCION PARA OBTENER LOS HORARIOS DE UN EMPLEADO EN REORDENACION DE HORARIOS
function FHhorarioEmpleado($fechaMarcacion, $idEmpleado)
{

    //*FECHAS DESPUES Y ANTES DE LA MARCACION
    $diaMarcacion = Carbon::create($fechaMarcacion)->format('Y-m-d');
    $diaAnterior = Carbon::create($fechaMarcacion)->subDays(1)->format('Y-m-d');

    /* --------------------------------------------------------------- */

    /*-------------- VERIFICAMOS SI TIENE HORARIO--------------------------- */
    $horarioEmpleado = DB::table('horario_empleado as he')
        ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
        ->select(
            'he.horarioEmp_id as idHorarioEmpleado',
            'h.horaI as horaI',
            'h.horaF as horaF',
            'h.horario_tolerancia as toleranciaI',
            'h.horario_toleranciaF as toleranciaF',
            'hd.start', 'h.horario_id', 'he.fuera_horario',
            'he.horaAdic', 'he.nHoraAdic', 'h.horasObliga'
        )
        ->where('he.empleado_emple_id', '=', $idEmpleado)
        ->whereBetween(DB::raw('DATE(hd.start)'), [$diaAnterior, $diaMarcacion])
        ->where('he.estado', '=', 1)
        
        ->orderBy('h.horaI', 'ASC')
        ->get();

    return $horarioEmpleado = FHIntervaloHorario($horarioEmpleado);
}

//**  FUNCION PARA OBTENR HORA INCIAL Y FINAL DE HORARIO SIN HORAS EXTRAS
function FHIntervaloHorario($horarioEmpleado)
{

    foreach ($horarioEmpleado as $horarioEmpleados) {

        //*verificamos si hora fin de horario pertenece a hoy
        $fecha = Carbon::create($horarioEmpleados->start);
        $fechaHorario = $fecha->isoFormat('YYYY-MM-DD');
        $despues = $fecha->addDays(1);
        $fechaMan = $despues->isoFormat('YYYY-MM-DD');

        if (Carbon::parse($horarioEmpleados->horaF)->lt(Carbon::parse($horarioEmpleados->horaI))) {

            $horarioEmpleados->horaI = Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaI)
                ->subMinutes($horarioEmpleados->toleranciaI);
            $horarioEmpleados->horaF = Carbon::parse($fechaMan . " " . $horarioEmpleados->horaF)
                ->addMinutes($horarioEmpleados->toleranciaF);
        } else {
            $horarioEmpleados->horaI = Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaI)
                ->subMinutes($horarioEmpleados->toleranciaI);
            $horarioEmpleados->horaF = Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaF)
                ->addMinutes($horarioEmpleados->toleranciaF);
        }

        //*ANADIENDO TIEMPO EXTRA A SALIDA

    }

    return $horarioEmpleado;
}

//** FUNCION PARA OBTENER TIEMPO TRABAJADO DE EMPLEADO EN MARCACION PUERTA
function FHTiempoTrabajado($fechaHorahoy, $idEmpleado, $horarioEmp_id,$idHorarioAnt)
{
    //* SE OBTIENE HORARIO ANTERIOR DE MARCACION PARA NO TOMARLO EN CUENTA
    $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
        ->where('m.marcaMov_emple_id', '=', $idEmpleado)
        ->whereNotNull('m.marcaMov_fecha')
        ->whereNotNull('m.marcaMov_salida')
        ->whereDate(DB::raw('DATE(marcaMov_fecha)'), '=', $fechaHorahoy)
        ->where('m.horarioEmp_id', '=', $horarioEmp_id)
        ->where('m.horarioEmp_id', '!=', $idHorarioAnt)
        ->get();

    // : CALCULAR TIEMPO TRABAJADO POR AHORA CON ESE HORARIO
    $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;

    return $sumaTotalDeHoras;

}

// * FUNCION DE COMPROBAR SI ESTA UAN HORA EN UN RANGO DE HORAS
function FHcheckHora($hora_ini, $hora_fin, $hora_now)
{
    $horaI = Carbon::parse($hora_ini);
    $horaF = Carbon::parse($hora_fin);
    $horaN = Carbon::parse($hora_now);
    // : CONDICIONAL DE MAYOR IGUAL && MENOR
    if ($horaN->gte($horaI) && $horaN->lt($horaF)) {
        return true;
    } else {
        return false;
    }

}

function FHverificarSalidaHorario($horarioDentro, $tiempoTotal, $tiempoTotalDeHorario, $salida)
{

    //*VALIDAMOS SI LA SALIDA PUEDE IRA HORARIO*********************
    /************************************************************ */
    if (FHcheckHora($horarioDentro->horaI, $horarioDentro->horaF, $salida) == true) {
        return true;
    } else {
        //* SI TIENE PERMITIDO TRABAJAR FUERA DE HORARIO
        if ($horarioDentro->fuera_horario == 1) {

            //*TIENE PERMITIDO TRABAJR FUERA DE HORARIO Y TIENE HORAS ADICIONALES, ENTONCES LE SUMO A SU RANGO QUE PUEDE PASAR
            if ($horarioDentro->horaAdic == 1) {

                //! SUS HORAS ADICIONALES LAS PUEDE HACER FUERA DEL HORARIO
                //! OSEA SE SUMA A SUS HORAS OBLIGAS NO AL TIEMPO DE HORARIO
                return true;
            } else {

                //*TIENE PERMITIDO TRABAJR FUERA DE HORARIO Y NO TIENE HORAS ADICIONALES
                 //! SU ENTRADA ES DETNRO DEL RANGO,PUEDE TRABAJR DENTRO O FUERA DE
                //!  ESTE RANGO PERO SOLAMENTE HASTA COMPLETAR SUS HORAS OBLIGADAS
                return true;
            }

        } else {

            if ($horarioDentro->horaAdic == 1) {
                //*SI NO TIENE PERMITIDO TRABAJAR FUERA DE HORARIO Y TIENE HORAS ADICIONALES
                //*SE TIENE QUE VALIDAR POR EJEMPLO SI TIENE 4 HORAS OBLIGADAS Y DE 9 A 18PM SI PUEDE SEGUIR MARCANDO SOLO EN ESE RANGO

                 //!ESTO SE VALIDA CUANDO SE ASIGNA SU HORARIO QUE SOLO PUEDA TRABAJR
                //! HASTA SUS HORAS ADICIONALES, SUS HORAS ADICIONALES SOLO DENTRO DE HORARIO
                return false;

            } else {
                //*SI NO TIENE PERMITIDO TRABAJAR FUERA DE HORARIO Y NO TIENE HORAS ADICIONALES
                //! ESTO SE VALIDA QUE CUMPLA SUS HORAS OBLIGATORIAS Y SI QUIERE TRABAJR
                //! MAS, YA NO IRA CON ESTE HORARIO (EXTRICTO)
                return false;
            }
        }
    }

}
