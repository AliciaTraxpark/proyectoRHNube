<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\eventos_usuario;
use Illuminate\Http\Request;

class EventosUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index()
    {
        //
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
        $eventos_usuario=new eventos_usuario();
        $eventos_usuario->title= $request->get('title');
        $eventos_usuario->color= $request->get('color');
        $eventos_usuario->textColor= $request->get('textColor');
        $eventos_usuario->start= $request->get('start');
        $eventos_usuario->end= $request->get('end');
        $eventos_usuario->tipo= $request->get('tipo');
        $eventos_usuario->evento_pais= $request->get('pais');
        $eventos_usuario->evento_departamento= $request->get('departamento');


        $eventos_usuario->users_id=Auth::user()->id;

        $eventos_usuario->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\eventos_usuario  $eventos_usuario
     * @return \Illuminate\Http\Response
     */
    public function show(eventos_usuario $eventos_usuario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\eventos_usuario  $eventos_usuario
     * @return \Illuminate\Http\Response
     */
    public function edit(eventos_usuario $eventos_usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\eventos_usuario  $eventos_usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, eventos_usuario $eventos_usuario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\eventos_usuario  $eventos_usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $eventos_usuario=eventos_usuario::findOrFail($id);
        eventos_usuario::destroy($id);
        return response()->json($id);
    }
}
