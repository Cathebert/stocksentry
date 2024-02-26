<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\ReceivedItem;
use App\Models\Inventory;
use App\Models\ItemTemp;
use App\Models\Issue;
use App\Models\Setting;
use App\Models\Requisition;
use Validator;
use DB;
class GeneralController extends Controller
{
    //

     public function receiveInventory(Request $request){
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
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
           
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.receive.recieve',$data);
    }
     public function showRequest(){
        $data['laboratories']=Laboratory::get();
        $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
        $sr_number=Requisition::select('id','sr_number')->orderBy('id', 'desc')->first();

if($sr_number){

             $data['sr_number']=$this->get_order_number($sr_number->id);
}
else{
       
             $data['sr_number']=   $this->get_order_number(1);
}
      
    
    $data['badges']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])->count();
    $data['approved']=Requisition::where([['lab_id','=',auth()->user()->laboratory_id],['status','=','approved']])->count();
    $data['requests']=Requisition::where([['lab_id','=',auth()->user()->laboratory_id],['status','=','not approved']])->count();
       
         
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.issues.tabs.requests',$data);
    }

         public function showIssue(){
        $data['laboratories']=Laboratory::get();
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
      $data['sections']=LaboratorySection::get();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
         

        }
        $sr_number=Requisition::select('id','sr_number')->orderBy('id', 'desc')->first();

 $issue=Issue::select('id')->latest('id')->first();

  $settings=Setting::find(1);
  if( $issue==NULL){
          $data['issue']=$settings->issue_prefix.'0001';
         }
         else{
          $number=str_pad($issue->id+1, 4, '0', STR_PAD_LEFT);
        
          $data['issue']=$settings->issue_prefix.''.$number;
         }
      
    
     $data['badges']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])->count();
       $data['approved']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','approved']])->count();
       $data['requests']=Requisition::where([['lab_id','=',auth()->user()->laboratory_id],['status','=','not approved']])->count();
       
       
       
          
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.issues.tabs.lab_issues',$data);
    }
public function showLabReceived(){
      $data['laboratories']=Laboratory::get();
          $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
         $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
        
            
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.issues.tabs.lab_receive_issued',$data);
}


    private function get_order_number($id)

{
    return 'SR' . str_pad($id, 4, "0", STR_PAD_LEFT);
}
     public function showLabInventory(){
      
      $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.item_name')
          ->where('t.lab_id','=',auth()->user()->laboratory_id)->paginate(7);
          
               $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
   
        return view('provider.inventory.inventory',$data);
    }
}
