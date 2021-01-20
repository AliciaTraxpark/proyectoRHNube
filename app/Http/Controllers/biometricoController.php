<?php

namespace App\Http\Controllers;

use App\dispositivos;
use App\dispositivo_area;
use App\dispositivo_empleado;
use App\organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class biometricoController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function vistaReporte()
    {
        $organizacion = organizacion::all('organi_id', 'organi_razonSocial');
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion) {
            if ($usuario_organizacion->rol_id == 4) {
                return view('Biometrico.reportePersonalizado', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacion->rol_id]);
            }
        } else {
            if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
                return redirect('/elegirorganizacion');
            } else {
                $usuario_organizacionR = DB::table('usuario_organizacion as uso')
                    ->where('uso.organi_id', '=', session('sesionidorg'))
                    ->where('uso.user_id', '=', Auth::user()->id)
                    ->get()->first();
                return view('Biometrico.reportePersonalizado', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacionR->rol_id]);
            }
        }
    }

    public function dispoStoreBiometrico(Request $request)
    {
        /* REGISTRAMOS NUEVO DISPOSITIVO */
        $dispositivos = new dispositivos();
        $dispositivos->tipoDispositivo = 3;
        $dispositivos->dispo_descripUbicacion = $request->descripcionBio;
        $dispositivos->dispo_movil = $request->ippuerto;
        $dispositivos->dispo_estadoActivo = 1;
        $dispositivos->dispo_estado = 0;
        $dispositivos->organi_id = session('sesionidorg');
        $dispositivos->dispo_todosEmp = $request->checkTodoEmp;
        $dispositivos->dispo_porEmp = $request->switchEmp;
        $dispositivos->dispo_porArea = $request->switchArea;
        $dispositivos->save();
        /* ----------------------------------- */
        $todosE = $request->checkTodoEmp;
        $porEmpleado = $request->switchEmp;
        $porArea = $request->switchArea;

        /* SI ES POR EMPLEADOS */
        if ($porEmpleado == 1) {

            /* SI NO SON TODOS */
            if ($todosE != 1) {
                /* RECORREMOS ARRAY Y REGISTRAMOS EMPLEADOS DE DISPO */
                foreach ($request->selectEmp as $empleados) {
                    $dispositivos_empleado = new dispositivo_empleado();
                    $dispositivos_empleado->idDispositivos = $dispositivos->idDispositivos;
                    $dispositivos_empleado->emple_id = $empleados;
                    $dispositivos_empleado->save();
                }
                /* ----------------------------------------------- */
            }

        } else
        /* SI ES POR AREA */
        {if ($porArea == 1) {
            foreach ($request->selectArea as $areas) {
                $dispositivo_area = new dispositivo_area();
                $dispositivo_area->idDispositivos = $dispositivos->idDispositivos;
                $dispositivo_area->area_id = $areas;
                $dispositivo_area->save();
            }

        }

        }
    }

    public function actualizarBiometrico(Request $request)
    {
        /*    ENCONTRAR DISPOSITIVO POR ID Y ACTUALIZAR SUS DATOS    */
        $dispositivos = dispositivos::findOrFail($request->idDisposEd_ed);
        $dispositivos->dispo_descripUbicacion = $request->descripccionUb_ed;
        $dispositivos->dispo_movil = $request->ippuerto_ed;
        $dispositivos->dispo_codigo = $request->nserie_ed;
        $dispositivos->version_firmware = $request->version_ed;
        $dispositivos->dispo_todosEmp = $request->checkTodoEmp_ed;
        $dispositivos->dispo_porEmp = $request->switchEmp_ed;
        $dispositivos->dispo_porArea = $request->switchArea_ed;
        $dispositivos->save();
        /* -------------------------------------------------------- */

        $todosE = $request->checkTodoEmp_ed;
        $porEmpleado = $request->switchEmp_ed;
        $porArea = $request->switchArea_ed;

        /* SI ES POR EMPLEADOS */
        if ($porEmpleado == 1) {

            /* -----------------BUSCAMOS DISPOSiTIVOS POR AREAS PARA CAMBIAR DE ESTADO --------------------------*/
            $dispositivo_areaAnt = dispositivo_area::where('idDispositivos', '=', $request->idDisposEd_ed)->get();
            /* SI ENCONTRAMOS CAMBIAMOS DE ESTADO */
            if ($dispositivo_areaAnt->isNotEmpty()) {
                foreach ($dispositivo_areaAnt as $disAreas) {
                    $disAreas->estado = 0;
                    $disAreas->save();
                }
            }
            /* -------------------------------------------------------------------------------------------------- */
            $dispositivo_empAnt = dispositivo_empleado::where('idDispositivos', '=', $request->idDisposEd_ed)->get();
            /* SI NO SON TODOS */
            if ($todosE != 1) {
                /* BUSCAMOS EMPLEADOS SI AHAT EN DISPOSITIVOS_EMPLEADO */

                /* PRIMERO COMPROBAMOS QUE NO HAYA YA EREGISTRADOS */
                if ($dispositivo_empAnt->isNotEmpty()) {

                    foreach ($request->selectEmp_ed as $emps) {
                        $estado = false;
                        for ($index = 0; $index < sizeof($dispositivo_empAnt); $index++) {
                            /* SI TENEMOS REGISTRADO LOS ID DE EMPELADOS QUE EQUEREMOS INSERTAR */

                            if ($dispositivo_empAnt[$index]->emple_id == $emps) {
                                $estado = true;

                            }
                         }
                            if ($estado == false) {
                                 /* SI NO COINCIDE LOS ID REGISTRADOS CON LOS NUEVOS ENTONCES REGISTRAMOS NUEVOS EMPLEADOS */
                                 $dispositivos_empleadoReg = new dispositivo_empleado();
                                 $dispositivos_empleadoReg->idDispositivos = $dispositivos->idDispositivos;
                                 $dispositivos_empleadoReg->emple_id = $emps;
                                 $dispositivos_empleadoReg->save();
                            }
                            else{
                                $dispositivo_empActu = dispositivo_empleado::where('idDispositivos', '=',$request->idDisposEd_ed)
                                ->where('emple_id', '=', $emps)->get()->first();
                                $dispositivo_empActu->estado = 1;
                                $dispositivo_empActu->save();

                            }

                    }

                      /* COMPARAMOS LOS REGISTRADOS CON LA LISTA DE EMPLEADOS PARA DESCARTAR LOS QUE YA NO TIENEN*/
                      foreach ($dispositivo_empAnt as $empRegis) {
                        $estadoEReg = false;
                        foreach ($request->selectEmp_ed as $emple) {
                            if ($empRegis->emple_id == $emple) {
                                $estadoEReg = true;
                            }
                        }
                        if($estadoEReg==false){

                                $empRegis->estado = 0;
                                $empRegis->save();

                        }


                    }
                } else {
                    /* SI NO HAY EMPLEADOS ENTONCES REGISTRAMOS TODOS LOS QUE TENEMOS DE FRONTED */
                    /* RECORREMOS ARRAY Y REGISTRAMOS EMPLEADOS DE DISPO */
                    foreach ($request->selectEmp_ed as $empleados) {
                        $dispositivos_empleado = new dispositivo_empleado();
                        $dispositivos_empleado->idDispositivos = $dispositivos->idDispositivos;
                        $dispositivos_empleado->emple_id = $empleados;
                        $dispositivos_empleado->save();
                    }
                    /* ----------------------------------------------- */
                }
                /* --------------------------------------------- */

            } else {
                /* SI TIENE ACCESO A TODOS LO EMPLEADOS */
                /* -----------------BUSCAMOS DISPOSiTIVOS POREMPLEADOS PARA CAMBIAR DE ESTADO --------------------------*/

                /* SI ENCONTRAMOS CAMBIAMOS DE ESTADO */
                if ($dispositivo_empAnt->isNotEmpty()) {
                    foreach ($dispositivo_empAnt as $disEmps) {
                        $disEmps->estado = 0;
                        $disEmps->save();
                    }
                }
                /* -------------------------------------------------------------------------------------------------- */
            }

        } else
        /* SI ES POR AREA */
        {if ($porArea == 1) {

            /* SI TIENE ACCESO A TODOS LO EMPLEADOS */
            /* -----------------BUSCAMOS DISPOSiTIVOS POREMPLEADOS PARA CAMBIAR DE ESTADO --------------------------*/
            $dispositivo_empAnt = dispositivo_empleado::where('idDispositivos', '=', $request->idDisposEd_ed)->get();
            /* SI ENCONTRAMOS CAMBIAMOS DE ESTADO */
            if ($dispositivo_empAnt->isNotEmpty()) {
                foreach ($dispositivo_empAnt as $disEmps) {
                    $disEmps->estado = 0;
                    $disEmps->save();
                }
            }
            /* -------------------------------------------------------------------------------------------------- */
            /* BUSCAMOS AREA DE BIOMETRICOS */
            $dispositivo_areaAnt1 = dispositivo_area::where('idDispositivos', '=', $request->idDisposEd_ed)->get();
            /* ----------------------------------------------------------------------------------- */
            /* PRIMERO COMPROBAMOS QUE NO HAYA AREAS DISPOSITIVOS YA EREGISTRADOS */
            if ($dispositivo_areaAnt1->isNotEmpty()) {

                foreach ($request->selectArea_ed as $areasD) {
                    $estadoA = false;
                    for ($index = 0; $index < sizeof($dispositivo_areaAnt1); $index++) {
                        /* SI TENEMOS REGISTRADO LOS ID DE EMPELADOS QUE EQUEREMOS INSERTAR */

                        if ($dispositivo_areaAnt1[$index]->area_id == $areasD) {
                            $estadoA = true;

                        }
                     }
                        if ($estadoA == false) {
                             /* SI NO COINCIDE LOS ID REGISTRADOS CON LOS NUEVOS ENTONCES REGISTRAMOS NUEVOS EMPLEADOS */
                             $dispositivo_areaReg = new dispositivo_area();
                             $dispositivo_areaReg->idDispositivos = $dispositivos->idDispositivos;
                             $dispositivo_areaReg->area_id = $areasD;
                             $dispositivo_areaReg->save();
                        }
                        else{
                            $dispositivo_areaActu =dispositivo_area::where('idDispositivos', '=', $request->idDisposEd_ed)
                            ->where('area_id', '=', $areasD)->get()->first();
                            $dispositivo_areaActu->estado = 1;
                            $dispositivo_areaActu->save();

                        }

                }

                  /* COMPARAMOS LOS REGISTRADOS CON LA LISTA DE AREAS PARA DESCARTAR LOS QUE YA NO TIENEN*/
                  foreach ($dispositivo_areaAnt1 as $areasRegis) {
                    $estadoERegA = false;
                    foreach ($request->selectArea_ed as $area) {
                        if ($areasRegis->area_id == $area) {
                            $estadoERegA = true;
                        }
                    }
                    if($estadoERegA==false){

                            $areasRegis->estado = 0;
                            $areasRegis->save();

                    }


                }
            } else {
                /* SI NO HAY AREAS ENTONCES REGISTRAMOS TODOS LOS QUE TENEMOS DE FRONTED */
                /* RECORREMOS ARRAY Y REGISTRAMOS AREAS DE DISPO */
                foreach ($request->selectArea_ed as $areas) {
                    $dispositivo_area = new dispositivo_area();
                    $dispositivo_area->idDispositivos = $dispositivos->idDispositivos;
                    $dispositivo_area->area_id = $areas;
                    $dispositivo_area->save();
                }
                /* ----------------------------------------------- */
            }
            /* ----------------------------------------------------------------------------------- */


        }

        }

    }

    public function selctEmpleado($id)
    {
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno',
                'p.perso_apMaterno as apMaterno')
            ->where('e.organi_id', '=', $id)
            ->groupBy('e.emple_id')
            ->get();

        return response()->json($empleados, 200);
    }

    public function DatosReporte(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $fecha = $request->get('fecha');

        $marcaciones = DB::table('marcacion_puerta as map')
            ->leftJoin('horario_empleado as hoeM', 'map.horarioEmp_id', '=', 'hoeM.horarioEmp_id')
            ->leftJoin('horario as horM', 'hoeM.horario_horario_id', '=', 'horM.horario_id')
            ->join('dispositivos as dis', 'map.dispositivos_idDispositivos', '=', 'dis.idDispositivos')
            ->select(
                'map.tipoMarcacionB',

                'dis.tipoDispositivo',
                'dis.dispo_descripUbicacion',
                'map.marcaMov_id as idMarcacion',
                'map.marcaMov_emple_id',
                DB::raw('IF(map.marcaMov_fecha is null, 0 , map.marcaMov_fecha) as entrada'),
                DB::raw('IF(map.horarioEmp_id is null, 0 , horM.horario_descripcion) as horario'),
                DB::raw('IF(map.marcaMov_salida is null, 0 , map.marcaMov_salida) as salida')
            )
            ->orderBy(DB::raw('IF(map.marcaMov_fecha is null, map.marcaMov_salida , map.marcaMov_fecha)', 'ASC'))
            ->whereDate(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida), DATE(map.marcaMov_fecha))'), '=', $fecha)
            ->where('map.marcaMov_emple_id', '=', $idEmpleado)
            ->where('dis.tipoDispositivo', '=', 3)
            ->get();

        return $marcaciones;

    }
}
