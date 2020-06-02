<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\proyecto;

class ProyectoController extends Controller
{
    public function index(){
       $proyecto=proyecto::all();
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
            ->get();

            return view('Proyecto.proyecto',['empleado'=> $empleado,'proyecto'=>$proyecto]);
    }

    public function store(Request $request){
        $proyecto=new proyecto();
        $proyecto->Proye_Nombre=$request->get('nombre');
        $proyecto->Proye_Detalle=$request->get('descripcion');
        $proyecto->save();


    }

}
