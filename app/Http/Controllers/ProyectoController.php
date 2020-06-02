<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    public function index(){
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
            ->get();

            return view('Proyecto.proyecto',['empleado'=> $empleado]);
    }

    public function store(Request $request){
        

    }

}
