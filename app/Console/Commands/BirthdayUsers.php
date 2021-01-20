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
        $organizaciones = organizacion::all();
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $datos = "";

        foreach ($organizaciones as $organizacion) {
           // ENVIAR NOTIFICACIONES A TODOS LOS EMPLEADOS DE CADA ORGANIZACIÓN
            $empleados = DB::table('empleado')
                    ->join('persona', 'empleado.emple_persona', '=', 'persona.perso_id')
                    ->where('empleado.organi_id', '=', $organizacion->organi_id)
                    ->select('persona.*', 'empleado.*')
                    ->get();
            foreach ($empleados as $persona) {
                // NOTIFICACIÓN POR DÍA DE CUMPLEAÑOS
                if($persona->perso_fechaNacimiento != NULL){
                   $hb = carbon::parse($persona->perso_fechaNacimiento);
                    $mes = $hb->month;
                    $dia = $hb->day;
                    $anioHb = $hb->year;
                    $today = carbon::now()->subHours(5);
                    $anio = $today->year;
                    $mesToday = $today->month;
                    $dayToday = $today->day;
                    $today = carbon::create($anio, $mesToday, $dayToday, 0, 0, 0, 'GMT');
                    $fHb = carbon::create($anio, $mes, $dia, 0, 0, 0, 'GMT');
                    $diff = $today->diffInDays($fHb);
                    $edad = $anio - $anioHb;
                    if($diff == 1 && $today < $fHb){
                        $mensaje =  [
                                        "idOrgani" => $organizacion->organi_id,
                                        "idEmpleado" => $persona->emple_persona,
                                        "empleado" => [
                                                $persona->perso_nombre,
                                                $persona->perso_apPaterno,
                                                $persona->perso_apMaterno
                                            ],
                                        "mensaje" => "Mañana es mi cumpleaños.",
                                        "asunto" => "birthday"
                                    ];
                        $recipient = User::find(1);
                        $recipient->notify(new NuevaNotification($mensaje)); 
                    }
                }
                // FIN DE NOTIFICACIÓN
            }
        }
    }
}
