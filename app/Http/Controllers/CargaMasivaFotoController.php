<?php

namespace App\Http\Controllers;

use App\empleado;
use Illuminate\Http\Request;

class CargaMasivaFotoController extends Controller
{
    public function empleado(){
        $empleado = empleado::all();
        return response()->json($empleado,200);
    }
}
