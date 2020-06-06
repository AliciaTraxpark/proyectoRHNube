<?php

namespace App\Http\Controllers;

use App\empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargaMasivaFotoController extends Controller
{
    public function empleado(){
        $empleado = empleado::all();
        return response()->json($empleado,200);
    }

    public function subirfoto(Request $request){
        $idempleado=$request->get('foto');
        $empleado= DB::table('empleado as e')
            ->where('e.emple_nroDoc','=',$idempleado)
            ->get();
        $empleado->emple_foto=$idempleado;
        return json_encode(array('status'=>true));
    }
}
