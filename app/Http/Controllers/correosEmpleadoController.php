<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class correosEmpleadoController extends Controller
{
    public function encode(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $codigoEmpresa = DB::table('users as u')
            ->join('usuario_organizacion as uo', 'uo.user_id', '=', 'u.id')
            ->select('uo.organi_id')
            ->where('u.id', '=', Auth::user()->id)
            ->get();
        $codigoEmpleado = DB::table('empleado as e')
            ->select('e.emple_codigo')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get();
        if ($codigoEmpleado != '') {
            $codigoHash = $codigoEmpresa + $idEmpleado + $codigoEmpleado;
            $encode = rtrim(strtr(base64_encode($codigoHash), '+/', '-_'));
        }
    }
}
