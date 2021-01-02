<?php

namespace App\Http\Controllers;

use App\actividad;
use App\actividad_area;
use App\actividad_empleado;
use App\captura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\PseudoTypes\True_;

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
            $actividad = actividad::where('Activi_id', '=', $a->idActividad)->where('estado', '=', 1)
                ->where(function ($query) {
                    $query->where('controlRemoto', '=', 1)
                        ->orWhere('controlRuta', '=', 1);
                })->get()->first();
            if ($actividad) {
                $actividad->eliminacionActividadEmpleado = $a->eliminacion;
                $actividad->estadoActividadEmpleado = $a->estado;
                array_push($respuesta, $actividad);
            }
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
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if ($invitadod->gestionActiv == 1) {
                        $permiso_invitado = DB::table('permiso_invitado')
                            ->where('idinvitado', '=', $invitadod->idinvitado)
                            ->get()->first();
                        return view('MantenedorActividades.actividades', [
                            'agregarActi' => $permiso_invitado->agregarActi, 'modifActi' => $permiso_invitado->modifActi, 'bajaActi' => $permiso_invitado->bajaActi
                        ]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('MantenedorActividades.actividades');
                }
            } else {
                return view('MantenedorActividades.actividades');
            }
        }
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
                'a.controlRuta',
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
        $actividadB = actividad::where('codigoActividad', '=', $request->get('codigo'))->where('organi_id', '=', session('sesionidorg'))->whereNotNull('codigoActividad')->get()->first();
        if ($actividadB) {
            return response()->json(array("estado" => 0, "actividad" => $actividadB), 200);
        }
        $actividad = new actividad();
        $actividad->Activi_Nombre = $request->get('nombre');
        $actividad->controlRemoto = $request->get('cr');
        $actividad->controlRuta = $request->get('crt');
        $actividad->asistenciaPuerta = $request->get('ap');
        $actividad->organi_id = session('sesionidorg');
        $actividad->codigoActividad = $request->get('codigo');
        $actividad->globalEmpleado = $request->get('globalEmpleado');
        $actividad->globalArea = $request->get('globalArea');
        $actividad->porEmpleados = $request->get('asignacionEmpleado');
        $actividad->porAreas = $request->get('asignacionArea');
        $actividad->save();

        $idActividad = $actividad->Activi_id;
        $listaE = $request->get('empleados');
        $listaA = $request->get('areas');
        if ($actividad->porEmpleados == 1) {
            // ASIGNAR EMPLEADOS A NUEVA ACTIVIDAD
            if (is_null($listaE) === false) {
                foreach ($listaE as $le) {
                    $actividad_empleado = new actividad_empleado();
                    $actividad_empleado->idActividad = $idActividad;
                    $actividad_empleado->idEmpleado = $le;
                    $actividad_empleado->estado = 1;
                    $actividad_empleado->eliminacion = 1;
                    $actividad_empleado->save();
                }
            }
        } else {
            if ($actividad->porAreas == 1) {
                if (is_null($listaA) === false) {
                    foreach ($listaA as $la) {
                        $empleadosArea = DB::table('empleado as e')
                            ->select('e.emple_id')
                            ->where('e.emple_area', '=', $la)
                            ->where('e.emple_estado', '=', 1)
                            ->get();
                        foreach ($empleadosArea as $ea) {
                            $actividad_empleado = new actividad_empleado();
                            $actividad_empleado->idActividad = $idActividad;
                            $actividad_empleado->idEmpleado = $ea->emple_id;
                            $actividad_empleado->estado = 1;
                            $actividad_empleado->eliminacion = 1;
                            $actividad_empleado->save();
                        }
                        //* REGISTRAR ACTIVIDAD AREAS
                        $actividad_area = new actividad_area();
                        $actividad_area->idActividad = $idActividad;
                        $actividad_area->idArea = $la;
                        $actividad_area->estado = 1;
                        $actividad_area->save();
                    }
                }
            }
        }

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
                'a.controlRuta',
                'a.asistenciaPuerta',
                'a.codigoActividad',
                'a.porEmpleados',
                'a.porAreas',
                'a.globalEmpleado'
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
        $areas = $request->get('areas');
        $buscarCodigo = actividad::where('codigoActividad', '=', $request->get('codigo'))->where('Activi_id', '!=', $idA)->get()->first();
        if (!$buscarCodigo) {
            if ($actividad) {
                //* ACTUALIZAR ATRIBUTOS DE ACTIVIDAD
                $actividad->codigoActividad = $request->get('codigo');
                $actividad->controlRemoto = $request->get('cr');
                $actividad->asistenciaPuerta = $request->get('ap');
                $actividad->controlRuta = $request->get('crt');
                $actividad->globalEmpleado = $request->get('globalEmpleado');
                $actividad->porEmpleados = $request->get('asignacionEmpleado');
                $actividad->porAreas = $request->get('asignacionArea');
                $actividad->globalArea = $request->get('globalArea');
                $actividad->save();

                //* ACTUALIZACION ACTIVIDADES DE EMPLEADOS
                $actividad_empleado = actividad_empleado::where('idActividad', '=', $actividad->Activi_id)->get();
                //* SI LA ASIGNACION ES POR EMPLEADO
                if ($actividad->porEmpleados == 1) {
                    // ARRAY DE EMPLEADOS SI ESTA VACIO
                    if (is_null($empleados) === true) {
                        foreach ($actividad_empleado as $ae) {
                            $ae->estado = 0;
                            $ae->save();
                        }
                    } else {
                        if (sizeof($actividad_empleado) == 0) {
                            foreach ($empleados as $emple) {
                                //* ASIGNAR EMPLEADOS ACTIVIDAD
                                $nuevaActividadE = new actividad_empleado();
                                $nuevaActividadE->idActividad = $actividad->Activi_id;
                                $nuevaActividadE->idEmpleado = $emple;
                                $nuevaActividadE->estado = 1;
                                $nuevaActividadE->eliminacion = 1;
                                $nuevaActividadE->save();
                            }
                        } else {
                            //* BUSCAR EMPLEADOS EN LA TABLA ACTIVIDAD
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
                            //* COMPARAR LAS ACTIVIDADES CON LA LISTA DE EMPLEADOS
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
                    //* DESACTIVAMOS POR AREAS
                    $actividad_area = actividad_area::where('idActividad', '=', $actividad->Activi_id)->get();
                    foreach ($actividad_area as $aa) {
                        $aa->estado = 0;
                        $aa->save();
                    }
                } else {
                    if ($actividad->porAreas == 1) {
                        //* FOREACH PARA BUSCAR EMPLEADO POR AREAS
                        foreach ($areas as $a) {
                            $empleadoArea = DB::table('empleado as e')
                                ->select('e.emple_id')
                                ->where('e.emple_area', '=', $a)
                                ->where('e.emple_estado', '=', 1)
                                ->get();
                            //* BUSCAR EMPLEADOS EN ACTIVIDAD EMPLEADO
                            foreach ($actividad_empleado as $activ) {
                                $busqueda = true;
                                foreach ($empleadoArea as $ea) {
                                    if ($activ->idEmpleado == $ea->emple_id) {
                                        $busqueda = false;
                                    }
                                }
                                if ($busqueda) {
                                    $ae = actividad_empleado::where('idActividad', '=', $actividad->Activi_id)->where('idEmpleado', '=', $activ->idEmpleado)->get()->first();
                                    $ae->estado = 0;
                                    $ae->save();
                                }
                            }
                            //* BUSCAR EMPLEADOS DE AREA EN ACTIVIDAD EMPLEADO
                            foreach ($empleadoArea as $ea) {
                                $busqueda = true;
                                foreach ($actividad_empleado as $activ) {
                                    if ($activ->idEmpleado == $ea->emple_id) {
                                        $busqueda = false;
                                    }
                                }
                                if ($busqueda) {
                                    $nuevoActividad_empleado = new actividad_empleado();
                                    $nuevoActividad_empleado->idActividad = $actividad->Activi_id;
                                    $nuevoActividad_empleado->idEmpleado = $ea->emple_id;
                                    $nuevoActividad_empleado->estado = 1;
                                    $nuevoActividad_empleado->eliminacion = 1;
                                    $nuevoActividad_empleado->save();
                                } else {
                                    $ae = actividad_empleado::where('idActividad', '=', $actividad->Activi_id)->where('idEmpleado', '=', $ea->emple_id)->get()->first();
                                    $ae->estado = 1;
                                    $ae->save();
                                }
                            }
                            //* BUSCAR ACTIVIDAD AREAS
                            $buscarActividad_area = actividad_area::where('idActividad', '=', $actividad->Activi_id)->where('idArea', '=', $a)->get()->first();
                            if (!$buscarActividad_area) {
                                //* REGISTRAR ACTIVIDAD AREAS
                                $actividad_area = new actividad_area();
                                $actividad_area->idActividad = $actividad->Activi_id;
                                $actividad_area->idArea = $a;
                                $actividad_area->estado = 1;
                                $actividad_area->save();
                            } else {
                                if ($buscarActividad_area->estado == 0) {
                                    $buscarActividad_area->estado = 1;
                                    $buscarActividad_area->save();
                                }
                            }
                        }
                        $actividad_area = actividad_area::where('idActividad', '=', $actividad->Activi_id)->get();
                        foreach ($actividad_area as $area) {
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
                        foreach ($actividad_empleado as $ae) {
                            $ae->estado = 0;
                            $ae->save();
                        }
                        //* DESACTIVAMOS POR AREAS
                        $actividad_area = actividad_area::where('idActividad', '=', $actividad->Activi_id)->get();
                        foreach ($actividad_area as $aa) {
                            $aa->estado = 0;
                            $aa->save();
                        }
                    }
                }
            }
            return response()->json($actividad, 200);
        } else {
            return 0;
        }
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
            if ($control == "CRT") {
                $actividad->controlRuta = $request->get('valor');
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
            ->where('a.estado', '=', 1)
            ->where(function ($query) {
                $query->where('a.controlRemoto', '=', 1)
                    ->orWhere('a.controlRuta', '=', 1);
            })
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

    // SELECT DE MOSTRAR EMPLEADOS EN REGISTRAR
    function datosActividad(Request $request)
    {
        $idActividad = $request->get('idA');
        //* ESTADO DE GLOBAL
        $actividad = actividad::findOrFail($idActividad);
        return response()->json($actividad, 200);
    }

    // SELECT DE EMPLEADOS EN REGISTRAR
    function listaEmpleadoReg()
    {
        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('rol_id', '=', 3)
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();

        if ($invitadod) {
            if ($invitadod->verTodosEmps == 1) {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                    ->where('e.emple_estado', '=', 1)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->get();
            } else {

                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {

                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.emple_estado', '=', 1)
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();
                } else {

                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.emple_estado', '=', 1)
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->get();
                }
            }
        } else {
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                ->where('e.emple_estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->get();
        }
        // TODOS LOS EMPLEADOS


        return response()->json($empleados, 200);
    }

    // ? SELECT DE ÁREAS EN ASIGNAR ACTIVIDAD
    function listaAreasEdit()
    {
        $areas = DB::table('empleado as e')
            ->join('area as a', 'a.area_id', '=', 'e.emple_area')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->groupBy('a.area_id')
            ->get();
        return response()->json($areas, 200);
    }

    //? SELEC DE ACTIVIDADES
    function listaActividades()
    {
        $actividades = DB::table('actividad as a')
            ->select('a.Activi_id as idActividad', 'a.Activi_Nombre as nombre')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->where('a.estado', '=', 1)
            ->where('a.eliminacion', '=', 1)
            ->where(function ($query) {
                $query->where('a.controlRemoto', '=', 1)
                    ->orWhere('a.controlRuta', '=', 1);
            })
            ->get();

        return response()->json($actividades, 200);
    }

    //? SELECT DE EMPLEADOS CON ÁREAS

    function empleadosConAreas(Request $request)
    {
        $idEmpleados = $request->get('empleados');
        $idAreas = $request->get('areas');

        // : Cuando los dos array tienen datos
        if (!is_null($idEmpleados) && !is_null($idAreas)) {
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->leftJoin('area as a', 'a.area_id', '=', 'e.emple_area')
                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->whereIn('e.emple_id', $idEmpleados)
                ->orWhereIn('e.emple_area', $idAreas)
                ->get();

            return response()->json($empleados, 200);
        } else {
            //: Cuando solo hay datos de areas
            if (!is_null($idAreas)) {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->leftJoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->WhereIn('e.emple_area', $idAreas)
                    ->get();

                return response()->json($empleados, 200);
            }
        }
        //: Cuando solo hay datos en empleados
        if (!is_null($idEmpleados)) {
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->get();

            return response()->json($empleados, 200);
        } else {
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->get();

            return response()->json($empleados, 200);
        }
    }

    // ? FUNCION DE ASIGNAR ACTIVIDADES POR AREAS
    public function asignacionPorAreas(Request $request)
    {
        $empleados = $request->get('empleados');
        $actividad = $request->get('idActividad');
        $areas = $request->get('areas');
        $globalEmpleado = $request->get('globalEmpleado');
        $globalArea = $request->get('globalArea');
        $porEmpleados = $request->get('asignacionEmpleado');
        $porAreas = $request->get('asignacionArea');
        //: ESTADO GLOBAL DE LA ACTIVIDAD
        $actividadB = actividad::findOrFail($actividad);
        if ($actividadB) {
            $actividadB->globalEmpleado = $globalEmpleado;
            $actividadB->globalArea = $globalArea;
            $actividadB->porEmpleados = $porEmpleados;
            $actividadB->porAreas = $porAreas;
            $actividadB->save();
        }

        //* ACTUALIZACION ACTIVIDADES DE EMPLEADOS
        $actividad_empleado = actividad_empleado::where('idActividad', '=', $actividadB->Activi_id)->get();
        //* SI LA ASIGNACION ES POR EMPLEADO
        if ($actividadB->porEmpleados == 1) {
            // ARRAY DE EMPLEADOS SI ESTA VACIO
            if (is_null($empleados) === true) {
                foreach ($actividad_empleado as $ae) {
                    $ae->estado = 0;
                    $ae->save();
                }
            } else {
                if (sizeof($actividad_empleado) == 0) {
                    foreach ($empleados as $emple) {
                        //* ASIGNAR EMPLEADOS ACTIVIDAD
                        $nuevaActividadE = new actividad_empleado();
                        $nuevaActividadE->idActividad = $actividadB->Activi_id;
                        $nuevaActividadE->idEmpleado = $emple;
                        $nuevaActividadE->estado = 1;
                        $nuevaActividadE->eliminacion = 1;
                        $nuevaActividadE->save();
                    }
                } else {
                    //* BUSCAR EMPLEADOS EN LA TABLA ACTIVIDAD
                    foreach ($empleados as $emple) {
                        $estado = false;
                        for ($index = 0; $index < sizeof($actividad_empleado); $index++) {
                            if ($actividad_empleado[$index]->idEmpleado == $emple) {
                                $estado = true;
                            }
                        }
                        if ($estado == false) {
                            $nuevaActividadE = new actividad_empleado();
                            $nuevaActividadE->idActividad = $actividadB->Activi_id;
                            $nuevaActividadE->idEmpleado = $emple;
                            $nuevaActividadE->estado = 1;
                            $nuevaActividadE->eliminacion = 1;
                            $nuevaActividadE->save();
                        } else {
                            $ae = actividad_empleado::where('idActividad', '=', $actividadB->Activi_id)->where('idEmpleado', '=', $emple)->get()->first();
                            $ae->estado = 1;
                            $ae->save();
                        }
                    }
                    //* COMPARAR LAS ACTIVIDADES CON LA LISTA DE EMPLEADOS
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
            //* DESACTIVAMOS POR AREAS
            $actividad_area = actividad_area::where('idActividad', '=', $actividadB->Activi_id)->get();
            foreach ($actividad_area as $aa) {
                $aa->estado = 0;
                $aa->save();
            }
        } else {
            if ($actividadB->porAreas == 1) {
                //* FOREACH PARA BUSCAR EMPLEADO POR AREAS
                foreach ($areas as $a) {
                    $empleadoArea = DB::table('empleado as e')
                        ->select('e.emple_id')
                        ->where('e.emple_area', '=', $a)
                        ->where('e.emple_estado', '=', 1)
                        ->get();
                    //* BUSCAR EMPLEADOS EN ACTIVIDAD EMPLEADO
                    foreach ($actividad_empleado as $activ) {
                        $busqueda = true;
                        foreach ($empleadoArea as $ea) {
                            if ($activ->idEmpleado == $ea->emple_id) {
                                $busqueda = false;
                            }
                        }
                        if ($busqueda) {
                            $ae = actividad_empleado::where('idActividad', '=', $actividadB->Activi_id)->where('idEmpleado', '=', $activ->idEmpleado)->get()->first();
                            $ae->estado = 0;
                            $ae->save();
                        }
                    }
                    //* BUSCAR EMPLEADOS DE AREA EN ACTIVIDAD EMPLEADO
                    foreach ($empleadoArea as $ea) {
                        $busqueda = true;
                        foreach ($actividad_empleado as $activ) {
                            if ($activ->idEmpleado == $ea->emple_id) {
                                $busqueda = false;
                            }
                        }
                        if ($busqueda) {
                            $nuevoActividad_empleado = new actividad_empleado();
                            $nuevoActividad_empleado->idActividad = $actividadB->Activi_id;
                            $nuevoActividad_empleado->idEmpleado = $ea->emple_id;
                            $nuevoActividad_empleado->estado = 1;
                            $nuevoActividad_empleado->eliminacion = 1;
                            $nuevoActividad_empleado->save();
                        } else {
                            $ae = actividad_empleado::where('idActividad', '=', $actividadB->Activi_id)->where('idEmpleado', '=', $ea->emple_id)->get()->first();
                            $ae->estado = 1;
                            $ae->save();
                        }
                    }
                    //* BUSCAR ACTIVIDAD AREAS
                    $buscarActividad_area = actividad_area::where('idActividad', '=', $actividadB->Activi_id)->where('idArea', '=', $a)->get()->first();
                    if (!$buscarActividad_area) {
                        //* REGISTRAR ACTIVIDAD AREAS
                        $actividad_area = new actividad_area();
                        $actividad_area->idActividad = $actividadB->Activi_id;
                        $actividad_area->idArea = $a;
                        $actividad_area->estado = 1;
                        $actividad_area->save();
                    } else {
                        if ($buscarActividad_area->estado == 0) {
                            $buscarActividad_area->estado = 1;
                            $buscarActividad_area->save();
                        }
                    }
                }
                $actividad_area = actividad_area::where('idActividad', '=', $actividadB->Activi_id)->get();
                foreach ($actividad_area as $area) {
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
                foreach ($actividad_empleado as $ae) {
                    $ae->estado = 0;
                    $ae->save();
                }
                //* DESACTIVAMOS POR AREAS
                $actividad_area = actividad_area::where('idActividad', '=', $actividadB->Activi_id)->get();
                foreach ($actividad_area as $aa) {
                    $aa->estado = 0;
                    $aa->save();
                }
            }
        }
        return response()->json($empleados, 200);
    }

    //: OBTENER DATOS POR ASIGNACION DE EMPLEADOS Y ACTIVIDAD
    public function asignacionEmpleadoActividad(Request $request)
    {
        $actividad = actividad::findOrFail($request->get('id'));
        $empleadoSA = [];
        $respuesta = [];
        $empleadosActividad  = [];
        // TODOS LOS EMPLEADOS
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();
        //* EMPLEADOS CON DICHA ACTIVIDAD
        $empleadosActividad = DB::table('actividad as a')
            ->join('actividad_empleado as ae', 'ae.idActividad', '=', 'a.Activi_id')
            ->join('empleado as e', 'e.emple_id', '=', 'ae.idEmpleado')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('ae.idEmpleado', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('a.Activi_id', '=', $request->get('id'))
            ->where('ae.estado', '=', 1)
            ->get();
        for ($index = 0; $index < sizeof($empleados); $index++) {
            $estado = true;
            foreach ($empleadosActividad as $ae) {
                if ($empleados[$index]->emple_id == $ae->idEmpleado) {
                    $estado = false;
                }
            }
            if ($estado) {
                array_push($empleadoSA, $empleados[$index]);
            }
        }
        // ****************
        // DATOS PARA RESULTADO
        array_push($respuesta, array("select" => $empleadosActividad, "noSelect" => $empleadoSA, "global" => $actividad->globalEmpleado));
        return response()->json($respuesta, 200);
    }
    //: OBTENER DATOS POR ASIGNACION DE AREAS Y ACTIVIDAD
    public function asignacionAreaActividad(Request $request)
    {
        $actividad = actividad::findOrFail($request->get('id'));
        $areaSA = [];
        $respuesta = [];
        $areasActividad = [];
        // TODOS LAS AREAS
        $areas = DB::table('empleado as e')
            ->join('area as a', 'a.area_id', '=', 'e.emple_area')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->groupBy('a.area_id')
            ->get();
        //* AREAS CON DICHA ACTIVIDAD
        $areasActividad = DB::table('actividad as a')
            ->join('actividad_area as aa', 'aa.idActividad', '=', 'a.Activi_id')
            ->join('area as ar', 'ar.area_id', '=', 'aa.idArea')
            ->select('ar.area_id', 'ar.area_descripcion')
            ->where('ar.organi_id', '=', session('sesionidorg'))
            ->where('aa.estado', '=', 1)
            ->where('a.Activi_id', '=', $request->get('id'))
            ->groupBy('ar.area_id')
            ->get();
        for ($index = 0; $index < sizeof($areas); $index++) {
            $estado = true;
            foreach ($areasActividad as $aa) {
                if ($areas[$index]->area_id == $aa->area_id) {
                    $estado = false;
                }
            }
            if ($estado) {
                array_push($areaSA, $areas[$index]);
            }
        }
        array_push($respuesta, array("select" => $areasActividad, "noSelect" => $areaSA, "global" => $actividad->globalArea));
        return response()->json($respuesta, 200);
    }
}
