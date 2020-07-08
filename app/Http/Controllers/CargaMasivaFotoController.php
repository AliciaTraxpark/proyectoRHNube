<?php

namespace App\Http\Controllers;

use App\empleado;
use App\persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargaMasivaFotoController extends Controller
{
    public function subirfoto(Request $request)
    {
        $file = $request->file('fileMasiva');
        $idempleado = explode(".", $file[0]->getClientOriginalName());
        $empleado = empleado::where('emple_nDoc', '=', $idempleado)->get()->first();
        if ($empleado) {
            $persona = persona::where('perso_id', '=', $empleado->emple_persona)->get()->first();
            $path = public_path() . '/fotosEmpleado';
            $fileName = uniqid() . $file[0]->getClientOriginalName();
            $file[0]->move($path, $fileName);
            $empleado->emple_foto = $fileName;
            $empleado->save();
            return json_encode($persona->perso_nombre, 200);
        }
        return response()->json('Empleado no encontrado', 400);
    }
}
