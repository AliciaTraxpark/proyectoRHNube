<?php

namespace App\Http\Controllers;

use App\organizacion;
use App\usuario_organizacion;
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
            ->where('c.users_id', '=', Auth::user()->id)
            ->get()
            ->first();

        if ($eventos) {
            $respuesta = true;
            return response()->json($respuesta, 200);
        }
        return response()->json($respuesta, 200);
    }

    public function eventosUsuario()
    {
        $respuesta = false;
        $eventos = DB::table('eventos_usuario as eu')
            ->where('eu.users_id', '=', Auth::user()->id)
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
        $usuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuario_organizacion->organi_id)->get()->first();
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->get();

        $area = DB::table('empleado as e')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->select('a.area_descripcion', DB::raw('COUNT(a.area_descripcion) as Total'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('a.area_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "area" => $area, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function nivel()
    {
        $datos = [];
        $usuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuario_organizacion->organi_id)->get()->first();
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->get();

        $nivel = DB::table('empleado as e')
            ->join('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
            ->select('n.nivel_descripcion', DB::raw('COUNT(n.nivel_descripcion) as Total'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('n.nivel_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "nivel" => $nivel, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function contrato()
    {
        $datos = [];
        $usuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuario_organizacion->organi_id)->get()->first();
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->get();

        $contrato = DB::table('empleado as e')
            ->join('contrato as c', 'c.idEmpleado', '=', 'e.emple_id')
            ->join('tipo_contrato as tc', 'tc.contrato_id', '=', 'c.id_tipoContrato')
            ->select('tc.contrato_descripcion', DB::raw('COUNT(tc.contrato_descripcion) as Total'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('tc.contrato_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "contrato" => $contrato, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function centro()
    {
        $datos = [];
        $usuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuario_organizacion->organi_id)->get()->first();
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->get();

        $centro = DB::table('empleado as e')
            ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select('cc.centroC_descripcion', DB::raw('COUNT(cc.centroC_descripcion) as Total'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('cc.centroC_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "centro" => $centro, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function local()
    {
        $datos = [];
        $usuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuario_organizacion->organi_id)->get()->first();
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->get();

        $local = DB::table('empleado as e')
            ->join('local as l', 'e.emple_local', '=', 'l.local_id')
            ->select('l.local_descripcion', DB::raw('COUNT(l.local_descripcion) as Total'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('l.local_id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "local" => $local, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function departamento()
    {
        $datos = [];
        $usuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuario_organizacion->organi_id)->get()->first();
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->get();
        $departamento = DB::table('empleado as e')
            ->join('ubigeo_peru_departments as d', 'd.id', '=', 'e.emple_departamento')
            ->select('d.name', DB::raw('COUNT(d.name) as total'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('d.id')
            ->get();

        array_push($datos, array("empleado" => $empleado, "departamento" => $departamento, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function edad()
    {
        $datos = [];
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->get();

        $edad = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select(DB::raw('YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) as edad'), DB::raw('COUNT(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento)) as total'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('edad')
            ->get();

        array_push($datos, array("empleado" => $empleado, "edad" => $edad));
        return response()->json($datos, 200);
    }

    public function rangoE()
    {
        $datos = [];
        $usuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->get()->first();
        $organizacion = organizacion::where('organi_id', '=', $usuario_organizacion->organi_id)->get()->first();
        $empleado = DB::table('empleado as e')
            ->select(DB::raw('COUNT(e.emple_id) as totalE'))
            ->where('e.users_id', '=', Auth::user()->id)
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
                     ELSE CASE WHEN(YEAR(CURDATE()) - YEAR(p.perso_fechaNacimiento) > 50) THEN "DE 50 A MÁS "END END END END END as rango'
                ),
                DB::raw('COUNT(*) as total')
            )
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('rango')
            ->get();
        array_push($datos, array("empleado" => $empleado, "edad" => $edad, "organizacion" => $organizacion));
        return response()->json($datos, 200);
    }

    public function horarioDias()
    {
        $respuesta = false;
        $horario = DB::table('horario as h')
            ->where('h.user_id', '=', Auth::user()->id)
            ->get()
            ->first();

        if ($horario) {
            $respuesta = true;
            return response()->json($respuesta, 200);
        }
        return response()->json($respuesta, 200);
    }
}
