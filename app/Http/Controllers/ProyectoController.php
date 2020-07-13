<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Support\Facades\Auth;
use App\empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\proyecto;
use App\proyecto_empleado;
use Illuminate\Support\Facades\Response;
class ProyectoController extends Controller
{   public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index()
    {
        $proyecto = proyecto::where('idUser', '=', Auth::user()->id)->get();
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();

        return view('Proyecto.proyecto', ['empleado' => $empleado, 'proyecto' => $proyecto]);
    }

    public function store(Request $request)
    {
        $proyecto = new proyecto();
        $proyecto->Proye_Nombre = $request->get('nombre');
        $proyecto->Proye_Detalle = $request->get('descripcion');
        $proyecto->Proye_estado = 1;
        $proyecto->idUser = Auth::user()->id;
        $proyecto->save();
    }
    public function proyectoV(Request $request)
    {
        $proyecto = proyecto::find($request->get('id'));
        return $proyecto;
    }

    public function registrarPrEm(Request $request)
    {

        $ids = $request->empleado;

        //dd($ids);
        foreach ($ids as $idse) {
            $proyecto_empleado = new proyecto_empleado();
            $proyecto_empleado->Proyecto_Proye_id = $request->get('proyecto');
            $proyecto_empleado->empleado_emple_id = $idse;
            $proyecto_empleado->save();
        }
    }

    public function selectValidar(Request $request)
    {
        $idproyecto = $request->get('id');
        /* $empleadoSelect = DB::table('empleado as em')
            ->leftJoin('proyecto_empleado as pe', 'em.emple_id', '=', 'pe.empleado_emple_id')
            ->join('persona as p', 'em.emple_persona', '=', 'p.perso_id')
            ->select('em.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','pe.Proyecto_Proye_id')
            ->where('pe.Proyecto_Proye_id','!=',$idproyecto)
            ->orwhereNull('pe.Proyecto_Proye_id')
            ->get(); */

        $empleadoSelect = DB::table('proyecto_empleado as pe')
            ->select('pe.empleado_emple_id', 'pe.Proyecto_Proye_id')
            ->where('pe.Proyecto_Proye_id', '=', $idproyecto)
            ->get();
        return response()->json($empleadoSelect);
        // return $empleadoSelect;


    }
    public function eliminar(Request $request)
    {
        $idproyecto = $request->get('idproyecto');
        $proyecto_empleado = proyecto_empleado::where('Proyecto_Proye_id', '=', $idproyecto)->delete();
        $proyecto = proyecto::where('Proye_id', '=', $idproyecto)->delete();
    }

    public function empleadosTabla(Request $request){
        $proyecto = proyecto::where('Proye_id','=',$request->get('id'))->get();
        foreach ($proyecto as $proyectos){
            $proyectoEmp=DB::table('proyecto_empleado as pe')
            ->join('empleado as e','pe.empleado_emple_id','=','e.emple_id')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->where('Proyecto_Proye_id','=',$proyectos->Proye_id)
            ->get();

        /* if(!$proyectoEmp->isEmpty()){
            foreach ( $proyectoEmp as $proyectoEmps){
            $empleadoTabE = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
            ->where('e.emple_id','=',$proyectoEmps->empleado_emple_id)
            ->get();
            return [$proyecto,$empleadoTabE];
            }

            } */

        }  return [$proyecto,$proyectoEmp];



    }

    public function eliminarEmpleado(Request $request){
    $id=$request->id;
    $proyecto_empleado = proyecto_empleado::where('proye_empleado_id', '=', $id)->delete();
    }
    public function editarProyecto(Request $request){
        $proyecto =proyecto::where('Proye_id', '=', $request->idPr)
        ->update(['Proye_Nombre' =>  $request->nombreP,'Proye_Detalle' =>  $request->detalleP]);


    }


}
