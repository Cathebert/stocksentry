<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\StockTake;
use App\Models\Discrepancy;
class ProcessStockCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $stocktake;
    public function __construct($id)
  
    {
        //
          $this->stocktake=$id;
           
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $stock=$this->stocktake;
      //  dd($this->stocktake);
$discre=Discrepancy::where('stock_id', $stock)->get();
    
        if(!empty($discre) && count($discre)>0){
       
        }
    
    }
}
