<?php

namespace App\Http\Controllers;

use App\empleado;
use App\area;
use Illuminate\Http\Request;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_districts;
use App\usuario_organizacion;

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
        return view('empleado.empleado',['departamento'=>$departamento,'provincia'=>$provincia,'distrito'=>$distrito]);
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
