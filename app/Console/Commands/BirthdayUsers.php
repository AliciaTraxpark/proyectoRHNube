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
        $this->info('Enviar notificaciones de cumpleaños.');
        
        $empleados = DB::table('organizacion')
                    ->insert([
                        ['organi_razonSocial' => 'SAC'],
                        ['organi_ruc' => 'SAC'],
                        ['organi_razonSocial' => 'SAC'],
                        ['organi_direccion' => 'SAC'],
                        ['organi_departamento' => 'SAC'],
                        ['organi_provincia' => 'SAC'],
                        ['organi_distrito' => 'SAC'],
                        ['organi_nempleados' => '2'],
                        ['organi_tipo' => 'SAC'],
                        ['organi_corteCaptura' => '2'],
                        ['created_at' => Carbon::now()],
                        ['updated_at' => Carbon::now()],
                    ]);
        
    }
}
