<?php

namespace App\Console;

use App\Auth\AuthReadModelProjection;
use App\Console\App\SeedAccountCommand;
use App\Console\Util\ReadModelWorkersCommand;
use App\Console\Util\ResetAppCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        ResetAppCommand::class,
        SeedAccountCommand::class,
        AuthReadModelProjection::class,
        ReadModelWorkersCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
