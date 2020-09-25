<?php

namespace App\Http\Controllers;

use App\organizacion;
use App\usuario_organizacion;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function respuestaCalendario()
    {
        $respuesta = false;
        $eventos = DB::table('calendario as c')
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->get()
            ->first();

        if ($eventos) {
            $respuesta = true;
            return response()->json($respuesta, 200);
        }
        return response()->json($respuesta, 200);
    }

    public function area()
    {
        $datos = [];

        $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('e.emple_estado', '=', 1)
                ->where('invi.estado', '=', 1)
                ->get();

            $area = DB::table('empleado as e')
                ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->select('a.area_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->where('invi.estado', '=', 1)
                ->groupBy('e.emple_area')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $area = DB::table('empleado as e')
                ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                ->select(
                    'a.area_descripcion',
                    DB::raw('COUNT(e.emple_id) as Total')
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_area')
                ->get();
        }


        array_push($datos, array("empleado" => $empleado, "area" => $area, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function nivel()
    {
        $datos = [];

        $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $nivel = DB::table('empleado as e')
                ->join('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
                ->select('n.nivel_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_nivel')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $nivel = DB::table('empleado as e')
                ->leftJoin('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
                ->select('n.nivel_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_nivel')
                ->get();
        }


        array_push($datos, array("empleado" => $empleado, "nivel" => $nivel, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function contrato()
    {
        $datos = [];

        $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $contrato = DB::table('empleado as e')
                ->leftJoin('contrato as c', 'c.idEmpleado', '=', 'e.emple_id')
                ->leftJoin('tipo_contrato as tc', 'tc.contrato_id', '=', 'c.id_tipoContrato')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->select('tc.contrato_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('invi.estado', '=', 1)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('c.idEmpleado')
                ->get();
        } else {

            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $contrato = DB::table('empleado as e')
                ->leftJoin('contrato as c', 'c.idEmpleado', '=', 'e.emple_id')
                ->leftJoin('tipo_contrato as tc', 'tc.contrato_id', '=', 'c.id_tipoContrato')
                ->select('tc.contrato_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('c.idEmpleado')
                ->get();
        }


        array_push($datos, array("empleado" => $empleado, "contrato" => $contrato, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function centro()
    {
        $datos = [];

        $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $centro = DB::table('empleado as e')
                ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->select('cc.centroC_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_centCosto')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $centro = DB::table('empleado as e')
                ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->select('cc.centroC_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_centCosto')
                ->get();
        }



        array_push($datos, array("empleado" => $empleado, "centro" => $centro, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function local()
    {
        $datos = [];

        $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $local = DB::table('empleado as e')
                ->leftJoin('local as l', 'e.emple_local', '=', 'l.local_id')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->select('l.local_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_local')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();

            $local = DB::table('empleado as e')
                ->leftJoin('local as l', 'e.emple_local', '=', 'l.local_id')
                ->select('l.local_descripcion', DB::raw('COUNT(e.emple_id) as Total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_local')
                ->get();
        }


        array_push($datos, array("empleado" => $empleado, "local" => $local, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function departamento()
    {
        $datos = [];

        $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();
            $departamento = DB::table('empleado as e')
                ->leftJoin('ubigeo_peru_departments as d', 'd.id', '=', 'e.emple_departamento')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->select('d.name', DB::raw('COUNT(e.emple_id) as total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_departamento')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();
            $departamento = DB::table('empleado as e')
                ->leftJoin('ubigeo_peru_departments as d', 'd.id', '=', 'e.emple_departamento')
                ->select('d.name', DB::raw('COUNT(e.emple_id) as total'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_departamento')
                ->get();
        }


        array_push($datos, array("empleado" => $empleado, "departamento" => $departamento, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function rangoE()
    {
        $datos = [];

        $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();
            $edad = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->where('invi.estado', '=', 1)
                ->select(
                    DB::raw(
                        'CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 18 AND 24) THEN "MEN. DE 24"
                    ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 25 AND 30) THEN "DE 25 A 30"
                    ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 31 AND 40) THEN "DE 31 A 40"
                    ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 41 AND 50) THEN "DE 41 A 50"
                     ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 50 AND 100) THEN "DE 50 A MÁS "END END END END END as rango'
                    ),
                    DB::raw('COUNT(*) as total')
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('rango')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();
            $edad = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->select(
                    DB::raw(
                        'CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 18 AND 24) THEN "MEN. DE 24"
                    ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 25 AND 30) THEN "DE 25 A 30"
                    ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 31 AND 40) THEN "DE 31 A 40"
                    ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 41 AND 50) THEN "DE 41 A 50"
                     ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) BETWEEN 50 AND 100) THEN "DE 50 A MÁS "END END END END END as rango'
                    ),
                    DB::raw('COUNT(*) as total')
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('rango')
                ->get();
        }

        array_push($datos, array("empleado" => $empleado, "edad" => $edad, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function horarioDias()
    {
        $respuesta = false;
        $horario = DB::table('horario as h')
            ->where('h.organi_id', '=', session('sesionidorg'))
            ->get()
            ->first();

        if ($horario) {
            $respuesta = true;
            return response()->json($respuesta, 200);
        }
        return response()->json($respuesta, 200);
    }

    // DASHBOARD PARA CONTROL REMOTO
    public function dashboardCR()
    {
        return view('dashboardCR');
    }
    public function globalControlRemoto()
    {
        $actividadCR = DB::table('empleado as e')
            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
            ->join('promedio_captura as pc', 'pc.idCaptura', '=', 'cp.idCaptura')
            ->select(
                DB::raw('SUM(pc.tiempo_rango) as totalRango'),
                DB::raw('SUM(cp.actividad) as totalActividad')
            )
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get()
            ->first();

        return response()->json($actividadCR, 200);
    }

    public function actividadArea(Request $request)
    {
        $fechas = $request->get('fechas');
        $area = DB::table('empleado as e')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->select(
                'a.area_descripcion',
                'a.area_id',
                'e.emple_id'
            )
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->groupBy('a.area_id')
            ->get();

        $respuesta = [];
        $data = [];
        foreach ($area as $a) {
            array_push($respuesta, array("idArea" => $a->area_id, "area" => $a->area_descripcion, "data" => $data));
        }
        for ($i = 0; $i < sizeof($respuesta); $i++) {
            foreach ($fechas as $fecha) {
                // DB::enableQueryLog();
                $actividadArea = DB::table('empleado as e')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('promedio_captura as pc', 'pc.idCaptura', '=', 'cp.idCaptura')
                    ->select(
                        'e.emple_id',
                        DB::raw('SUM(pc.tiempo_rango) as totalRango'),
                        DB::raw('SUM(cp.actividad) as totalActividad'),
                        DB::raw('((SUM(cp.actividad)/SUM(pc.tiempo_rango))*100) as division')
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_area', '=', $respuesta[$i]['idArea'])
                    ->whereRaw("DATE(cp.hora_fin) ='$fecha'")
                    ->get()
                    ->first();
                // dd(DB::getQueryLog());
                if (is_null($actividadArea->division) === false) {
                    array_push($data, $actividadArea->division);
                } else {
                    array_push($data, 0);
                }
            }
            $respuesta[$i]["data"] = $data;
            unset($data);
            $data = array();
        }

        return response()->json($respuesta, 200);
    }

    public function fechaOrganizacion()
    {
        $organizacion = DB::table('organizacion as o')->select(DB::raw('DATE(o.created_at) as created_at'))->where('o.organi_id', '=', session('sesionidorg'))->get()->first();

        return response()->json($organizacion, 200);
    }
}
