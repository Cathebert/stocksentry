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
use DB;
class updateReceived implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $received
    public function __construct($received)
    {
      this->received=$received;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //


        if(empty($this->received)){
            throw new Exception("Error Processing Request", 1);
            
        }
        try{

        }
        catch(Exception $e){
            DB::rollback();
        }
    }
}
