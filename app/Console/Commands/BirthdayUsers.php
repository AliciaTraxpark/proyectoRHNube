<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\empleado;
use App\persona;
use App\organizacion;
use App\User;
use App\Notifications\NuevaNotification;

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
    protected $description = 'Envía notificaciones de cumpleaños de los usuarios.';

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
        $organizaciones = organizacion::all();
        $empleados = DB::table('organizacion')
                    ->insert([
                        ['organi_razonSocial' => 'SAC'],
                            ]);
        foreach ($organizaciones as $organizacion) {
           // ENVIAR NOTIFICACIONES A TODOS LOS EMPLEADOS DE CADA ORGANIZACIÓN
            $empleados = DB::table('empleado')
                    ->join('persona', 'empleado.emple_persona', '=', 'persona.perso_id')
                    ->where('empleado.organi_id', '=', $organizacion->organi_id)
                    ->select('persona.perso_fechaNacimiento as perso_fechaNacimiento')
                    ->get();
            foreach ($empleados as $persona) {
                // NOTIFICACIÓN POR DÍA DE CUMPLEAÑOS
                $hb = carbon::parse($persona->perso_fechaNacimiento);
                $today = carbon::now();
                $mes = $hb->month;
                $dia = $hb->day;
                $anio = $today->year;
                $fHb = carbon::create($anio, $mes, $dia, 0, 0, 0, 'GMT');
                $diff = $hb->diffInDays($fHb);
                if($diff == 1){
                    $mensaje =  [
                                    "idOrgani" => session('sesionidorg'),
                                    "idEmpleado" => $persona->perso_id,
                                    "empleado" => [
                                            $persona->perso_nombre,
                                            $persona->perso_apPaterno,
                                            $persona->perso_apMaterno
                                        ],
                                    "mensaje" => "Mañana es mi cumpleaños.",
                                    "asunto" => "birthday"
                                ];
                } else {
                    if($diff == 0){
                        $mensaje =  [
                                        "idOrgani" => session('sesionidorg'),
                                        "idEmpleado" => $persona->perso_id,
                                        "empleado" => [
                                                $persona->perso_nombre,
                                                $persona->perso_apPaterno,
                                                $persona->perso_apMaterno
                                            ],
                                        "mensaje" => "Estoy de cumpleaños.",
                                        "asunto" => "birthday"
                                    ];
                    }
                }

                $recipient = User::find(1);
                $recipient->notify(new NuevaNotification($mensaje));
                // FIN DE NOTIFICACIÓN
            }

        }
        
        
    }
}
