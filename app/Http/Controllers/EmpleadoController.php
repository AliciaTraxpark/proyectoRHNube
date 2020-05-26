<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\DB;
use Illiminate\Support\Facades\File;


class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


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
    {

        return view('empleado.cargarEmpleado');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tabla(){
        $tabla_empleado1 = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select('p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno','c.cargo_descripcion',
            'a.area_descripcion','cc.centroC_descripcion','e.emple_id')
            ->get();
            //dd($tabla_empleado);
        return view('empleado.tablaEmpleado',['tabla_empleado'=> $tabla_empleado1]);
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
        $empleado->emple_departamentoN=$objEmpleado['departamento'];
        $empleado->emple_provinciaN=$objEmpleado['provincia'];
        $empleado->emple_distritoN=$objEmpleado['distrito'];
        $empleado->emple_cargo=$objEmpleado['cargo'];
        $empleado->emple_area=$objEmpleado['area'];
        $empleado->emple_centCosto=$objEmpleado['centroc'];
        $empleado->emple_departamento=$objEmpleado['dep'];
        $empleado->emple_provincia=$objEmpleado['prov'];
        $empleado->emple_distrito=$objEmpleado['dist'];
        $empleado->emple_tipoContrato=$objEmpleado['contrato'];
        $empleado->emple_local=$objEmpleado['local'];
        $empleado->emple_nivel=$objEmpleado['nivel'];

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

            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('tipo_documento as tipoD', 'e.emple_tipoDoc', '=', 'tipoD.tipoDoc_id')
            ->join('ubigeo_peru_departments as depar', 'e.emple_departamento', '=', 'depar.id')
            ->join('ubigeo_peru_provinces as provi', 'e.emple_provincia', '=', 'provi.id')
            ->join('ubigeo_peru_districts as dist', 'e.emple_distrito', '=', 'dist.id')

            ->join('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->join('ubigeo_peru_departments as para', 'e.emple_departamentoN', '=', 'para.id')
            ->join('ubigeo_peru_provinces as proviN', 'e.emple_provinciaN', '=', 'proviN.id')
            ->join('ubigeo_peru_districts as distN', 'e.emple_distritoN', '=', 'distN.id')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')


            ->select('e.emple_id','p.perso_id','p.perso_nombre','tipoD.tipoDoc_descripcion','e.emple_nDoc','p.perso_apPaterno',
            'p.perso_apMaterno', 'p.perso_fechaNacimiento' ,'p.perso_direccion','p.perso_sexo',
            'depar.id as depar','depar.id as deparNo','provi.id as proviId','provi.name as provi','dist.id as distId','dist.name as distNo',
            'c.cargo_descripcion', 'a.area_descripcion','cc.centroC_descripcion','para.id as iddepaN',
            'para.id as depaN','proviN.id as idproviN','proviN.name as proviN','distN.id as iddistN',
            'distN.name as distN','e.emple_id','c.cargo_id','a.area_id', 'cc.centroC_id','e.emple_tipoContrato',
            'e.emple_local','e.emple_nivel','e.emple_departamento','e.emple_provincia','e.emple_distrito','e.emple_foto as foto')
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

        $empleado->emple_nDoc=$objEmpleado['numDocumento_v'];
        $empleado->emple_cargo=$objEmpleado['cargo_v'];
        $empleado->emple_area=$objEmpleado['area_v'];
        $empleado->emple_centCosto=$objEmpleado['centroc_v'];
        $empleado->emple_departamento=$objEmpleado['dep_v'];
        $empleado->emple_provincia=$objEmpleado['prov_v'];
        $empleado->emple_distrito=$objEmpleado['dist_v'];
        $empleado->emple_tipoContrato=$objEmpleado['contrato_v'];
        $empleado->emple_local=$objEmpleado['local_v'];
        $empleado->emple_nivel=$objEmpleado['nivel_v'];
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

}
