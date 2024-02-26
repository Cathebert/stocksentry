<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratory;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use DB;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['labs']=Laboratory::count();
        $data['users']=User::count();
        $data['item']=Inventory::where([['lab_id', '=',auth()->user()->laboratory_id],[ 'expiry_date', '>', date('Y-m-d') ]])->count();
        $data['requests']=Requisition::where('status','approved')->count();
       // $data['notifications']=auth()->user()->unreadNotifications;
 $data['total']=DB::table('consumption_details')->sum('consumed_quantity');
   // dd($sum) ;  
   $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}

    $data['consumption']=DB::table('consumption_details as inv')
                ->join('laboratories as l','l.id','=','inv.lab_id')
                ->select('l.lab_name','inv.consumed_quantity',DB::raw('sum(inv.consumed_quantity) as percentage') , DB::raw('round(avg(inv.consumed_quantity),2) as avg') )
                ->groupBy('l.lab_name')->get();
    $is_registered=User::where('id',auth()->user()->id)->select('is_registered')->first();
    //$data['is_registered']=$is_registered->is_registered;
if($is_registered->is_registered==1){
     $data['users'] = Auth::user();
return view('initialization.setup',$data);
}else{
  return view('dashboard',$data);  
}
        
    }
    public function welcome(){
         return view('welcome',$data);
    }
}
