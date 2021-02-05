<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class schedulingWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:work';

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
        $this->info('Schedule worker started successfully.');
        // LAS EJECUCIÓN SE DARÁ TODOS LOS DÍAS A LAS 5:00 AM (5Hrs GTM-5)
        while (true) {
            $today = Carbon::now();
            if ( $today->hour === 11 && $today->minute === 11 && $today->second === 0) {
                $this->call('schedule:run');
            }

            sleep(1);
        }
    }
}
