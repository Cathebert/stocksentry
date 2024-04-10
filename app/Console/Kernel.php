<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\User;
use App\Models\ScheduleReport;
use App\Jobs\ConsumptionJob;
use App\Jobs\StockLevelJob;
use App\Jobs\RequisitionJob;
use App\Jobs\DisposalJob;
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
    /**
     * type one is consumption report
     * type two is stock level report
     * type three is requisition
     * type four is disposal
     * type five is issue
     * */
   switch($report->type){
    //consumption
    case 1:
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
          break;
     }
    break;
    //stock level
case 2:
switch ($report->frequency) {
      case 1:
         $schedule->job(new StockLevelJob($report->id))->weekly();
         
            break;
        case 2:
         $schedule->job(new StockLevelJob($report->id))->monthly();
         
        case 3:
        $schedule->job(new StockLevelJob($report->id))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new StockLevelJob($report->id))->yearly();
          break;
} 

break;

//requisition

case 3:

    switch ($report->frequency) {
      case 1:
         $schedule->job(new RequisitionJob($report->id))->weekly();
         
            break;
        case 2:
         $schedule->job(new RequisitionJob($report->id))->monthly();
         
        case 3:
        $schedule->job(new RequisitionJob($report->id))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new RequisitionJob($report->id))->yearly();
          break;
 }
break;
//disposal
case 4:

switch ($report->frequency) {
      case 1:
         $schedule->job(new DisposalJob($report->id))->weekly();
         
            break;
        case 2:
         $schedule->job(new DisposalJob($report->id))->monthly();
         
        case 3:
        $schedule->job(new DisposalJob($report->id))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new DisposalJob($report->id))->yearly();
          break;
 }
break;
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
