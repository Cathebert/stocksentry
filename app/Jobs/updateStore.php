<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Inventory;
use App\Models\Requisition;
use App\Models\Received;
 use App\Models\User;
use App\Services\BinCardService;
use App\Notifications\OrderCompletedNotification;
use Illuminate\Support\Facades\Notification;
use DB;
class updateStore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $requested;
    public function __construct($requested)
    {
       $this->requested=$requested;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //

        if(empty($this->requested)){
            throw new Exception("Error Processing Request", 1);
            
        }
        try{
           
        DB::beginTransaction();
      for ($i=0;$i<count($this->requested['requisition']);$i++) {
            $inventory=DB::table('inventories')->where([['id','=',$this->requested['requisition'][$i]->item_id],['lab_id','=',auth()->user()->laboratory_id]])->first();
            //$this->updateReceived($entry,$this->requested['requisition'][$i]->quantity_requested);
             DB::table('inventories')->where([['id','=',$this->requested['requisition'][$i]->item_id],['lab_id','=',auth()->user()->laboratory_id]])
         ->decrement('quantity',$this->requested['requisition'][$i]->quantity_requested,['updated_at'=>now()]);

         $bincard=new BinCardService();
         $bincard->updateStoreOrder($this->requested['requisition'][$i]->item_id,$this->requested['requisition'][$i]->quantity_requested,$this->requested['lab_requested']->lab_id);

$exists=DB::table('inventories')->where([['item_id','=',$inventory->item_id],['lab_id','=',$this->requested['lab_requested']->lab_id]])->exists();



        if($exists){
         DB::table('inventories')->where([['item_id','=',$inventory->item_id],['lab_id','=',$this->requested['lab_requested']->lab_id]])
         ->increment('quantity',$this->requested['requisition'][$i]->quantity_requested,['updated_at'=>now()]);
         $bincard=new BinCardService();
         $bincard->updateOrderReceivingLabCard($inventory->item_id,$this->requested['lab_requested']->lab_id, $this->requested['requisition'][$i]->quantity_requested);
        }
        else{
            $quantity=$this->requested['requisition'][$i]->quantity_requested;
           $inventory= DB::table('inventories')->where([['id','=',$this->requested['requisition'][$i]->item_id],['lab_id','=',auth()->user()->laboratory_id]])->first();
            $this->createNewInventory($inventory,$this->requested['lab_requested']->lab_id,$this->requested['lab_requested']->section_id,$quantity);
        }
        
       
    

        
    }
    Requisition::where('id',$this->requested['lab_requested']->id)->update([
  'status'=>'delivered',
    ]);
    
     DB::commit();
     $requisition_approver=Requisition::where('id',$this->requested['lab_requested']->id)->select('requested_by','approved_by','requested_date')->first();
    $approver=User::where('id',$requisition_approver->approved_by)->select('id','email')->first();
    $requester=User::where('id',$requisition_approver->requested_by)->select('id','email')->first();
    $request_date= date('d,M Y',strtotime($requisition_approver->requested_date));
    $approver->notify(new OrderCompletedNotification($request_date));
    $requester->notify(new OrderCompletedNotification($request_date));
   
}
catch(Exception $e){
            DB::rollback();
        }
}
private function createNewInventory($data,$lab_id,$section_id,$quantity){

$inventory=new Inventory();
$inventory->lab_id=$lab_id;
$inventory->section_id=$section_id;
$inventory->recieved_id=$data->recieved_id;
$inventory->grn_number=$data->grn_number;
$inventory->item_id=$data->item_id;
$inventory->batch_number=$data->batch_number;
$inventory->quantity=$quantity;
$inventory->expiry_date=$data->expiry_date;
$inventory->cost=$data->cost;
$inventory->pp_no=$data->pp_no;
$inventory->created_at=now();
$inventory->updated_at=NULL;
$inventory->save();

$item_id=$inventory->id;
 $bincard=new BinCardService();
 $bincard->updateByInventoryIdOrderReceivingLabCard($item_id,$lab_id, $quantity);
}
}