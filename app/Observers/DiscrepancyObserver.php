<?php

namespace App\Observers;

use App\Models\Discrepancy;
use App\Models\StockTake;
class DiscrepancyObserver
{
    /**
     * Handle the Discrepancy "created" event.
     */
    public function created(Discrepancy $discrepancy): void
    {
        //
  
          $discre=StockTake::where('id',$discrepancy->stock_id)->get();
     
    

    }

    /**
     * Handle the Discrepancy "updated" event.
     */
    public function updated(Discrepancy $discrepancy): void
    {
        //
    }

    /**
     * Handle the Discrepancy "deleted" event.
     */
    public function deleted(Discrepancy $discrepancy): void
    {
        //
    }

    /**
     * Handle the Discrepancy "restored" event.
     */
    public function restored(Discrepancy $discrepancy): void
    {
        //
    }

    /**
     * Handle the Discrepancy "force deleted" event.
     */
    public function forceDeleted(Discrepancy $discrepancy): void
    {
        //
    }
}
