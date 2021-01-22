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
use Illuminate\Support\Facades\Mail;
use App\Mail\correoHappyBirthday;

class HappyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'happyBirth:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->info('Enviar notificación de cumpleaños.');
        $organizaciones = organizacion::all('organi_id');
        $todayNow = carbon::now()->subHours(5);
        $today = carbon::create($todayNow->year, $todayNow->month, $todayNow->day, 0, 0, 0, 'GMT');

        foreach ($organizaciones as $organizacion) {
           // ENVIAR NOTIFICACIONES A TODOS LOS EMPLEADOS DE CADA ORGANIZACIÓN
            $empleados = DB::table('empleado')
                    ->join('persona', 'empleado.emple_persona', '=', 'persona.perso_id')
                    ->where('empleado.organi_id', '=', $organizacion->organi_id)
                    ->select('persona.perso_fechaNacimiento', 'persona.perso_nombre', 'persona.perso_apPaterno', 'persona.perso_apMaterno', 'empleado.emple_persona')
                    ->get();
            $admin = DB::table('usuario_organizacion')
                    ->where('organi_id', $organizacion->organi_id)
                    ->select('usuario_organizacion.user_id')
                    ->first();

            $invitado_admin = DB::table('invitado')
                    ->where('organi_id', $organizacion->organi_id)
                    ->where('rol_id', 1)
                    ->select('invitado.user_Invitado')
                    ->first();

            foreach ($empleados as $persona) {
                // NOTIFICACIÓN POR DÍA DE CUMPLEAÑOS
                if($persona->perso_fechaNacimiento != NULL){
                    $hb = carbon::parse($persona->perso_fechaNacimiento);
                    $fHb = carbon::create($today->year, $hb->month, $hb->day, 0, 0, 0, 'GMT');
                    $diff = $today->diffInDays($fHb);
                    $edad = $today->year - $hb->year;
                    if($diff == 0){
                        $mensaje =  [
                                        "idOrgani" => $organizacion->organi_id,
                                        "idEmpleado" => $persona->emple_persona,
                                        "empleado" => [
                                                $persona->perso_nombre,
                                                $persona->perso_apPaterno,
                                                $persona->perso_apMaterno
                                            ],
                                        "mensaje" => "Hoy está de cumpleaños, ".$edad." años.",
                                        "asunto" => "birthday"
                                    ];
                        if($admin != ""){
                            $recipient = User::find($admin->user_id);
                            $recipient->notify(new NuevaNotification($mensaje)); 
                        }
                        if($invitado_admin != ""){
                            $recipient = User::find($invitado_admin->user_Invitado);
                            $recipient->notify(new NuevaNotification($mensaje));
                        }
                    }
                }
                // FIN DE NOTIFICACIÓN
            }
        }

        //BORRAR LAS NOTIFICACIONES DE UN DÍA ANTES
        $users = User::all();
        foreach($users as $user){
            foreach ($user->notifications as $notificacion) {
                if($notificacion->data['0']['mensaje'] == "Mañana está de cumpleaños."){
                     DB::table('notifications')->where('id', $notificacion->id)->delete();
                }
            }
        }
        // FIN DE BORRADO DE NOTIFICACIONES
    }
}
