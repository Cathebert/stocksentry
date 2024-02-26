<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Inventory;
use App\Models\User;
use App\Models\BinCard;
use App\Models\StockTake;
use App\Notifications\StockUpdateNotification;
use Illuminate\Support\Facades\Notification;
use DB;

class updateStockTakenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $stock;
    protected $stock_take_id;
    public function __construct($stock,$stock_take_id)

    {
        $this->stock=$stock;
        $this->stock_take_id=$stock_take_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        if(empty($this->stock)){
            throw new Exception("Error Processing request...");
        }
        try{
DB::beginTransaction();
      foreach($this->stock as $stock){
       Inventory::where('id',$stock->item_id)->update([
        'quantity'=>$stock->physical_count,
        'updated_at'=>now(),
       ]);
       $this->updateItemBinCard($stock->item_id,$stock->physical_count);

      }
      $this->updateStockTakeDetails($this->stock_take_id);

$stock=StockTake::where('id',$this->stock_take_id)->select('approved_by')->first();

          $user = User::where([['id','=',$stock->approved_by]])->select('id','email')->first();
        Notification::send($user, new StockUpdateNotification()); 
        DB::commit(); 
        }
        catch(Exception $e){
DB::rollback();
        }
    }

    protected function updateItemBinCard($item_id,$physical_count){

        try{
            $inventory=Inventory::where('id',$item_id)->first();
            $bincard=new BinCard();
            $bincard->inventory_id=$item_id;
            $bincard->date=now();
            $bincard->description= config('stocksentry.stocktaken.bin_description');
            $bincard->transaction_type='stocktaken';
            $bincard->transaction_number= $inventory->grn_number;
            $bincard->batch_number= $inventory->batch_number;
            $bincard->item_id=$inventory->item_id;
            $bincard->quantity= $physical_count;
            $bincard->balance=$physical_count;
            $bincard->lab_id=auth()->user()->laboratory_id;
            $bincard->section_id=auth()->user()->section_id;
            $bincard->created_at=now();
            $bincard->updated_at=NULL;
            $bincard->save();

        }
        catch(Exception $e){

        }

    }

    protected function updateStockTakeDetails($stock_take_id){
        StockTake::where('id',$stock_take_id)->update([
            'is_approved'=>'yes',
            'approved_by'=>auth()->user()->id,
            'updated_at'=>now(),
        ]);
    }
}
