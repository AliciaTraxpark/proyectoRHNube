<?php

namespace App\Http\Controllers;


use App\captura;
use Illuminate\Http\Request;
use App\control;
use Illuminate\Support\Facades\DB;
use App\empleado;
use App\envio;
use DateTime;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index()
    {
        $empleado = DB::table('empleado as e')
            ->join('proyecto_empleado as pe', 'pe.empleado_emple_id', '=', 'e.emple_id')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('p.perso_id')
            ->get();
        return view('tareas.tareas', ['empleado' => $empleado]);
    }

    public function ReporteS()
    {
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('proyecto_empleado as pe', 'pe.empleado_emple_id', '=', 'e.emple_id')
            ->join('proyecto as pr', 'pr.Proye_id', '=', 'pe.Proyecto_Proye_id')
            ->leftJoin('envio as en', function ($join) {
                $join->on('en.idEmpleado', '=', 'e.emple_id');
            })
            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'en.Total_Envio')
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('e.emple_id')
            ->get();
        return view('tareas.reporteSemanal', ['empleado' => $empleado]);
    }

    public function EmpleadoReporte(Request $request)
    {
        $fecha = $request->get('fecha');
        $fechaF = explode("a", $fecha);
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('proyecto_empleado as pe', 'pe.empleado_emple_id', '=', 'e.emple_id')
            ->join('proyecto as pr', 'pr.Proye_id', '=', 'pe.Proyecto_Proye_id')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('e.emple_id')
            ->get();

        $sql = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.fecha_hora)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.fecha_hora)), DAY(DATE(cp.fecha_hora)) ),
        if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
        $horasTrabajadas = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('envio as en', 'en.idEmpleado', '=', 'e.emple_id')
            ->leftJoin('captura as cp', 'cp.idEnvio', '=', 'en.idEnvio')
            ->leftJoin('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
            ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
            ->select(
                'e.emple_id',
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                DB::raw('IF(h.id is null, DATE(cp.fecha_hora), DATE(h.start)) as fecha'),
                DB::raw('TIME(cp.fecha_hora) as hora_ini'),
                DB::raw('MAX(en.Total_Envio) as Total_Envio'),
                DB::raw('SUM(promedio.promedio) as promedio'),
                DB::raw('COUNT(promedio.id) as total'),
                DB::raw($sql),
                DB::raw('DATE(cp.fecha_hora) as fecha_captura')
            )
            ->where(DB::raw('IF(h.id is null, DATE(cp.fecha_hora), DATE(h.start))'), '>=', $fechaF[0])
            ->where(DB::raw('IF(h.id is null, DATE(cp.fecha_hora), DATE(h.start))'), '<=', $fechaF[1])
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('fecha_captura', 'e.emple_id')
            ->get();

        $respuesta = [];

        $date1 = new DateTime($fechaF[0]);
        $date2 = new DateTime($fechaF[1]);
        $diff = $date1->diff($date2);
        //Array
        $horas = array();
        $dias = array();
        $promedio = array();
        $total = array();

        for ($i = 0; $i <= $diff->days; $i++) {
            array_push($horas, "00:00:00");
            array_push($promedio, "0.0");
            array_push($total, "0");
            $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));

            array_push($dias, date('Y-m-j', $dia));
        }

        foreach ($empleados as $empleado) {
            array_push($respuesta, array(
                "id" => $empleado->emple_id, "nombre" => $empleado->nombre, "apPaterno" => $empleado->apPaterno,
                "apMaterno" => $empleado->apMaterno, "horas" => $horas, "fechaF" => $dias, "promedio" => $promedio, "total" => $total
            ));
        }
        for ($j = 0; $j < sizeof($respuesta); $j++) {
            for ($i = 0; $i < sizeof($horasTrabajadas); $i++) {
                if ($respuesta[$j]["id"] == $horasTrabajadas[$i]->emple_id) {
                    $respuesta[$j]["horas"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->Total_Envio == null ? "00:00:00" : $horasTrabajadas[$i]->Total_Envio;
                    $respuesta[$j]["promedio"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->promedio == null ? "00:00:00" : $horasTrabajadas[$i]->promedio;
                    $respuesta[$j]["total"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->total == null ? "0" : $horasTrabajadas[$i]->total;
                }
            }
            $respuesta[$j]['horas'] = array_reverse($respuesta[$j]['horas']);
            $respuesta[$j]['promedio'] = array_reverse($respuesta[$j]['promedio']);
            $respuesta[$j]['total'] = array_reverse($respuesta[$j]['total']);
        }
        return response()->json($respuesta, 200);
    }

    public function proyecto(Request $request)
    {
        $idempleado = $request->get('value');
        $proyecto = DB::table('empleado as e')
            ->join('proyecto_empleado as pe', 'pe.empleado_emple_id', '=', 'e.emple_id')
            ->join('proyecto as p', 'p.Proye_id', '=', 'pe.Proyecto_Proye_id')
            ->select('P.Proye_id', 'P.Proye_Nombre')
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_id', '=', $idempleado)
            ->get();
        return response()->json($proyecto, 200);
    }

    public function show(Request $request)
    {

        function controlAJson($array)
        {
            $resultado = array();

            foreach ($array as $captura) {
                $horaCaptura = explode(":", $captura->hora_ini);
                if (!isset($resultado[$horaCaptura[0]])) {
                    $resultado[$horaCaptura[0]] = array();
                }
                if (!isset($resultado[$horaCaptura[0]][$horaCaptura[1][0]])) {
                    $resultado[$horaCaptura[0]][$horaCaptura[1][0]] = array();
                }
                array_push($resultado[$horaCaptura[0]][$horaCaptura[1][0]], $captura);
            }
            return $resultado;
        }

        $idempleado = $request->get('value');
        $fecha = $request->get('fecha');
        $control = DB::table('empleado as e')
            ->join('envio as en', 'en.idEmpleado', '=', 'e.emple_id')
            ->join('captura as cp', 'cp.idEnvio', '=', 'en.idEnvio')
            ->join('promedio_captura as pc', 'pc.idCaptura', '=', 'cp.idCaptura')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'pc.idHorario')
            ->join('control as c', 'c.idEnvio', '=', 'en.idEnvio')
            ->join('proyecto as p', 'p.Proye_id', '=', 'c.Proyecto_Proye_id')
            ->select(
                DB::raw('IF(hd.id is null, DATE(cp.fecha_hora), DATE(hd.start))'),
                'P.Proye_id',
                'P.Proye_Nombre',
                'p.Proye_estado',
                'en.idEnvio',
                'cp.imagen',
                'cp.promedio',
                'en.hora_Envio',
                'cp.fecha_hora',
                'en.Total_Envio',
                DB::raw('DATE(cp.fecha_hora) as fecha'),
                DB::raw('TIME(cp.fecha_hora) as hora_ini'),
                'pc.promedio as prom'
            )
            ->where(DB::raw('IF(hd.id is null, DATE(cp.fecha_hora), DATE(hd.start))'), '=', $fecha)
            ->where('e.emple_id', '=', $idempleado)
            ->where('e.users_id', '=', Auth::user()->id)
            ->groupBy('cp.idCaptura')
            ->orderBy('cp.fecha_hora', 'asc')
            ->get();
        $control = controlAJson($control);
        return response()->json($control, 200);
    }
}
