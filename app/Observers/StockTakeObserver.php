<?php

namespace App\Observers;

use App\Models\StockTake;
use App\Jobs\ProcessStockCheck;
use App\Models\Discrepancy;
use App\Notifications\StockDiscrepancyNotification ;
use  App\Models\User;
class StockTakeObserver
{
    /**
     * Handle the StockTake "created" event.
     */

       public $afterCommit = true;
    public function created(StockTake $stockTake): void
    {
        //

    
      $discre=Discrepancy::where('stock_id',$stockTake->id)->get();
    $user=User::where([['id','=',$stockTake->supervisor_id]])->select('id','email')->first();
        if(count($discre)>0){
        $user->notify(new StockDiscrepancyNotification($stockTake,$discre));
        }
      

    }

    /**
     * Handle the StockTake "updated" event.
     */
    public function updated(StockTake $stockTake): void
    {
        //
    }

    /**
     * Handle the StockTake "deleted" event.
     */
    public function deleted(StockTake $stockTake): void
    {
        //
    }

    /**
     * Handle the StockTake "restored" event.
     */
    public function restored(StockTake $stockTake): void
    {
        //
    }

    /**
     * Handle the StockTake "force deleted" event.
     */
    public function forceDeleted(StockTake $stockTake): void
    {
        //
    }
}
