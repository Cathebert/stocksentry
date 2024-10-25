<?php

namespace App\Http\Controllers\ColdRoom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
use App\Models\Supplier;
use App\Models\Issue;
use DB;
class ColdRoomIssueController extends Controller
{
    //

    public function  showColdIssue(){

        $data['laboratories']=Laboratory::get();
        $data['sections']=LaboratorySection::get();
        $sr_number=Requisition::select('id','sr_number')->orderBy('id', 'desc')->first();



             $data['sr_number']='SR'.str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

      
    
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
public function showNewReceipt(){
     $data['suppliers']=Supplier::select('id','supplier_name')->get();
     $data['users']=User::where('laboratory_id',auth()->user()->laboratory_id)->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->where('id',auth()->user()->laboratory_id)->get();
            $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
                   $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where([['l.lab_id','=',auth()->user()->laboratory_id],['l.section_id','=',auth()->user()->section_id]])->get();
}

    
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}

    return view('cold.inventory.tabs.receive.new_receipt',$data);
}
public function showColdAllReceived(){
    $data['suppliers']=Supplier::select('id','supplier_name')->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->where('id',auth()->user()->laboratory_id)->get();
           $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where([['l.lab_id','=',auth()->user()->laboratory_id],['l.section_id','=',auth()->user()->section_id]])->get();
}

    
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
} 

return view('cold.inventory.tabs.receive.all_receipts',$data); 
}

public function showReceivedCheckList(){
  $data['suppliers']=Supplier::select('id','supplier_name')->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->where('id',auth()->user()->laboratory_id)->get();
           $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where([['l.lab_id','=',auth()->user()->laboratory_id],['l.section_id','=',auth()->user()->section_id]])->get();
}

    
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
} 
return view('cold.inventory.tabs.receive.received_status',$data);  
}
}