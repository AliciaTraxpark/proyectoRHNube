<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\BirthdayUsers',
        'App\Console\Commands\FinContrato',
        'App\Console\Commands\MailHappyBirthday',
        'App\Console\Commands\HappyBirthday',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
       $schedule->command('telescope:prune --hours=12')->hourly();
       $schedule->command('happyBirth:users')->hourly();
       $schedule->command('birthday:users')->hourly();
       $schedule->command('happyBirthdayMail:users')->hourly();
       $schedule->command('finContrato:users')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
