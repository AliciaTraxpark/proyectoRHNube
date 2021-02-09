<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// * FUNCION DE AGRUPAR HORAS MINUTOS
function horasRemotoRutaJson($array)
{
    $resultado = array();

    foreach ($array  as $dato) {
        $hora = explode(":", $dato->hora);
        $horaInteger = intval($hora[0]); //: CONVERTIR DE STRING A ENTERO
        $minuto = substr($hora[1], 0, 1);
        $minutoInteger = intval($minuto);
        if (!isset($resultado[$horaInteger])) {
            $resultado[$horaInteger] = array("hora" => $horaInteger, "fecha" => $dato->fecha, "minuto" => array());
        }
        if (!isset($resultado[$horaInteger]["minuto"][$minutoInteger])) {
            $resultado[$horaInteger]["minuto"][$minutoInteger] = array();
        }
        array_push($resultado[$horaInteger]["minuto"][$minutoInteger], $dato);
    }
    return array_values($resultado);
}

// * FUNCION DE COMPROBAR SI ESTA UAN HORA EN UN RANGO DE HORAS
function checkHora($hora_ini, $hora_fin, $hora_now)
{
    $horaI = Carbon::parse($hora_ini);
    $horaF = Carbon::parse($hora_fin);
    $horaN = Carbon::parse($hora_now);

    if ($horaN->gte($horaI) && $horaN->lt($horaF)) {
        return true;
    } else return false;
}

// * FUNCION PARA ORDENAR UN ARRAY DE OBJECTOS
function object_sorter($clave, $orden = null)
{
    return function ($a, $b) use ($clave, $orden) {
        $result =  ($orden == "DESC") ? strnatcmp($b->$clave, $a->$clave) :  strnatcmp($a->$clave, $b->$clave);
        return $result;
    };
}

// * FUNCION DE AGRUPAR PARA LISTAS EN MARCACIONES PARA SALIDA
function agruparMarcacionesHorario($array)
{
    $resultado = array();

    foreach ($array as $horario) {
        if (!isset($resultado[$horario->idH])) {
            $resultado[$horario->idH] = (object) array("idH" => $horario->idH, "horario" => $horario->horario);
        }
        if (!isset($resultado[$horario->idH]->data)) {
            $resultado[$horario->idH]->data = array();
        }
        $arraySalida = (object) array(
            "id" => $horario->id,
            "salida" => $horario->salida
        );
        array_push($resultado[$horario->idH]->data, $arraySalida);
    }

    return array_values($resultado);
}

// * FUNCION DE AGRUPAR PARA LISTAS EN MARCACIONES PARA ENTRADA
function agruparMarcacionesEHorario($array)
{
    $resultado = array();

    foreach ($array as $horario) {
        if (!isset($resultado[$horario->idH])) {
            $resultado[$horario->idH] = (object) array("idH" => $horario->idH, "horario" => $horario->horario);
        }
        if (!isset($resultado[$horario->idH]->data)) {
            $resultado[$horario->idH]->data = array();
        }
        $arrayEntrada = (object) array(
            "id" => $horario->id,
            "entrada" => $horario->entrada
        );
        array_push($resultado[$horario->idH]->data, $arrayEntrada);
    }

    return array_values($resultado);
}

// * FUNCION DE OBTENER HORAS DE RHBOX - ESCRITORIO
function horasRHbox($idEmpleado, $fecha)
{
    $horasRHbox = DB::table('empleado as e')
        ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
        ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
        ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
        ->select(
            'cp.actividad',
            DB::raw('TIME(cp.hora_ini) as hora_ini'),
            DB::raw('TIME(cp.hora_fin) as hora_fin'),
            DB::raw('DATE(cp.hora_ini) as fecha'),
            DB::raw('TIME(cp.hora_ini) as hora'),
            'promedio.tiempo_rango as rango'
        )
        ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '=', $fecha)
        ->where('e.emple_id', '=', $idEmpleado)
        ->orderBy('cp.hora_ini', 'asc')
        ->get();

    return $horasRHbox;
}

// * FUNCION DE OBTENER HORAS DE RHBOX - MOVIL
function horasRHboxMovil($idEmpleado, $fecha)
{
    $horasRuta = DB::table('empleado as e')
        ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
        ->leftJoin('horario_dias as h', 'h.id', '=', 'u.idHorario_dias')
        ->select(
            'u.actividad_ubicacion as actividad',
            DB::raw('TIME(u.hora_ini) as hora_ini'),
            DB::raw('TIME(u.hora_fin) as hora_fin'),
            DB::raw('DATE(u.hora_ini) as fecha'),
            DB::raw('TIME(u.hora_ini) as hora'),
            'u.rango as rango'
        )
        ->where(DB::raw('IF(h.id is null, DATE(u.hora_ini), DATE(h.start))'), '=', $fecha)
        ->where('e.emple_id', '=', $idEmpleado)
        ->orderBy('u.hora_ini', 'asc')
        ->get();

    return $horasRuta;
}

