<?php

use Carbon\Carbon;

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
