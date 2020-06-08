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
        $empleado = empleado::where('emple_nDoc',$idempleado)->first();
        if($empleado){
            $path = public_path() . '/fotosEmpleado';
            $fileName = uniqid().$file[0]->getClientOriginalName();
            $file[0]->move($path,$fileName);
            $empleado->emple_foto=$fileName;
            $empleado->save();
            return json_encode(array('status'=>true));
        }
        return response()->json('Empleado no encontrado',400);
    }
}
