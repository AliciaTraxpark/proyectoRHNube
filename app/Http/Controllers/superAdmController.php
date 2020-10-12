<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use PhpParser\Builder\Function_;

class superAdmController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function indexDashboard(){
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

         if($usuario_organizacion->rol_id==4){
            $organizacion = DB::table('organizacion as or')
            ->select(DB::raw('COUNT(or.organi_id) as totalOrga'))->get()->first();

            $nusuariosA = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '!=', null)
            ->where('uso.rol_id', '=', 1)
            ->select(DB::raw('COUNT(uso.user_id) as totalUA'))->get()->first();

            $nusuariosI = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '!=', null)
            ->where('uso.rol_id', '=', 3)
            ->select(DB::raw('COUNT(uso.user_id) as totalUI'))->get()->first();

            $empleado = DB::table('empleado as e')
                ->select(DB::raw('COUNT(e.emple_id) as totalE'))
                ->where('e.emple_estado', '=', 1)
                ->get()->first();

                $listaempleado = DB::table('empleado as e')
                ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno','e.created_at','or.organi_razonSocial')
                ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('organizacion as or', 'e.organi_id', '=', 'or.organi_id')
                ->where('e.emple_estado', '=', 1) ->latest()
                ->take(6)
                ->get();

            return view('MenuSuperAdmin.dashboard',['nOrganizaciones'=>$organizacion->totalOrga,
            'nusuAdmin'=>$nusuariosA->totalUA,'nusuInv'=>$nusuariosI->totalUI,
            'nempleado'=>$empleado->totalE,'listaempleado'=>$listaempleado]);

         } else{
            return redirect('/dashboard');
         }

    }

    public function datosOrgani(Request $request){
        $organizacion = DB::table('organizacion as or')
        ->leftJoin('empleado as e','or.organi_id','=','e.organi_id')
        ->select('or.organi_razonSocial as category','or.organi_nempleados as first', DB::raw('IF(e.emple_id is null, 0, COUNT(e.emple_id)) as second'))
        ->where('e.emple_id',null)
        ->groupBy('or.organi_id');
            $organizacion1 = DB::table('organizacion as or')
            ->leftJoin('empleado as e','or.organi_id','=','e.organi_id')
            ->select('or.organi_razonSocial as category','or.organi_nempleados as first', DB::raw('IF(e.emple_id is null, 0, COUNT(e.emple_id)) as second'))
            ->where('e.emple_estado',1)
            ->groupBy('or.organi_id')
            ->union($organizacion)
            ->paginate(7);

            return $organizacion1;

    }

    public function tipoOrg(){
        $organizacion = DB::table('organizacion as or')
            ->select(DB::raw('COUNT(or.organi_id) as totalOrga'))->get()->first();
        $organizacionEm = DB::table('organizacion as or')
            ->select(DB::raw('COUNT(or.organi_id) as Empresa'))
            ->where('or.organi_tipo','=','Empresa')
            ->get()->first();

            $Empresa=$organizacionEm->Empresa*100/$organizacion->totalOrga;

        $organizacionGo = DB::table('organizacion as or')
        ->select(DB::raw('COUNT(or.organi_id) as Gobierno'))
        ->where('or.organi_tipo','=','Gobierno')
        ->get()->first();

        $Gobierno=$organizacionGo->Gobierno*100/$organizacion->totalOrga;

        $organizacionONG = DB::table('organizacion as or')
        ->select(DB::raw('COUNT(or.organi_id) as ONG'))
        ->where('or.organi_tipo','=','ONG')
        ->get()->first();

        $ONG=$organizacionONG->ONG*100/$organizacion->totalOrga;

        $organizacionAso = DB::table('organizacion as or')
        ->select(DB::raw('COUNT(or.organi_id) as Asociación'))
        ->where('or.organi_tipo','=','Asociación')
        ->get()->first();
        $Asociación=$organizacionAso->Asociación*100/$organizacion->totalOrga;

        $organizacionOt = DB::table('organizacion as or')
        ->select(DB::raw('COUNT(or.organi_id) as Otros'))
        ->where('or.organi_tipo','=','Otros')
        ->get()->first();

        $Otros=$organizacionOt->Otros*100/$organizacion->totalOrga;

        return[$Empresa,$Gobierno,$ONG,$Asociación,$Otros];

    }
}
