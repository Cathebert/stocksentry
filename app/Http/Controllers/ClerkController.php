<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
use DB;
class ClerkController extends Controller
{
    //
    public function index(Request $request){
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
if($is_registered->is_registered==1){
     $data['users'] = Auth::user();
return view('initialization.setup',$data);
}
else{
    return view('clerk.layout.index',$data);
}

    }
}
