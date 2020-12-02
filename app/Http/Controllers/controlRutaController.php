<?php

namespace App\Http\Controllers;

use App\organizacion;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class controlRutaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        return view('ruta.rutaDiaria');
    }

    public function indexReporte()
    {
        $areas = DB::table('area as a')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->get();

        $cargos = DB::table('cargo as c')
            ->select('c.cargo_id', 'c.cargo_descripcion')
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->get();

        return view('ruta.reporteSemanalRuta', ['areas' => $areas, 'cargos' => $cargos]);
    }

    public function showConRuta(Request $request)
    {

        //? FUNCION PARA UNIR CAPTURAS POR HORAS Y MINUTOS
        function controlRRJson($array)
        {
            $resultado = array();

            foreach ($array as $captura) {
                $horaCaptura = explode(":", $captura->hora);
                $horaInteger = intval($horaCaptura[0]); //* CONVERTIR STRING A ENTERO
                $sub = substr($horaCaptura[1], 0, 1);
                $subInteger = intval($sub); //* CONVERTIR STRING  A ENTERO
                if (!isset($resultado[$horaInteger])) {
                    $resultado[$horaInteger] = array("horaCaptura" => $horaInteger, "fecha" => $captura->fecha, "minutos" => array());
                }
                if (!isset($resultado[$horaInteger]["minutos"][$subInteger])) {
                    $resultado[$horaInteger]["minutos"][$subInteger] = array();
                }
                array_push($resultado[$horaInteger]["minutos"][$subInteger], $captura);
            }
            return array_values($resultado);
        }
        // ? ********************************************
        // ? FUNCION PARA UNIR RUTAS POR HORAS Y MINUTOS
        function controlRJson($array)
        {
            $resultado = array();

            foreach ($array as $ubicacion) {
                $horaUbicacion = explode(":", $ubicacion->hora);
                $horaInteger = intval($horaUbicacion[0]); //* CONVERTIR STRING A ENTERO
                $sub = substr($horaUbicacion[1], 0, 1);
                $subInteger = intval($sub); //* CONVERTIR STRING A ENTERO
                if (!isset($resultado[$horaInteger])) {
                    $resultado[$horaInteger] = array("horaUbicacion" => $horaInteger, "fecha" => $ubicacion->fecha, "minutos" => array());
                }
                if (!isset($resultado[$horaInteger]["minutos"][$subInteger])) {
                    $resultado[$horaInteger]["minutos"][$subInteger] = array();
                }
                array_push($resultado[$horaInteger]["minutos"][$subInteger], $ubicacion);
            }

            return array_values($resultado);
        }
        // ? *********************************************
        $idempleado = $request->get('value');
        $fecha = $request->get('fecha');
        // ? RECUPERAR DATOS DE CAPTURAS POR FECHA INDICADA Y EMPLEADO
        $control = DB::table('empleado as e')
            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
            ->join('actividad as a', 'a.Activi_id', '=', 'cp.idActividad')
            ->join('promedio_captura as pc', 'pc.idCaptura', '=', 'cp.idCaptura')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'pc.idHorario')
            ->select(
                DB::raw('IF(hd.id is null, DATE(cp.hora_ini), DATE(hd.start))'),
                'a.Activi_id',
                'a.Activi_Nombre',
                'a.estado',
                'cp.idCaptura',
                'cp.actividad',
                'cp.hora_fin',
                DB::raw('DATE(cp.hora_ini) as fecha'),
                DB::raw('TIME(cp.hora_ini) as hora'),
                'pc.promedio as prom',
                'pc.tiempo_rango as rango',
                DB::raw('TIME(cp.hora_ini) as hora_ini'),
                DB::raw('TIME(cp.hora_fin) as hora_fin'),
                'cp.actividad as tiempoA'
            )
            ->where(DB::raw('IF(hd.id is null, DATE(cp.hora_ini), DATE(hd.start))'), '=', $fecha)
            ->where('e.emple_id', '=', $idempleado)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->orderBy('cp.hora_ini', 'asc')
            ->get();
        foreach ($control as $c) {
            $capturas = DB::table('captura_imagen as ci')
                ->select('ci.id as idImagen', 'ci.miniatura as imagen')
                ->where('ci.idCaptura', '=', $c->idCaptura)
                ->get();
            $datos = [];
            foreach ($capturas as $cp) {
                array_push($datos, $cp);
            }
            $c->imagen = $datos;
        }
        // ? REALIZAMOS UNION DE HORAS Y MINUTOS EN CAPTURA
        $control = controlRRJson($control);
        // ? **********************************************
        // ? RECUPERAR DATOS DE UBICACIONES POR FECHA Y EMPLEADO DADO
        $control_ruta = DB::table('empleado as e')
            ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
            ->join('actividad as a', 'a.Activi_id', '=', 'u.idActividad')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'u.idHorario_dias')
            ->select(
                DB::raw('IF(hd.id is null, DATE(u.hora_ini), DATE(hd.start))'),
                'a.Activi_Nombre',
                'u.id as idUbicacion',
                DB::raw('DATE(u.hora_ini) as fecha'),
                DB::raw('TIME(u.hora_ini) as hora'),
                DB::raw('TIME(u.hora_ini) as hora_ini'),
                DB::raw('TIME(u.hora_fin) as hora_fin'),
                'u.actividad_ubicacion as actividad',
                'u.rango'
            )
            ->where(DB::raw('IF(hd.id is null, DATE(u.hora_ini), DATE(hd.start))'), '=', $fecha)
            ->where('e.emple_id', '=', $idempleado)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->orderBy('u.hora_ini', 'asc')
            ->get();

        foreach ($control_ruta as $cr) {
            $ubicaciones = DB::table('ubicacion_ruta as ur')
                ->select('ur.latitud_ini', 'ur.longitud_ini', 'ur.latitud_fin', 'ur.longitud_fin')
                ->where('ur.idUbicacion', '=', $cr->idUbicacion)
                ->get();
            $datos = [];
            foreach ($ubicaciones as $u) {
                array_push($datos, $u);
            }
            $cr->ubicaciones = $datos;
        }
        // ? REALIZAMOS UNION DE HORAS Y MINUTOS EN UBICACION
        $control_ruta = controlRJson($control_ruta);
        //* FUNCION DE BUSQUEDA DE HORA EN ARRAY NUEVO
        function busquedaHora($array, $hora)
        {
            foreach ($array as $key => $value) {
                if ($value["hora"] == $hora) {
                    return false;
                }
            }
            return true;
        }
        //* ******************************************
        // TODO -> NUEVA FORMA DE UNIR
        // dd($control, $control_ruta);
        $respuesta = array();
        $fechaIgual = array(); //* ARRAYS CON FECHA DE BUSQUEDA
        $fechaDiferente = array(); //* ARRAYS CON FECHA DIFERENTE 
        if (!empty($control) && !empty($control_ruta)) { //* CUANDO LOS DOS ARRAYS CONTIENE DATOS
            //* RECORREMOS EN FORMATO HORAS
            for ($hora = 0; $hora < 24; $hora++) {
                $ingresoHora = true;
                for ($index = 0; $index < sizeof($control); $index++) {
                    for ($element = 0; $element < sizeof($control_ruta); $element++) {
                        //* BUSCAMOS SI TIENEN ESA MISMA HORA LOS DOS ARRAYS
                        if ($control[$index]["horaCaptura"] == $hora && $control_ruta[$element]["horaUbicacion"] == $hora) {
                            $ingresoHora = false;
                            if (date($control[$index]["fecha"]) == date($fecha)) {
                                if (empty($fechaIgual)) {
                                    $arrayMinuto = [];
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($minuto = 0; $minuto < 6; $minuto++) {
                                        if (isset($control[$index]["minutos"][$minuto]) && isset($control_ruta[$element]["minutos"][$minuto])) {
                                            $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                            $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                        } else {
                                            if (isset($control[$index]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                $arrayMinuto[$minuto]["ubicacion"] = array();
                                            } else {
                                                if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                    $arrayMinuto[$minuto]["captura"] = array();
                                                    $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                                }
                                            }
                                        }
                                    }
                                    array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                } else {
                                    $busqueda = busquedaHora($fechaIgual, $hora);
                                    if ($busqueda) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control[$index]["minutos"][$minuto]) && isset($control_ruta[$element]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                            } else {
                                                if (isset($control[$index]["minutos"][$minuto])) {
                                                    $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                    $arrayMinuto[$minuto]["ubicacion"] = array();
                                                } else {
                                                    if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                        $arrayMinuto[$minuto]["captura"] = array();
                                                        $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                                    }
                                                }
                                            }
                                        }
                                        array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                    }
                                }
                            } else {
                                if (empty($fechaDiferente)) {
                                    $arrayMinuto = [];
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($minuto = 0; $minuto < 6; $minuto++) {
                                        if (isset($control[$index]["minutos"][$minuto]) && isset($control_ruta[$element]["minutos"][$minuto])) {
                                            $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                            $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                        } else {
                                            if (isset($control[$index]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                $arrayMinuto[$minuto]["ubicacion"] = array();
                                            } else {
                                                if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                    $arrayMinuto[$minuto]["captura"] = array();
                                                    $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                                }
                                            }
                                        }
                                    }
                                    array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                } else {
                                    $busqueda = busquedaHora($fechaDiferente, $hora);
                                    if ($busqueda) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control[$index]["minutos"][$minuto]) && isset($control_ruta[$element]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                            } else {
                                                if (isset($control[$index]["minutos"][$minuto])) {
                                                    $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                    $arrayMinuto[$minuto]["ubicacion"] = array();
                                                } else {
                                                    if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                        $arrayMinuto[$minuto]["captura"] = array();
                                                        $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                                    }
                                                }
                                            }
                                        }
                                        array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                    }
                                }
                            }
                        }
                    }
                    if ($ingresoHora) {
                        if ($control[$index]["horaCaptura"] == $hora) { //* BUSCAMOS SI CAPTURA TIENE ESA HORA
                            $ingresoHora = false;
                            if (date($control[$index]["fecha"]) == date($fecha)) {
                                if (empty($fechaIgual)) {
                                    $arrayMinuto = [];
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($minuto = 0; $minuto < 6; $minuto++) {
                                        if (isset($control[$index]["minutos"][$minuto])) {
                                            $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                            $arrayMinuto[$minuto]["ubicacion"] = array();
                                        }
                                    }
                                    array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                } else {
                                    $busqueda = busquedaHora($fechaIgual, $hora);
                                    if ($fechaIgual) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control[$index]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                $arrayMinuto[$minuto]["ubicacion"] = array();
                                            }
                                        }
                                        array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                    }
                                }
                            } else {
                                if (empty($fechaDiferente)) {
                                    $arrayMinuto = [];
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($minuto = 0; $minuto < 6; $minuto++) {
                                        if (isset($control[$index]["minutos"][$minuto])) {
                                            $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                            $arrayMinuto[$minuto]["ubicacion"] = array();
                                        }
                                    }
                                    array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                } else {
                                    $busqueda = busquedaHora($fechaDiferente, $hora);
                                    if ($fechaDiferente) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control[$index]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                $arrayMinuto[$minuto]["ubicacion"] = array();
                                            }
                                        }
                                        array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                    }
                                }
                            }
                        }
                    }
                }
                if ($ingresoHora) {
                    for ($element = 0; $element < sizeof($control_ruta); $element++) {
                        if ($control_ruta[$element]["horaUbicacion"] == $hora) { //* BUSCAMOS SI UBICACION TIENE ESA HORA
                            if (date($control_ruta[$element]["fecha"]) == date($fecha)) {
                                if (empty($fechaIgual)) {
                                    $arrayMinuto = [];
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($minuto = 0; $minuto < 6; $minuto++) {
                                        if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                            $arrayMinuto[$minuto]["captura"] = array();
                                            $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                        }
                                    }
                                    array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control_ruta[$element]["fecha"]), "minuto" => $arrayMinuto));
                                } else {
                                    $busqueda = busquedaHora($fechaIgual, $hora);
                                    if ($busqueda) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = array();
                                                $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                            }
                                        }
                                        array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control_ruta[$element]["fecha"]), "minuto" => $arrayMinuto));
                                    }
                                }
                            } else {
                                if (empty($fechaDiferente)) {
                                    $arrayMinuto = [];
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($minuto = 0; $minuto < 6; $minuto++) {
                                        if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                            $arrayMinuto[$minuto]["captura"] = array();
                                            $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                        }
                                    }
                                    array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control_ruta[$element]["fecha"]), "minuto" => $arrayMinuto));
                                } else {
                                    $busqueda = busquedaHora($fechaDiferente, $hora);
                                    if ($busqueda) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = array();
                                                $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                            }
                                        }
                                        array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control_ruta[$element]["fecha"]), "minuto" => $arrayMinuto));
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $respuesta = array_merge($respuesta, $fechaIgual, $fechaDiferente);
            return response()->json($respuesta, 200);
        } else {
            if (!empty($control)) {
                //* RECORREMOS EN FORMATO DE HORAS
                for ($hora = 0; $hora < 24; $hora++) {
                    for ($index = 0; $index < sizeof($control); $index++) {
                        if ($control[$index]["horaCaptura"] == $hora) { //* BUSCAMOS SI CAPTURA TIENE ESA HORA
                            if (date($control[$index]["fecha"]) == date($fecha)) {
                                if (empty($fechaIgual)) {
                                    $arrayMinuto = [];
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($minuto = 0; $minuto < 6; $minuto++) {
                                        if (isset($control[$index]["minutos"][$minuto])) {
                                            $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                            $arrayMinuto[$minuto]["ubicacion"] = array();
                                        }
                                    }
                                    array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                } else {
                                    $busqueda = busquedaHora($fechaIgual, $hora);
                                    if ($fechaIgual) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control[$index]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                $arrayMinuto[$minuto]["ubicacion"] = array();
                                            }
                                        }
                                        array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                    }
                                }
                            } else {
                                if (empty($fechaDiferente)) {
                                    $arrayMinuto = [];
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($minuto = 0; $minuto < 6; $minuto++) {
                                        if (isset($control[$index]["minutos"][$minuto])) {
                                            $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                            $arrayMinuto[$minuto]["ubicacion"] = array();
                                        }
                                    }
                                    array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                } else {
                                    $busqueda = busquedaHora($fechaDiferente, $hora);
                                    if ($fechaDiferente) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control[$index]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = $control[$index]["minutos"][$minuto];
                                                $arrayMinuto[$minuto]["ubicacion"] = array();
                                            }
                                        }
                                        array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control[$index]["fecha"]), "minuto" => $arrayMinuto));
                                    }
                                }
                            }
                        }
                    }
                }
                $respuesta = array_merge($respuesta, $fechaIgual, $fechaDiferente);
                return response()->json($respuesta, 200);
            } else {
                if (!empty($control_ruta)) {
                    for ($hora = 0; $hora < 24; $hora++) {
                        for ($element = 0; $element < sizeof($control_ruta); $element++) {
                            if ($control_ruta[$element]["horaUbicacion"] == $hora) { //* BUSCAMOS SI UBICACION TIENE ESA HORA
                                if (date($control_ruta[$element]["fecha"]) == date($fecha)) {
                                    if (empty($fechaIgual)) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = array();
                                                $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                            }
                                        }
                                        array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control_ruta[$element]["fecha"]), "minuto" => $arrayMinuto));
                                    } else {
                                        $busqueda = busquedaHora($fechaIgual, $hora);
                                        if ($busqueda) {
                                            $arrayMinuto = [];
                                            //* RECORREMOS EN FORMATO MINUTOS
                                            for ($minuto = 0; $minuto < 6; $minuto++) {
                                                if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                    $arrayMinuto[$minuto]["captura"] = array();
                                                    $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                                }
                                            }
                                            array_push($fechaIgual, array("hora" => $hora, "fecha" => date($control_ruta[$element]["fecha"]), "minuto" => $arrayMinuto));
                                        }
                                    }
                                } else {
                                    if (empty($fechaDiferente)) {
                                        $arrayMinuto = [];
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($minuto = 0; $minuto < 6; $minuto++) {
                                            if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                $arrayMinuto[$minuto]["captura"] = array();
                                                $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                            }
                                        }
                                        array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control_ruta[$element]["fecha"]), "minuto" => $arrayMinuto));
                                    } else {
                                        $busqueda = busquedaHora($fechaDiferente, $hora);
                                        if ($busqueda) {
                                            $arrayMinuto = [];
                                            //* RECORREMOS EN FORMATO MINUTOS
                                            for ($minuto = 0; $minuto < 6; $minuto++) {
                                                if (isset($control_ruta[$element]["minutos"][$minuto])) {
                                                    $arrayMinuto[$minuto]["captura"] = array();
                                                    $arrayMinuto[$minuto]["ubicacion"] = $control_ruta[$element]["minutos"][$minuto];
                                                }
                                            }
                                            array_push($fechaDiferente, array("hora" => $hora, "fecha" => date($control_ruta[$element]["fecha"]), "minuto" => $arrayMinuto));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $respuesta = array_merge($respuesta, $fechaIgual, $fechaDiferente);
                    return response()->json($respuesta, 200);
                }
            }
        }
        // TODO ***********************
    }

    public function reporte(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $fecha = $request->get('fecha');
        $fechaF = explode("a", $fecha);
        $area = $request->get('area');
        $cargo = $request->get('cargo');
        if (is_null($area) === true && is_null($cargo) === true) {
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
        } else {
            if (is_null($area) === false && is_null($cargo) === true) {
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
                            ->whereIn('e.emple_area', $area)
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
                                ->whereIn('e.emple_area', $area)
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
                                ->whereIn('e.emple_area', $area)
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
                        ->whereIn('e.emple_area', $area)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
            if (is_null($area) === true && is_null($cargo) === false) {
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
                            ->whereIn('e.emple_cargo', $cargo)
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
                                ->whereIn('e.emple_cargo', $cargo)
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
                                ->whereIn('e.emple_cargo', $cargo)
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
                        ->whereIn('e.emple_cargo', $cargo)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
            if (is_null($area) === false && is_null($cargo) === false) {
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
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_cargo', $cargo)
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
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_cargo', $cargo)
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
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_cargo', $cargo)
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
                        ->whereIn('e.emple_area', $area)
                        ->whereIn('e.emple_cargo', $cargo)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
        }

        $respuesta = [];

        if (sizeof($empleados) > 0) {
            //* FUNCION PARA AGRUPAR CAPTURAS POR FECHA , HORAS Y MINUTOS
            function agruparEmpleadosCaptura($array)
            {
                $resultado = array();
                foreach ($array as $empleado) {
                    $hora = explode(":", $empleado->hora_ini);
                    $fechaA = $empleado->dia;
                    if (!isset($resultado[$empleado->emple_id])) {
                        $resultado[$empleado->emple_id] = array("empleado" => $empleado->emple_id, "datos" => array());
                    }
                    if (!isset($resultado[$empleado->emple_id]["datos"][$fechaA])) {
                        $resultado[$empleado->emple_id]["datos"][$fechaA] = array();
                    }
                    $horaInteger = intval($hora[0]); //* CONVERTIR DE STRING A ENTERO
                    $sub = substr($hora[1], 0, 1);
                    $subInteger = intval($sub); //* CONVERTIR DE STRING A ENTERO
                    if (!isset($resultado[$empleado->emple_id]["datos"][$fechaA][$horaInteger]["minuto"][$subInteger])) {
                        $resultado[$empleado->emple_id]["datos"][$fechaA][$horaInteger]["minuto"][$subInteger] = array();
                    }
                    array_push($resultado[$empleado->emple_id]["datos"][$fechaA][$horaInteger]["minuto"][$subInteger], $empleado);
                }
                return array_values($resultado);
            }
            //* *********************************************************************************************************
            //* QUERY CONDICIONAL DE CAPTURA DE FECHA E HORARIO
            $sqlCaptura = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)), DAY(DATE(cp.hora_ini)) ),
            if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            //* ************************************************
            //: OBTENIENDO DATOS DE CAPTURAS
            $tiempoDiaCaptura = DB::table('empleado as e')
                ->leftJoin('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                ->join('actividad as a', 'a.Activi_id', '=', 'cp.idActividad')
                ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                ->select(
                    'e.emple_id',
                    DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start)) as fecha'),
                    DB::raw('TIME(cp.hora_ini) as hora_ini'),
                    DB::raw('TIME(cp.hora_fin) as hora_fin'),
                    DB::raw($sqlCaptura),
                    'cp.actividad',
                    'promedio.tiempo_rango'
                )
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '>=', $fechaF[0])
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '<=', $fechaF[1])
                ->orderBy('cp.hora_ini', 'asc')
                ->get();
            $tiempoDiaCaptura = agruparEmpleadosCaptura($tiempoDiaCaptura);
            //: *************************************************************
            //* QUERY CONDICIONAL DE UBICACION DE FECHA E HORARIO
            $sqlUbicacion = "IF(h.id is null, if(DATEDIFF('" . $fechaF[1] . "' , DATE(u.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "', DATE(u.hora_ini)) , DAY(DATE(u.hora_ini)) ) ,
            if(DATEDIFF('" . $fechaF[1] . "', DATE(h.start)) >= 0, DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            //* ******************************************************************
            //: OBTENIENDO DATOS DE UBICACION
            $tiempoDiaUbicacion = DB::table('empleado as e')
                ->leftJoin('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
                ->join('actividad as a', 'a.Activi_id', '=', 'u.idActividad')
                ->leftJoin('horario_dias as h', 'h.id', '=', 'u.idHorario_dias')
                ->select(
                    'e.emple_id',
                    DB::raw('IF(h.id is null, DATE(u.hora_ini), DATE(h.start)) as fecha'),
                    DB::raw('TIME(u.hora_ini) as hora_ini'),
                    DB::raw('TIME(u.hora_fin) as hora_fin'),
                    DB::raw($sqlUbicacion),
                    'u.actividad_ubicacion',
                    'u.rango'
                )
                ->where(DB::raw('IF(h.id is null, DATE(u.hora_ini), DATE(h.start))'), '>=', $fechaF[0])
                ->where(DB::raw('IF(h.id is null, DATE(u.hora_ini), DATE(h.start))'), '<=', $fechaF[1])
                ->orderBy('u.hora_ini', 'asc')
                ->get();
            $tiempoDiaUbicacion = agruparEmpleadosCaptura($tiempoDiaUbicacion);
            //: ***************************************************************************************
            $date1 = new DateTime($fechaF[0]);
            $date2 = new DateTime($fechaF[1]);
            $diff = $date1->diff($date2);
            //* FUNCION DE RANGOS DE HORAS
            function checkHora($hora_ini, $hora_fin, $hora_now)
            {
                $horaI = Carbon::parse($hora_ini);
                $horaF = Carbon::parse($hora_fin);
                $horaN = Carbon::parse($hora_now);

                if ($horaN->gte($horaI) && $horaN->lt($horaF)) {
                    return true;
                } else return false;
            }
            //* ******************************************
            //* FUNCION DE BUSQUEDA DE EMPLEADO EN ARRAY NUEVO
            function busquedaEmpleadoA($array, $empleado)
            {
                foreach ($array as $key => $value) {
                    if ($value["empleado"] == $empleado) {
                        return false;
                    }
                }
                return true;
            }
            $capturaUbicacion = []; //* GUARDAR NUEVA DATA 
            //* UNIR DATOS EN UNO SOLO
            //TODO-> SOLO SI @tiempoDiaCaptura y @tiempoDiaUbicacion CONTIENEN DATOS
            if (sizeof($tiempoDiaCaptura) != 0 && sizeof($tiempoDiaUbicacion) != 0) {
                for ($i = 0; $i < sizeof($tiempoDiaCaptura); $i++) {
                    $busquedaEmpleado = true;
                    for ($j = 0; $j < sizeof($tiempoDiaUbicacion); $j++) {
                        //* BUSCAMOS SI EL EMPLEADO SE ENCUENTRA TAMBIEN EN ARRAY DE UBICACION
                        if ($tiempoDiaCaptura[$i]["empleado"] == $tiempoDiaUbicacion[$j]["empleado"]) {
                            $busquedaEmpleado = false;
                            for ($d = 0; $d <= $diff->days; $d++) { //* Recorremos la cantidad de días por el rango
                                $diffRango = 0;
                                $diffActividad = 0;
                                //* Buscamos si existe esos dias en los 2 arrays
                                if (isset($tiempoDiaCaptura[$i]["datos"][$d]) && isset($tiempoDiaUbicacion[$j]["datos"][$d])) {
                                    $horaCaptura = $tiempoDiaCaptura[$i]["datos"][$d];
                                    $horaUbicacion = $tiempoDiaUbicacion[$j]["datos"][$d];
                                    for ($hora = 0; $hora < 24; $hora++) { //* Recorremos en formato de 24 horas
                                        if (isset($horaCaptura[$hora]) && isset($horaUbicacion[$hora])) { //* CAPTURA Y UBICACION CONTIENE LA MISMA HORA
                                            //* Recorremos en formato minutos 
                                            for ($m = 0; $m < 6; $m++) {
                                                if (isset($horaCaptura[$hora]["minuto"][$m]) && isset($horaUbicacion[$hora]["minuto"][$m])) { //* Comparamos si existe
                                                    $arrayMinutoCaptura = $horaCaptura[$hora]["minuto"][$m];
                                                    $arrayMinutoUbicacion = $horaUbicacion[$hora]["minuto"][$m];
                                                    //* RECORREMOS ARRAY DE MINUTOS EN CAPTURA
                                                    for ($indexMinutosC = 0; $indexMinutosC < sizeof($arrayMinutoCaptura); $indexMinutosC++) {
                                                        $valorIngreso = true;
                                                        //* RECORREMOS ARRAY DE MINUTOS EN UBICACIÓN
                                                        for ($indexMinutosU = 0; $indexMinutosU < sizeof($arrayMinutoUbicacion); $indexMinutosU++) {
                                                            //* FORMATO DE MINUTOS CON CARBON
                                                            $carbonCaptura = Carbon::parse($arrayMinutoCaptura[$indexMinutosC]->hora_ini)->isoFormat("HH:mm");
                                                            $carbonUbicacion = Carbon::parse($arrayMinutoUbicacion[$indexMinutosU]->hora_ini)->isoFormat("HH:mm");
                                                            //* BUSCAR CON MINUTOS IGUALES
                                                            if ($carbonCaptura == $carbonUbicacion) {
                                                                $valorIngreso = false;
                                                                $nuevaActividad = (($arrayMinutoCaptura[$indexMinutosC]->actividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion) / 2);
                                                                $nuevoRango = (($arrayMinutoCaptura[$indexMinutosC]->tiempo_rango + $arrayMinutoUbicacion[$indexMinutosU]->rango) / 2);
                                                                $diffRango = $diffRango + $nuevoRango;
                                                                $diffActividad = $diffActividad + $nuevaActividad;
                                                            } else {
                                                                if ($carbonCaptura < $carbonUbicacion) {
                                                                    //* PARAMETROS PARA ENVIAR A FUNCION
                                                                    $horaInicioRango = $arrayMinutoCaptura[$indexMinutosC]->hora_ini;
                                                                    $horaFinRango = $arrayMinutoCaptura[$indexMinutosC]->hora_fin;
                                                                    $horaNowRango = $arrayMinutoUbicacion[$indexMinutosU]->hora_ini;
                                                                    //* **************************************************************
                                                                    $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                                                    //* ***************************************************************
                                                                    if ($check) {
                                                                        $valorIngreso = !$check;
                                                                        $nuevaActividad = (($arrayMinutoCaptura[$indexMinutosC]->actividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion) / 2);
                                                                        $nuevoRango = (($arrayMinutoCaptura[$indexMinutosC]->tiempo_rango + $arrayMinutoUbicacion[$indexMinutosU]->rango) / 2);
                                                                        $diffRango = $diffRango + $nuevoRango;
                                                                        $diffActividad = $diffActividad + $nuevaActividad;
                                                                    }
                                                                } else {
                                                                    //* PARAMETROS PARA ENVIAR A FUNCION
                                                                    $horaInicioRango = $arrayMinutoUbicacion[$indexMinutosU]->hora_ini;
                                                                    $horaFinRango = $arrayMinutoUbicacion[$indexMinutosU]->hora_fin;
                                                                    $horaNowRango = $arrayMinutoCaptura[$indexMinutosC]->hora_ini;
                                                                    //* ***************************************************************
                                                                    $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                                                    //* ****************************************************************
                                                                    if ($check) {
                                                                        $valorIngreso = !$check;
                                                                        $nuevaActividad = (($arrayMinutoCaptura[$indexMinutosC]->actividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion) / 2);
                                                                        $nuevoRango = (($arrayMinutoCaptura[$indexMinutosC]->tiempo_rango + $arrayMinutoUbicacion[$indexMinutosU]->rango) / 2);
                                                                        $diffRango = $diffRango + $nuevoRango;
                                                                        $diffActividad = $diffActividad + $nuevaActividad;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        if ($valorIngreso) {
                                                            $diffRango = $diffRango + $arrayMinutoCaptura[$indexMinutosC]->tiempo_rango;
                                                            $diffActividad = $diffActividad + $arrayMinutoCaptura[$indexMinutosC]->actividad;
                                                        }
                                                    }
                                                    //* RECORREMOS ARRAY DE MINUTOS EN UBICACION
                                                    for ($indexMinutosU = 0; $indexMinutosU < sizeof($arrayMinutoUbicacion); $indexMinutosU++) {
                                                        $valorIngreso = true;
                                                        //* RECORREMOS ARRAY DE MINUTOS EN CAPTURA
                                                        for ($indexMinutosC = 0; $indexMinutosC < sizeof($arrayMinutoCaptura); $indexMinutosC++) {
                                                            //* FORMATO DE MINUTOS CON CARBON
                                                            $carbonUbicacion = Carbon::parse($arrayMinutoUbicacion[$indexMinutosU]->hora_ini)->isoFormat("HH:mm");
                                                            $carbonCaptura = Carbon::parse($arrayMinutoCaptura[$indexMinutosC]->hora_ini)->isoFormat("HH:mm");
                                                            //* BUSCAR MINUTOS IGUALES
                                                            if ($carbonUbicacion == $carbonCaptura) {
                                                                $valorIngreso = false;
                                                            } else {
                                                                if ($carbonUbicacion < $carbonCaptura) {
                                                                    //* PARAMETROS PARA ENVIAR A FUNCION
                                                                    $horaInicioRango = $arrayMinutoUbicacion[$indexMinutosU]->hora_ini;
                                                                    $horaFinRango = $arrayMinutoUbicacion[$indexMinutosU]->hora_fin;
                                                                    $horaNowRango = $arrayMinutoCaptura[$indexMinutosC]->hora_ini;
                                                                    $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                                                    if ($check) $valorIngreso = !$check;
                                                                } else {
                                                                    $horaInicioRango = $arrayMinutoCaptura[$indexMinutosC]->hora_ini;
                                                                    $horaFinRango = $arrayMinutoCaptura[$indexMinutosC]->hora_fin;
                                                                    $horaNowRango = $arrayMinutoUbicacion[$indexMinutosU]->hora_ini;
                                                                    $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                                                    if ($check) $valorIngreso = !$check;
                                                                }
                                                            }
                                                        }
                                                        if ($valorIngreso) {
                                                            $diffRango = $diffRango + $arrayMinutoUbicacion[$indexMinutosU]->rango;
                                                            $diffActividad = $diffActividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion;
                                                        }
                                                        if ($tiempoDiaUbicacion[$j]["empleado"] == 2 && $d == 6) {
                                                            dd($diffRango, $diffActividad);
                                                        }
                                                    }
                                                } else {
                                                    if (isset($horaCaptura[$hora]["minuto"][$m])) { //* Comparar si existe solo el minuto en captura
                                                        $arrayMinutoCaptura = $horaCaptura[$hora]["minuto"][$m];
                                                        //* RECORREMOS ARRAY DE MINUTOS EN CAPTURA
                                                        for ($indexMinutosC = 0; $indexMinutosC < sizeof($arrayMinutoCaptura); $indexMinutosC++) {
                                                            $diffRango = $diffRango + $arrayMinutoCaptura[$indexMinutosC]->tiempo_rango;
                                                            $diffActividad = $diffActividad  + $arrayMinutoCaptura[$indexMinutosC]->actividad;
                                                        }
                                                    } else {
                                                        if (isset($horaUbicacion[$hora]["minuto"][$m])) { //* Comparar si existe solo el minuto en ubicación
                                                            $arrayMinutoUbicacion = $horaUbicacion[$hora]["minuto"][$m];
                                                            //* RECORREMOS ARRAY DE MINUTOS EN UBICACION
                                                            for ($indexMinutosU = 0; $indexMinutosU < sizeof($arrayMinutoUbicacion); $indexMinutosU++) {
                                                                $diffRango = $diffRango + $arrayMinutoUbicacion[$indexMinutosU]->rango;
                                                                $diffActividad = $diffActividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if (isset($horaCaptura[$hora])) { //* BUSCAMOS SI SOLO CAPTURA CONTIENE ESA HORA
                                                //* RECORREMOS EN FORMATO MINUTOS
                                                for ($m = 0; $m < 6; $m++) {
                                                    if (isset($horaCaptura[$hora]["minuto"][$m])) { //* COMPARAR SI EXISTE EL MINUTO
                                                        $arrayMinutoCaptura = $horaCaptura[$hora]["minuto"][$m];
                                                        //* RECORREMOS ARRAY DE MINUTOS EN CAPTURA
                                                        for ($indexMinutosC = 0; $indexMinutosC < sizeof($arrayMinutoCaptura); $indexMinutosC++) {
                                                            $diffRango = $diffRango + $arrayMinutoCaptura[$indexMinutosC]->tiempo_rango;
                                                            $diffActividad = $diffActividad + $arrayMinutoCaptura[$indexMinutosC]->actividad;
                                                        }
                                                    }
                                                }
                                            } else {
                                                if (isset($horaUbicacion[$hora])) { //* BUSCAMOS SI SOLO UBICACION CONTIENE ESA HORA
                                                    //* RECORREMOS EN FORMATO MINUTOS
                                                    for ($m = 0; $m < 6; $m++) {
                                                        if (isset($horaUbicacion[$hora]["minuto"][$m])) { //* COMPARAR SI EXISTE EL MINUTO
                                                            $arrayMinutoUbicacion = $horaUbicacion[$hora]["minuto"][$m];
                                                            //* RECORREMOS ARRAY DE MINUTOS EN UBICACION
                                                            for ($indexMinutosU = 0; $indexMinutosU < sizeof($arrayMinutoUbicacion); $indexMinutosU++) {
                                                                $diffRango = $diffRango + $arrayMinutoUbicacion[$indexMinutosU]->rango;
                                                                $diffActividad = $diffActividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (sizeof($capturaUbicacion) == 0) {
                                        //* UNIR DATOS EN NUEVO ARRAY
                                        if (!isset($capturaUbicacion[0]["empleado"])) {
                                            $capturaUbicacion[0]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                        }
                                        if (!isset($capturaUbicacion[0]["datos"][$d])) {
                                            $capturaUbicacion[0]["datos"][$d] = array();
                                        }
                                        $capturaUbicacion[0]["datos"][$d]["rango"] = $diffRango;
                                        $capturaUbicacion[0]["datos"][$d]["actividad"] = $diffActividad;
                                    } else {
                                        $encontradoCU = true;
                                        $idEmpleado = $tiempoDiaCaptura[$i]["empleado"];
                                        for ($indexNuevo = 0; $indexNuevo < sizeof($capturaUbicacion); $indexNuevo++) {
                                            if ($capturaUbicacion[$indexNuevo]["empleado"] == $idEmpleado) {
                                                $encontradoCU = false;
                                                if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                    $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                                }
                                                $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                                $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                            }
                                        }
                                        if ($encontradoCU) {
                                            $indexNuevo = sizeof($capturaUbicacion);
                                            //* UNIR DATOS EN NUEVO ARRAY
                                            if (!isset($capturaUbicacion[$indexNuevo]["empleado"])) {
                                                $capturaUbicacion[$indexNuevo]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                            }
                                            if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                            }
                                            $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                            $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                        }
                                    }
                                } else {
                                    if (isset($tiempoDiaCaptura[$i]["datos"][$d])) {
                                        $horaCaptura = $tiempoDiaCaptura[$i]["datos"][$d];
                                        for ($hora = 0; $hora < 24; $hora++) {
                                            if (isset($horaCaptura[$hora])) {
                                                //* RECORREMOS EN FORMATO MINUTOS
                                                for ($m = 0; $m < 6; $m++) {
                                                    if (isset($horaCaptura[$hora]["minuto"][$m])) {
                                                        $arrayMinutoCaptura = $horaCaptura[$hora]["minuto"][$m];
                                                        for ($indexMinutosC = 0; $indexMinutosC < sizeof($arrayMinutoCaptura); $indexMinutosC++) {
                                                            $diffRango = $diffRango + $arrayMinutoCaptura[$indexMinutosC]->tiempo_rango;
                                                            $diffActividad = $diffActividad + $arrayMinutoCaptura[$indexMinutosC]->actividad;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if (sizeof($capturaUbicacion) == 0) {
                                            //* UNIR DATOS EN NUEVO ARRAY
                                            if (!isset($capturaUbicacion[0]["empleado"])) {
                                                $capturaUbicacion[0]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                            }
                                            if (!isset($capturaUbicacion[0]["datos"][$d])) {
                                                $capturaUbicacion[0]["datos"][$d] = array();
                                            }
                                            $capturaUbicacion[0]["datos"][$d]["rango"] = $diffRango;
                                            $capturaUbicacion[0]["datos"][$d]["actividad"] = $diffActividad;
                                        } else {
                                            $encontradoCU = true;
                                            $idEmpleado = $tiempoDiaCaptura[$i]["empleado"];
                                            for ($indexNuevo = 0; $indexNuevo < sizeof($capturaUbicacion); $indexNuevo++) {
                                                if ($capturaUbicacion[$indexNuevo]["empleado"] == $idEmpleado) {
                                                    $encontradoCU = false;
                                                    if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                        $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                                    }
                                                    $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                                    $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                                }
                                            }
                                            if ($encontradoCU) {
                                                $indexNuevo = sizeof($capturaUbicacion);
                                                //* UNIR DATOS EN NUEVO ARRAY
                                                if (!isset($capturaUbicacion[$indexNuevo]["empleado"])) {
                                                    $capturaUbicacion[$indexNuevo]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                                }
                                                if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                    $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                                }
                                                $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                                $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                            }
                                        }
                                    } else {
                                        if (isset($tiempoDiaUbicacion[$j]["datos"][$d])) {
                                            $horaUbicacion = $tiempoDiaUbicacion[$j]["datos"][$d];
                                            for ($hora = 0; $hora < 24; $hora++) {
                                                if (isset($horaUbicacion[$hora])) {
                                                    for ($m = 0; $m < 6; $m++) {
                                                        if (isset($horaUbicacion[$hora]["minuto"][$m])) {
                                                            $arrayMinutoUbicacion = $horaUbicacion[$hora]["minuto"][$m];
                                                            for ($indexMinutosU = 0; $indexMinutosU < sizeof($arrayMinutoUbicacion); $indexMinutosU++) {
                                                                $diffRango = $diffRango + $arrayMinutoUbicacion[$indexMinutosU]->rango;
                                                                $diffActividad = $diffActividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            if (sizeof($capturaUbicacion) == 0) {
                                                //* UNIR DATOS EN NUEVO ARRAY
                                                if (!isset($capturaUbicacion[0]["empleado"])) {
                                                    $capturaUbicacion[0]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                                }
                                                if (!isset($capturaUbicacion[0]["datos"][$d])) {
                                                    $capturaUbicacion[0]["datos"][$d] = array();
                                                }
                                                $capturaUbicacion[0]["datos"][$d]["rango"] = $diffRango;
                                                $capturaUbicacion[0]["datos"][$d]["actividad"] = $diffActividad;
                                            } else {
                                                $encontradoCU = true;
                                                $idEmpleado = $tiempoDiaCaptura[$i]["empleado"];
                                                for ($indexNuevo = 0; $indexNuevo < sizeof($capturaUbicacion); $indexNuevo++) {
                                                    if ($capturaUbicacion[$indexNuevo]["empleado"] == $idEmpleado) {
                                                        $encontradoCU = false;
                                                        if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                            $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                                        }
                                                        $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                                        $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                                    }
                                                }
                                                if ($encontradoCU) {
                                                    $indexNuevo = sizeof($capturaUbicacion);
                                                    //* UNIR DATOS EN NUEVO ARRAY
                                                    if (!isset($capturaUbicacion[$indexNuevo]["empleado"])) {
                                                        $capturaUbicacion[$indexNuevo]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                                    }
                                                    if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                        $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                                    }
                                                    $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                                    $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($busquedaEmpleado) {
                        $arrayDatos = [];
                        //* RECORREMOS LA CANTIDAD DE DIAS DEL RANGO
                        for ($d = 0; $d <= $diff->days; $d++) {
                            $diffRango = 0;
                            $diffActividad = 0;
                            //* BUSCAMOS SI EXISTE ESE DIA EN EL ARRAY
                            if (isset($tiempoDiaCaptura[$i]["datos"][$d])) {
                                $horaCaptura = $tiempoDiaCaptura[$i]["datos"][$d];
                                //* RECORREMOS EN FORMATO HORAS
                                for ($hora = 0; $hora < 24; $hora++) {
                                    if (isset($horaCaptura[$hora])) {
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($m = 0; $m < 6; $m++) {
                                            if (isset($horaCaptura[$hora]["minuto"][$m])) {
                                                $arrayMinutoCaptura = $horaCaptura[$hora]["minuto"][$m];
                                                for ($indexMinutosC = 0; $indexMinutosC < sizeof($arrayMinutoCaptura); $indexMinutosC++) {
                                                    $diffRango = $diffRango + $arrayMinutoCaptura[$indexMinutosC]->tiempo_rango;
                                                    $diffActividad = $diffActividad + $arrayMinutoCaptura[$indexMinutosC]->actividad;
                                                }
                                            }
                                        }
                                    }
                                }
                                if (!isset($arrayDatos[$d])) {
                                    $arrayDatos[$d]["rango"] = $diffRango;
                                    $arrayDatos[$d]["actividad"] = $diffActividad;
                                }
                            }
                        }
                        if (sizeof($capturaUbicacion) == 0) {
                            //* UNIR DATOS EN NUEVO ARRAY
                            if (!isset($capturaUbicacion[0]["empleado"])) {
                                $capturaUbicacion[0]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                            }
                            if (!isset($capturaUbicacion[0]["datos"])) {
                                $capturaUbicacion[0]["datos"] = $arrayDatos;
                            }
                        } else {
                            $idEmpleado = $tiempoDiaCaptura[$i]["empleado"];
                            $respuestaFuncion = busquedaEmpleadoA($capturaUbicacion, $idEmpleado);
                            if ($respuestaFuncion) {
                                $indexNuevo = sizeof($capturaUbicacion);
                                //* UNIR DATOS EN NUEVO ARRAY
                                if (!isset($capturaUbicacion[$indexNuevo]["empleado"])) {
                                    $capturaUbicacion[$indexNuevo]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                }
                                if (!isset($capturaUbicacion[$indexNuevo]["datos"])) {
                                    $capturaUbicacion[$indexNuevo]["datos"] = $arrayDatos;
                                }
                            }
                        }
                    }
                }
                //* BUSCAMOS EMPLEADOS DE UBICACION QUE NO ESTAN EN ARRAY NUEVO
                for ($j = 0; $j < sizeof($tiempoDiaUbicacion); $j++) {
                    $idEmpleado = $tiempoDiaUbicacion[$j]["empleado"];
                    $respuestaFuncion = busquedaEmpleadoA($capturaUbicacion, $idEmpleado);
                    if ($respuestaFuncion) {
                        $arrayDatos = [];
                        //* RECORREMOS LA CANTIDAD DE DIAS DEL RANGO
                        for ($d = 0; $d <= $diff->days; $d++) {
                            $diffRango = 0;
                            $diffActividad = 0;
                            //* BUSCAMOS SI EXISTE ESE DIA EN EL ARRAY
                            if (isset($tiempoDiaUbicacion[$j]["datos"][$d])) {
                                $horaUbicacion = $tiempoDiaUbicacion[$j]["datos"][$d];
                                //* RECORREMOS EN FORMATO HORAS
                                for ($hora = 0; $hora < 24; $hora++) {
                                    if (isset($horaUbicacion[$hora])) {
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($m = 0; $m < 6; $m++) {
                                            if (isset($horaUbicacion[$hora]["minuto"][$m])) {
                                                $arrayMinutoUbicacion = $horaUbicacion[$hora]["minuto"][$m];
                                                for ($indexMinutosU = 0; $indexMinutosU < sizeof($arrayMinutoUbicacion); $indexMinutosU++) {
                                                    $diffRango = $diffRango + $arrayMinutoUbicacion[$indexMinutosU]->rango;
                                                    $diffActividad = $diffActividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion;
                                                }
                                            }
                                        }
                                    }
                                }
                                if (!isset($arrayDatos[$d])) {
                                    $arrayDatos[$d]["rango"] = $diffRango;
                                    $arrayDatos[$d]["actividad"] = $diffActividad;
                                }
                            }
                        }
                        if (sizeof($capturaUbicacion) == 0) {
                            //* UNIR DATOS EN NUEVO ARRAY
                            if (!isset($capturaUbicacion[0]["empleado"])) {
                                $capturaUbicacion[0]["empleado"] = $tiempoDiaUbicacion[$j]["empleado"];
                            }
                            if (!isset($capturaUbicacion[0]["datos"])) {
                                $capturaUbicacion[0]["datos"] = $arrayDatos;
                            }
                        } else {
                            $idEmpleado = $tiempoDiaUbicacion[$j]["empleado"];
                            $respuestaFuncion = busquedaEmpleadoA($capturaUbicacion, $idEmpleado);
                            if ($respuestaFuncion) {
                                $indexNuevo = sizeof($capturaUbicacion);
                                //* UNIR DATOS EN NUEVO ARRAY
                                if (!isset($capturaUbicacion[$indexNuevo]["empleado"])) {
                                    $capturaUbicacion[$indexNuevo]["empleado"] = $tiempoDiaUbicacion[$j]["empleado"];
                                }
                                if (!isset($capturaUbicacion[$indexNuevo]["datos"])) {
                                    $capturaUbicacion[$indexNuevo]["datos"] = $arrayDatos;
                                }
                            }
                        }
                    }
                }
            } else { //TODO -> SOLO SI @tiempoDiaCaptura CONTIENE DATOS
                if (sizeof($tiempoDiaCaptura) != 0) {
                    for ($i = 0; $i < sizeof($tiempoDiaCaptura); $i++) {
                        $indexNuevo = sizeof($capturaUbicacion);
                        for ($d = 0; $d <= $diff->days; $d++) { //* Recorremos la cantidad de días por el rango
                            $diffRango = 0;
                            $diffActividad = 0;
                            //* Buscamos si existe el dia en el array
                            if (isset($tiempoDiaCaptura[$i]["datos"][$d])) {
                                $horaCaptura = $tiempoDiaCaptura[$i]["datos"][$d];
                                for ($hora = 0; $hora < 24; $hora++) {
                                    if (isset($horaCaptura[$hora])) {
                                        //* RECORREMOS EN FORMATO MINUTOS
                                        for ($m = 0; $m < 6; $m++) {
                                            if (isset($horaCaptura[$hora]["minuto"][$m])) {
                                                $arrayMinutoCaptura = $horaCaptura[$hora]["minuto"][$m];
                                                for ($indexMinutosC = 0; $indexMinutosC < sizeof($arrayMinutoCaptura); $indexMinutosC++) {
                                                    $diffRango = $diffRango + $arrayMinutoCaptura[$indexMinutosC]->tiempo_rango;
                                                    $diffActividad = $diffActividad + $arrayMinutoCaptura[$indexMinutosC]->actividad;
                                                }
                                            }
                                        }
                                    }
                                }
                                if (sizeof($capturaUbicacion) == 0) {
                                    //* UNIR DATOS EN NUEVO ARRAY
                                    if (!isset($capturaUbicacion[0]["empleado"])) {
                                        $capturaUbicacion[0]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                    }
                                    if (!isset($capturaUbicacion[0]["datos"][$d])) {
                                        $capturaUbicacion[0]["datos"][$d] = array();
                                    }
                                    $capturaUbicacion[0]["datos"][$d]["rango"] = $diffRango;
                                    $capturaUbicacion[0]["datos"][$d]["actividad"] = $diffActividad;
                                } else {
                                    $encontradoCU = true;
                                    $idEmpleado = $tiempoDiaCaptura[$i]["empleado"];
                                    for ($indexNuevo = 0; $indexNuevo < sizeof($capturaUbicacion); $indexNuevo++) {
                                        if ($capturaUbicacion[$indexNuevo]["empleado"] == $idEmpleado) {
                                            $encontradoCU = false;
                                            if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                            }
                                            $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                            $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                        }
                                    }
                                    if ($encontradoCU) {
                                        $indexNuevo = sizeof($capturaUbicacion);
                                        //* UNIR DATOS EN NUEVO ARRAY
                                        if (!isset($capturaUbicacion[$indexNuevo]["empleado"])) {
                                            $capturaUbicacion[$indexNuevo]["empleado"] = $tiempoDiaCaptura[$i]["empleado"];
                                        }
                                        if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                            $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                        }
                                        $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                        $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (sizeof($tiempoDiaUbicacion) != 0) {
                        for ($i = 0; $i < sizeof($tiempoDiaUbicacion); $i++) {
                            $indexNuevo = sizeof($capturaUbicacion);
                            for ($d = 0; $d <= $diff->days; $d++) { //* Recorremos la cantidad de días por el rango
                                $diffRango = 0;
                                $diffActividad = 0;
                                //* Buscamos si existe el dia en el array
                                if (isset($tiempoDiaUbicacion[$i]["datos"][$d])) {
                                    $horaUbicacion = $tiempoDiaUbicacion[$i]["datos"][$d];
                                    //* RECORREMOS EN FORMATO DE HORAS
                                    for ($hora = 0; $hora < 24; $hora++) {
                                        if (isset($horaUbicacion[$hora])) {
                                            //* RECORREMOS EN FORMATO MINUTOS
                                            for ($m = 0; $m < 6; $m++) {
                                                if (isset($horaUbicacion[$hora]["minuto"][$m])) {
                                                    $arrayMinutoUbicacion = $horaUbicacion[$hora]["minuto"][$m];
                                                    for ($indexMinutosU = 0; $indexMinutosU < sizeof($arrayMinutoUbicacion); $indexMinutosU++) {
                                                        $diffRango = $diffRango + $arrayMinutoUbicacion[$indexMinutosU]->rango;
                                                        $diffActividad = $diffActividad + $arrayMinutoUbicacion[$indexMinutosU]->actividad_ubicacion;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (sizeof($capturaUbicacion) == 0) {
                                        //* UNIR DATOS EN NUEVO ARRAY
                                        if (!isset($capturaUbicacion[0]["empleado"])) {
                                            $capturaUbicacion[0]["empleado"] = $tiempoDiaUbicacion[$i]["empleado"];;
                                        }
                                        if (!isset($capturaUbicacion[0]["datos"][$d])) {
                                            $capturaUbicacion[0]["datos"][$d] = array();
                                        }
                                        $capturaUbicacion[0]["datos"][$d]["rango"] = $diffRango;
                                        $capturaUbicacion[0]["datos"][$d]["actividad"] = $diffActividad;
                                    } else {
                                        $encontradoCU = true;
                                        $idEmpleado = $tiempoDiaUbicacion[$i]["empleado"];
                                        for ($indexNuevo = 0; $indexNuevo < sizeof($capturaUbicacion); $indexNuevo++) {
                                            if ($capturaUbicacion[$indexNuevo]["empleado"] == $idEmpleado) {
                                                $encontradoCU = false;
                                                if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                    $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                                }
                                                $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                                $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                            }
                                        }
                                        if ($encontradoCU) {
                                            $indexNuevo = sizeof($capturaUbicacion);
                                            //* UNIR DATOS EN NUEVO ARRAY
                                            if (!isset($capturaUbicacion[$indexNuevo]["empleado"])) {
                                                $capturaUbicacion[$indexNuevo]["empleado"] = $tiempoDiaUbicacion[$i]["empleado"];
                                            }
                                            if (!isset($capturaUbicacion[$indexNuevo]["datos"][$d])) {
                                                $capturaUbicacion[$indexNuevo]["datos"][$d] = array();
                                            }
                                            $capturaUbicacion[$indexNuevo]["datos"][$d]["rango"] = $diffRango;
                                            $capturaUbicacion[$indexNuevo]["datos"][$d]["actividad"] = $diffActividad;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //* **********************
            //* ARRAYS
            $horas = array();
            $dias = array();
            $sumaActividad = array();
            $sumaRango = array();

            for ($i = 0; $i <= $diff->days; $i++) {
                array_push($horas, "00:00:00");
                array_push($sumaActividad, "0");
                array_push($sumaRango, "0");
                $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));

                array_push($dias, date('Y-m-j', $dia));
            }
            foreach ($empleados as $empleado) {
                array_push($respuesta, array(
                    "id" => $empleado->emple_id,
                    "nombre" => $empleado->nombre,
                    "apPaterno" => $empleado->apPaterno,
                    "apMaterno" => $empleado->apMaterno,
                    "horas" => $horas,
                    "fechaF" => $dias,
                    "sumaActividad" => $sumaActividad,
                    "sumaRango" => $sumaRango
                ));
            }
            for ($j = 0; $j < sizeof($respuesta); $j++) {
                for ($i = 0; $i < sizeof($capturaUbicacion); $i++) {
                    if ($respuesta[$j]["id"] == $capturaUbicacion[$i]["empleado"]) {
                        $datos = $capturaUbicacion[$i]["datos"];
                        for ($h = 0; $h <= $diff->days; $h++) {
                            if (isset($datos[$h])) {
                                $respuesta[$j]["horas"][$h] = $datos[$h]["rango"] == 0 ? "00:00:00" : gmdate('H:i:s', $datos[$h]["rango"]);
                                $respuesta[$j]["sumaActividad"][$h] = $datos[$h]["actividad"] == 0 ? "0" : $datos[$h]["actividad"];
                                $respuesta[$j]["sumaRango"][$h] = $datos[$h]["rango"] == 0 ? "0" : $datos[$h]["rango"];
                            }
                        }
                    }
                }
                $respuesta[$j]["horas"] = array_reverse($respuesta[$j]["horas"]);
                $respuesta[$j]["sumaActividad"] = array_reverse($respuesta[$j]["sumaActividad"]);
                $respuesta[$j]["sumaRango"] = array_reverse($respuesta[$j]["sumaRango"]);
            }
        }
        // dd($capturaUbicacion, $respuesta);
        return response()->json($respuesta, 200);
    }

    //* REPORTE PERSONALIZADO
    public function reportePersonalizadoRuta(Request $request)
    {
        $organizacion = organizacion::all('organi_id', 'organi_razonSocial');
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion) {
            if ($usuario_organizacion->rol_id == 4) {
                return view('ruta.reportePersonalizado', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacion->rol_id]);
            }
        } else {
            if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
                return redirect('/elegirorganizacion');
            } else {
                $usuario_organizacionR = DB::table('usuario_organizacion as uso')
                    ->where('uso.organi_id', '=', session('sesionidorg'))
                    ->where('uso.user_id', '=', Auth::user()->id)
                    ->get()->first();
                return view('ruta.reportePersonalizado', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacionR->rol_id]);
            }
        }
    }

    public function buscarEmpleado($id)
    {
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.organi_id', '=', $id)
            ->groupBy('e.emple_id')
            ->get();

        return response()->json($empleados, 200);
    }

    public function obtenerUbicaciones(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $fecha = $request->get('fecha');

        $ubicacion = DB::table('ubicacion as u')
            ->leftJoin('ubicacion_ruta as ur', 'ur.idUbicacion', '=', 'u.id')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'u.idHorario_dias')
            ->select(
                'u.id',
                'u.hora_ini',
                'u.hora_fin',
                'u.actividad_ubicacion as actividad',
                DB::raw("CASE WHEN(ur.idUbicacion) IS NULL THEN 0 ELSE COUNT('ur.idCaptura') END AS cantidadU"),
                DB::raw("CASE WHEN(u.idHorario_dias) IS NULL THEN 0 ELSE DATE(hd.start) END AS horario"),
                DB::raw('TIME_FORMAT(SEC_TO_TIME(u.rango), "%H:%i:%s") as rango')
            )
            ->where('u.idEmpleado', '=', $idEmpleado)
            ->where(DB::raw('DATE(u.hora_ini)'), '=', $fecha)
            ->groupBy('u.id')
            ->get();

        $dispositivos = DB::table('vinculacion_ruta as vr')
            ->select(
                DB::raw("CASE WHEN (vr.modelo) IS NULL THEN 0 ELSE vr.modelo END AS nombreCel")
            )
            ->where('vr.idEmpleado', '=', $idEmpleado)
            ->groupBy('vr.id')
            ->get();

        return response()->json(array("ubicacion" => $ubicacion, "dispositivo" => $dispositivos));
    }
}
