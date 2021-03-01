<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\empleado;
use App\persona;
use App\organizacion;
use App\User;
use App\Notifications\NuevaNotification;
use Illuminate\Support\Facades\DB;

class BirthdayUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones de cumpleaños de los usuarios, un día antes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Enviar alerta de cumpleaños.');
        $organizaciones = organizacion::all('organi_id');
        $todayNow = carbon::now();
        $today = carbon::create($todayNow->year, $todayNow->month, $todayNow->day, 0, 0, 0, 'GMT');

        foreach ($organizaciones as $organizacion) {

            //COLECCIÓN DE ADMINISTRADORES POR ORGANIZACIÓN
            $admins = DB::table('usuario_organizacion')
                    ->join('users', 'users.id', '=', 'usuario_organizacion.user_id')
                    ->leftjoin('invitado', 'invitado.user_Invitado', '=', 'users.id')
                    ->select('usuario_organizacion.user_id', 'usuario_organizacion.rol_id')
                    ->where('usuario_organizacion.organi_id', $organizacion->organi_id)
                    ->where(function ($query) {
                        $query->where('invitado.gestionHb', '<>', 0)
                              ->orWhereNull('invitado.gestionHb');
                    })
                    ->where(function ($estadoInv) {
                        $estadoInv->where('invitado.estado', '=', 1)
                              ->orWhereNull('invitado.estado');
                    })
                    ->where(function ($estadoUs) {
                        $estadoUs->where('users.user_estado', '=', 1)
                              ->orWhereNull('users.user_estado');
                    })
                    ->get();
            $admins = $admins->unique()->all();

            foreach ($admins as $admin) {
                if ($admin->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', $admin->user_id)
                        ->get()->first();
                    if ($invitado->verTodosEmps == 1) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'p.perso_fechaNacimiento')
                            ->where('e.organi_id', '=', $organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {
                        $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                            ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                            ->where('invem.area_id', '=', null)
                            ->where('invem.emple_id', '!=', null)
                            ->get()->first();
                        if ($invitado_empleadoIn != null) {
                            $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'p.perso_fechaNacimiento')
                                ->where('e.organi_id', '=', $organizacion->organi_id)
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->groupBy('e.emple_id')
                                ->get();
                        } else {
                            $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'p.perso_fechaNacimiento')
                                ->where('e.organi_id', '=', $organizacion->organi_id)
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->groupBy('e.emple_id')
                                ->get();
                        }
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'p.perso_fechaNacimiento')
                        ->where('e.organi_id', '=', $organizacion->organi_id)
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_id')
                        ->get();
                }


                foreach ($empleados as $empleado) {
                    // NOTIFICACIÓN POR DÍA DE CUMPLEAÑOS
                    if($empleado->perso_fechaNacimiento != NULL){
                        $hb = carbon::parse($empleado->perso_fechaNacimiento);  // 31 de diciembre del 2020    ->  01 de enero 2021  
                        $tomorrow = carbon::tomorrow();
                        $fHb = carbon::create($tomorrow->year, $hb->month, $hb->day, 0, 0, 0, 'GMT');
                        $diff = $today->diffInDays($fHb);

                        if($diff == 1 && $today < $fHb){
                            $mensaje =  [
                                            "idOrgani" => $organizacion->organi_id,
                                            "idEmpleado" => $empleado->emple_id,
                                            "empleado" => [
                                                    $empleado->nombre,
                                                    $empleado->apPaterno,
                                                    $empleado->apMaterno
                                                ],
                                            "mensaje" => "Mañana está de cumpleaños.",
                                            "asunto" => "birthday"
                                        ];

                            $recipient = User::find($admin->user_id);
                            $recipient->notify(new NuevaNotification($mensaje));
                        }
                    }
                    // FIN DE NOTIFICACIÓN
                }
            }
        }
    }
}
