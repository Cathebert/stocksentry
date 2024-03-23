<?php

namespace App\Observers;

use App\Models\Laboratory;
use App\Models\User;
use App\Models\Inventory;
use App\Models\LabSection;
use App\Models\BinCard;
use App\Models\Consumption;
use App\Models\ConsumptionDetail;
class LaboratoryObserver 
{
    /**
     * Handle the Laboratory "created" event.
     */
    public function created(Laboratory $laboratory): void
    {
        //
    }

    /**
     * Handle the Laboratory "updated" event.
     */
    public function updated(Laboratory $laboratory): void
    {
        //
    }

    /**
     * Handle the Laboratory "deleted" event.
     */
    public function deleted(Laboratory $laboratory): void
    {
        //
        
        User::where('laboratory_id',$laboratory->id)->delete();
        Inventory::where('lab_id',$laboratory->id)->delete();
        LabSection::where('lab_id',$laboratory->id)->delete();
        BinCard::where('lab_id',$laboratory->id)->delete();
        Consumption::where('lab_id',$laboratory->id)->delete();
        ConsumptionDetail::where('lab_id',$laboratory->id)->delete();
       
    }

    /**
     * Handle the Laboratory "restored" event.
     */
    public function restored(Laboratory $laboratory): void
    {
        //
    }

    /**
     * Handle the Laboratory "force deleted" event.
     */
    public function forceDeleted(Laboratory $laboratory): void
    {
        //
        User::where('laboratory_id',$laboratory->id)->delete();
        Inventory::where('lab_id',$laboratory->id)->delete();
        LabSection::where('lab_id',$laboratory->id)->delete();
        BinCard::where('lab_id',$laboratory->id)->delete();
        Consumption::where('lab_id',$laboratory->id)->delete();
        ConsumptionDetail::where('lab_id',$laboratory->id)->delete();
           
    }
}
