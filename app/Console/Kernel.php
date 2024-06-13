<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // \App\Console\Commands\TestCron::class,
        \App\Console\Commands\ActionCampaignCron::class,
        \App\Console\Commands\ActionLeadCron::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('test:cron')->cron("* * * * *")->appendOutputTo(storage_path('logs/cron.log'));
        $schedule->command('action:campaign')->cron('* * * * *')->appendOutputTo(storage_path('logs/campaign_action.log'));
        $schedule->command('action:lead')->cron('* * * * *')->appendOutputTo(storage_path('logs/lead_action.log'));
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
