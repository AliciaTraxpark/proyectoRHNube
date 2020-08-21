<?php

namespace App\Http\Controllers;

use App\condicion_pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class condicionPagoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function store(Request $request)
    {
        $condicion = new condicion_pago();
        $condicion->condicion = $request->get('condicion');
        $condicion->user_id = Auth::user()->id;
        $condicion->save();
        return  $condicion;
    }
}
