<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\User;
use App\Models\ScheduleReport;
use App\Jobs\ConsumptionJob;
use App\Console\Commands\CheckAboutToExpire;

use DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(CheckAboutToExpire::class)->everyFifteenSeconds()->appendOutputTo(storage_path('logs/check_expiry_dates.log'));
        // $schedule->command('inspire')->hourly();
$scheduled_report=ScheduleReport::where('status','active')->get();
if(!empty($scheduled_report) && count($scheduled_report)> 0){
  
foreach ($scheduled_report as $report) {
   if($report->type==1){
     switch ($report->frequency) {
        case 1:
         $schedule->job(new ConsumptionJob($report->id))->weekly();
         
            break;
        case 2:
         $schedule->job(new ConsumptionJob($report->id))->monthly();
         
        case 3:
        $schedule->job(new ConsumptionJob($report->id))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new ConsumptionJob($report->id))->yearly();
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
