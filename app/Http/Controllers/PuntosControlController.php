<?php

namespace App\Http\Controllers;

use App\punto_control;
use App\punto_control_area;
use App\punto_control_empleado;
use App\punto_control_geo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PuntosControlController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            return view("puntosControl.puntoControl");
        }
    }

    //* DATOS PARA  TABLA DE PUNTOS DE ORGANIZACION
    public function puntosControlOrganizacion()
    {
        $puntosC = DB::table('punto_control as pc')
            ->select(
                'pc.id',
                'pc.descripcion',
                'pc.controlRuta',
                'pc.asistenciaPuerta',
                DB::raw("CASE WHEN(pc.codigoControl) IS NULL THEN 'No definido' ELSE pc.codigoControl END AS codigoP"),
            )
            ->where('pc.organi_id', '=', session('sesionidorg'))
            ->where('pc.estado', '=', 1)
            ->groupBy('pc.id')
            ->get();

        return response()->json($puntosC, 200);
    }

    // * DATOS PARA UN PUNTO DE CONTROL
    public function puntoDeControl(Request $request)
    {
        //* FUNCION PARA AGRUPAR GEOLICALIZACION
        function agruparGeoEnPuntos($array)
        {
            $resultado = array();

            foreach ($array as $punto) {
                if (!isset($resultado[$punto->id])) {
                    $resultado[$punto->id] = (object) array(
                        "id" => $punto->id,
                        "descripcion" => $punto->descripcion,
                        "controlRuta" => $punto->controlRuta,
                        "asistenciaPuerta" => $punto->asistenciaPuerta,
                        "codigoControl" => $punto->codigoControl,
                        "porEmpleados" => $punto->porEmpleados,
                        "porAreas" => $punto->porAreas
                    );
                }
                if (!isset($resultado[$punto->id]->geo)) {
                    $resultado[$punto->id]->geo = array();
                }
                $arrayGeo = array(
                    "idGeo" => $punto->idGeo,
                    "latitud" => $punto->latitud,
                    "longitud" => $punto->longitud,
                    "radio" => $punto->radio,
                    "color" => $punto->color
                );
                array_push($resultado[$punto->id]->geo, $arrayGeo);
            }
            return array_values($resultado);
        }

        $idPunto = $request->get('idPunto');

        $puntoC = DB::table('punto_control as pc')
            ->join('punto_control_geo as pcg', 'pcg.idPuntoControl', '=', 'pc.id')
            ->select(
                'pc.id',
                'pc.descripcion',
                'pc.controlRuta',
                'pc.asistenciaPuerta',
                'pc.codigoControl',
                'pc.porEmpleados',
                'pc.porAreas',
                'pcg.id as idGeo',
                'pcg.latitud',
                'pcg.longitud',
                'pcg.radio',
                'pcg.color'
            )
            ->where('pc.organi_id', '=', session('sesionidorg'))
            ->where('pc.id', '=', $idPunto)
            ->get();

        $puntoC = agruparGeoEnPuntos($puntoC);

        return response()->json($puntoC, 200);
    }

    //* EMPLEADOS POR PUNTOS
    public function empleadosPorPuntos(Request $request)
    {
        $empleadosSinPuntoC = [];
        $respuesta = [];
        $empleadosPuntos = [];
        // TODOS LOS EMPLEADOS
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();
        //* EMPLEADOS CON DICHO PUNTO DE CONTROL
        $empleadosPuntos = DB::table('punto_control as pc')
            ->join('punto_control_empleado as pce', 'pce.idPuntoControl', '=', 'pc.id')
            ->join('empleado as e', 'e.emple_id', '=', 'pce.idEmpleado')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('pc.id', '=', $request->get('idPunto'))
            ->where('pce.estado', '=', 1)
            ->get();

        for ($index = 0; $index < sizeof($empleados); $index++) {
            $estado = true;
            foreach ($empleadosPuntos as $ep) {
                if ($empleados[$index]->emple_id == $ep->emple_id) {
                    $estado = false;
                }
            }
            if ($estado) {
                array_push($empleadosSinPuntoC, $empleados[$index]);
            }
        }
        //* DATOS PARA RESULTADO
        array_push($respuesta, array("select" => $empleadosPuntos, "noSelect" => $empleadosSinPuntoC));

        return response()->json($respuesta, 200);
    }

    //* AREAS POR PUNTOS
    public function areasPorEmpleados(Request $request)
    {
        $areasSinPuntosC = [];
        $respuesta = [];
        $areasPunto = [];
        // TODOS LAS AREAS
        $areas = DB::table('empleado as e')
            ->join('area as a', 'a.area_id', '=', 'e.emple_area')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->groupBy('a.area_id')
            ->get();

        //* AREAS CONDICHA ACTIVIDAD
        $areasPunto = DB::table('punto_control as pc')
            ->join('punto_control_area as pca', 'pca.idPuntoControl', '=', 'pc.id')
            ->join('area as a', 'a.area_id', '=', 'pca.idArea')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('pca.estado', '=', 1)
            ->where('pc.id', '=', $request->get('idPunto'))
            ->groupBy('a.area_id')
            ->get();

        // * RECORRIDO DE AREAS
        for ($index = 0; $index < sizeof($areas); $index++) {
            $estado = true;
            foreach ($areasPunto as $ap) {
                if ($areas[$index]->area_id == $ap->area_id) {
                    $estado = false;
                }
            }
            if ($estado) {
                array_push($areasSinPuntosC, $areas[$index]);
            }
        }
        array_push($respuesta, array("select" => $areasPunto, "noSelect" => $areasSinPuntosC));

        return response()->json($respuesta, 200);
    }

    //* EDITAR PUNTO DE CONTROL
    public function editarPuntoControl(Request $request)
    {
        $empleados = $request->get('empleados');
        $areas = $request->get('areas');

        $buscarCodigo = punto_control::where('codigoControl', '=', $request->get('codigo'))->where('id', '!=', $request->get('id'))->get()->first();

        if (!$buscarCodigo) {
            $puntoControl = punto_control::findOrFail($request->get('id'));
            $puntoControl->codigoControl = $request->get('codigo');
            $puntoControl->controlRuta = $request->get('cr');
            $puntoControl->asistenciaPuerta = $request->get('ap');
            $puntoControl->porEmpleados = $request->get('porEmpleados');
            $puntoControl->porAreas = $request->get('porAreas');
            $puntoControl->save();

            //* BUSCAR EMPLEADOS CON PUNTO DE CONTROL
            $puntosControlEmpleado = punto_control_empleado::where('idPuntoControl', '=', $puntoControl->id)->get();
            //* SI LA ASIGNACION ES POR EMPLEADO
            if ($puntoControl->porEmpleados == 1) {
                if (sizeof($puntosControlEmpleado) == 0) {
                    if (!is_null($empleados)) {
                        foreach ($empleados as $emple) {
                            $punto_control_empleado = new punto_control_empleado();
                            $punto_control_empleado->idEmpleado = $emple;
                            $punto_control_empleado->idPuntoControl = $puntoControl->id;
                            $punto_control_empleado->save();
                        }
                    }
                } else {
                    if (is_null($empleados)) {
                        foreach ($puntosControlEmpleado as $pce) {
                            $pce->estado = 0;
                            $pce->save();
                        }
                    } else {
                        //* BUSCAR EMPLEADOS EN LA TABLA PUNTO CONTROL
                        foreach ($empleados as $e) {
                            $estado = false;
                            for ($index = 0; $index < sizeof($puntosControlEmpleado); $index++) {
                                if ($puntosControlEmpleado[$index]->idEmpleado == $e) {
                                    $estado = true;
                                }
                            }
                            if ($estado) {
                                $pe = punto_control_empleado::where('idPuntoControl', '=', $puntoControl->id)
                                    ->where('idEmpleado', '=', $e)->get()->first();
                                $pe->estado = 1;
                                $pe->save();
                            } else {
                                $nuevoPuentoE = new punto_control_empleado();
                                $nuevoPuentoE->idEmpleado = $e;
                                $nuevoPuentoE->idPuntoControl = $puntoControl->id;
                                $nuevoPuentoE->estado = 1;
                                $nuevoPuentoE->save();
                            }
                        }
                        //* COMPARAR LOS PUNTOS DE CONTROL CON LA LISTA DE EMPLEADOS
                        foreach ($puntosControlEmpleado as $pce) {
                            $estadoB = false;
                            foreach ($empleados as $em) {
                                if ($pce->idEmpleado == $em) {
                                    $estadoB = true;
                                }
                            }
                            if (!$estadoB) {
                                $pce->estado = 0;
                                $pce->save();
                            }
                        }
                    }
                }
            } else {
                if ($puntoControl->porAreas == 1) {
                    //* FOREACH PARA BUSCAR EMPLEADO POR AREAS
                    foreach ($areas as $a) {
                        $empleadoArea = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_area', '=', $a)
                            ->where('e.emple_estado', '=', 1)
                            ->get();
                        //* BUSCAR EMPLEADOS EN PUNTOS CONTROL EMPLEADO
                        foreach ($puntosControlEmpleado as $pcem) {
                            $busqueda = true;
                            foreach ($empleadoArea as $ea) {
                                if ($pcem->idEmpleado == $ea->emple_id) {
                                    $busqueda = false;
                                }
                            }
                            if ($busqueda) {
                                $puntoCE = punto_control_empleado::where('idPuntoControl', '=', $puntoControl->id)
                                    ->where('idEmpleado', '=', $pcem->idEmpleado)->get()->first();
                                $puntoCE->estado = 0;
                                $puntoCE->save();
                            }
                        }
                        //* BUSCAR EMPLEADOS DE AREA EN PUNTO CONTROL EMPLEADO
                        foreach ($empleadoArea as $emA) {
                            $busqueda = true;
                            foreach ($puntosControlEmpleado as $puntCE) {
                                if ($puntCE->idEmpleado == $emA->emple_id) {
                                    $busqueda = false;
                                }
                            }
                            if ($busqueda) {
                                $nuevoPunto_empleado = new punto_control_empleado();
                                $nuevoPunto_empleado->idPuntoControl = $puntoControl->id;
                                $nuevoPunto_empleado->idEmpleado = $emA->emple_id;
                                $nuevoPunto_empleado->estado = 1;
                                $nuevoPunto_empleado->save();
                            } else {
                                $pControlE = punto_control_empleado::where('idPuntoControl', '=', $puntoControl->id)
                                    ->where('idEmpleado', '=', $emA->emple_id)->get()->first();
                                $pControlE->estado = 1;
                                $pControlE->save();
                            }
                        }
                        //* BUSCAR PUNTOS CONTROL AREAS
                        $buscarPuntoArea = punto_control_area::where('idPuntoControl', '=', $puntoControl->id)
                            ->where('idArea', '=', $a)->get()->first();
                        if (!$buscarPuntoArea) {
                            //* REGISTRAR PUNTOS CONTROL AREA
                            $punto_control_area = new punto_control_area();
                            $punto_control_area->idPuntoControl = $puntoControl->id;
                            $punto_control_area->idArea = $a;
                            $punto_control_area->estado = 1;
                            $punto_control_area->save();
                        } else {
                            if ($buscarPuntoArea->estado == 0) {
                                $buscarPuntoArea->estado = 1;
                                $buscarPuntoArea->estado->save();
                            }
                        }
                    }
                    $puntoControlA = punto_control_area::where('idPuntoControl', '=', $puntoControl->id)->get();
                    foreach ($puntoControlA as $area) {
                        $busqueda = true;
                        foreach ($areas as $a) {
                            if ($area->idArea == $a) {
                                $busqueda = false;
                            }
                        }
                        if ($busqueda) {
                            $area->estado = 0;
                            $area->save();
                        }
                    }
                } else {
                    //* DESACTIVAMOS POR EMPLEADOS
                    foreach ($puntosControlEmpleado as $pce) {
                        $pce->estado = 0;
                        $pce->save();
                    }
                    //* DESACTIVAMOS POR AREAS
                    $punto_area = punto_control_area::where('idPuntoControl', '=', $puntoControl->id)->get();
                    foreach ($punto_area as $pa) {
                        $pa->estado = 0;
                        $pa->save();
                    }
                }
            }
            // * ACTUALIZAR O INSERTAR GEOLICALIZACION
            // dd($request->get('puntosGeo'));
            if (!empty($request->get('puntosGeo'))) {
                foreach ($request->get('puntosGeo') as $punto) {
                    // * BUSCAR PUNTO CONTROL GEO
                    $puntoControlGeo = punto_control_geo::where('id', '=', $punto["idGeo"])->get()->first();
                    if ($puntoControlGeo) {
                        if (!is_null($punto["latitud"]) && !is_null($punto["longitud"]) && !is_null($punto["radio"])) {
                            $puntoControlGeo->latitud = $punto["latitud"];
                            $puntoControlGeo->longitud = $punto["longitud"];
                            $puntoControlGeo->radio = $punto["radio"];
                            $puntoControlGeo->color = $punto["color"];
                            $puntoControlGeo->save();
                        } else {
                            $puntoControlGeo->delete();
                        }
                    } else {
                        if (!is_null($punto["latitud"]) && !is_null($punto["longitud"]) && !is_null($punto["radio"])) {
                            $nuevoPuntoGeo = new punto_control_geo();
                            $nuevoPuntoGeo->latitud = $punto["latitud"];
                            $nuevoPuntoGeo->longitud = $punto["longitud"];
                            $nuevoPuntoGeo->radio = $punto["radio"];
                            $nuevoPuntoGeo->color = $punto["color"];
                            $nuevoPuntoGeo->idPuntoControl = $puntoControl->id;
                            $nuevoPuntoGeo->save();
                        }
                    }
                }
            }
            return response()->json($request->get('id'), 200);
        } else {
            return 0;
        }
    }
}
