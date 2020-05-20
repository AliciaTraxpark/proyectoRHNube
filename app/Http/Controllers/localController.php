<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\local;

class localController extends Controller
{
    public function store(Request $request){
        $local = new local();
        $local->local_descripcion=$request->get('local_descripcion');
        $local->save();
        return $local;
    }
}
