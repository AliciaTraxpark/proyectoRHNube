<?php

namespace App\Http\Controllers;

use App\marcacion_biometrico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class apiBiometricoController extends Controller
{
    //
    public function marcacionBiometrico(Request $request)
    {
        $fechaHoy = Carbon::now('America/Lima');
        $horaActual = $fechaHoy->isoFormat('YYYY-MM-DD HH:mm:ss');

        foreach ($request->all() as $req) {

            $marcacion_biometrico = new marcacion_biometrico();
            $marcacion_biometrico->tipoMarcacion = $req['tipoMarcacion'];
            /* VALIDANDO FECHA  */
            if(Carbon::create($req['fechaMarcacion'])->gt(Carbon::create($horaActual))){
                return response()->json(array('status' => 500, 'title' => 'No se pudo validar fecha',
                'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
            } else{
                $marcacion_biometrico->fechaMarcacion = $req['fechaMarcacion'];
            }

            $marcacion_biometrico->idEmpleado = $req['idEmpleado'];
            $marcacion_biometrico->idDisposi = $req['idDisposi'];
            /* VALIDANDO EMPLEADOIIIII */
            $empleados = DB::table('empleado as e')
                ->join('organizacion as or', 'e.organi_id', '=', 'or.organi_id')
                ->where('e.emple_id', '=', $req['idEmpleado'])
                ->get()->first();
            if ($empleados) {
                $marcacion_biometrico->organi_id = $empleados->organi_id;

                if (empty($req['idHoraEmp'])) {} else {
                    $marcacion_biometrico->idHoraEmp = $req['idHoraEmp'];
                }

                $marcacion_biometrico->save();
            } else {
                return response()->json(array('status' => 500, 'title' => 'No se pudo encontrar empleado',
                    'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
            }

        }

        if ($marcacion_biometrico) {
            return response()->json(array('status' => 200, 'title' => 'Marcacion registrada correctamente',
                'detail' => 'Marcacion registrada correctamente en la base de datos'), 200);
        } else {
            return response()->json(array('status' => 400, 'title' => 'No se pudo registrar marcacion',
                'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 400);
        }
    }
}
