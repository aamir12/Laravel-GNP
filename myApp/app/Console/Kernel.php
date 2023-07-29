<?php

namespace App\Console;

use App\Jobs\PushUserMetrics;
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
        \App\Console\Commands\DeleteUser::class,
        \App\Console\Commands\StartCompetitions::class,
        \App\Console\Commands\EndCompetitions::class,
        \App\Console\Commands\ExportArchivedKpiData::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('competitions:end')->everyMinute()->appendOutputTo('/var/log/end.log');
        $schedule->command('competitions:start')->everyMinute()->appendOutputTo('/var/log/start.log');
        $schedule->command('passport:purge')->hourly();
        $schedule->command('invitation-reminder-email:send')->weekly();

        if (config('kpi.destructive_update')) {
            $schedule->command('kpi:export')->daily()->appendOutputTo('/var/log/kpi-export.log');
        }

        if (config('services.kumulos.url')) {
            $schedule->job(new PushUserMetrics)->hourly()->environments(['staging', 'production']);
        }
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
