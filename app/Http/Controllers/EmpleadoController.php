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
use App\persona;

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
        return view('empleado.empleado',['departamento'=>$departamento,'provincia'=>$provincia,'distrito'=>$distrito,
        'tipo_doc'=>$tipo_doc,'tipo_cont'=>$tipo_cont,'area'=>$area,'cargo'=>$cargo,'centro_costo'=>$centro_costo]);
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

        $persona=new persona();
        $persona->perso_nombre=$request->get('nombres');
        $persona->perso_apPaterno=$request->get('apPaterno');
        $persona->perso_apMaterno=$request->get('apMaterno');
        $f1 = explode("/",$request->get('fechaN'));
        $fechaN = $f1[2]."-".$f1[1]."-".$f1[0];
        $persona->perso_fechaNacimiento=$fechaN;
        $persona->perso_sexo=$request->get('tipo');
        $persona->save();
        $emple_persona=$persona->perso_id;


        $empleado= new empleado();
        $empleado->emple_tipoDoc=$request->get('documento');
        $empleado->emple_nDoc=$request->get('numDocumento');
        $empleado->emple_persona=$emple_persona;
        $empleado->emple_departamento=$request->get('departamento');
        $empleado->emple_provincia=$request->get('provincia');
        $empleado->emple_distrito=$request->get('distrito');
        $empleado->emple_cargo=$request->get('cargo');
        $empleado->emple_area=$request->get('area');
        $empleado->emple_centCosto=$request->get('centroc');
        $empleado->emple_departamentoN=$request->get('dep');
        $empleado->emple_provinciaN=$request->get('prov');
        $empleado->emple_distritoN=$request->get('dist');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(empleado $empleado)
    {
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
    public function update(Request $request, empleado $empleado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy(empleado $empleado)
    {
        //
    }
}
