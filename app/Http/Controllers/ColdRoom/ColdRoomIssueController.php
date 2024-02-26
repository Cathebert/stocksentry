<?php

namespace App\Http\Controllers\ColdRoom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
use App\Models\Issue;
use DB;
class ColdRoomIssueController extends Controller
{
    //

    public function  showColdIssue(){

        $data['laboratories']=Laboratory::get();
        $data['sections']=LaboratorySection::get();
        $sr_number=Requisition::select('id','sr_number')->orderBy('id', 'desc')->first();

if($sr_number){

             $data['sr_number']=$this->get_order_number($sr_number->id);
}
else{
       
             $data['sr_number']=   $this->get_order_number(1);
}
      
    
     $data['badges']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])->count();
       $data['approved']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','approved']])->count();
       $data['requests']=Requisition::where([['lab_id','=',auth()->user()->laboratory_id],['status','=','not approved']])->count();
       
            $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('cold.issue.tabs.cold_issue',$data);
    }



    public function showColdRoomReceived(){
      $data['laboratories']=Laboratory::get();
        $data['sections']=LaboratorySection::get();
        
        
             $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('cold.issue.tabs.cold_received',$data);
}

    private function get_order_number($id)

{
    return 'SR' . str_pad($id, 4, "0", STR_PAD_LEFT);
}
}
