<?php

namespace App\Http\Controllers;

use App\actividad;
use App\actividad_empleado;
use App\captura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActividadesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function actividadesEmpleado(Request $request)
    {
        $respuesta = [];
        $id = $request->get('id');
        $actividad_empleado = actividad_empleado::where('idEmpleado', '=', $id)->get();
        foreach ($actividad_empleado as $a) {
            $actividad = actividad::findOrFail($a->idActividad);
            $actividad->eliminacionActividadEmpleado = $a->eliminacion;
            $actividad->estadoActividadEmpleado = $a->estado;
            array_push($respuesta, $actividad);
        }
        return response()->json($respuesta, 200);
    }

    public function editarActividadE(Request $request)
    {
        $idA = $request->get('idA');
        $actividad = actividad::where('Activi_id', '=', $idA)->get()->first();
        if ($actividad) {
            $actividad->Activi_Nombre = $request->get('actividad');
            $actividad->save();
            return response()->json($actividad, 200);
        }
    }

    public function editarEstadoActividad(Request $request)
    {
        $idA = $request->get('idA');
        $idE = $request->get('idE');
        $actividad_empleado = actividad_empleado::where('idEmpleado', '=', $idE)->where('idActividad', '=', $idA)->get()->first();
        if ($actividad_empleado) {
            $actividad_empleado->estado = $request->get('estado');
            $actividad_empleado->save();
            return response()->json($actividad_empleado, 200);
        }
    }

    // VISTAS DE ACTIVIDADES

    public function actividades()
    {
        return view('MantenedorActividades.actividades');
    }

    public function actividadesOrganizaciones()
    {
        // DB::enableQueryLog();
        $actividades = DB::table('actividad as a')
            ->leftJoin('captura as cp', 'cp.idActividad', '=', 'a.Activi_id')
            ->select(
                'a.Activi_id',
                'a.Activi_Nombre',
                'a.controlRemoto',
                'a.asistenciaPuerta',
                'a.eliminacion',
                DB::raw("CASE WHEN(a.codigoActividad) IS NULL THEN 'No definido' ELSE a.codigoActividad END AS codigoA"),
                DB::raw("CASE WHEN(cp.idCaptura) IS NULL THEN 'No' ELSE 'Si' END AS respuesta")
            )
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.estado', '=', 1)
            ->groupBy('a.Activi_id')
            ->get();
        // dd(DB::getQueryLog());
        return response()->json($actividades, 200);
    }

    // REGISTRAR ACTIVIDAD PARA ORGANIZACION
    public function registrarActividadE(Request $request)
    {
        $actividadBuscar = actividad::where('Activi_Nombre', '=', $request->get('nombre'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($actividadBuscar) {
            return response()->json(array("estado" => 1, "actividad" => $actividadBuscar), 200);
        }
        $actividadB = actividad::where('codigoActividad', '=', $request->get('codigo'))->where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($actividadB) {
            return response()->json(array("estado" => 0, "actividad" => $actividadB), 200);
        }
        $actividad = new actividad();
        $actividad->Activi_Nombre = $request->get('nombre');
        $actividad->controlRemoto = $request->get('cr');
        $actividad->asistenciaPuerta = $request->get('ap');
        $actividad->organi_id = session('sesionidorg');
        $actividad->codigoActividad = $request->get('codigo');
        $actividad->save();

        return response()->json($actividad, 200);
    }

    // EDITAR ACTIVIDAD

    public function editarActividad(Request $request)
    {
        $idA = $request->get('idA');
        $actividades = DB::table('actividad as a')
            ->leftJoin('captura as cp', 'cp.idActividad', '=', 'a.Activi_id')
            ->select(
                'a.Activi_id',
                'a.Activi_Nombre',
                'a.controlRemoto',
                'a.asistenciaPuerta',
                'a.codigoActividad'
            )
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.Activi_id', '=', $idA)
            ->where('a.estado', '=', 1)
            ->get()
            ->first();

        return response()->json($actividades, 200);
    }

    //REGISTRAR DATOS AL EDITAR
    public function editarCambios(Request $request)
    {
        $idA = $request->get('idA');
        $actividad = actividad::findOrFail($idA);
        $empleados = $request->get('empleados');
        // dd($empleados);
        if ($actividad) {
            // ACTUALIZAR ATRIBUTOS DE ACTIVIDAD
            $actividad->codigoActividad = $request->get('codigo');
            $actividad->controlRemoto = $request->get('cr');
            $actividad->asistenciaPuerta = $request->get('ap');
            $actividad->save();

            // ACTUALIZACION ACTIVIDADES DE EMPLEADOS
            $actividad_empleado = actividad_empleado::where('idActividad', '=', $actividad->Activi_id)->get();
            // ARRAY DE EMPLEADOS SI ESTA VACIO
            if (is_null($empleados) === true) {
                foreach ($actividad_empleado as $ae) {
                    $ae->estado = 0;
                    $ae->save();
                }
            } else {
                if (sizeof($actividad_empleado) == 0) {
                    foreach ($empleados as $emple) {
                        // ASIGNAR EMPLEADOS ACTIVIDAD
                        $nuevaActividadE = new actividad_empleado();
                        $nuevaActividadE->idActividad = $actividad->Activi_id;
                        $nuevaActividadE->idEmpleado = $emple;
                        $nuevaActividadE->estado = 1;
                        $nuevaActividadE->eliminacion = 1;
                        $nuevaActividadE->save();
                    }
                } else {
                    // BUSCAR EMPLEADOS EN LA TABLA ACTIVIDAD
                    foreach ($empleados as $emple) {
                        $estado = false;
                        for ($index = 0; $index < sizeof($actividad_empleado); $index++) {
                            if ($actividad_empleado[$index]->idEmpleado == $emple) {
                                $estado = true;
                            }
                        }
                        if ($estado == false) {
                            $nuevaActividadE = new actividad_empleado();
                            $nuevaActividadE->idActividad = $actividad->Activi_id;
                            $nuevaActividadE->idEmpleado = $emple;
                            $nuevaActividadE->estado = 1;
                            $nuevaActividadE->eliminacion = 1;
                            $nuevaActividadE->save();
                        } else {
                            $ae = actividad_empleado::where('idActividad', '=', $actividad->Activi_id)->where('idEmpleado', '=', $emple)->get()->first();
                            $ae->estado = 1;
                            $ae->save();
                        }
                    }
                    // COMPARAR LAS ACTIVIDADES CON LA LISTA DE EMPLEADOS
                    foreach ($actividad_empleado as $actvE) {
                        $estadoB = false;
                        foreach ($empleados as $emple) {
                            if ($actvE->idEmpleado == $emple) {
                                $estadoB = true;
                            }
                        }
                        if ($estadoB == false) {
                            $actvE->estado = 0;
                            $actvE->save();
                        }
                    }
                }
            }
        }

        return response()->json($actividad, 200);
    }

    // MODIFCAR ESTADOS ACTIVIDADES DE CONTROL

    public function cambiarEstadoActividadControl(Request $request)
    {
        $idActividad = $request->get('id');
        $control = $request->get('control');
        // BUSCAMOS ACTIVIDAD
        $actividad = actividad::findOrFail($idActividad);
        if ($actividad) {
            if ($control == "CR") {
                $actividad->controlRemoto = $request->get('valor');
            }
            if ($control == "AP") {
                $actividad->asistenciaPuerta = $request->get('valor');
            }
            $actividad->save();
        }

        return response()->json($actividad, 200);
    }

    //CAMBIAR ESTADO A ACTIVIDAD

    public function cambiarEstadoActividad(Request $request)
    {
        $idActividad = $request->get('id');
        $actividad = actividad::findOrFail($idActividad);
        $buscar_actividad = captura::where('idActividad', '=', $idActividad)->get()->first();
        if ($buscar_actividad) {
            return 1;
        }
        if ($actividad) {
            $actividad->estado = 0;
            $actividad->save();
        }

        return response()->json($actividad, 200);
    }

    //RECUPERAR ACTIVIDAD
    public function recuperarActividad(Request $request)
    {
        $idActividad = $request->get('id');
        $actividad = actividad::findOrFail($idActividad);
        if ($actividad) {
            $actividad->estado = 1;
            $actividad->save();
        }

        return response()->json($actividad, 200);
    }

    // MOSTRAR TAREAS PARA ASIGNAR AL EMPLEADO

    public function asignarActividadesE(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        // DB::enableQueryLog();
        $actividadEmpleado = DB::table('actividad as a')
            ->join('actividad_empleado as ae', 'ae.idActividad', '=', 'a.Activi_id')
            ->select('a.Activi_id')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.controlRemoto', '=', 1)
            ->where('ae.idEmpleado', '=', $idEmpleado)
            ->where('a.estado', '=', 1)
            ->get();
        $actividad = DB::table('actividad as a')
            ->select('a.Activi_id', 'a.Activi_Nombre', 'a.codigoActividad')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.controlRemoto', '=', 1)
            ->where('a.estado', '=', 1)
            ->get();
        $respuesta = [];
        for ($index = 0; $index < sizeof($actividad); $index++) {
            $estado = false;
            foreach ($actividadEmpleado as $ae) {
                if ($actividad[$index]->Activi_id == $ae->Activi_id) {
                    $estado = true;
                }
            }
            if ($estado == false) {
                $valor = $actividad[$index]->codigoActividad == null ? "No definido" : $actividad[$index]->codigoActividad;
                array_push($respuesta, array("value" => $actividad[$index]->Activi_id, "text" => $actividad[$index]->Activi_Nombre . " | " . $valor));
            }
        }
        // dd(DB::getQueryLog());
        // foreach ($actividad as $a) {
        //     array_push($respuesta, array("value" => $a->Activi_id, "text" => $a->Activi_Nombre));
        // }

        return response()->json($respuesta, 200);
    }

    // ASIGNAR TAREAS
    public function registrarActividadEmpleado(Request $request)
    {
        $idE = $request->get('idE');
        $idActividad = $request->get('idA');

        foreach ($idActividad as $idA) {
            $actividad_empleado = new actividad_empleado();
            $actividad_empleado->idActividad = $idA;
            $actividad_empleado->idEmpleado = $idE;
            $actividad_empleado->estado = 1;
            $actividad_empleado->eliminacion = 1;
            $actividad_empleado->save();
        }

        return response()->json($actividad_empleado, 200);
    }

    // SELECT DE MOSTRAR EMPLEADOS
    function empleadoSelect(Request $request)
    {
        $respuesta = [];
        $empleadoSA = [];
        $idActividad = $request->get('idA');
        // EMPLEADOS ASIGNADOS A DICHA ACTIVIDAD
        $empleadosA = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
            ->select('ae.id', 'ae.idEmpleado', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.emple_estado', '=', 1)
            ->where('ae.estado', '=', 1)
            ->where('ae.idActividad', '=', $idActividad)
            ->groupBy('ae.idEmpleado')
            ->get();
        // *************************************
        // TODOS LOS EMPLEADOS
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();
        // ******************
        // SEPARAR EMPLEADOS 
        for ($index = 0; $index < sizeof($empleados); $index++) {
            $estado = false;
            foreach ($empleadosA as $ae) {
                if ($empleados[$index]->emple_id == $ae->idEmpleado) {
                    $estado = true;
                }
            }
            if ($estado == false) {
                array_push($empleadoSA, $empleados[$index]);
            }
        }
        // ****************
        // DATOS PARA RESULTADO
        array_push($respuesta, array("select" => $empleadosA, "noSelect" => $empleadoSA));
        // dd($respuesta);

        return response()->json($respuesta, 200);
    }
}
