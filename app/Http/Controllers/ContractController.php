<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Contract;
use App\Models\Laboratory;
use App\Models\ContractHistory;
use DB;
use Carbon\Carbon;
class ContractController extends Controller
{
    //

    public function add(){
        $data['suppliers']=Supplier::all();
        $data['subscriptions']=DB::table('subscription_categories')->get();

        $data['contracts']=DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->get();
                                                           
             $labs=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
             $data['lab_name']='Logged Into: '.$labs->lab_name;
        return view('contracts.show',$data);
    }
    public function load(Request $request){
        
        $columns= array(
    0 => 'id', 
    1=>'contract_number',
    2=>'contract_name',
    3=>'contract_desc',
    4=>'contract_start',
    5=>'contract_end',
    6=>'sub_type',

    7=>'supplier',
    8=>'status',
    9=>'action',
    10=>'frequency',
    11=>'contract_unit'
    );
   
   $totalData = DB::table('contracts as c')
                                    
                                    ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');


            $terms =DB::table('contracts as c')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id'
                                       
                                )
                ->where(function ($query) use ($search){
                  return  $query->where('contract_number', 'LIKE', "%{$search}%")
                  ->orWhere('contract_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
        
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 


              $totalFiltered =  $totalRec ;

          $data = array();
          if (!empty($terms)) {
$x=1;

            foreach ($terms as $term) {

if($term->supplier_id!=NULL){
    $supplier=Supplier::where('id',$term->supplier_id)->select('supplier_name')->first();
     $supplier_name=$supplier->supplier_name;
}
else{
    $supplier_name="N/A";
}
$expiryDate = Carbon::parse($term->contract_enddate);
$currentDate = Carbon::now();
$expiryThreshold = $currentDate->copy()->addDays(30);
             $nestedData['id']=$x;
                $nestedData['contract_number']=$term->contract_number;
             
                 $nestedData['contract_name']= $term->contract_name;
                    $nestedData['contract_desc']= $term->contract_description;
                   
            $nestedData['contract_start'] = date('d,M Y',strtotime($term->contract_startdate));
            $nestedData['frequency']=$term->frequency;
             if ($term->contract_unit==1) {
               $nestedData['contract_unit']="Month";
                
             }
             else{
                $nestedData['contract_unit']="Year";
             }
                   
                
            $nestedData['contract_end']=date('d,M Y',strtotime($term->contract_enddate));
                 if($term->contract_type==1){
                     $nestedData['sub_type']='Supplier';
                 }
                 else{
                    $nestedData['sub_type']='Service';
                 }
                 
                  $nestedData['supplier']  = $supplier_name;
if ($currentDate->lt($expiryDate)) {
  $nestedData['status']  ="<span class='badge badge-success'>Good</span>";
   $nestedData['action']= "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i> View</a> | <a class='btn btn-success btn-sm' id='$term->id' onclick='updateContract(this.id)'><i class='fa fa-check'></i> Update</a> ";
} 
if ($expiryDate->lt($expiryThreshold)) {

     $nestedData['status']  ="<span class='badge badge-warning>warning</span>";
      $nestedData['action'] = "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i> View</a> | <a class='btn btn-success btn-sm' id='$term->id' onclick='updateContract(this.id)'><i class='fa fa-check'></i> Update</a> ";
} 
        
if ($currentDate->gt($expiryDate)) {
     $nestedData['status']  ="<span class='badge badge-danger>Expired</span>";
     $nestedData['action'] = "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i> View</a> | <a class='btn btn-success btn-sm' id='$term->id' onclick='updateContract(this.id)'><i class='fa fa-check'></i> Update</a> ";
}  
                 
              
               
                   $x++;
                $data[] = $nestedData;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
    );

      echo json_encode($json_data);

    }
    public function showModal(){
          $data['suppliers']=Supplier::all();
        $data['subscriptions']=DB::table('subscription_categories')->get();
        return view('inventory.modal.contract_modal',$data);
    }

    public function saveContract(Request $request){
       
       $contract=new Contract();
       $contract->contract_number=$request->contract_number;
       $contract->contract_name=$request->contract_name;
       $contract->contract_description=$request->contract_desc;
       $contract->contract_startdate=$request->contract_startdate;
       $contract->contract_enddate=$request->contract_enddate;
       $contract->frequency=$request->contract_frequency;
       $contract->contract_unit=$request->cont_unit;
       $contract->contract_type=$request->cont_type;
       if($request->supplier!=NULL){
       $contract->supplier_id=$request->supplier;
   }
   else{
     $contract->supplier_id=NULL;
   }
       $contract->created_at=now();
       $contract->updated_at=NULL;
       $contract->save();

       return back()->with('success',"Contract created Successfully");
    }

    public function saveSubscriptionType(Request $request)
    {
       DB::table('subscription_categories')->insert([
    ['name' => $request->sub_name, 'description' => $request->description]
    ]);

       return response()->json([
        'message'=>"done",
        'error'=>false
       ]);
    }
}
