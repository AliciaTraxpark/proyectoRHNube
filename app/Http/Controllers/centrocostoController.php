<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\centro_costo;
use App\empleado;
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

    public function listaCentroCosto()
    {
        $centroC = DB::table('centro_costo as c')
            ->leftJoin('empleado as e', function ($left) {
                $left->on('e.emple_centCosto', '=', 'c.centroC_id')
                    ->where('e.emple_estado', '=', 1);
            })
            ->select(
                'c.centroC_id as id',
                'c.centroC_descripcion as descripcion',
                DB::raw("CASE WHEN(e.emple_id) IS NULL THEN 'No' ELSE 'Si' END AS respuesta"),
                DB::raw("COUNT(e.emple_id) as contar")
            )
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->where('estado', '=', 1)
            ->groupBy('c.centroC_id')
            ->get();

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
            'porEmpleado'
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
            $centro->save();
            // * EMPLEADOS EN CENTRO DE COSTO
            $empleadoCentro = DB::table('centro_costo as c')
                ->join('empleado as e', 'e.emple_centCosto', '=', 'c.centroC_id')
                ->select('e.emple_id')
                ->where('c.centroC_id', '=', $centro->centroC_id)
                ->where('e.emple_estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->get();
            // * SI ARRAY EMPLEADOS ESTA VACIO
            if (is_null($empleados)) {
                foreach ($empleadoCentro as $ec) {
                    $emp = empleado::where('emple_id', '=', $ec->emple_id)->get()->first();
                    $emp->emple_centCosto = NULL;
                    $emp->save();
                }
            } else {
                // * BUSCAR EMPLEADOS CON CENTRO COSTO
                foreach ($empleados as $e) {
                    $estado = true;
                    for ($index = 0; $index < sizeof($empleadoCentro); $index++) {
                        if ($empleadoCentro[$index]->emple_id == $e) {
                            $estado = false;
                        }
                    }
                    if ($estado) {
                        $emp = empleado::where('emple_id', '=', $e)->get()->first();
                        $emp->emple_centCosto = $centro->centroC_id;
                        $emp->save();
                    }
                }

                // * COMPARAR EMPLEADOS CENTRO CON LISTA DE EMPLEADOS
                foreach ($empleadoCentro as $ec) {
                    $estadoB = true;
                    foreach ($empleados as $em) {
                        if ($ec->emple_id == $em) {
                            $estadoB = false;
                        }
                    }
                    if ($estadoB) {
                        $emp = empleado::where('emple_id', '=', $ec->emple_id)->get()->first();
                        $emp->emple_centCosto = NULL;
                        $emp->save();
                    }
                }
            }
            return response()->json($id, 200);
        } else {
            return response()->json(array("respuesta" => 1, "mensaje" => "Ya existe un centro de costo con este código"), 200);
        }
    }

    public function listaCentroC()
    {
        $centroC = centro_costo::select('centroC_id as id', 'centroC_descripcion as descripcion')
            ->where('organi_id', '=', session('sesionidorg'))
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
            ->whereNull('e.emple_centCosto')
            ->get();

        // * EMPLEADOS EN CENTRO DE COSTO
        $empleadoCentro = DB::table('centro_costo as c')
            ->join('empleado as e', 'e.emple_centCosto', '=', 'c.centroC_id')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('c.centroC_id', '=', $centro->centroC_id)
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
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

    public function asignarCentros(Request $request)
    {
        $id = $request->get('id');
        $empleados = $request->get('empleados');

        $centro = centro_costo::findOrFail($id);

        // * EMPLEADOS EN CENTRO DE COSTO
        $empleadoCentro = DB::table('centro_costo as c')
            ->join('empleado as e', 'e.emple_centCosto', '=', 'c.centroC_id')
            ->select('e.emple_id')
            ->where('c.centroC_id', '=', $centro->centroC_id)
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();
        // * SI ARRAY EMPLEADOS ESTA VACIO
        if (is_null($empleados)) {
            foreach ($empleadoCentro as $ec) {
                $emp = empleado::where('emple_id', '=', $ec->emple_id)->get()->first();
                $emp->emple_centCosto = NULL;
                $emp->save();
            }
        } else {
            // * BUSCAR EMPLEADOS CON CENTRO COSTO
            foreach ($empleados as $e) {
                $estado = true;
                for ($index = 0; $index < sizeof($empleadoCentro); $index++) {
                    if ($empleadoCentro[$index]->emple_id == $e) {
                        $estado = false;
                    }
                }
                if ($estado) {
                    $emp = empleado::where('emple_id', '=', $e)->get()->first();
                    $emp->emple_centCosto = $centro->centroC_id;
                    $emp->save();
                }
            }

            // * COMPARAR EMPLEADOS CENTRO CON LISTA DE EMPLEADOS
            foreach ($empleadoCentro as $ec) {
                $estadoB = true;
                foreach ($empleados as $em) {
                    if ($ec->emple_id == $em) {
                        $estadoB = false;
                    }
                }
                if ($estadoB) {
                    $emp = empleado::where('emple_id', '=', $ec->emple_id)->get()->first();
                    $emp->emple_centCosto = NULL;
                    $emp->save();
                }
            }
        }

        return response()->json($id, 200);
    }

    public function listaEmpleados()
    {
        // TODO LOS EMPLEADOS
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->whereNull('e.emple_centCosto')
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
        $centro->organi_id = session('sesionidorg');
        $centro->save();
        // : ID DE CENTRO COSTO
        $idCentro = $centro->centroC_id;
        // : EMPLEADOS
        $empleados = $request->get('empleados');
        if (!is_null($empleados)) {
            foreach ($empleados as $e) {
                $emp = empleado::findOrFail($e);
                $emp->emple_centCosto = $idCentro;
                $emp->save();
            }
        }

        return response()->json($idCentro, 200);
    }

    public function recuperarCentro(Request $request)
    {
        $id = $request->get('id');
        $centro = centro_costo::findOrFail($id);
        $centro->estado = 1;
        $centro->save();

        return response()->json($centro->centroC_id, 200);
    }

    public function eliminarCentro(Request $request)
    {
        $id = $request->get('id');
        $empleado = empleado::where('emple_centCosto', '=', $id)->where('emple_estado', '=', 1)->get()->first();

        if ($empleado) {
            return 0;
        } else {
            $centro = centro_costo::findOrFail($id);
            $centro->estado = 0;
            $centro->save();

            return response()->json($id, 200);
        }
    }
}
