<?php

namespace App\Http\Controllers;

use App\punto_control;
use App\punto_control_area;
use App\punto_control_detalle;
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
                DB::raw("CASE WHEN(pc.codigoControl) IS NULL THEN 'No definido' ELSE pc.codigoControl END AS codigoP")
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
                        "porAreas" => $punto->porAreas,
                        "verificacion" => $punto->verificacion
                    );
                }
                // * GEOLICALIZACION
                if (!isset($resultado[$punto->id]->geo)) {
                    $resultado[$punto->id]->geo = array();
                }
                if (!is_null($punto->idGeo)) {
                    $arrayGeo = array(
                        "idGeo" => $punto->idGeo,
                        "latitud" => $punto->latitud,
                        "longitud" => $punto->longitud,
                        "radio" => $punto->radio,
                        "color" => $punto->color
                    );
                    array_push($resultado[$punto->id]->geo, $arrayGeo);
                }
            }
            return array_values($resultado);
        }

        $idPunto = $request->get('idPunto');

        $puntoC = DB::table('punto_control as pc')
            ->leftJoin('punto_control_geo as pcg', 'pcg.idPuntoControl', '=', 'pc.id')
            ->select(
                'pc.id',
                'pc.descripcion',
                'pc.controlRuta',
                'pc.asistenciaPuerta',
                'pc.codigoControl',
                'pc.porEmpleados',
                'pc.porAreas',
                'pc.verificacion',
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
        // * BUSCAR DETALLES
        foreach ($puntoC as $pc) {
            $arrayDetalle = [];
            $detalle = punto_control_detalle::where('idPuntoControl', '=', $pc->id)->get();
            foreach ($detalle as $d) {
                array_push($arrayDetalle, array("idDetalle" => $d->id, "detalle" => $d->descripcion));
            }
            $pc->detalles = $arrayDetalle;
        }

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

        $buscarCodigo = punto_control::where('codigoControl', '=', $request->get('codigo'))
            ->where('id', '!=', $request->get('id'))
            ->where('organi_id', '=', session('sesionidorg'))
            ->whereNotNull('codigoControl')
            ->get()
            ->first();

        if (!$buscarCodigo) {
            $puntoControl = punto_control::findOrFail($request->get('id'));
            $puntoControl->codigoControl = $request->get('codigo');
            $puntoControl->controlRuta = $request->get('cr');
            $puntoControl->asistenciaPuerta = $request->get('ap');
            $puntoControl->porEmpleados = $request->get('porEmpleados');
            $puntoControl->porAreas = $request->get('porAreas');
            $puntoControl->verificacion = $request->get('verificacion');
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
            if (!empty($request->get('descripciones'))) {
                foreach ($request->get('descripciones') as $desc) {
                    $puntoControlDet = punto_control_detalle::where('id', '=', $desc["id"])->get()->first();
                    if ($puntoControlDet) {
                        if (!is_null($desc["descripcion"])) {
                            $puntoControlDet->descripcion = $desc["descripcion"];
                            $puntoControlDet->save();
                        } else {
                            $puntoControlDet->delete();
                        }
                    } else {
                        if (!is_null($desc["descripcion"])) {
                            $nuevoPuntoDet = new punto_control_detalle();
                            $nuevoPuntoDet->descripcion = $desc["descripcion"];
                            $nuevoPuntoDet->idPuntoControl = $puntoControl->id;
                            $nuevoPuntoDet->save();
                        }
                    }
                }
            }
            return response()->json($request->get('id'), 200);
        } else {
            return 0;
        }
    }

    // * SELECT DE PUNTOS DE CONTROL
    public function listaPuntoControl()
    {
        $puntoControl = DB::table('punto_control as pc')
            ->select('pc.id as idPunto', 'pc.descripcion')
            ->where('pc.organi_id', '=', session('sesionidorg'))
            ->where('pc.estado', '=', 1)
            ->where('pc.controlRuta', '=', 1)
            ->get();

        return response()->json($puntoControl, 200);
    }

    // * DATOS DE PUNTO PARA CONTROL
    public function datosPuntoControl(Request $request)
    {
        $idPunto = $request->get('idP');
        $punto = punto_control::findOrFail($idPunto);
        return response()->json($punto, 200);
    }

    // * ASIGNACION DE PUNTOS MASIVO
    public function asignacionDePuntos(Request $request)
    {
        $empleados = $request->get('empleados');
        $puntoControl = $request->get('idPunto');
        $areas = $request->get('areas');
        $porEmpleados = $request->get('porEmpleados');
        $porAreas = $request->get('porAreas');

        $puntoB = punto_control::findOrFail($puntoControl);
        if ($puntoB) {
            $puntoB->porEmpleados = $porEmpleados;
            $puntoB->porAreas = $porAreas;
            $puntoB->save();
        }
        // * ACTUALIZACION PUNTO DE CONTROL DE EMPLEADOS
        $punto_empleado = punto_control_empleado::where('idPuntoControl', '=', $puntoB->id)->get();
        if ($puntoB->porEmpleados == 1) {
            // * ARRAY DE EMPLEADOS SI ESTA VACIO
            if (is_null($empleados)) {
                foreach ($punto_empleado as $pe) {
                    $pe->estado = 0;
                    $pe->save();
                }
            } else {
                if (sizeof($punto_empleado) == 0) {
                    foreach ($empleados as $emple) {
                        // * ASIGNAR EMPLEADOS A PUNTO DE CONTROL
                        $nuevoPuntoE = new punto_control_empleado();
                        $nuevoPuntoE->idPuntoControl = $puntoB->id;
                        $nuevoPuntoE->idEmpleado = $emple;
                        $nuevoPuntoE->estado = 1;
                        $nuevoPuntoE->save();
                    }
                } else {
                    // * BUSCAR EMPLEADOS EN LA TABLA PUNTO CONTROL EMPLEADO
                    foreach ($empleados as $emple) {
                        $estado = true;
                        for ($index = 0; $index < sizeof($punto_empleado); $index++) {
                            if ($punto_empleado[$index]->idEmpleado == $emple) {
                                $estado = false;
                            }
                        }
                        if ($estado) {
                            $nuevoPuntoE = new punto_control_empleado();
                            $nuevoPuntoE->idPuntoControl = $puntoB->id;
                            $nuevoPuntoE->idEmpleado = $emple;
                            $nuevoPuntoE->estado = 0;
                            $nuevoPuntoE->save();
                        } else {
                            $pe = punto_control_empleado::where('idPuntoControl', '=', $puntoB->id)
                                ->where('idEmpleado', '=', $emple)->get()->first();
                            $pe->estado = 1;
                            $pe->save();
                        }
                    }
                    // * COMPARAR LOS PUNTOS DE CONTROL CON LA LISTA DE EMPLEADOS
                    foreach ($punto_empleado as $puntoE) {
                        $estadoB = true;
                        foreach ($empleados as $emple) {
                            if ($puntoE->idEmpleado == $emple) {
                                $estadoB = false;
                            }
                        }
                        if ($estadoB) {
                            $puntoE->estado = 0;
                            $puntoE->save();
                        }
                    }
                }
            }
            // * DESACTIVAMOS POR AREAS
            $punto_area = punto_control_area::where('idPuntoControl', '=', $puntoB->id)->get();
            foreach ($punto_area as $pa) {
                $pa->estado = 0;
                $pa->save();
            }
        } else {
            if ($puntoB->porAreas == 1) {
                // * FOREACH PARA BUSCAR EMPLEADO POR AREAS
                foreach ($areas as $a) {
                    $empleadoArea = DB::table('empleado as e')
                        ->select('e.emple_id')
                        ->where('e.emple_area', '=', $a)
                        ->where('e.emple_estado', '=', 1)
                        ->get();
                    // * BUSCAR EMPLEADOS EN ACTIVIDAD EMPLEADO
                    foreach ($punto_empleado as $puntE) {
                        $busqueda = true;
                        foreach ($empleadoArea as $ea) {
                            if ($puntE->idEmpleado == $ea->emple_id) {
                                $busqueda = false;
                            }
                        }
                        if ($busqueda) {
                            $pe = punto_control_empleado::where('idPuntoControl', '=', $puntoB->id)
                                ->where('idEmpleado', '=', $puntE->idEmpleado)->get()->first();
                            $pe->estado = 0;
                            $pe->save();
                        }
                    }
                    // * BUSCAR EMPLEADOS DE AREA EN PUNTO CONTROL EMPLEADO
                    foreach ($empleadoArea as $ea) {
                        $busqueda = true;
                        foreach ($punto_empleado as $puntEm) {
                            if ($puntEm->idEmpleado == $ea->emple_id) {
                                $busqueda = false;
                            }
                        }
                        if ($busqueda) {
                            $nuevoPuntoEmpleado = new punto_control_empleado();
                            $nuevoPuntoEmpleado->idPuntoControl = $puntoB->id;
                            $nuevoPuntoEmpleado->idEmpleado = $ea->emple_id;
                            $nuevoPuntoEmpleado->estado = 1;
                            $nuevoPuntoEmpleado->save();
                        } else {
                            $pe = punto_control_empleado::where('idPuntoControl', '=', $puntoB->id)
                                ->where('idEmpleado', '=', $ea->emple_id)->get()->first();
                            $pe->estado = 1;
                            $pe->save();
                        }
                    }
                    // * BUSCAR PUNTO CONTROL AREA
                    $buscarPuntoArea = punto_control_area::where('idPuntoControl', '=', $puntoB->id)
                        ->where('idArea', '=', $a)->get()->first();
                    if (!$buscarPuntoArea) {
                        $punto_area = new punto_control_area();
                        $punto_area->idPuntoControl = $puntoB->id;
                        $punto_area->idArea = $a;
                        $punto_area->estado = 1;
                        $punto_area->save();
                    } else {
                        if ($buscarPuntoArea->estado == 0) {
                            $buscarPuntoArea->estado = 1;
                            $buscarPuntoArea->save();
                        }
                    }
                }
                $punto_area = punto_control_area::where('idPuntoControl', '=', $puntoB->id)->get();
                foreach ($punto_area as $area) {
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
                foreach ($punto_empleado as $pe) {
                    $pe->estado = 0;
                    $pe->save();
                }
                // * DESACTIVAMOS POR AREAS
                $punto_area = punto_control_area::where('idPuntoControl', '=', $puntoB->id)->get();
                foreach ($punto_area as $pa) {
                    $pa->estado = 0;
                    $pa->save();
                }
            }
        }
        return response()->json($empleados, 200);
    }

    // * SELECT EMPLEADO EM REGISTRAR
    public function empleadosPuntos()
    {
        // TODOS LOS EMPLEADOS
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();

        return response()->json($empleados, 200);
    }

    // * SELECT AREAS EN REGISTRAR
    public function areasPuntos()
    {
        // TODOS LAS AREAS
        $areas = DB::table('empleado as e')
            ->join('area as a', 'a.area_id', '=', 'e.emple_area')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->groupBy('a.area_id')
            ->get();

        return response()->json($areas, 200);
    }

    // * REGISTRAR PUNTO DE CONTROL
    public function registrarPunto(Request $request)
    {
        $puntoBuscar = punto_control::where('descripcion', '=', $request->get('descripcion'))
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()
            ->first();
        if ($puntoBuscar) {
            return response()->json(array("estado" => 1, "punto" => $puntoBuscar), 200);
        }
        $puntoB = punto_control::where('codigoControl', '=', $request->get('codigo'))
            ->where('organi_id', '=', session('sesionidorg'))
            ->whereNotNull('codigoControl')
            ->get()
            ->first();
        if ($puntoB) {
            return response()->json(array("estado" => 0, "punto" => $puntoB), 200);
        }
        $puntoControl = new punto_control();
        $puntoControl->descripcion = $request->get('descripcion');
        $puntoControl->controlRuta = $request->get('cr');
        $puntoControl->asistenciaPuerta = $request->get('ap');
        $puntoControl->organi_id = session('sesionidorg');
        $puntoControl->codigoControl = $request->get('codigo');
        $puntoControl->porEmpleados = $request->get('porEmpleados');
        $puntoControl->porAreas = $request->get('porAreas');
        $puntoControl->verificacion = $request->get('verificacion');
        $puntoControl->save();

        $idPunto = $puntoControl->id;
        $listaE = $request->get('empleados');
        $listaA = $request->get('areas');

        if ($puntoControl->porEmpleados == 1) {
            // * ASIGNAR EMPLEADOS A NUEVO PUNTO CONTROL
            if (!is_null($listaE)) {
                foreach ($listaE as $le) {
                    $punto_empleado = new punto_control_empleado();
                    $punto_empleado->idPuntoControl = $idPunto;
                    $punto_empleado->idEmpleado = $le;
                    $punto_empleado->estado = 1;
                    $punto_empleado->save();
                }
            }
        } else {
            if ($puntoControl->porAreas == 1) {
                if (!is_null($listaA)) {
                    foreach ($listaA as $la) {
                        $empleadosArea = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_area', '=', $la)
                            ->where('e.emple_estado', '=', 1)
                            ->get();
                        foreach ($empleadosArea as $ea) {
                            $punto_empleado = new punto_control_empleado();
                            $punto_empleado->idPuntoControl = $idPunto;
                            $punto_empleado->idEmpleado = $ea->emple_id;
                            $punto_empleado->estado = 1;
                            $punto_empleado->save();
                        }
                        // * REGISTRAR PUNTO AREAS
                        $punto_area = new punto_control_area();
                        $punto_area->idPuntoControl = $idPunto;
                        $punto_area->idArea = $la;
                        $punto_area->estado = 1;
                        $punto_area->save();
                    }
                }
            }
        }
        if (!empty($request->get('puntosGeo'))) {
            foreach ($request->get('puntosGeo') as $punto) {
                if (!is_null($punto["latitud"]) && !is_null($punto["longitud"]) && !is_null($punto["radio"])) {
                    $nuevoPuntoGeo = new punto_control_geo();
                    $nuevoPuntoGeo->latitud = $punto["latitud"];
                    $nuevoPuntoGeo->longitud = $punto["longitud"];
                    $nuevoPuntoGeo->radio = $punto["radio"];
                    $nuevoPuntoGeo->color = $punto["color"];
                    $nuevoPuntoGeo->idPuntoControl = $idPunto;
                    $nuevoPuntoGeo->save();
                }
            }
        }
        if (!empty($request->get('descripciones'))) {
            foreach ($request->get('descripciones') as $desc) {
                if (!is_null($desc["descripcion"])) {
                    $nuevoPuntoDet = new punto_control_detalle();
                    $nuevoPuntoDet->descripcion = $desc["descripcion"];
                    $nuevoPuntoDet->idPuntoControl = $idPunto;
                    $nuevoPuntoDet->save();
                }
            }
        }
        return response()->json($puntoControl, 200);
    }

    // * RECUPERAR PUNTO
    public function recuperarPunto(Request $request)
    {
        $idPunto = $request->get('id');
        $punto = punto_control::findOrFail($idPunto);
        if ($punto) {
            $punto->estado = 1;
            $punto->save();
        }

        return response()->json($punto, 200);
    }

    // * CAMBIAR ESTADO PUNTO CONTROL
    public function cambiarEstadoPunto(Request $request)
    {
        $idPunto = $request->get('id');
        $punto = punto_control::findOrFail($idPunto);
        if ($punto) {
            $punto->estado = 0;
            $punto->save();
        }

        return response()->json($punto, 200);
    }

    // * CAMBIAR ESTADO DE SWITCH
    public function cambiarEstadoActividadControl(Request $request)
    {
        $idActividad = $request->get('id');
        $control = $request->get('control');
        // BUSCAMOS ACTIVIDAD
        $punto = punto_control::findOrFail($idActividad);
        if ($punto) {
            if ($control == "CR") {
                $punto->controlRuta = $request->get('valor');
            }
            if ($control == "AP") {
                $punto->asistenciaPuerta = $request->get('valor');
            }
            $punto->save();
        }

        return response()->json($punto, 200);
    }
}
