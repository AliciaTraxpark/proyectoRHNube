<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AgendaController extends Controller
{
    public function getFreeHour(Request $request){
    	$fechas = DB::table('agenda')
			    	->select(DB::raw('TIME(fecha) as hora'), DB::raw('DATE(fecha) as dia'))
			    	->where(DB::raw('DATE(fecha)'), $request->day)
			    	->get();

    	$hoy = Carbon::now();
    	$hoyD = $hoy->year."-".$hoy->month."-".$hoy->day;
    	$mid = Carbon::createMidnightDate($hoy->year, $hoy->month, $hoy->day);
    	$sec = $mid->diffInSeconds($hoy);
   		$agenda = new Collection();
    	$segI = 32400;
    	$band = true;
    	for ($i=0; $i < 12; $i++) { 
    		$segF = $segI + 2100;
    		$inicio = gmdate('H:i:s', $segI);
    		$fin = gmdate('H:i:s', $segF);

    		if($fechas->count() > 0){
    			foreach ($fechas as $fecha) {
	    			if($band){
	    				if($inicio == $fecha->hora){
			    			$obj = (object)array(
				    			'inicio' => $inicio,
				    			'fin' => $fin,
				    			'estado' => "<span class='badge badge-danger p-1'>Agendado</span>",
				    			'html' => '',
				    			'hoy' => $sec
				    		);
				    		$band = false;
			    		} else {
			    			$obj = (object)array(
				    			'inicio' => $inicio,
				    			'fin' => $fin,
				    			'estado' => "<span class='badge p-1'>Libre</span>",
				    			'html' => "<button onClick='javascript:check(".$i.")' class='btn btn-light p-1'><img src='landing/images/check.svg' width='10'/> <span class='badge'> Elegir</span></button>",
				    			'hoy' => $sec
				    		);
			    		}
	    			}
		    	}
    		} else {
    			$obj = (object)array(
	    			'inicio' => $inicio,
	    			'fin' => $fin,
	    			'estado' => "<span class='badge p-1'>Libre</span>",
	    			'html' => "<button onClick='javascript:check(".$i.")' class='btn btn-light p-1'><img src='landing/images/check.svg' width='10'/> <span class='badge'> Elegir</span></button>",
	    			'hoy' => $sec
	    		);
    		}
    		
			if($request->day == $hoyD){
				if($segI <= $sec){
	    			$obj = (object)array(
		    			'inicio' => $inicio,
		    			'fin' => $fin,
		    			'estado' => "<span class='badge badge-secondary p-1'>No disponible</span>",
		    			'html' => "",
		    			'hoy' => $sec
		    		);
	    		}
			}
    		
	    	$band = true;
	    	$agenda->push($obj);
    		// HORA DE ALMUERZO
    		if($segI == 44400){
				$segI = 50400;
    		} else 
    			$segI = $segI + 2400;
    	}

		return response()->json($agenda, 200);   	
    }

    public function verAgenda(){
    	return view('tardanzas.agenda');
    }
}
