<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\nivel;

class nivelController extends Controller
{
    public function store(Request $request){
        $nivel= new nivel();
        $nivel->nivel_descripcion=$request->get('nivel_descripcion');
        $nivel->save();
        return 1;
    }
}
