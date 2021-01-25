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
use App\Mail\correoFinContrato;

class FinContrato extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finContrato:users';

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
        $this->info('Enviar correo de fin de contrato.');
        $organizaciones = organizacion::all('organi_id', 'organi_razonSocial', 'organi_tipo');
        $todayNow = carbon::now()->subHours(5);
        $today = carbon::create($todayNow->year, $todayNow->month, $todayNow->day, 0, 0, 0, 'GMT');
        
        foreach ($organizaciones as $organizacion) {
            $datos = "";
           // ENVIAR NOTIFICACIONES A TODOS LOS EMPLEADOS DE CADA ORGANIZACIÓN
            $empleados = DB::table('empleado')
                    ->join('persona', 'empleado.emple_persona', '=', 'persona.perso_id')
                    ->join('contrato', 'emple_id', '=', 'contrato.idEmpleado')
                    ->where('empleado.organi_id', '=', $organizacion->organi_id)
                    ->select('persona.perso_fechaNacimiento', 'persona.perso_nombre', 'persona.perso_apPaterno', 'persona.perso_apMaterno', 'empleado.emple_id', 'contrato.fechaFinal', 'contrato.notiTiempo')
                    ->get();

            $admins = DB::table('usuario_organizacion')
                    ->where('organi_id', $organizacion->organi_id)
                    ->select('usuario_organizacion.user_id')
                    ->get();

            foreach ($empleados as $persona) {
                // NOTIFICACIÓN POR DÍA DE CUMPLEAÑOS
                if ($persona->fechaFinal != NULL) {
                    $fc = carbon::parse($persona->fechaFinal);
                    $F_fc = carbon::create($today->year, $fc->month, $fc->day, 0, 0, 0, 'GMT');
                    $diff = $today->diffInDays($F_fc);
                    $edad = $today->year - $fc->year;
                    if($diff < $persona->notiTiempo && $today <= $F_fc){
                        $datos = $datos."<div class=''><strong>• ".$persona->perso_nombre." ".$persona->perso_apPaterno." ".$persona->perso_apMaterno."</strong>, su contrato finaliza el <strong>".$persona->fechaFinal."</strong> le quedan ".$diff." días. </div><br>";
                        $mensaje =  [
                                        "idOrgani" => $organizacion->organi_id,
                                        "idEmpleado" => $persona->emple_id,
                                        "empleado" => [
                                                $persona->perso_nombre,
                                                $persona->perso_apPaterno,
                                                $persona->perso_apMaterno
                                            ],
                                        "mensaje" => "Su contrato finaliza en ".$diff." días.",
                                        "asunto" => "contract"
                                    ];

                        if($admins){
                            foreach ($admins as $admin){
                                $recipient = User::find($admin->user_id);
                                $recipient->notify(new NuevaNotification($mensaje)); 
                            }
                        }
                    }
                }
                // FIN DE NOTIFICACIÓN
            }
            if($datos != "" && isset($admins)){
                $mail_body = "<h4 style='color: #163552'>".$organizacion->organi_tipo.": ". $organizacion->organi_razonSocial ."</h4>".$datos;
                foreach ($admins as $admin) {
                    $email = User::find($admin->user_id)->email;
                    $envio = Mail::to($email)->queue(new correoFinContrato($mail_body));
                }
            }
        }
    }
}