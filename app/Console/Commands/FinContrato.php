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
      // SE OBTIENE TODAS LAS ORGANIZACIONES, PORQUE ENVIAMOS CORREOS POR ORGANIZACIONES
      $organizaciones = organizacion::all('organi_id', 'organi_razonSocial', 'organi_tipo');
      // OBTENEMOS EL DÍA DE HOY
      $todayNow = carbon::now()->subHours(5);
      $today = carbon::create($todayNow->year, $todayNow->month, $todayNow->day, 0, 0, 0, 'GMT');
      
      // RECORREMOS CADA ORGANIZACIÓN
      foreach ($organizaciones as $organizacion) {
        $datos = "";
        // SE OBTIENE LOS ADMINISTRADORES O INVITADOS CON PERMISOS DE RECIBIR NOTIFICACIONES DE FIN DE CONTRATO
        $admins = DB::table('usuario_organizacion')
                ->join('users', 'users.id', '=', 'usuario_organizacion.user_id')
                ->leftjoin('invitado', 'invitado.user_Invitado', '=', 'users.id')
                ->where('usuario_organizacion.organi_id', $organizacion->organi_id)
                ->where(function ($query) {
                    $query->where('invitado.gestionContract', '<>', 0) // DIFERENTE DE CERO
                          ->orWhereNull('invitado.gestionContract'); // IGUAL A NULL, PORQUE EL CREADOR DE LA ORGANIZACIÓN TIENE NULL
                    })
                ->select('usuario_organizacion.user_id', 'usuario_organizacion.rol_id')
                ->get();
        // RECORREMOS CADA COLECCIÓN DE ADMIS
        foreach($admins as $admin){
          if ($admin->rol_id == 3) {
            $invitado = DB::table('invitado as in')
                ->where('rol_id', '=', 3)
                ->where('in.user_Invitado', '=', $admin->user_id)
                ->get()->first();
            if ($invitado->verTodosEmps == 1) {
              $empleados = DB::table('empleado as e')
                  ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                  ->join('contrato', 'e.emple_id', '=', 'contrato.idEmpleado')
                  ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'contrato.fechaFinal', 'contrato.notiTiempo')
                  ->where('e.organi_id', '=', $organizacion->organi_id)
                  ->where('e.emple_estado', '=', 1)
                  ->where('contrato.estado', '=', 1)
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
                      ->join('contrato', 'e.emple_id', '=', 'contrato.idEmpleado')
                      ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'contrato.fechaFinal', 'contrato.notiTiempo')
                      ->where('e.organi_id', '=', $organizacion->organi_id)
                      ->where('e.emple_estado', '=', 1)
                      ->where('contrato.estado', '=', 1)
                      ->where('invi.estado', '=', 1)
                      ->where('invi.idinvitado', '=', $invitado->idinvitado)
                      ->groupBy('e.emple_id')
                      ->get();
              } else {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                    ->join('contrato', 'e.emple_id', '=', 'contrato.idEmpleado')
                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'contrato.fechaFinal', 'contrato.notiTiempo')
                    ->where('e.organi_id', '=', $organizacion->organi_id)
                    ->where('e.emple_estado', '=', 1)
                    ->where('contrato.estado', '=', 1)
                    ->where('invi.estado', '=', 1)
                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                    ->groupBy('e.emple_id')
                    ->get();
              }
            }
          } else {
              $empleados = DB::table('empleado as e')
                  ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                  ->join('contrato', 'e.emple_id', '=', 'contrato.idEmpleado')
                  ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno', 'contrato.fechaFinal', 'contrato.notiTiempo')
                  ->where('e.organi_id', '=', $organizacion->organi_id)
                  ->where('e.emple_estado', '=', 1)
                  ->where('contrato.estado', '=', 1)
                  ->groupBy('e.emple_id')
                  ->get();
          }

          foreach ($empleados as $empleado) {
            // NOTIFICACIÓN POR DÍA DE CUMPLEAÑOS
            if ($empleado->fechaFinal != NULL) {
              $fc = carbon::parse($empleado->fechaFinal);
              $diff = $today->diffInDays($fc);
              $edad = $today->year - $fc->year;
              // CERO SIGNIFICA QUE TIENE FECHA INDEFINIDA DE CONTRATO.
              if($empleado->notiTiempo > 0 && $diff <= $empleado->notiTiempo && $today <= $fc){
                $datos = $datos."<div class=''><strong>• ".$empleado->nombre." ".$empleado->apPaterno." ".$empleado->apMaterno."</strong>, su contrato finaliza el <strong>".$empleado->fechaFinal."</strong> le quedan ".$diff." días. </div><br>";
                $mensaje =  [
                              "idOrgani" => $organizacion->organi_id,
                              "idEmpleado" => $empleado->emple_id,
                              "empleado" => [
                                      $empleado->nombre,
                                      $empleado->apPaterno,
                                      $empleado->apMaterno
                                  ],
                              "mensaje" => "Su contrato finaliza en ".$diff." días.",
                              "asunto" => "contract"
                            ];
                //ENVÍA LA NOTIFICACIÓN            
                $recipient = User::find($admin->user_id);
                $recipient->notify(new NuevaNotification($mensaje)); 
              }
            }
            // FIN DE NOTIFICACIÓN
          }
          // ENVÍA EL CORREO
          if($datos != "" && isset($admins)){
            $mail_body = "<h4 style='color: #163552'>".$organizacion->organi_tipo.": ". $organizacion->organi_razonSocial ."</h4>".$datos;
            $email = User::find($admin->user_id)->email;
            $envio = Mail::to($email)->queue(new correoFinContrato($mail_body));
          }
          $datos = "";
          $mail_body = "";
        }
      }
    }
}
