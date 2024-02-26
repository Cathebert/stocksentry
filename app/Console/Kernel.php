<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\User;
use App\Models\ScheduleReport;
use App\Jobs\GenerateExpiryReport;

use DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
$scheduled_report=ScheduleReport::get();
if(!empty($scheduled_report) && count($scheduled_report)> 0){
  
foreach ($scheduled_report as $report) {
   if($report->type=="expiry"){
     switch ($report->frequency) {
        case 1:
         $schedule->job(new GenerateExpiryReport($report->id))->everyFifteenSeconds();
         
            break;
        
        default:
            # code...
            break;
     }

   }
}

}
        
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
