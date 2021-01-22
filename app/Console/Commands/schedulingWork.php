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
        $today = Carbon::now();
        $temp = Carbon::create($today->year, $today->month, $today->day, 5, 0, 0, 'GMT');
        // LAS EJECUCIÓN SE DARÁ TODOS LOS DÍAS A LAS 5:00 AM (5Hrs GTM-5)
        while (true) {
            $today = Carbon::now();
            $diffD = $today->diffInDays($temp);
            if (( $today->minute === 0 && $diffD > 0 ) || ( $today->hour === 5 )) {
                $this->call('schedule:run');
                $temp = Carbon::create($today->year, $today->month, $today->day, 5, 0, 0, 'GMT');
            }

            sleep(1);
        }
    }
}
