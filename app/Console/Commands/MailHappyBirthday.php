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
        $organizaciones = organizacion::all('organi_id', 'organi_razonSocial', 'organi_tipo');
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $todayNow = carbon::now()->subHours(5);
        $today = carbon::create($todayNow->year, $todayNow->month, $todayNow->day, 0, 0, 0, 'GMT');
        
        foreach ($organizaciones as $organizacion) {
            $datos = "";
           // ENVIAR NOTIFICACIONES A TODOS LOS EMPLEADOS DE CADA ORGANIZACIÓN
            $empleados = DB::table('empleado')
                ->join('persona', 'empleado.emple_persona', '=', 'persona.perso_id')
                ->where('empleado.organi_id', '=', $organizacion->organi_id)
                ->select('persona.perso_fechaNacimiento', 'persona.perso_nombre', 'persona.perso_apPaterno', 'persona.perso_apMaterno')
                ->get();

            $admins = DB::table('usuario_organizacion')
                    ->where('organi_id', $organizacion->organi_id)
                    ->select('usuario_organizacion.user_id')
                    ->get();

            foreach ($empleados as $persona) {
                // NOTIFICACIÓN POR DÍA DE CUMPLEAÑOS
                if($persona->perso_fechaNacimiento != NULL){
                    $hb = carbon::parse($persona->perso_fechaNacimiento);
                    $fHb = carbon::create($today->year, $hb->month, $hb->day, 0, 0, 0, 'GMT');
                    $diff = $today->diffInDays($fHb);
                    $edad = $today->year - $hb->year;
                    if($diff <= 7 && $today <= $fHb){
                        $datos = $datos."<div class=''> • <strong>".$persona->perso_nombre." ".$persona->perso_apPaterno." ".$persona->perso_apMaterno."</strong>&nbsp;&nbsp;&nbsp;".$hb->day." de ".$meses[($hb->month)-1]." &nbsp;&nbsp;&nbsp; (".$edad." años)"."</div><br>";
                    }
                // FIN DE NOTIFICACIÓN
                }  
            }
            if($datos != "" && isset($admins)){
                $mail_body = "<h4 style='color: #163552'>".$organizacion->organi_tipo.": ". $organizacion->organi_razonSocial ."</h4><br>".$datos;
                foreach ($admins as $admin) {
                    $email = User::find($admin->user_id)->email;
                    $envio = Mail::to($email)->queue(new correoHappyBirthday($mail_body));
                }
            }
        }
    }
}
