<?php

namespace App\Http\Controllers;

use App\actividad;
use Illuminate\Support\Facades\Hash;
use App\empleado;
use App\area;
use App\cargo;
use App\centro_costo;
use Illuminate\Http\Request;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_districts;
use App\tipo_documento;
use App\tipo_contrato;
use App\nivel;
use App\local;
use App\persona;
use App\proyecto;
use App\proyecto_empleado;
use App\tarea;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illiminate\Support\Facades\File;


class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth')->except('provincias','distritos','fechas','api','logueoEmpleado','apiTarea','apiActividad','editarApiTarea');
    }
    public function fechas($id){
        return tipo_contrato::where('contrato_id',$id)->get();
     }
    public function provincias($id){
        return ubigeo_peru_provinces::where('departamento_id',$id)->get();
    }
     public function distritos($id){
         return ubigeo_peru_districts::where('province_id',$id)->get();
    }
    public function index()
    {

        $departamento=ubigeo_peru_departments::all();
        $provincia=ubigeo_peru_provinces::all();
        $distrito=ubigeo_peru_districts::all();
        $tipo_doc=tipo_documento::all();
        $tipo_cont=tipo_contrato::all();
        $area=area::all();
        $cargo=cargo::all();
        $centro_costo=centro_costo::all();
        $nivel=nivel::all();
        $local=local::all();
        $empleado=empleado::all();
        $tabla_empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','c.cargo_descripcion',
            'a.area_descripcion','cc.centroC_descripcion')
            ->get();
            //dd($tabla_empleado);
        return view('empleado.empleado',['departamento'=>$departamento,'provincia'=>$provincia,'distrito'=>$distrito,
        'tipo_doc'=>$tipo_doc,'tipo_cont'=>$tipo_cont,'area'=>$area,'cargo'=>$cargo,'centro_costo'=>$centro_costo,
        'nivel'=>$nivel,'local'=>$local,'empleado'=>$empleado,'tabla_empleado'=> $tabla_empleado]);
    }
    public function cargarDatos()
    {   //DATOS DE TABLA PARA CARGAR EXCEL
        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('tipo_documento as tipoD', 'e.emple_tipoDoc', '=', 'tipoD.tipoDoc_id')
            ->leftJoin('ubigeo_peru_departments as depar', 'e.emple_departamento', '=', 'depar.id')
            ->leftJoin('ubigeo_peru_provinces as provi', 'e.emple_provincia', '=', 'provi.id')
            ->leftJoin('ubigeo_peru_districts as dist', 'e.emple_distrito', '=', 'dist.id')

            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('ubigeo_peru_departments as para', 'e.emple_departamentoN', '=', 'para.id')
            ->leftJoin('ubigeo_peru_provinces as proviN', 'e.emple_provinciaN', '=', 'proviN.id')
            ->leftJoin('ubigeo_peru_districts as distN', 'e.emple_distritoN', '=', 'distN.id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')


            ->select('e.emple_id','p.perso_id','p.perso_nombre','tipoD.tipoDoc_descripcion','e.emple_nDoc','p.perso_apPaterno',
            'p.perso_apMaterno', 'p.perso_fechaNacimiento' ,'p.perso_direccion','p.perso_sexo',
            'depar.id as depar','depar.name as deparNom','provi.id as proviId','provi.name as provi','dist.id as distId','dist.name as distNo',
            'c.cargo_descripcion', 'a.area_descripcion','cc.centroC_descripcion','para.id as iddepaN',
            'para.name as depaN','proviN.id as idproviN','proviN.name as proviN','distN.id as iddistN',
            'distN.name as distN','e.emple_id','c.cargo_id','a.area_id', 'cc.centroC_id','e.emple_tipoContrato',
            'e.emple_local','e.emple_nivel','e.emple_departamento','e.emple_provincia','e.emple_distrito','e.emple_foto as foto',
            'e.emple_celular','e.emple_telefono','e.emple_fechaIC','e.emple_fechaFC','e.emple_Correo')

            ->get();

        //

        return view('empleado.cargarEmpleado',['empleado'=>$empleado]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tabla(){
        $tabla_empleado1 = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','c.cargo_descripcion',
            'a.area_descripcion','cc.centroC_descripcion','e.emple_id')
            ->get();
            //dd($tabla_empleado);
        return view('empleado.tablaEmpleado',['tabla_empleado'=> $tabla_empleado1]);
    }

    public function api(){
        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','c.cargo_descripcion',
            'a.area_descripcion','cc.centroC_descripcion','e.emple_id')
            ->get();
            return $empleado;
    }

    public function logueoEmpleado(Request $request){
       $pass = DB::table('empleado as e')
        ->select('e.emple_pasword')
        ->where('e.emple_nDoc','=',$request->get('emple_nDoc'))
        ->get();

        if(count($pass)==0)  return response()->json(null,404);
        //if(password_verify($request->get("emple_pasword"),$pass[0]->emple_pasword)){
        if(Hash::check($request->get("emple_pasword"), $pass[0]->emple_pasword)){
            $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('proyecto_empleado as pe','pe.empleado_emple_id','=','e.emple_id')
            ->select('e.emple_id',DB::raw('CONCAT(p.perso_nombre ," ", p.perso_apPaterno, " ", p.perso_apMaterno) AS nombre'),'pe.proye_empleado_id','e.emple_estado')
            ->where('e.emple_nDoc','=',$request->get('emple_nDoc'))
            ->get();
            $factory = JWTFactory::customClaims([
                'sub' => env('API_ID'),
            ]);
            $payload = $factory->make();
            $token = JWTAuth::encode($payload);
            return response()->json(array('data' => $empleado, 'token' => $token->get()),200);
        }else{
            return response()->json(null,403);
        }
    }

    public function apiTarea(Request $request){
        $Proye_id = $request['Proye_id'];
        $proyecto = proyecto::where('Proye_id',$Proye_id)->first();
        if($proyecto){
            $tarea = new tarea();
            $tarea->Tarea_Nombre=$request['Tarea_Nombre'];
            $tarea->Proyecto_Proye_id=$Proye_id;
            $tarea->empleado_emple_id=$request['emple_id'];
            $tarea->save();
            $Tarea_Tarea_id=$tarea->Tarea_id;
            if($request['Activi_Nombre'] != ""){
                $actividad = new actividad();
                $actividad->Activi_Nombre=$request['Activi_Nombre'];
                $actividad->Tarea_Tarea_id=$Tarea_Tarea_id;
                $actividad->empleado_emple_id=$request['emple_id'];
                $actividad->save();
            }
            return response()->json($proyecto,200);
        }

        return response()->json($proyecto,400);
    }

    public function apiActividad(Request $request){
        $actividad = new actividad();
        $actividad->Activi_Nombre=$request['Activi_Nombre'];
        $actividad->Tarea_Tarea_id=$request['Tarea_Tarea_id'];
        $actividad->empleado_emple_id=$request['emple_id'];
        $actividad->save();
        return response()->json($actividad,200);
    }

    public function editarApiTarea(Request $request){
        $Tarea_id = $request['Tarea_id'];
        $Activi_id = $request['Activi_id'];
        $tarea = tarea::where('Tarea_id',$Tarea_id)->first();
        if($tarea){
            $tarea->Tarea_Nombre=$request['Tarea_Nombre'];
            if($request['Activi_id'] != ''){
                $actividad = actividad::where('Activi_id',$Activi_id)->first();
                if($actividad){
                    $actividad->Activi_Nombre=$request['Activi_Nombre'];
                    $actividad->save();
                    return response()->json($actividad,200);
                }
            }
            $tarea->save();
            return response()->json($tarea,200);
        }
        return response()->json($tarea,400);
    }
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $objEmpleado = json_decode($request->get('objEmpleado'),true);

        $persona=new persona();
        $persona->perso_nombre=$objEmpleado['nombres'];
        $persona->perso_apPaterno=$objEmpleado['apPaterno'];
        $persona->perso_apMaterno=$objEmpleado['apMaterno'];
        $persona->perso_direccion=$objEmpleado['direccion'];
        $persona->perso_fechaNacimiento=$objEmpleado['fechaN'];
        $persona->perso_sexo=$objEmpleado['tipo'];
        $persona->save();
        $emple_persona=$persona->perso_id;


        $empleado= new empleado();
        $empleado->emple_tipoDoc=$objEmpleado['documento'];
        $empleado->emple_nDoc=$objEmpleado['numDocumento'];
        $empleado->emple_persona=$emple_persona;
        if($objEmpleado['departamento'] != ''){
            $empleado->emple_departamentoN=$objEmpleado['departamento'];
            $empleado->emple_provinciaN=$objEmpleado['provincia'];
            $empleado->emple_distritoN=$objEmpleado['distrito'];
        }
        if($objEmpleado['cargo'] != ''){
            $empleado->emple_cargo=$objEmpleado['cargo'];
        }
        if($objEmpleado['area'] != ''){
            $empleado->emple_area=$objEmpleado['area'];
        }
        if($objEmpleado['centroc'] != ''){
            $empleado->emple_centCosto=$objEmpleado['centroc'];
        }

        if($objEmpleado['dep'] != ''){
            $empleado->emple_departamento=$objEmpleado['dep'];
            $empleado->emple_provincia=$objEmpleado['prov'];
            $empleado->emple_distrito=$objEmpleado['dist'];
        }
        if($objEmpleado['contrato'] != ''){
            $empleado->emple_tipoContrato=$objEmpleado['contrato'];
        }
        if($objEmpleado['local'] != ''){
            $empleado->emple_local=$objEmpleado['local'];
        }
        if($objEmpleado['nivel'] != ''){
            $empleado->emple_nivel=$objEmpleado['nivel'];
        }
            $empleado->emple_celular=$objEmpleado['celular'];
            $empleado->emple_telefono=$objEmpleado['telefono'];
        if($objEmpleado['fechaI'] != '' && $objEmpleado['fechaF'] != '' ){
            $empleado->emple_fechaIC=$objEmpleado['fechaI'];
            $empleado->emple_fechaFC=$objEmpleado['fechaF'];
        }
        $empleado->emple_Correo=$objEmpleado['correo'];
        $empleado->emple_foto='';

        if($request->hasFile('file')){
            $file = $request->file('file');
            $path = public_path() . '/fotosEmpleado';
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($path,$fileName);
            $empleado->emple_foto=$fileName;
        }

        $empleado->save();

        return json_encode(array('status'=>true));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $idempleado=$request->get('value');
        $departamento=ubigeo_peru_departments::all();
        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('tipo_documento as tipoD', 'e.emple_tipoDoc', '=', 'tipoD.tipoDoc_id')
            ->leftJoin('ubigeo_peru_departments as depar', 'e.emple_departamento', '=', 'depar.id')
            ->leftJoin('ubigeo_peru_provinces as provi', 'e.emple_provincia', '=', 'provi.id')
            ->leftJoin('ubigeo_peru_districts as dist', 'e.emple_distrito', '=', 'dist.id')

            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('ubigeo_peru_departments as para', 'e.emple_departamentoN', '=', 'para.id')
            ->leftJoin('ubigeo_peru_provinces as proviN', 'e.emple_provinciaN', '=', 'proviN.id')
            ->leftJoin('ubigeo_peru_districts as distN', 'e.emple_distritoN', '=', 'distN.id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')


            ->select('e.emple_id','p.perso_id','p.perso_nombre','tipoD.tipoDoc_descripcion','e.emple_nDoc','p.perso_apPaterno',
            'p.perso_apMaterno', 'p.perso_fechaNacimiento' ,'p.perso_direccion','p.perso_sexo',
            'depar.id as depar','depar.id as deparNo','provi.id as proviId','provi.name as provi','dist.id as distId','dist.name as distNo',
            'c.cargo_descripcion', 'a.area_descripcion','cc.centroC_descripcion','para.id as iddepaN',
            'para.id as depaN','proviN.id as idproviN','proviN.name as proviN','distN.id as iddistN',
            'distN.name as distN','e.emple_id','c.cargo_id','a.area_id', 'cc.centroC_id','e.emple_tipoContrato',
            'e.emple_local','e.emple_nivel','e.emple_departamento','e.emple_provincia','e.emple_distrito','e.emple_foto as foto',
            'e.emple_celular','e.emple_telefono','e.emple_fechaIC','e.emple_fechaFC','e.emple_Correo')
            ->where('emple_id','=',$idempleado)
            ->get();
        return $empleado;
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function edit(empleado $empleado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idE)
    {

        $objEmpleado = json_decode($request->get('objEmpleadoA'),true);
        if($request==null)return false;
        $empleado= Empleado::findOrFail($idE);

        if($objEmpleado['cargo_v'] != ''){
            $empleado->emple_cargo=$objEmpleado['cargo_v'];
        }
        if($objEmpleado['area_v'] != ''){
            $empleado->emple_area=$objEmpleado['area_v'];
        }
        if($objEmpleado['departamento_v'] != ''){
            $empleado->emple_departamentoN=$objEmpleado['departamento_v'];
            $empleado->emple_provinciaN=$objEmpleado['provincia_v'];
            $empleado->emple_distritoN=$objEmpleado['distrito_v'];
        }
        if($objEmpleado['centroc_v'] != ''){
            $empleado->emple_centCosto=$objEmpleado['centroc_v'];
        }
        if($objEmpleado['dep_v'] != ''){
            $empleado->emple_departamento=$objEmpleado['dep_v'];
            $empleado->emple_provincia=$objEmpleado['prov_v'];
            $empleado->emple_distrito=$objEmpleado['dist_v'];
        }
        if($objEmpleado['contrato_v'] != ''){
            $empleado->emple_tipoContrato=$objEmpleado['contrato_v'];
        }
        if($objEmpleado['local_v'] != ''){
            $empleado->emple_local=$objEmpleado['local_v'];
        }
        if($objEmpleado['nivel_v'] != ''){
            $empleado->emple_nivel=$objEmpleado['nivel_v'];
        }
        $empleado->emple_celular=$objEmpleado['celular_v'];
        $empleado->emple_telefono=$objEmpleado['telefono_v'];
        $empleado->emple_Correo=$objEmpleado['correo_v'];
        $empleado->emple_fechaIC=$objEmpleado['fechaI_v'];
        $empleado->emple_fechaFC=$objEmpleado['fechaF_v'];
        if($request->hasfile('file')){
            $file = $request->file('file');
            $path = public_path() . '/fotosEmpleado';
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($path,$fileName);
            $empleado->emple_foto=$fileName;
        }
        $empleado->save();

        $idpersona = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->select('p.perso_id')
        ->where('emple_id','=',$idE)
        ->get();

        $persona = Persona::findOrFail($idpersona[0]->perso_id);
        $persona->perso_nombre=$objEmpleado['nombres_v'];
        $persona->perso_apPaterno=$objEmpleado['apPaterno_v'];
        $persona->perso_apMaterno=$objEmpleado['apMaterno_v'];
        $persona->perso_direccion=$objEmpleado['direccion_v'];
        $persona->perso_fechaNacimiento=$objEmpleado['fechaN_v'];
        $persona->perso_sexo=$objEmpleado['tipo_v'];
        $persona->save();
        return json_encode(array('status'=>true));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {



        $empleado = empleado::find($request->get('id'));


        $empleado->delete();

        $persona= persona::where('perso_id','=', $empleado->emple_persona)->delete();


    }

    public function eliminarFoto(Request $request,$v_id){
        $empleado= Empleado::findOrFail($v_id);
        $idFoto= DB::table('empleado as e')
        ->select('e.emple_foto')
        ->where('emple_id','=',$v_id)
        ->get();
        unlink(public_path().'/fotosEmpleado/'.$idFoto[0]->emple_foto);
        $empleado->emple_foto="";
        $empleado->save();
        return json_encode(array("result"=>true));
    }

     public function deleteAll(Request $request)
    {
        $ids = $request->ids;

        $empleado = empleado::whereIn('emple_id',explode(",",$ids))->get();
        //$empleado = empleado::find(explode(",",$ids))->first();

        $array = array();
        foreach($empleado as $t){

        $array[] = $t->emple_persona;

        } $idem = implode(',', $array);

            //dd($idem);

        $empleado->each->delete();
        $persona= persona::whereIn('perso_id',explode(",",$idem))->get();
        $persona->each->delete();
        //$persona= persona::where('perso_id','=',$empleado->emple_persona);
        //dd($empleado->emple_persona);
       
    }



}
