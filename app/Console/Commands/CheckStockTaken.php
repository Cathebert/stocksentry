<?php

namespace App\Console\Commands;
use App\Models\User;
use App\Models\UserSetting;
use App\Models\Laboratory;
use App\Models\StockTake;
use Carbon\Carbon;
use DB;
use App\Notifications\StockTakeReminder;
use App\Notifications\NoStockTakeReminder;
use Illuminate\Console\Command;

class CheckStockTaken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-stock-taken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for stocktaken history';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
 $labs=Laboratory::select('lab_name','id')->get(); 
     

 foreach($labs as $lab){
       $stock=StockTake::where([['lab_id','=',$lab->id],['is_approved','=','yes']])->select('stock_date')->orderBy('id', 'DESC')->first();
    
if ($stock){
$start=Carbon::parse($stock->stock_date);
   $end=Carbon::now();
      
$diff_in_days = $start->diffInDays($end,false);

 if($diff_in_days>30){
    
           $approver_list=UserSetting::where('lab_id',$lab->id)->select('user_id')->get();   
   if(!empty($approver_list) && count($approver_list)>0){
foreach ($approver_list as $list){  
 $user=User::find($list->user_id);
 $user->notify(new StockTakeReminder($diff_in_days,$lab->lab_name));
 }
 }
 else{
   $approver_list=User::where([['laboratory_id','=',$lab->id],['authority','=',2]])->select('id')->get();
   foreach ($approver_list as $list){  
 $user=User::find($list->id);
 $user->notify(new StockTakeReminder($diff_in_days,$lab->lab_name));
 }
 }
 
 }
 else{
 }
 }
 

 else{
             $approver_list=UserSetting::where('lab_id',$lab->id)->select('user_id')->get();   
   if(!empty($approver_list) && count($approver_list)>0){
foreach ($approver_list as $list){  
 $user=User::find($list->user_id);
 $user->notify(new NoStockTakeReminder($lab->lab_name));
 }
 }
 else{
   $approver_list=User::where([['laboratory_id','=',$lab->id],['authority','=',2]])->select('id')->get();
   foreach ($approver_list as $list){  
 $user=User::find($list->id);
 $user->notify(new NoStockTakeReminder($lab->lab_name));
 }
 }  
 }
 
 }
 
 

 
 
 
    }

    
}