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
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {
            $empleado = DB::table('empleado as e')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->where('invi.estado', '=', 1)
                ->groupBy('p.perso_id')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('p.perso_id')
                ->get();
        }

        return view('tareas.tareas', ['empleado' => $empleado]);
    }

    public function ReporteS()
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {

            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->where('invi.estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }


        return view('tareas.reporteSemanal', ['empleado' => $empleado]);
    }

    public function ReporteM()
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {

            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->where('invi.estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }


        return view('tareas.reporteMensual', ['empleado' => $empleado]);
    }

    public function empleadoRefresh()
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion->rol_id == 3) {

            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->where('invi.estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }


        return response()->json($empleado, 200);
    }

    public function EmpleadoReporte(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $fecha = $request->get('fecha');
        $fechaF = explode("a", $fecha);
        if ($usuario_organizacion->rol_id == 3) {
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->where('invi.estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        } else {
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad as a', 'a.empleado_emple_id', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }


        $sql = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_fin)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_fin)), DAY(DATE(cp.hora_fin)) ),
        if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
        $horasTrabajadas = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
            ->join('actividad as a', 'a.Activi_id', '=', 'cp.idActividad')
            ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
            ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
            ->select(
                'e.emple_id',
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                DB::raw('IF(h.id is null, DATE(cp.hora_fin), DATE(h.start)) as fecha'),
                DB::raw('TIME(cp.hora_fin) as hora_ini'),
                DB::raw('TIME_FORMAT(SEC_TO_TIME(SUM(promedio.tiempo_rango)), "%H:%i:%s") as Total_Envio'),
                DB::raw('SUM(promedio.promedio) as promedio'),
                DB::raw('COUNT(promedio.idCaptura) as total'),
                DB::raw('SUM(cp.actividad) as sumaA'),
                DB::raw('SUM(promedio.tiempo_rango) as sumaR'),
                DB::raw($sql),
                DB::raw('DATE(cp.hora_fin) as fecha_captura')
            )
            ->where(DB::raw('IF(h.id is null, DATE(cp.hora_fin), DATE(h.start))'), '>=', $fechaF[0])
            ->where(DB::raw('IF(h.id is null, DATE(cp.hora_fin), DATE(h.start))'), '<=', $fechaF[1])
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->groupBy('e.emple_id')
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
        $sumaActividad = array();
        $sumaRango = array();

        for ($i = 0; $i <= $diff->days; $i++) {
            array_push($horas, "00:00:00");
            array_push($promedio, "0.0");
            array_push($total, "0");
            array_push($sumaActividad,"0");
            array_push($sumaRango,"0");
            $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));

            array_push($dias, date('Y-m-j', $dia));
        }

        foreach ($empleados as $empleado) {
            array_push($respuesta, array(
                "id" => $empleado->emple_id, "nombre" => $empleado->nombre, "apPaterno" => $empleado->apPaterno,
                "apMaterno" => $empleado->apMaterno, "horas" => $horas, "fechaF" => $dias, "promedio" => $promedio, "total" => $total,
                "sumaActividad" => $sumaActividad, "sumaRango" => $sumaRango
            ));
        }
        for ($j = 0; $j < sizeof($respuesta); $j++) {
            for ($i = 0; $i < sizeof($horasTrabajadas); $i++) {
                if ($respuesta[$j]["id"] == $horasTrabajadas[$i]->emple_id) {
                    $respuesta[$j]["horas"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->Total_Envio == null ? "00:00:00" : $horasTrabajadas[$i]->Total_Envio;
                    $respuesta[$j]["promedio"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->promedio == null ? "0.0" : $horasTrabajadas[$i]->promedio;
                    $respuesta[$j]["total"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->total == null ? "0" : $horasTrabajadas[$i]->total;
                    $respuesta[$j]["sumaActividad"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->sumaA == null ? "0" : $horasTrabajadas[$i]->sumaA;
                    $respuesta[$j]["sumaRango"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->sumaR == null ? "0" : $horasTrabajadas[$i]->sumaR;
                }
            }
            $respuesta[$j]['horas'] = array_reverse($respuesta[$j]['horas']);
            $respuesta[$j]['promedio'] = array_reverse($respuesta[$j]['promedio']);
            $respuesta[$j]['total'] = array_reverse($respuesta[$j]['total']);
            $respuesta[$j]['sumaActividad'] = array_reverse($respuesta[$j]['sumaActividad']);
            $respuesta[$j]['sumaRango'] = array_reverse($respuesta[$j]['sumaRango']);
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
            ->where('e.organi_id', '=', session('sesionidorg'))
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
                $horaCaptura = explode(":", $captura->hora);
                if (!isset($resultado[$horaCaptura[0]])) {
                    $resultado[$horaCaptura[0]] = array("horaCaptura" => $horaCaptura[0], "minutos" => array());
                }
                if (!isset($resultado[$horaCaptura[0]]["minutos"][$horaCaptura[1][0]])) {
                    $resultado[$horaCaptura[0]]["minutos"][$horaCaptura[1][0]] = array();
                }
                array_push($resultado[$horaCaptura[0]]["minutos"][$horaCaptura[1][0]], $captura);
            }
            return array_values($resultado);
        }

        $idempleado = $request->get('value');
        $fecha = $request->get('fecha');
        $control = DB::table('empleado as e')
            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
            ->join('actividad as a', 'a.Activi_id', '=', 'cp.idActividad')
            ->join('promedio_captura as pc', 'pc.idCaptura', '=', 'cp.idCaptura')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'pc.idHorario')
            ->select(
                DB::raw('IF(hd.id is null, DATE(cp.hora_fin), DATE(hd.start))'),
                'a.Activi_id',
                'a.Activi_Nombre',
                'a.estado',
                'cp.imagen',
                'cp.actividad',
                'cp.hora_fin',
                DB::raw('DATE(cp.hora_fin) as fecha'),
                DB::raw('TIME(cp.hora_fin) as hora'),
                'pc.promedio as prom',
                'pc.tiempo_rango as rango',
                DB::raw('TIME(cp.hora_ini) as hora_ini'),
                DB::raw('TIME(cp.hora_fin) as hora_fin')
            )
            ->where(DB::raw('IF(hd.id is null, DATE(cp.hora_fin), DATE(hd.start))'), '=', $fecha)
            ->where('e.emple_id', '=', $idempleado)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->groupBy('cp.idCaptura')
            ->orderBy('cp.hora_ini', 'asc')
            ->get();
        $control = controlAJson($control);
        return response()->json($control, 200);
    }
}
