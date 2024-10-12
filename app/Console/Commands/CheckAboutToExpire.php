<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\User;
use App\Models\UserSetting;
use App\Models\Laboratory;
use PDF;
use DB;
use App\Notifications\AboutToExpireNotification;
class CheckAboutToExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-about-to-expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'checks items that are about to expire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $labs=Laboratory::select('lab_name','id')->get(); 
      $start=Carbon::now();
 $expiryDate = Carbon::now()->addDays(30);
 foreach($labs as $lab){
       $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
        
     ->whereBetween('t.expiry_date', [$start,  $expiryDate])
     ->where('t.quantity','>',0)
     ->where('t.lab_id',$lab->id)
     ->get();
  
 if(count($data['info']) >0){
    $name=$lab->lab_name."_about_to_expire_30_days.pdf";
    $data['lab_name']=$lab->lab_name;
    $path=public_path('reports').'/'.$name;
        
		$pdf=PDF::loadView('pdf.reports.about_expire',$data);
		$pdf->save($path); 
		   $approver_list=UserSetting::where('lab_id',$lab->id)->select('user_id')->get();   
   if(!empty($approver_list) && count($approver_list)>0){
foreach ($approver_list as $list){  
 $user=User::find($list->user_id);
 $user->notify(new AboutToExpireNotification($path,$lab->lab_name));
 }
 }
 else{
   $approver_list=User::where([['laboratory_id','=',$lab->id],['authority','=',2]])->select('id')->get();
   foreach ($approver_list as $list){  
 $user=User::find($list->id);
 $user->notify(new AboutToExpireNotification($path,$lab->lab_name));
 }
 }
 
 }
 else{
 //dd($data['info']);
 }
 
 
 
 
    }
}
}