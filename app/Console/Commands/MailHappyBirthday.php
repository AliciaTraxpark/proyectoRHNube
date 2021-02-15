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
        $todayNow = carbon::now();
        $today = carbon::create($todayNow->year, $todayNow->month, $todayNow->day, 0, 0, 0, 'GMT');
        // ES EL INTERVALO DE
        $timeAlert = 8;
        
        foreach ($organizaciones as $organizacion) {
            $datos = "";
            $admins = DB::table('usuario_organizacion')
                    ->join('users', 'users.id', '=', 'usuario_organizacion.user_id')
                    ->leftjoin('invitado', 'invitado.user_Invitado', '=', 'users.id')
                    ->where('usuario_organizacion.organi_id', $organizacion->organi_id)
                    ->where(function ($query) {
                        $query->where('invitado.gestionHb', '<>', 0)
                              ->orWhereNull('invitado.gestionHb');
                    })
                    ->select('usuario_organizacion.user_id', 'usuario_organizacion.rol_id')
                    ->get();

            foreach($admins as $admin){
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

                $date = Carbon::create($today->year, $today->month, $today->day, 0, 0, 0);
                // RECORREMOS DESDE EL DÍA ACTUAL HASTA EL DÍA LÍMITE
                for($i = 0; $i < $timeAlert; $i++){
                    if($i != 0){
                        $date->addDays(1);
                    }
                    // RECORRE LA COLECCIÓN DE LOS EMPLEADOS
                    foreach ($empleados as $empleado) {
                        if($empleado->perso_fechaNacimiento != NULL){
                            $hb_temp = Carbon::parse($empleado->perso_fechaNacimiento);
                            // OBTENEMOS FECHA EN EL FORMATO
                            $hb = Carbon::create($hb_temp->year, $hb_temp->month, $hb_temp->day, 0, 0, 0);
                            $fHb = Carbon::create($date->year, $hb->month, $hb->day, 0, 0, 0);
                            // COMPARA DÍA DE CUMPLEAÑOS CON DÍA ACTUAL
                            if($date->eq($fHb)){
                                $edad = $date->diffInYears($hb);
                                $datos = $datos."<div class=''> • <strong>".$empleado->nombre." ".$empleado->apPaterno." ".$empleado->apMaterno."</strong>&nbsp;&nbsp;&nbsp;".$hb->day." de ".$meses[($hb->month)-1]." &nbsp;&nbsp;&nbsp; (".$edad." años)"."</div><br>";
                            }
                        }
                    }       
                }
                // ENVIA CORREO
                if($datos != ""){
                    $mail_body = "<h4 style='color: #163552'>".$organizacion->organi_tipo.": ". $organizacion->organi_razonSocial ."</h4><br>".$datos;
                    $email = User::find($admin->user_id)->email;
                    $envio = Mail::to($email)->queue(new correoHappyBirthday($mail_body));
                    $datos = "";
                    $mail_body = "";
                }
                $datos = "";
            }
        }
    }
}
