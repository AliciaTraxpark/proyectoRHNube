<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class controlRutaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        return view('ruta.rutaDiaria');
    }

    public function indexReporte()
    {
        $areas = DB::table('area as a')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->get();

        $cargos = DB::table('cargo as c')
            ->select('c.cargo_id', 'c.cargo_descripcion')
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->get();

        return view('ruta.reporteSemanalRuta', ['areas' => $areas, 'cargos' => $cargos]);
    }
}
