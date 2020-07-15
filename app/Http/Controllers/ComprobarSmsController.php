<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComprobarSmsController extends Controller
{
    public function comprobar(Request $request)
    {
        $respuesta = [];
        $codigoR = true;
        $usuarioR = true;
        if ($request->get('codigo') != null) {
            $codigo = $request->get('codigo');
            $decode = base_convert(intval($codigo), 10, 36);
            $explode = explode("c", $decode);
            if (Auth::user()->id == $explode[0]) {
                $user = User::where('id', Auth::user()->id)->first();
                $user->email_verified_at = Carbon::now();
                $user->confirmation_code = null;
                $user->save();
                array_push($respuesta, array("codigo" => $codigoR, "user" => $usuarioR));
                return response()->json($respuesta, 200);
            }
            $codigoR = false;
            array_push($respuesta, array("codigo" => $codigoR, "user" => $usuarioR));
            return response()->json($respuesta, 200);
        }
        $usuarioR = false;
        array_push($respuesta, array("codigo" => $codigoR, "user" => $usuarioR));
        return response()->json($respuesta, 200);
    }
}
