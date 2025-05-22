<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendScheduledSurveys; // ← add this

class Kernel extends ConsoleKernel
{
    protected $commands = [
        SendScheduledSurveys::class, // ← register your command
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('surveys:send-scheduled')->dailyAt('08:00'); 
        // 🔥 This will automatically run daily at 8AM server time
    }

    protected function commands()
    {
        $this->load(app_path('Console/Commands'));

        require base_path('routes/console.php');
    }
}
