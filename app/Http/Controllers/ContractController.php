<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Contract;
use DB;
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



             $nestedData['id']=$x;
                $nestedData['contract_number']=$term->contract_number;
             
                 $nestedData['contract_name']= $term->contract_name;
                    $nestedData['contract_desc']= 
                   
                 $nestedData['contract_start'] = date('d,M Y',strtotime($term->contract_startdate));
                 $nestedData['contract_end']  =date('d,M Y',strtotime($term->contract_enddate));
                  $nestedData['sub_type']  =$term->name;
                  $nestedData['supplier']  =$term->supplier_name;
                   $nestedData['status']  ="";
                $nestedData['action']= " <a class='btn btn-info btn-sm' id='$term->id' onclick='viewOrder(this.id)'><i class='fa fa-eye'></i> View</a>  ";
              
               
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
       $contract->subscription_type=$request->sub_type;
       $contract->supplier_id=$request->supplier;
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
