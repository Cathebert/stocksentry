<?php

namespace App\Http\Controllers\ColdRoom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
use App\Models\Inventory;
use DB;
class ColdRoomStockTakeController extends Controller
{
   

 public function   showColdRoomStockTake(){

         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
          $data['count']=Inventory::where('lab_id',auth()->user()->laboratory_id)->count(); 
          
          
               $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
$data['labs']=Laboratory::get();
        return view('cold.inventory.tabs.cold_stocktaking',$data);
    }

public function showColdRoomStockTakeHistory(){
    $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->withTrashed()->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('cold.inventory.tabs.cold_stock_history',$data);
}

}