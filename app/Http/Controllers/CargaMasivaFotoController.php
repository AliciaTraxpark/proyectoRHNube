<?php

namespace App\Http\Controllers;

use App\empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargaMasivaFotoController extends Controller
{
    public function subirfoto(Request $request){
        $file = $request->file('fileMasiva');
        $idempleado=explode(".",$file[0]->getClientOriginalName());
        $empleado= DB::table('empleado as e')
            ->where('e.emple_nDoc','=',$idempleado[0])
            ->get();
        $empleado->emple_foto=$idempleado;
        return json_encode(array('status'=>true));
    }
}