// * FUNCION PARA UNIR LA DOS DATAS DE RHBOX - ESCRITORIO Y RHBOX - MOVIL
function unionDeDataRHbox($horasRHbox, $horasRuta)
{
    $rango = 0;
    $actividad = 0;
    $productividad = 0;
    if (sizeof($horasRHbox) != 0 && sizeof($horasRuta) != 0) {
        for ($hora = 0; $hora < 24; $hora++) {
            $busquedaHora = true;
            for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                for ($j = 0; $j < sizeof($horasRuta); $j++) {
                    //* RECORREMOS EN FORMATO HORAS
                    if ($horasRHbox[$i]["hora"] == $hora && $horasRuta[$j]["hora"] == $hora) {
                        $busquedaHora = false;
                        //* RECORREMOS EN FORMATO MINUTOS
                        for ($m = 0; $m < 6; $m++) {
                            if (isset($horasRHbox[$i]["minuto"][$m]) && isset($horasRuta[$j]["minuto"][$m])) {
                                $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                //: DATOS DE RH BOX
                                $horaInicioRHbox = "23:00:00";
                                $horaFinRHbox = "00:00:00";
                                $rangoRHbox = 0;
                                $actividadRHbox = 0;
                                //: DATOS DE RUTA
                                $horaInicioRuta = "23:00:00";
                                $horaFinRuta = "00:00:00";
                                $rangoRuta = 0;
                                $actividadRuta = 0;
                                //* RECORREMOS MINUTOS RH BOX
                                for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                    if (Carbon::parse($horaInicioRHbox) > Carbon::parse($arrayMinutoRHbox[$index]->hora_ini)) {
                                        $horaInicioRHbox = $arrayMinutoRHbox[$index]->hora_ini;
                                    }
                                    if (Carbon::parse($horaFinRHbox) < Carbon::parse($arrayMinutoRHbox[$index]->hora_fin)) {
                                        $horaFinRHbox = $arrayMinutoRHbox[$index]->hora_fin;
                                    }
                                    $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                    $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                }
                                //* RECORREMOS MINUTOS RUTA
                                for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                    if (Carbon::parse($horaInicioRuta) > Carbon::parse($arrayMinutoRuta[$element]->hora_ini)) {
                                        $horaInicioRuta = $arrayMinutoRuta[$element]->hora_ini;
                                    }
                                    if (Carbon::parse($horaFinRuta) < Carbon::parse($arrayMinutoRuta[$element]->hora_fin)) {
                                        $horaFinRuta = $arrayMinutoRuta[$element]->hora_fin;
                                    }
                                    $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                    $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                }
                                //* COMPARAMOS TIEMPOS
                                if (Carbon::parse($horaInicioRHbox) < Carbon::parse($horaInicioRuta)) {
                                    //* PARAMETROS PARA ENVIAR A FUNCION
                                    $horaInicioRango = $horaInicioRHbox;
                                    $horaFinRango = $horaFinRHbox;
                                    $horaNowRango = $horaInicioRuta;
                                    //* *********************************
                                    $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                    if ($check) {
                                        // ! RANGOS
                                        $nuevoRango = ($rangoRHbox + $rangoRuta) / 2;
                                        $rango = $rango + $nuevoRango;
                                        // ! ACTIVIDAD
                                        $nuevaActividad = ($actividadRHbox + $actividadRuta) / 2;
                                        $actividad = $actividad + $nuevaActividad;
                                    } else {
                                        // ! RANGOS
                                        $nuevoRango = $rangoRHbox + $rangoRuta;
                                        $rango = $rango + $nuevoRango;
                                        // ! ACTIVIDAD
                                        $nuevaActividad = $actividadRHbox + $actividadRuta;
                                        $actividad = $actividad + $nuevaActividad;
                                    }
                                } else {
                                    //* PARAMETROS PARA ENVIAR A FUNCION
                                    $horaInicioRango = $horaInicioRuta;
                                    $horaFinRango = $horaFinRuta;
                                    $horaNowRango = $horaInicioRHbox;
                                    //* *********************************
                                    $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                    if ($check) {
                                        // ! RANGOS
                                        $nuevoRango = ($rangoRHbox + $rangoRuta) / 2;
                                        $rango = $rango + $nuevoRango;
                                        // ! ACTIVIDAD
                                        $nuevaActividad = ($actividadRHbox + $actividadRuta) / 2;
                                        $actividad = $actividad + $nuevaActividad;
                                    } else {
                                        // ! RANGOS
                                        $nuevoRango = $rangoRHbox + $rangoRuta;
                                        $rango = $rango + $nuevoRango;
                                        // ! ACTIVIDAD
                                        $nuevaActividad = $actividadRHbox + $actividadRuta;
                                        $actividad = $actividad + $nuevaActividad;
                                    }
                                }
                            } else {
                                if (isset($horasRHbox[$i]["minuto"][$m])) {
                                    $rangoRHbox = 0;
                                    $actividadRHbox = 0;
                                    $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                    //* RECORREMOS MINUTOS RH BOX
                                    for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                        $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                        $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                    }
                                    $rango = $rango + $rangoRHbox;               //: -> RANGO
                                    $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                                } else {
                                    if (isset($horasRuta[$j]["minuto"][$m])) {
                                        $rangoRuta = 0;
                                        $actividadRuta = 0;
                                        $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                        //* RECORREMOS MINUTOS RUTA
                                        for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                            $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                            $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                        }
                                        $rango = $rango + $rangoRuta;                //: -> RANGO
                                        $actividad = $actividad + $actividadRuta;    //: -> ACTIVIDAD
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($busquedaHora) {
                for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                    if ($horasRHbox[$i]["hora"] == $hora) {
                        //* RECORREMOS EN FORMATO MINUTOS
                        for ($m = 0; $m < 6; $m++) {
                            if (isset($horasRHbox[$i]["minuto"][$m])) {
                                $rangoRHbox = 0;
                                $actividadRHbox = 0;
                                $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                //* RECORREMOS MINUTOS RH BOX
                                for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                    $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                    $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                }
                                $rango = $rango + $rangoRHbox;                //: -> RANGO
                                $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                            }
                        }
                    }
                }
                for ($j = 0; $j < sizeof($horasRuta); $j++) {
                    if ($horasRuta[$j]["hora"] == $hora) {
                        //* RECORREMOS EN FORMATO MINUTOS
                        for ($m = 0; $m < 6; $m++) {
                            if (isset($horasRuta[$j]["minuto"][$m])) {
                                $rangoRuta = 0;
                                $actividadRuta = 0;
                                $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                //* RECORREMOS MINUTOS RUTA
                                for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                    $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                    $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                }
                                $rango = $rango + $rangoRuta;               //: -> RANGO
                                $actividad = $actividad + $actividadRuta;   //: -> ACTIVIDAD
                            }
                        }
                    }
                }
            }
        }
    } else {
        if (sizeof($horasRHbox) != 0) {
            for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                //* RECORREMOS EN FORMATO HORAS
                for ($hora = 0; $hora < 24; $hora++) {
                    if ($horasRHbox[$i]["hora"] == $hora) {
                        //* RECORREMOS EN FORMATO MINUTOS
                        for ($m = 0; $m < 6; $m++) {
                            if (isset($horasRHbox[$i]["minuto"][$m])) {
                                $rangoRHbox = 0;
                                $actividadRHbox = 0;
                                $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                //* RECORREMOS MINUTOS RH BOX
                                for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                    $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                    $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                }
                                $rango = $rango + $rangoRHbox;               //: -> RANGO
                                $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                            }
                        }
                    }
                }
            }
        } else {
            if (sizeof($horasRuta) != 0) {
                for ($j = 0; $j < sizeof($horasRuta); $j++) {
                    //* RECORREMOS EN FORMATO HORAS
                    for ($hora = 0; $hora < 24; $hora++) {
                        if ($horasRuta[$j]["hora"] == $hora) {
                            //* RECORREMOS EN FORMATO MINUTOS
                            for ($m = 0; $m < 6; $m++) {
                                if (isset($horasRuta[$j]["minuto"][$m])) {
                                    $rangoRuta = 0;
                                    $actividadRuta = 0;
                                    $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                    //* RECORREMOS MINUTOS RUTA
                                    for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                        $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                        $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                    }
                                    $rango = $rango + $rangoRuta;
                                    $actividad = $actividad + $actividadRuta;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if ($rango != 0) {
        $productividad = ($actividad / $rango) * 100;
        $productividad = (float) number_format($productividad, 2);
    }

    return (object)array("productividad" => $productividad, "rango" => gmdate('H:i:s', $rango));
}
