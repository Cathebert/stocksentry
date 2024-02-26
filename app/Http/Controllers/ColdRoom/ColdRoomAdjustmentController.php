<?php

namespace App\Http\Controllers\ColdRoom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
class ColdRoomAdjustmentController extends Controller
{
    //

    public function showAdjustment(){
      $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
    
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
         return view('cold.inventory.tabs.cold_stockadjustment',$data);
}
}
