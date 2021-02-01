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
           // ENVIAR NOTIFICACIONES A TODOS LOS EMPLEADOS DE CADA ORGANIZACIÓN
            //SOLAMENTE EMPLEADOS
            $empleados = DB::table('empleado')
                    ->join('persona', 'empleado.emple_persona', '=', 'persona.perso_id')
                    ->where('empleado.organi_id', '=', $organizacion->organi_id)
                    ->select('persona.perso_fechaNacimiento', 'persona.perso_nombre', 'persona.perso_apPaterno', 'persona.perso_apMaterno', 'empleado.emple_id')
                    ->get();

            //COLECCIÓN DE ADMINISTRADORES POR ORGANIZACIÓN
            $admins = DB::table('usuario_organizacion')
                    ->join('users', 'users.id', '=', 'usuario_organizacion.user_id')
                    ->leftjoin('invitado', 'invitado.user_Invitado', '=', 'users.id')
                    ->where('usuario_organizacion.organi_id', $organizacion->organi_id)
                    ->where(function ($query) {
                        $query->where('invitado.gestionHb', '<>', 0)
                              ->orWhereNull('invitado.gestionHb');
                    })
                    ->select('usuario_organizacion.user_id')
                    ->get();

            foreach ($empleados as $persona) {
                // NOTIFICACIÓN POR DÍA DE CUMPLEAÑOS
                if($persona->perso_fechaNacimiento != NULL){
                    $hb = carbon::parse($persona->perso_fechaNacimiento);  // 31 de diciembre del 2020    ->  01 de enero 2021  

                    $tomorrow = carbon::tomorrow();
                    $fHb = carbon::create($tomorrow->year, $hb->month, $hb->day, 0, 0, 0, 'GMT');
                    $diff = $today->diffInDays($fHb);

                    if($diff == 1 && $today < $fHb){
                        $mensaje =  [
                                        "idOrgani" => $organizacion->organi_id,
                                        "idEmpleado" => $persona->emple_id,
                                        "empleado" => [
                                                $persona->perso_nombre,
                                                $persona->perso_apPaterno,
                                                $persona->perso_apMaterno
                                            ],
                                        "mensaje" => "Mañana está de cumpleaños.",
                                        "asunto" => "birthday"
                                    ];

                        if($admins){
                            foreach ($admins as $admin) {
                                $recipient = User::find($admin->user_id);
                                $recipient->notify(new NuevaNotification($mensaje));
                            }
                        }
                    }
                }
                // FIN DE NOTIFICACIÓN
            }
        }
    }
}
