<?php

namespace App\Http\Controllers\ColdRoom;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
class ColdRoomController extends Controller
{
    //

    public function index(){
                    $data['total']=DB::table('consumption_details')->sum('consumed_quantity');
   // dd($sum) ;  
    $data['consumption']=DB::table('consumption_details as inv')
                ->join('laboratories as l','l.id','=','inv.lab_id')
                ->select('l.lab_name','inv.consumed_quantity',DB::raw('sum(inv.consumed_quantity) as percentage') , DB::raw('round(avg(inv.consumed_quantity),2) as avg') )
                ->where('inv.section_id',auth()->user()->section_id)
                ->groupBy('l.lab_name')->get();
                
                 $data['requests']=Requisition::where('section_id',auth()->user()->section_id)->count();
        $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['section_id','=',auth()->user()->section_id]])->count();
           $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}

        return view('cold.show',$data);
    }

    public function coldProfile(){
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
        return view('cold.user.profile',$data);
    }

    public function coldSignature(){

         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();

        return view('cold.user.signature',$data);
    }

public function coldPassword(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();

        return view('cold.user.password.change-password',$data);

}
}
