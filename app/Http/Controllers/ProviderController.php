<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\LabSection;
use App\Models\ReceivedItem;
use App\Models\Inventory;
use App\Models\ItemTemp;
use App\Models\Issue;
use App\Models\Requisition;
use Validator;
use DB;
use Illuminate\Support\Facades\Auth;
class ProviderController extends Controller
{
    //
    public function index(){
       
        $data['users']=User::where('laboratory_id',auth()->user()->laboratory_id)->count();

        $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
        $data['total']=DB::table('consumption_details')->sum('consumed_quantity');
   // dd($sum) ;  
    $data['consumption']=DB::table('consumption_details as inv')
                ->join('laboratories as l','l.id','=','inv.lab_id')
                ->where('inv.lab_id',auth()->user()->laboratory_id)
                ->select('l.lab_name','inv.consumed_quantity',DB::raw('sum(inv.consumed_quantity) as percentage') , DB::raw('round(avg(inv.consumed_quantity),2) as avg') )
                
                ->groupBy('l.lab_name')->get();
        
         
   
   
$data['lab_name']='Logged Into: '.$lab->lab_name;


$is_registered=User::where('id',auth()->user()->id)->select('is_registered')->first();
    //$data['is_registered']=$is_registered->is_registered;
if($is_registered->is_registered==1){
     $data['users'] = Auth::user();
return view('initialization.setup',$data);
}else{
        return view('provider.provider',$data);
}
    }
    public function receiveInventory(Request $request){
          $data['suppliers']=Supplier::select('id','supplier_name')->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->where('id',auth()->user()->laboratory_id)->get();
           $data['sections']=LaboratorySection::select('id','section_name')->get();
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
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
     public function showIssue(){
        $data['laboratories']=Laboratory::get();
        $data['sections']=LaboratorySection::get();
              $data['badges']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])->count();
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

       return view('provider.issues.issue',$data);
    }

    public function showSections(){
          $data['sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
        $data['sectionlist']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->select('s.id as id','s.section_name')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->get();
                     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section')->first();
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
        }
             $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.sections.index',$data);
    }
}