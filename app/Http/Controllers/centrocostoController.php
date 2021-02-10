<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\centro_costo;
use App\centrocosto_empleado;
use App\empleado;
use App\historial_centro_costo;
use App\historial_empleado;
use App\marcacion_puerta;
use App\marcacion_tareo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class centrocostoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function store(Request $request)
    {
        $centro_costo = new centro_costo();
        $centro_costo->centroC_descripcion = $request->get('centroC_descripcion');
        $centro_costo->organi_id = session('sesionidorg');
        $centro_costo->save();
        return $centro_costo;
    }

    // * ************************ MANTENEDOR DE CENTRO COSTO ******************

    public function index()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            return view("CentroCosto.centrocosto");
        }
    }

    // * LSITAS DE CENTROS DE COSTOS POR ORGANIZACION
    public function listaCentroCosto()
    {
        $centroC = DB::table('centro_costo as c')
            ->select(
                'c.centroC_id as id',
                DB::raw("CASE WHEN (c.codigo) is null THEN 'No definido' ELSE c.codigo END as codigo"),
                'c.centroC_descripcion as descripcion',
                'c.asistenciaPuerta',
                'c.modoTareo'
            )
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->where('estado', '=', 1)
            ->groupBy('c.centroC_id')
            ->orderBy('c.centroC_descripcion', 'ASC')
            ->get();

        // : ************************ BUSCAR USO DE CENTRO COSTO ***********************
        foreach ($centroC as $c) {
            // : BUSCAR SI CONTIENE EMPLEADOS
            $centroEmpleado = DB::table('centrocosto_empleado as ce')
                ->join('empleado as e', 'e.emple_id', 'ce.idEmpleado')
                ->select('e.emple_estado', 'e.emple_id')
                ->where('ce.idCentro', '=', $c->id)
                ->where('e.emple_estado', '=', 1)
                ->where('ce.estado', '=', 1)
                ->get();
            if (sizeof($centroEmpleado) != 0) {
                foreach ($centroEmpleado as $ce) {
                    if ($ce->emple_estado == 1) {
                        $c->respuesta = 1;
                    } else {
                        // : HISTORIAL EMPLEADO
                        $historialEmpleado = historial_empleado::select('fecha_baja')
                            ->latest('idhistorial_empleado')
                            ->where('emple_id', '=', $ce->emple_id)
                            ->get()
                            ->first();
                        // : CENTRO DE COSTO EMPLEADO
                        $centroE = centrocosto_empleado::findOrFail($c->id);
                        $centroE->fecha_baja = $historialEmpleado->fecha_baja;
                        $centroE->estado = 0;
                        $centroE->save();
                    }
                }
            } else {
                // : BUSCAR EN REPORTE DE ASISTENCIA EN PUERTA
                $marcacion = marcacion_puerta::where('centC_id', '=', $c->id)->get()->first();
                if ($marcacion) {
                    $c->respuesta = 1;
                } else {
                    // : BUSCAR EN REPORTE TAREO
                    $tareo = marcacion_tareo::where('centroC_id', '=', $c->id)->get()->first();
                    if ($tareo) {
                        $c->respuesta = 1;
                    } else {
                        $c->respuesta = 0;
                    }
                }
            }
        }

        return response()->json($centroC, 200);
    }

    // * MOSTRAR CENTRO DE COSTO POR ID
    public function centroCosto(Request $request)
    {
        $id = $request->get('id');
        $centro = centro_costo::select(
            'centroC_descripcion as descripcion',
            'centroC_id as id',
            'codigo',
            'porEmpleado',
            'asistenciaPuerta',
            'modoTareo'
        )
            ->where('centroC_id', '=', $id)->get()->first();

        return response()->json($centro, 200);
    }

    // * EDITAR CENTRO COSTO
    public function actualizarCentro(Request $request)
    {
        $id = $request->get('id');
        $empleados = $request->get('empleados');
        $codigo = $request->get('codigo');
        $porEmpleado = $request->get('porEmpleado');
        $asistenciaPuerta = $request->get('asistenciaPuerta');
        $modoTareo = $request->get('modoTareo');
        // : BUSCAR CENTRO DE COSTO CON EL MISMO CODIGO
        $buscarCodigoCentro = centro_costo::where('codigo', '=', $codigo)
            ->where('centroC_id', '!=', $id)
            ->whereNotNull('codigo')
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('estado', '=', 1)
            ->get()
            ->first();
        if (!$buscarCodigoCentro) {
            $centro = centro_costo::findOrFail($id);
            $centro->codigo = $codigo;
            $centro->porEmpleado = $porEmpleado;
            $centro->asistenciaPuerta = $asistenciaPuerta;
            $centro->modoTareo = $modoTareo;
            $centro->save();
            // * EMPLEADOS EN CENTRO DE COSTO
            $empleadoCentro = centrocosto_empleado::where('idCentro', '=', $centro->centroC_id)->where('estado', '=', 1)->get();
            if ($centro->porEmpleado == 1) {
                // * SI ARRAY EMPLEADOS ESTA VACIO
                if (is_null($empleados)) {
                    foreach ($empleadoCentro as $ec) {
                        $emp = centrocosto_empleado::where('id', '=', $ec->id)->get()->first();
                        $emp->estado = 0;
                        $emp->fecha_baja = Carbon::now();
                        $emp->save();
                    }
                } else {
                    // * BUSCAR EMPLEADOS CON CENTRO COSTO
                    foreach ($empleados as $e) {
                        $estado = true;
                        for ($index = 0; $index < sizeof($empleadoCentro); $index++) {
                            if ($empleadoCentro[$index]->idEmpleado == $e) {
                                $estado = false;
                            }
                        }
                        if ($estado) {
                            $nuevoCentroCosto = new centrocosto_empleado();
                            $nuevoCentroCosto->idCentro = $centro->centroC_id;
                            $nuevoCentroCosto->idEmpleado = $e;
                            $nuevoCentroCosto->fecha_alta = Carbon::now();
                            $nuevoCentroCosto->save();
                        }
                    }

                    // * COMPARAR EMPLEADOS CENTRO CON LISTA DE EMPLEADOS
                    foreach ($empleadoCentro as $ec) {
                        $estadoB = true;
                        foreach ($empleados as $em) {
                            if ($ec->idEmpleado == $em) {
                                $estadoB = false;
                            }
                        }
                        if ($estadoB) {
                            $emp = centrocosto_empleado::where('id', '=', $ec->id)->get()->first();
                            $emp->estado = 0;
                            $emp->fecha_baja = Carbon::now();
                            $emp->save();
                        }
                    }
                }
            } else {
                foreach ($empleadoCentro as $ec) {
                    $emp = centrocosto_empleado::where('id', '=', $ec->id)->get()->first();
                    $emp->estado = 0;
                    $emp->fecha_baja = Carbon::now();
                    $emp->save();
                }
            }
            return response()->json($id, 200);
        } else {
            return response()->json(array("respuesta" => 1, "mensaje" => "Ya existe un centro de costo con este código"), 200);
        }
    }

    // * LISTA DE CENTRO DE COSTOS PARA ASIGNACION
    public function listaCentroC()
    {
        $centroC = centro_costo::select('centroC_id as id', 'centroC_descripcion as descripcion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('porEmpleado', '=', 1)
            ->where('estado', '=', 1)
            ->get();

        return response()->json($centroC, 200);
    }

    // * EMPLEADOS POR CENTRO DE COSTO
    public function empleadosCentros(Request $request)
    {
        $id = $request->get('id');
        $empleadoSinCentro = [];
        $centro = centro_costo::findOrFail($id);
        // TODO LOS EMPLEADOS
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();

        // * EMPLEADOS EN CENTRO DE COSTO
        $empleadoCentro = DB::table('centro_costo as c')
            ->join('centrocosto_empleado as ce', 'ce.idCentro', '=', 'c.centroC_id')
            ->join('empleado as e', 'e.emple_id', '=', 'ce.idEmpleado')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('c.centroC_id', '=', $centro->centroC_id)
            ->where('ce.estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->get();

        for ($index = 0; $index < sizeof($empleados); $index++) {
            $estado = true;
            foreach ($empleadoCentro as $ec) {
                if ($empleados[$index]->emple_id == $ec->emple_id) {
                    $estado = false;
                }
            }
            if ($estado) {
                array_push($empleadoSinCentro, $empleados[$index]);
            }
        }

        return response()->json(array("select" => $empleadoCentro, "noSelect" => $empleadoSinCentro), 200);
    }

    // * ASIGNAR CENTROS
    public function asignarCentros(Request $request)
    {
        $id = $request->get('id');
        $empleados = $request->get('empleados');

        $centro = centro_costo::findOrFail($id);

        // * EMPLEADOS EN CENTRO DE COSTO
        $empleadoCentro = centrocosto_empleado::where('idCentro', '=', $centro->centroC_id)->where('estado', '=', 1)->get();
        // * SI ARRAY EMPLEADOS ESTA VACIO
        if (is_null($empleados)) {
            foreach ($empleadoCentro as $ec) {
                $emp = centrocosto_empleado::where('id', '=', $ec->id)->get()->first();
                $emp->estado = 0;
                $emp->fecha_baja = Carbon::now();
                $emp->save();
            }
        } else {
            // * BUSCAR EMPLEADOS CON CENTRO COSTO
            foreach ($empleados as $e) {
                $estado = true;
                for ($index = 0; $index < sizeof($empleadoCentro); $index++) {
                    if ($empleadoCentro[$index]->idEmpleado == $e) {
                        $estado = false;
                    }
                }
                if ($estado) {
                    $nuevoCentroCosto = new centrocosto_empleado();
                    $nuevoCentroCosto->idCentro = $centro->centroC_id;
                    $nuevoCentroCosto->idEmpleado = $e;
                    $nuevoCentroCosto->fecha_alta = Carbon::now();
                    $nuevoCentroCosto->save();
                }
            }

            // * COMPARAR EMPLEADOS CENTRO CON LISTA DE EMPLEADOS
            foreach ($empleadoCentro as $ec) {
                $estadoB = true;
                foreach ($empleados as $em) {
                    if ($ec->idEmpleado == $em) {
                        $estadoB = false;
                    }
                }
                if ($estadoB) {
                    $emp = centrocosto_empleado::where('id', '=', $ec->id)->get()->first();
                    $emp->estado = 0;
                    $emp->fecha_baja = Carbon::now();
                    $emp->save();
                }
            }
        }

        return response()->json($id, 200);
    }

    // * LISTA DE EMPLEADOS EN REGISTAR
    public function listaEmpleados()
    {
        // TODO LOS EMPLEADOS
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select(
                'e.emple_id',
                'p.perso_nombre as nombre',
                'p.perso_apPaterno as apPaterno',
                'p.perso_apMaterno as apMaterno'
            )
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();

        return response()->json($empleados, 200);
    }

    // * AGREGAR CENTROS DE COSTOS
    public function agregarCentroC(Request $request)
    {
        // : BUSCAR COINCIDENCIAS CON NOMBRE DE CENTRO DE COSTOS -> ATRIBUTO DESCRIPCION = 1
        $buscarCentroDescripcion = centro_costo::where('centroC_descripcion', '=', $request->get('descripcion'))
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()
            ->first();
        if ($buscarCentroDescripcion) {
            // : ALERTA DE CENTRO DE COSTO ACTIVO CON MISMO NOMBRE
            if ($buscarCentroDescripcion->estado == 1) {
                return response()->json(array(
                    "respuesta" => 1,
                    "campo" => 1,
                    "mensaje" => "Ya existe un centro costo con este nombre."
                ), 200);
            } else {
                // : ALERTA DE CENTRO DE COSTO INACTIVO CON MISMO NOMBRE
                return response()->json(array(
                    "respuesta" => 0,
                    "campo" => 1,
                    "mensaje" => "Ya existe un centro costo inactivo con este nombre. ¿Desea recuperarla si o no?",
                    "id" => $buscarCentroDescripcion->centroC_id
                ), 200);
            }
        }
        // : BUSCAR COINCIDENCIAS CON CODIGO DE CENTRO DE COSTOS -> ATRIBUTO CODIGO = 2
        $buscarCentroCodigo = centro_costo::where('codigo', '=', $request->get('codigo'))
            ->whereNotNull('codigo')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()
            ->first();
        if ($buscarCentroCodigo) {
            // : ALERTA DE CENTRO DE COSTO ACTIVO CON MISMO CODIGO
            if ($buscarCentroCodigo->estado == 1) {
                return response()->json(array(
                    "respuesta" => 1,
                    "campo" => 2,
                    "mensaje" => "Ya existe un centro costo con este código."
                ), 200);
            } else {
                // : ALERTA DE CENTRO DE COSTO INACTIVO CON MISMO CÓDIGO
                return response()->json(array(
                    "respuesta" => 0,
                    "campo" => 2,
                    "mensaje" => "Ya existe un centro costo inactivo con este código. ¿Desea recuperarla si o no?",
                    "id" => $buscarCentroCodigo->centroC_id
                ), 200);
            }
        }
        // : AGREGAMOS NUEVO CENTRO COSTO
        $centro = new centro_costo();
        $centro->centroC_descripcion = $request->get('descripcion');
        $centro->codigo = $request->get('codigo');
        $centro->porEmpleado = $request->get('porEmpleado');
        $centro->asistenciaPuerta = $request->get('asistenciaPuerta');
        $centro->modoTareo = $request->get('modoTareo');
        $centro->organi_id = session('sesionidorg');
        $centro->save();
        // : ID DE CENTRO COSTO
        $idCentro = $centro->centroC_id;
        // : EMPLEADOS
        $empleados = $request->get('empleados');
        if ($centro->porEmpleado == 1) {
            if (!is_null($empleados)) {
                foreach ($empleados as $e) {
                    $centroEmpleado = new centrocosto_empleado();
                    $centroEmpleado->idCentro = $idCentro;
                    $centroEmpleado->idEmpleado = $e;
                    $centroEmpleado->fecha_alta = Carbon::now();
                    $centroEmpleado->save();
                }
            }
        }
        // : HISTORIAL DE CENTRO COSTO
        $historialCentroCosto = new historial_centro_costo();
        $historialCentroCosto->idCentro = $idCentro;
        $historialCentroCosto->fechaAlta = Carbon::now();
        $historialCentroCosto->save();

        return response()->json($idCentro, 200);
    }

    // * RECUPERAR CENTRO DE COSTO
    public function recuperarCentro(Request $request)
    {
        $id = $request->get('id');
        // : TABLA DE CENTRO DE COSTO
        $centro = centro_costo::findOrFail($id);
        $centro->estado = 1;
        $centro->save();
        // : TABLA DE HISTORIAL DE CENTRO DE COSTO
        $historial = new historial_centro_costo();
        $historial->idCentro = $id;
        $historial->fechaAlta = Carbon::now();
        $historial->save();
        // : TABLA DE CENTRO DE COSTO EMPLEADO
        $centroEmpleado = centrocosto_empleado::where('idCentro', '=', $id)->get();

        return response()->json($centro->centroC_id, 200);
    }

    // * CAMBIAR ESTADO CENTRO DE COSTO
    public function eliminarCentro(Request $request)
    {
        $id = $request->get('id');

        // : TABLA DE CENTRO DE COSTO
        $centro = centro_costo::findOrFail($id);
        $centro->estado = 0;
        $centro->save();
        // : TABLA DE HISTORIAL DE CENTRO DE COSTO
        $historial = historial_centro_costo::where('idCentro', '=', $id)->whereNull('fechaBaja')->get()->first();
        $historial->fechaBaja = Carbon::now();
        // : TABLA DE CENTRO DE COSTO EMPLEADO
        $centroEmpleado = centrocosto_empleado::where('idCentro', '=', $id)->where('estado', '=', 1)->get();
        foreach ($centroEmpleado as $ce) {
            $ce->estado = 0;
            $ce->fecha_baja = Carbon::now();
            $ce->save();
        }
        return response()->json($id, 200);
    }

    // * CAMBIAR ESTADOS DE MODOS
    public function cambiarEstadosControlesCC(Request $request)
    {
        $idCentro = $request->get('id');
        $control = $request->get('control');
        // BUSCAMOS ACTIVIDAD
        $centro = centro_costo::findOrFail($idCentro);
        if ($centro) {
            if ($control == "AP") {
                $centro->asistenciaPuerta = $request->get('valor');
            }
            if ($control == "MT") {
                $centro->modoTareo = $request->get('valor');
            }
            $centro->save();
        }
        return response()->json($idCentro, 200);
    }
}
