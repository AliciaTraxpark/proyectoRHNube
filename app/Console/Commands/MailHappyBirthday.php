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

class MailHappyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'happyBirthdayMail:users';

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
        $this->info('Enviar correo de cumpleaños.');
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
                    if($diff < 7 && $today <= $fHb){
                        $datos = $datos."<div class='text-left'> • ".$persona->perso_nombre." ".$persona->perso_apPaterno." ".$persona->perso_apMaterno."</div> <div class='text-left'>".$dia." de ".$meses[$mes-1]." (".$edad.")"."</div><br>";
                    }
                // FIN DE NOTIFICACIÓN
                }  
            }
        }
        if($datos != ""){
            $email = 'miguelpacheco.1622@gmail.com';
            $envio = Mail::to($email)->queue(new correoHappyBirthday($datos));
        }
    }
}
