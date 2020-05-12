<?php

namespace App\Http\Controllers;
use App\eventos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class calendarioController extends Controller
{
    //
    public function index(){
        if (Auth::check()) {



            return view ('calendario.calendario');



        }
        else{
            return redirect(route('principal'));
        }



    }
    public function store(Request $request){
      $datosEvento=request()->all();
      print_r($datosEvento);

    }
}
