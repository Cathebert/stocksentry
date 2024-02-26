<?php

namespace App\Http\Controllers\ColdRoom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
use DB;
class ColdRoomBinCardController extends Controller
{
    //

    public function  showColdRoomBinCard(){

     $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
        
         
      $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
          ->groupBy('s.id')
             ->where('t.lab_id',auth()->user()->laboratory_id)
          ->paginate(15);
      $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('cold.inventory.tabs.cold_bincard', $data);
}
}
