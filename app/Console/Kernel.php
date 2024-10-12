<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\User;
use App\Models\ScheduleReport;
use App\Models\Contract;
use App\Models\BackupSchedule;
use App\Jobs\ConsumptionJob;
use App\Jobs\StockLevelJob;
use App\Jobs\RequisitionJob;
use App\Jobs\DisposalJob;
use App\Jobs\ContractManagementJob;
use App\Jobs\ExpiredItemJob;
use App\Jobs\BackupDatabaseJob;
use App\Console\Commands\CheckAboutToExpire;
use App\Console\Commands\CheckStockTaken;

use DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
    
        $schedule->command(CheckAboutToExpire::class)->weekly()->appendOutputTo(storage_path('logs/check_expiry_dates.log'));
        // $schedule->command('inspire')->hourly();
      
 //check stock taken
  $schedule->command(CheckStockTaken::class)->lastDayOfMonth('13:00')->appendOutputTo(storage_path('logs/stocken_dates.log'));
  
  //contracts
       $contracts=Contract::whereBetween('contract_enddate',[now(), now()->addDays(90)])
    ->get();
    
  
   //contracts
   
if(!empty($contracts) && count($contracts)>0){

    foreach($contracts as $contract) {
       $schedule->job(new ContractManagementJob($contract->id))->weeklyOn(1, '8:00');
    }
}

  //backup schedules
   $backups=BackupSchedule::where('status','active')->get();
   if(!empty($backups) && count($backups)> 0){
   foreach($backups as $backup){
   switch($backup->frequency){
   
   //daily
   case 1:
   $type="daily";
     $schedule->job(new BackupDatabaseJob($backup->receiver, $type))->daily();
   break;
   
   //weekly
   case 2:
     $type="weekly";
      $schedule->job(new BackupDatabaseJob($backup->receiver, $type))->weekly();
   break;
   
   
   //monthly
   case 3:
     $type="monthly";
      $schedule->job(new BackupDatabaseJob($backup->receiver, $type))->monthly();
   break;
   }
   }
   
   }
   
    //scheduled Report

$reports=ScheduleReport::where('status','active')->select('id','lab_id','type','frequency','start_date','attach_as')->get();

if(!empty($reports) && count($reports)> 0){
 /**
     * type one is consumption report
     * type two is stock level report
     * type three is requisition
     * type four is disposal
     * type five is Expiry
     * */
   
    foreach ($reports as $report){
   switch($report->type){
    //consumption
    case 1:
     switch ($report->frequency) {
        case 1:
         $schedule->job(new ConsumptionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->weeklyOn(1, '13:00');
          // $schedule->job(new ConsumptionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->everyTwoMinutes();
            break;
        case 2:
         $schedule->job(new ConsumptionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->monthlyOn(2, '8:00');
         
        case 3:
        $schedule->job(new ConsumptionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new ConsumptionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->yearly();
          break;
     }
break;
    //stock level
case 2:
switch ($report->frequency) {
      case 1:
   
         $schedule->job(new StockLevelJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->weeklyOn(1, '15:00');
         // $schedule->job(new StockLevelJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->everyTwoMinutes();
            break;
        case 2:
         $schedule->job(new StockLevelJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->monthlyOn(2, '13:00');
         
        case 3:
        $schedule->job(new StockLevelJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new StockLevelJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->yearly();
          break;
} 

break;

//requisition

case 3:

    switch ($report->frequency) {
      case 1:
         $schedule->job(new RequisitionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->weeklyOn(1, '10:00');
          //$schedule->job(new RequisitionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->everyTwoMinutes();
            break;
        case 2:
         $schedule->job(new RequisitionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->monthlyOn(2, '16:00');
         
        case 3:
        $schedule->job(new RequisitionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new RequisitionJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->yearly();
          break;
 }
break;
//disposal
case 4:

switch ($report->frequency) {
      case 1:
        $schedule->job(new DisposalJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->weeklyOn(1, '22:00');
          //$schedule->job(new DisposalJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->everyTwoMinutes();
            break;
        case 2:
         $schedule->job(new DisposalJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->monthlyOn(2, '19:00');
         
        case 3:
        $schedule->job(new DisposalJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new DisposalJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->yearly();
          break;
 }
break;
case 5:

switch ($report->frequency) {
      case 1:
     
        $schedule->job(new ExpiredItemJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->weeklyOn(1, '16:00');
         // $schedule->job(new ExpiredItemJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->everyTwoMinutes();
            break;
        case 2:
         $schedule->job(new ExpiredItemJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->monthlyOn(2, '22:00');
         
        case 3:
        $schedule->job(new ExpiredItemJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->quarterly();
            # code...
            break;
        case 4:
          $schedule->job(new ExpiredItemJob($report->id,$report->lab_id,$report->start_date,$report->attach_as))->yearly();
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