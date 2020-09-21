<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\nivel;

class nivelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function store(Request $request)
    {
        $nivel = new nivel();
        $nivel->nivel_descripcion = $request->get('nivel_descripcion');
        $nivel->organi_id=session('sesionidorg');
        $nivel->save();
        return  $nivel;
    }
}
