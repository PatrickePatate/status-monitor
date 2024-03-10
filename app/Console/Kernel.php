<?php

namespace App\Console;

use App\Jobs\CheckerJob;
use App\Models\Checks\DnsCheck;
use App\Models\Checks\HttpCheck;
use App\Services\DnsCheckService;
use App\Services\HttpCheckService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(CheckerJob::class)->everyThirtySeconds();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
