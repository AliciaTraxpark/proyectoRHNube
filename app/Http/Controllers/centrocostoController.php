<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\centro_costo;
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
            ->groupBy('c.centroC_id')
            ->get();

        return response()->json($centroC, 200);
    }

    public function centroCosto(Request $request)
    {
        $id = $request->get('id');
        $empleadoSinCentro = [];
        $respuesta = [];
        $centro = centro_costo::select('centroC_descripcion as descripcion', 'centroC_id as id')
            ->where('centroC_id', '=', $id)->get()->first();
        // TODO LOS EMPLEADOS
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get();

        // * EMPLEADOS EN CENTRO DE COSTO
        $empleadoCentro = DB::table('centro_costo as c')
            ->join('empleado as e', 'e.emple_centCosto', '=', 'c.centroC_id')
            ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('c.centroC_id', '=', $centro->id)
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
        // * DATOS PARA RESULTADO
        array_push($respuesta, array("select" => $empleadoCentro, "noSelect" => $empleadoSinCentro, "centro" => $centro));

        return response()->json($respuesta, 200);
    }
}
