<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\empleado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class diasLaborablesController extends Controller
{
    //
    public function indexMenu(){
        $empleado = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->where('users_id','=',Auth::user()->id)
        ->get();
        return View('horarios.diasLaborales',['empleado'=>$empleado]);
    }
}
