<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Contract;
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
        return view('contracts.show',$data);
    }
    public function load(Request $request){
        $date=date('Y-m-d');
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
    );
   
   $totalData = DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');


            $terms =DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->join('subscription_categories as ct','ct.id','=','c.subscription_type')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'ct.name',
                                        's.supplier_name'
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

$expiryDate = Carbon::parse($term->contract_enddate);
$currentDate = Carbon::now();
$expiryThreshold = $currentDate->copy()->addDays(30);

             $nestedData['id']=$x;
                $nestedData['contract_number']=$term->contract_number;
             
                 $nestedData['contract_name']= $term->contract_name;
                    $nestedData['contract_desc']= 
                   
                 $nestedData['contract_start'] = date('d,M Y',strtotime($term->contract_startdate));
                 $nestedData['contract_end']  =date('d,M Y',strtotime($term->contract_enddate));
                $nestedData['sub_type']  =$term->name;
                $nestedData['supplier']  =$term->supplier_name;

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
        $validatedData=$request->validate([
            'contract_name'=>'required',
            'contract_number'=>'required',
            'contract_startdate'=>'required',
            'contract_enddate'=>'required'
        ]);
        try{
            DB::beginTransaction();
       $contract=new Contract();
       $contract->contract_number=$request->contract_number;
       $contract->contract_name=$request->contract_name;
       $contract->contract_description=$request->contract_desc;
       $contract->contract_startdate=$request->contract_startdate;
       $contract->contract_enddate=$request->contract_enddate;
       $contract->subscription_type=$request->sub_type;
       $contract->supplier_id=$request->supplier;
       $contract->created_at=now();
       $contract->updated_at=NULL;
       $contract->save();
 $id=$contract->id;
$history=new ContractHistory();
$history->contract_id=$id;
$history->start_date=$request->contract_startdate;
$history->end_date=$request->contract_enddate;
$history->updated_by=auth()->user()->id;
$history->created_at=now();
$history->updated_at=NULL;
$history->save();
DB::commit();
       return back()->with('success',"Contract created Successfully");
}
catch(Exception $e){
    DB::rollback();
 return back()->with('error',"Failed to create Contract");
}
    }

    public function saveSubscriptionType(Request $request)
    {
        parse_str($request->sub_form,$out);
  $filtered=$out;
 
      // dd($filtered['sub_name']);

       DB::table('subscription_categories')->insert([
    'name' => $filtered['sub_name'],
     'description' => $filtered['sub_description']
    ]);

       return response()->json([
        'message'=>"done",
        'error'=>false
       ]);
    }

    public function viewContract(Request $request){
        $data['contract']=DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->join('subscription_categories as ct','ct.id','=','c.subscription_type')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'ct.name',
                                        'ct.description',
                                        's.supplier_name'
                                )->where('c.id',$request->id)->first();

            $data['contract_details']=DB::table('contract_histories as h')
                                            ->join('users as u','u.id','=','h.updated_by')
                                            ->where('h.contract_id',$request->id)
                                            ->get();
            return view('inventory.modal.view_contract',$data);
    }

    public function updateContract(Request $request){
 $data['contract']=DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->join('subscription_categories as ct','ct.id','=','c.subscription_type')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'ct.name',
                                        'ct.description',
                                        's.supplier_name'
                                )->where('c.id',$request->id)->first();
    $data['id']=$request->id;

            return view('inventory.modal.update_contract',$data);

    }

    public function keepupContract(Request $request){
//dd($request);
        Contract::where('id',$request->contract_id)->update([
            'contract_startdate'=>$request->contract_startdate,
            'contract_enddate'=>$request->contract_enddate
        ]);
        $history= new ContractHistory();
        $history->contract_id= $request->contract_id;
        $history->start_date=$request->contract_startdate;
        $history->end_date=$request->contract_enddate;
        $history->updated_by=auth()->user()->id;

        $history->save();

        return back()->with('success',"Updated Successfully");
    }

public function filterContract(Request $request){
   
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
    );
   switch($request->type){
    case 'number':
   $totalData = DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->where('c.contract_number','LIKE',"%{$request->value}%")
                                    ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');


            $terms =DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->join('subscription_categories as ct','ct.id','=','c.subscription_type')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'ct.name',
                                        's.supplier_name'
                                )->where('c.contract_number','LIKE',"%{$request->value}%")
                ->where(function ($query) use ($search){
                  return  $query->where('contract_number', 'LIKE', "%{$search}%")
                  ->orWhere('contract_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
        
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 

            break;
    case 'name':

    $totalData = DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->where('c.contract_name','LIKE',"%{$request->value}%")
                                    ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');


            $terms =DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->join('subscription_categories as ct','ct.id','=','c.subscription_type')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'ct.name',
                                        's.supplier_name'
                                )->where('c.contract_name','LIKE',"%{$request->value}%")
                ->where(function ($query) use ($search){
                  return  $query->where('contract_number', 'LIKE', "%{$search}%")
                  ->orWhere('contract_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
        
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 
            
            break; 

}
              $totalFiltered =  $totalRec ;

          $data = array();
          if (!empty($terms)) {
$x=1;
 
            foreach ($terms as $term) {

$expiryDate = Carbon::parse($term->contract_enddate);
$currentDate = Carbon::now();
$expiryThreshold = $currentDate->copy()->addDays(30);

             $nestedData['id']=$x;
                $nestedData['contract_number']=$term->contract_number;
             
                 $nestedData['contract_name']= $term->contract_name;
                    $nestedData['contract_desc']= 
                   
                 $nestedData['contract_start'] = date('d,M Y',strtotime($term->contract_startdate));
                 $nestedData['contract_end']  =date('d,M Y',strtotime($term->contract_enddate));
                $nestedData['sub_type']  =$term->name;
                $nestedData['supplier']  =$term->supplier_name;

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
}

