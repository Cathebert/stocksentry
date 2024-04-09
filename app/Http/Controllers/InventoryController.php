<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemTemp;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\Inventory;
use App\Models\Issue;
use App\Models\IssueDetails;
use App\Models\User;
use App\Models\ItemOrder;
use App\Models\Setting;
use App\Models\ConsumptionDetail;
use App\Services\UpdateInventoryService;
use App\Services\AcceptIssueService;
use App\Services\BinCardService;
use App\Services\LogActivityService;
use App\Models\BinCard;
use App\Models\ReceivedItem;
use Carbon\Carbon;
use App\Notifications\PendingIssueNotification;
use App\Notifications\ApprovedIssueNotification;
use Validator;
use DB;


class InventoryController extends Controller
{
    //

   
    
    public function Fetch(){
        $data=Item::select('id','item_name')->limit(5)->get();
     
        return response()->json([
           
            'fetched'=>$data,
           
    ]);

    }

    public function showRequisition(){
        $data['laboratories']=Laboratory::get();
        $data['badges']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])->count();
        $data['approved']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','approved']])->count();
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
        
        return view('inventory.issues_tab.requisition',$data);
    }
    
       public function showIssue(){
        $data['laboratories']=Laboratory::get();
        $data['badges']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])->count();
        $data['approved']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','approved']])->count();
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
                 $issue=Issue::select('id')->latest('id')->first();

  $settings=Setting::find(1);
  if( $issue==NULL){
          $data['issue']=$settings->issue_prefix.'0001';
         }
         else{
          $number=str_pad($issue->id+1, 4, '0', STR_PAD_LEFT);
        
          $data['issue']=$settings->issue_prefix.''.$number;
         }
        return view('inventory.issues_tab.issue',$data);
    }
public function showApprovals(){
        return view('inventory.modal.approvals');
    }
public function showReceivedIssued(){
        $data['laboratories']=Laboratory::get();
        $data['badges']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])->count();
        $data['approved']=Issue::where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','approved']])->count();
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
        return view('inventory.issues_tab.issue_to',$data);
    }
public function getReceivedIssues(Request $request){
   $date=date('Y-m-d');

         $columns = array(
            0 =>'id',
            1=>'issue_date',
            2=> 'issue_from',
            3=>'issue_to',
            4=>'action',
           
        ); 
   $totalData = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','l.lab_name','t.issuing_date')
          ->where('t.to_lab_id','=',auth()->user()->laboratory_id)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','t.from_lab_id','t.to_lab_id','t.issuing_date','t.approve_status','t.issued_by')
          ->where('t.to_lab_id','=',auth()->user()->laboratory_id)
                ->where(function ($query) use ($search){
                  return  $query->where('t.siv_number', 'LIKE', "%{$search}%")
                  ->orWhere('t.from_lab_id','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.issuing_date','desc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;

            foreach ($terms as $term) {

$download=route('download',['id'=>$term->siv_number,'action'=>'download','type'=>'issue']);
$print=route('download',['id'=>$term->siv_number,'action'=>'print','type'=>'issue']);

             
                $nestedData['id']="<a href='#'>".$term->siv_number."</a>";
             
                 $nestedData['issue_date']= date('d, M Y',strtotime($term->issuing_date));
                $lab=Laboratory::select('id','lab_name')->where('id',$term->from_lab_id)->first();
                    $nestedData['issue_from']= $lab->lab_name;
                    $lab=Laboratory::select('id','lab_name')->where('id',$term->to_lab_id)->first();
                 $nestedData['issue_to'] = $lab->lab_name;
               if($term->approve_status=="received"){
$nestedData['action']= "<a type='button'><i class='fa fa-check'> Received</i></a> | <a id='$term->id' class='btn btn-info' onclick='viewIssue(this.id)'<i class='fa fa-eye'</i> view</a> |";
                   $nestedData['action'].="<ul ><li class='nav-item dropdown' >
         <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fa fa-file'></i>   Export To
        </a>
         <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
          <a class='dropdown-item' href='$download'><i class='fa fa-file-pdf'></i> PDF</a>
          <a class='dropdown-item' href='#' hidden>Excel</a>
          <div class='dropdown-divider'></div>
          <a class='dropdown-item' href='$print'><i class='fa fa-print'></i> Print</a>
        </div>
      </li>
    
    </ul>";
               }
               else{
                $nestedData['action']= "<a id='$term->id' onclick='ReceiveIssue(this.id)'><i class='btn btn-success'> Accept</i></a> | <a id='$term->id' class='btn btn-info' onclick='viewIssue(this.id)'<i class='fa fa-eye'</i> view</a> ";
                   $nestedData['action'].="<ul class='navbar-nav'><li class='nav-item dropdown' >
         <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fa fa-file'></i>   Export To
        </a>
         <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
          <a class='dropdown-item' href='$download'>PDF</a>
          <a class='dropdown-item' href='#' hidden>Excel</a>
          <div class='dropdown-divider'></div>
          <a class='dropdown-item' href='$print'>Print</a>
        </div>
      </li>
    
    </ul>";
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
public function approveIssue(Request $request){
 


         $columns = array(
            0=>'check',
            1=>'id',
            2=>'issue_date',
            3=> 'issue_to',
            4=>'issue_by',
            5=>'status',
           6=>'action',
           
        ); 
   $totalData = Issue::select('id','siv_number','to_lab_id','issuing_date','issued_by','approve_status')
         ->where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =Issue::select('id','siv_number','to_lab_id','to_section_id','issuing_date','issued_by','approve_status')
               ->where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','pending']])

                ->where(function ($query) use ($search){
                  return  $query->where('siv_number', 'LIKE', "%{$search}%")
                  ->orWhere('to_lab_id','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->distinct()
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 

          $totalFiltered =  $totalRec ;
//  0 => 'id',
        
          $data = array();
          if (!empty($terms)) {
$x=1;
 

            foreach ($terms as $term) {
 $lab=Laboratory::select('id','lab_name')->where('id',$term->to_lab_id)->first();

if(!$lab){
    $lab_name='';
}
else{
   
    $lab_name=$lab->lab_name;
}

               $nestedData['check']="<input type='checkbox' id='$term->id' />";
                $nestedData['id']="<a>".$term->siv_number."</a>";
             
                 $nestedData['issue_date']= date('d, M Y',strtotime($term->issuing_date));
                
                       $user=User::select('id','name','last_name')->where('id',$term->issued_by)->first();
                    $nestedData['issue_by']= $user->name.' '.$user->last_name;
                   
                 $nestedData['issue_to'] = $lab_name ;
                 $nestedData['status']  =$term->approve_status;
                 $nestedData['action']=$term->approve_status=='approved' ? "":"<a class='btn btn-success btn-sm' id='$term->id' onclick='ApproveItem(this.id)'><i class='fa fa-check'></i> Approve</a> |<a class='btn btn-danger btn-sm'   id='$term->id' onclick='VoidItem(this.id)'><i class='fa fa-times'></i> Void</a> |" ;
                $nestedData['action'].= " <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewItem(this.id)'><i class='fa fa-eye'></i> View</a>  ";
              
               
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
public function showAllInventory(){
  $data['labs']=Laboratory::select('id','lab_name')->get();
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('inventory.all_inventory',$data);
}
public function loadAllInventory(Request $request){
 
         $columns = array(
            0=>'id',
            1=>'code',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'unit',
           6=>'available',
           
        ); 
   $totalData = DB::table('items as items')
                ->join('inventories as inv','inv.item_id','=','items.id')
              
            ->groupBy('items.id')
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as items')
                    ->join('inventories as inv','inv.item_id','=','items.id')
                    ->select('items.id as id','items.code','items.brand','items.unit_issue','inv.batch_number','items.item_name',  DB::raw("(sum(inv.quantity)) as quantity"))

                ->where(function ($query) use ($search){
                  return  $query->where('items.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('inv.batch_number','LIKE',"%{$search}%")
                  ->orWhere('items.code','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('inv.id','asc')
            ->groupBy('items.id')
            ->get(); 

          $totalFiltered = count($terms)  ;
   
//  0 => 'id',
        
          $data = array();
          if (!empty($terms)) {
$x=1;
 
    
            foreach ($terms as $term) {



               $nestedData['id']=$term->id;
                $nestedData['code']="<a>".$term->code."</a>";
             
                 $nestedData['batch_number']= $term->batch_number;
                    $nestedData['brand']= $term->brand;
                       //$user=User::select('id','name','last_name')->where('id',$term->issued_by)->first();
//$nestedData['issue_by']= $user->name.' '.$user->last_name;
                    //$lab=Laboratory::select('id','lab_name')->where('id',$term->to_lab_id)->first();
                 $nestedData['name'] = $term->item_name;
                 $nestedData['unit']  =$term->unit_issue;
                 $nestedData['available']=$term->quantity;
               // $nestedData['action'].= " <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewItem(this.id)'><i class='fa fa-eye'></i> View</a>  ";
              
               
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
public function showMore(Request $request){
  $inventory=DB::table('inventories as inv')
  ->join('laboratories as lab','lab.id','=','inv.lab_id')
  ->where('inv.item_id',$request->name)
  ->select('inv.item_id','lab.id','lab.lab_name', DB::raw("(sum(inv.quantity)) as quantity"))->groupBy('lab.lab_name')->get();
  return response()->json([
'name'=>$request->name,
'data'=>$inventory,
  ]);
}
public function showForecasting(Request $request){
   $order=ItemOrder::select('id')->latest('id')->first();

  $settings=Setting::find(1);
  if($order==NULL){
          $data['order']=$settings->order_prefix.'0001';
         }
         else{
          $number=str_pad($order->id+1, 4, '0', STR_PAD_LEFT);
        
          $data['order']=$settings->order_prefix.''.$number;
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
  return view('inventory.inventory_tab.stock_forecasting',$data);
}
public function showLabInventoyForm(Request $request){
  //dd($request);
$lab=Laboratory::where('id',$request->id)->select("lab_name")->first();
$item=Item::where('id',$request->item)->select("item_name")->first();
$data['lab_name']=$lab->lab_name;
$data['item']=$item->item_name;
$data['consumed']=DB::table('consumption_details as cons')
                    ->join('inventories as inv','inv.item_id','=','cons.item_id')
->where([['cons.lab_id','=',$request->id],['cons.item_id','=',$request->item]])->get();
$data['orders_consolidated']=DB::table('item_orders')->where([['lab_id','=',$request->id],['is_consolidated','=','yes']])->select('id','order_number')->count();
$data['orders_pending']=DB::table('item_orders')->where([['lab_id','=',$request->id],['is_delivered','=','pending']])->select('id','order_number')->count();
$data['orders_received']=DB::table('item_orders')->where([['lab_id','=',$request->id],['is_delivered','=','yes']])->select('id','order_number')->count();
$data['issued_out']=DB::table('issues')->where('from_lab_id',$request->id)->count();
$data['issued_received']=DB::table('issues')->where('to_lab_id',$request->id)->count();
  return view('inventory.modal.lab_inventory',$data);
}
public function showInventoryByLocation(Request $request){
  $columns = array(
            0=>'id',
            1=>'code',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'unit',
           6=>'available',
           
        ); 
   $totalData = DB::table('items as items')
                    ->join('inventories as inv','inv.item_id','=','items.id')
                   ->where('inv.lab_id',$request->id)
                      ->groupBy('items.item_name')
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as items')
                    ->join('inventories as inv','inv.item_id','=','items.id')
                    ->select('items.id as id','items.code','items.brand','items.unit_issue','inv.batch_number','items.item_name',  DB::raw("(sum(inv.quantity)) as quantity"))
                  ->where('inv.lab_id',$request->id)
                ->where(function ($query) use ($search){
                  return  $query->where('items.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('inv.batch_number','LIKE',"%{$search}%")
                  ->orWhere('items.code','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
          
            ->limit($limit)
            ->orderBy('inv.id','asc')
            ->groupBy('inv.item_id')
            ->get(); 

          $totalFiltered =  count($terms) ;
//  0 => 'id',
        
          $data = array();
          if (!empty($terms)) {
$x=1;
 
    
            foreach ($terms as $term) {



               $nestedData['id']=$x;
                $nestedData['code']="<a>".$term->code."</a>";
             
                 $nestedData['batch_number']= $term->batch_number;
                    $nestedData['brand']= $term->brand;
                       //$user=User::select('id','name','last_name')->where('id',$term->issued_by)->first();
//$nestedData['issue_by']= $user->name.' '.$user->last_name;
                    //$lab=Laboratory::select('id','lab_name')->where('id',$term->to_lab_id)->first();
                 $nestedData['name'] = $term->item_name;
                 $nestedData['unit']  =$term->unit_issue;
                 $nestedData['available']=$term->quantity;
               // $nestedData['action'].= " <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewItem(this.id)'><i class='fa fa-eye'></i> View</a>  ";
              
               
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
    public function showInventory(){
        $date=date('Y-m-d');
  $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
       
         
      $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
          ->groupBy('s.id')
             ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.expiry_date','>',$date]])
          ->paginate(15);
      $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('inventory.inventory_tab.bincard',$data);
    }
public function showStockTake(Request $request){
    $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
         
   $data['count']=Inventory::where('lab_id',auth()->user()->laboratory_id)->count();
      $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
$data['labs']=Laboratory::get();
        return view('inventory.inventory_tab.stock_take',$data);
}
public function showStockAdjustment(Request $request){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  
        return view('inventory.inventory_tab.stock_adjustment',$data);
}
public function showStockDisposal(Request $request){
    $data['laboratories']=Laboratory::select('id','lab_name')->get();
       $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('inventory.inventory_tab.stock_disposal',$data);
}
    public function showConsumptionForm(Request $request){
        $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
             ->where('t.lab_id','=',auth()->user()->laboratory_id)->get();
         
      $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('inventory.inventory_tab.update_consumption',$data);
    }
 
    public function getItemDetails(Request $request){
        $items= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.cost','t.batch_number','s.item_image','s.unit_issue','s.item_name','t.expiry_date')
          ->where('t.id',$request->id)->first();

          return response()->json([
'item'=>$items,
          ]);
   
    }
    public function getIssues(Request $request){
  
 $date=date('Y-m-d');

         $columns = array(
            0 =>'id',
            1=>'code',
            2=> 'name',
            3=>'available',
            4=>'quantity',
            5=>'brand',
            6=>'status',
            7=>'batch_number'
        ); 
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
          ->where('t.lab_id','=',auth()->user()->laboratory_id)
            ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.cost','t.batch_number','s.item_name','t.expiry_date')
         ->where('t.lab_id','=',auth()->user()->laboratory_id)
          ->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 

            foreach ($terms as $term) {


               $nestedData['item_id']=$term->id;
                $nestedData['id']="<input type='checkbox' id='$term->id' name='selected_check' onclick='AddIdToArray(this.id)' disabled/>";
                $nestedData['code']=$term->code;
              $nestedData['batch_number']=$term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['available']= $term->quantity;
                 $nestedData['quantity'] = "<input type='number'  size='5' id='q_$term->id' min='1' class='form-control' placeholder='Enter Quantity' name='$term->id' onchange='getText(this.id,this.name)' /><span id='l_$term->id' hidden>Checking...</span>";
                $nestedData['brand']= $term->brand;
                $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                if( $diff_in_days>=1 && $diff_in_days <30){
                    $nestedData['status']="<span class='text-danger'>  expiring (".$diff_in_days. " day(s)) </span>";
                }
  else if( $diff_in_days>=30 && $diff_in_days <60){
                    $nestedData['status']="<span class='text-warning'>expiring (".$diff_in_days. " days)</span>";
                }
      elseif( $diff_in_days>=60 && $diff_in_days <90){
                    $nestedData['status']="<span class='text-success'>expiring (".$diff_in_days." days)</span>";
                }
                  else{
                    $nestedData['status']="<span class='text-success'>".$diff_in_days." days  remaining</span>";
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
public function getSelectedItems(Request $request){
$d=array();
for($j=0; $j<count($request->items);$j++){
 $f=explode("_",$request->items[$j]);
 $ids[]=$f[0];
$requested[]=$f[1];
}
     $data = array();
for($i=0; $i<count($ids); $i++ ){

  $dat= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','t.batch_number','t.quantity','s.code','t.item_id','s.brand','s.item_description','s.unit_issue','t.cost','s.item_name','t.expiry_date')
             ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.id','=',$ids[$i]]])->first();

$data[]=$dat;


}
return response()->json([
  'data'=>$data,
  'quantity'=>$requested,
]);
}

public function checkQuantity(Request $request){
  $quantity=Inventory::where('id',$request->id)->select('quantity')->first();
  if($quantity->quantity<$request->quantity){
  return response()->json([
    'error'=>true,
    'message'=>'The entered quantity  is more than available quantity'
  ]);
}
  if($quantity->quantity==0){
   
  return response()->json([
   'error'=>true,
    'message'=>'The entered quantity  cannot be dispatched'
  ]);
  }
  return response()->json([
   'error'=>false,
    'message'=>'done'
  ]);
}
public function saveIssued(Request $request){
//dd($request);
  $ids=array();
  $quantities=array();
  $data=array();
for($i= 0; $i<count($request->quantity); $i++){
$items= explode('_',$request->quantity[$i]);

if($items[1]!=""){
  $ids[]= $items[0];
$quantities[]= $items[1];
}

}
try{
    if($request->form_data['to_section_id']==NULL){
        $to_section=NULL;
    }
    else{
        $to_section=$request->form_data['to_section_id'];
    }
    if (array_key_exists("from_section_id",$request->form_data)){
    if($request->form_data['from_section_id']==NULL){
        $from_section=NULL;
    }
    else{
        $from_section=$request->form_data['from_section_id'];
    }
}
else{
     $from_section=NULL; 
}
    
DB::beginTransaction();
$issue=new Issue();
$issue->siv_number=$request->form_data['siv'];
$issue->from_lab_id	=$request->form_data['from_lab_id'];
$issue->to_lab_id=$request->form_data['to_lab_id'];
$issue->from_section_id=$from_section;
$issue->to_section_id= $to_section;
$issue->issued_by=auth()->user()->id;
$issue->received_by=$request->form_data['recieved_by'];
$issue->approved_by=NULL;
$issue->issuing_date=$request->form_data['issue_date'];
$issue->created_at=now();
$issue->updated_at=NULL;
$issue->approve_status="pending";
$issue->save();
$issue_id=$issue->id;
for($i= 0; $i<count($quantities); $i++){
$items= explode('_',$request->quantity[$i]);
$issueDetails=new IssueDetails();
$issueDetails->issue_id=$issue_id;
$issueDetails->siv_number=$request->form_data['siv'];
$issueDetails->item_id=$ids[$i];
$issueDetails->quantity=$quantities[$i];
$issueDetails->created_at=now();
$issueDetails->updated_at=NULL;
$issueDetails->save();
 $number=str_pad($issue_id, 4, '0', STR_PAD_LEFT);
}

$approvers=User::where([['authority','=',2],['laboratory_id','=',auth()->user()->laboratory_id]])->get();
$issuer=auth()->user()->name.' '.auth()->user()->last_name;
$stock_tranfer_no=$request->form_data['siv'];
$issued_to=Laboratory::where('id',$request->form_data['to_lab_id'])->select('lab_name')->first();
foreach ($approvers as $user) {
   $user->notify(new PendingIssueNotification($issuer,$issued_to->lab_name,$stock_tranfer_no));
}
DB::commit();

$test="Request to Issue has been made pending Approval";
 return response([
'message'=> $test,
'id'=>'ISS'.$number,
'error'=>false,

  ]);
}
catch(Exception $e){
  DB::rollback();
$test="Failed";
  return response([
'message'=> $test,
'error'=>true,
  ]);
}


}
//save approved issues 
public function saveApprovedIssue(Request $request){
   // dd($request);
  $data['issue'] = Issue::with('detailIssue')->where('id',$request->id)->get();
  try{
$updateInventory=new UpdateInventoryService();
$status=$updateInventory->UpdateInventory($data);

if($status==1){
    $issurer=Issue::where('id',$request->id)->select('siv_number','issued_by')->first();
    $user=User::where('id',$issurer->issued_by)->first();
    $user->notify(new ApprovedIssueNotification($issurer->siv_number));
 LogActivityService::saveToLog('Issue Approval',''.auth()->user()->name.' '.auth()->user()->last_name.' approved issue number'.$request->form_data['siv'],'low');
 return response()->json([
    'message'=>config('stocksentry.issue_approved'),
    'error'=>false

  ]);   
}
else{

}

  }
  catch(Exception $e){
return response()->json([
    'message'=>$e.message,
    'error'=>true

  ]);
  }
 // dd($issue);
  

}

// view issued item

public function voidIssue(Request $request){
$issue=Issue::where($request->id)->first();
  Issue::where('id',$request->id)
  ->update([
'approve_status'=>'declined',
'updated_at'=>now()
  ]);
   LogActivityService::saveToLog('Issue Approval',''.auth()->user()->name.' '.auth()->user()->last_name.' cancelled issue number'.$issue->siv_number,'low');
  return response()->json([
'message'=>"issue has been made void ".$request->id
  ]);
}

public function showApprovedIssue(){
    return view('inventory.modal.approved');
}
/**
 * approved issues
 */

public function IssuesApproved(Request $request){
  
   $columns = array(
            0=>'id',
            1=>'issued_date',
            2=> 'issued_to',
            3=>'issued_by',
            4=>'approved_by',
            5=>'receipt',
            6=>'action',
           
        ); 
   $totalData = Issue::select('id','siv_number','to_lab_id','issuing_date','issued_by','approved_by','issue_document')
          ->where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','approved']])
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =Issue::select('id','siv_number','to_lab_id','issuing_date','issued_by','approved_by','issue_document')
               ->where([['from_lab_id','=',auth()->user()->laboratory_id],['approve_status','=','approved']])

                ->where(function ($query) use ($search){
                  return  $query->where('siv_number', 'LIKE', "%{$search}%")
                  ->orWhere('to_lab_id','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->distinct()
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 

          $totalFiltered =  $totalRec ;
//  0 => 'id',
         
          $data = array();
          if (!empty($terms)) {
$x=1;
 
  
       $c=url('/').'/assets/icon/pdf.png';    
            foreach ($terms as $term) {

$download=route('download',['id'=>$term->siv_number,'action'=>'download','type'=>'issue']);
$print=route('download',['id'=>$term->siv_number,'action'=>'print','type'=>'issue']);

            
                $nestedData['id']="<a>".$term->siv_number."</a>";
             
                 $nestedData['issued_date']= date('d, M Y',strtotime($term->issuing_date));
                     $lab=Laboratory::select('id','lab_name')->where('id',$term->to_lab_id)->first();
                  $nestedData['issued_to'] = $lab->lab_name;
                       $user=User::select('id','name','last_name')->where('id',$term->issued_by)->first();
                    $nestedData['issued_by']= $user->name.' '.$user->last_name;
                
                       $approver=User::select('id','name','last_name')->where('id',$term->approved_by)->first();
                     $nestedData['approved_by']= $approver->name.' '.$approver->last_name;
               
                 $nestedData['receipt']  ="<a href='#' id='$term->id' onclick='ViewItem(this.id)' ><img height=20 width=20 src ='$c'/><u>View</u></a>";
                 $nestedData['action']="<a class='btn btn-success btn-sm' href='$download'><i class='fa fa-download'></i></a> |<a href='$print' class='btn btn-secondary btn-sm'   id='$term->id' ><i class='fa fa-print'></i></a> |" ;
                $nestedData['action'].= " <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewItem(this.id)'><i class='fa fa-eye'></i></a>  ";
              
               
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
public function AcceptIssue(Request $request){
   $data['issue'] = Issue::with('detailIssue')->where('id',$request->id)->get();

   try{
$issueService=new AcceptIssueService();
$status=$issueService->acceptIssue($data);
if($status==0){
  LogActivityService::saveToLog('Issue Accept',''.auth()->user()->name.' '.auth()->user()->last_name.' accepted issue number '.$data['issue'][0]->siv_number,'low');
    return response()->json([
'message'=>config('stocksentry.issue_accept'),
  ]);
}

   }
   catch(Exception $e){
return response()->json([
'message'=>$e.message,
  ]);
   }
  
}
public function viewIssue(Request $request){
    
  $issues=Issue::select('siv_number','approve_status','issued_by','received_by','approved_by','from_lab_id','to_lab_id','issuing_date')->where('id',$request->id)->first();
  //dd($issues);

$from_lab=Laboratory::where('id',$issues->from_lab_id)->select('lab_name')->first();
$to_lab= Laboratory::where('id',$issues->to_lab_id)->select('lab_name')->first();
$data['from_lab']= $from_lab->lab_name;
$data['to_lab']= $to_lab->lab_name;
$data['issuing_date']=date('d, M Y',strtotime($issues->issuing_date));
$data['siv']=$issues->siv_number;
$data['status']=$issues->approve_status;

if($issues->issued_by!=NULL){
   $user=User::where('id',$issues->issued_by)->select('name','last_name','signature')->first();
   
    $data['issued_by']=$user->name.' '.$user->last_name;
$data['signature']=$user->signature;

}
else{
      $data['issued_by']=NULL; 
     $data['signature']=NULL;
}
if($issues->approved_by!=NULL){
    $approver=User::where('id',$issues->approved_by)->select('name','last_name','signature')->first();
    $data['approver']=$approver->name." ".$approver->last_name;
    $data['approver_sign']=$approver->signature;
}
else{
     $data['approver']=NULL; 
      $data['approver_sign']=NULL;
}
if($issues->received_by!=NULL){
 $receiver=User::where('id',$issues->received_by)->select('name','last_name','signature')->first();
    $data['receiver']=$receiver->name." ".$receiver->last_name;
    $data['receiver_sign']=$receiver->signature;
} 
else{
   $data['receiver']=NULL; 
    $data['receiver_sign']=NULL;
}  

/* $data['print_data']= DB::table('items as t') 
              ->join('inventories AS i', 'i.item_id', '=', 't.id')
              ->select('i.id as id','t.item_name','t.unit_issue','i.quantity','i.batch_number','i.cost')
              ->where('i.grn_number',$request->id)
              // ->groupBy('t.item_name')
              ->get(); */
  $data['print_data']=DB::table('items as itm')
      ->join('inventories as inv','inv.item_id','=','itm.id')
      ->join('issue_details as d','d.item_id','=','inv.id')
      ->select('itm.item_name','itm.unit_issue','inv.cost','d.quantity', 'inv.batch_number','inv.item_id')
     ->where([['d.issue_id','=',$request->id]])
      ->groupBy('itm.item_name')
      ->get();


  return view('inventory.modal.issue_modal',$data);
}
public function binCard(Request $request){
      $date=date('Y-m-d');
   
         $columns = array(
            0=>'id',
            1=>'date',
            2=>'quantity_in',
            3=>'batch_number',
            4=> 'description',
            5=>'supplier',
            6=>'cost',
            7=>'expiry_date',
            8=>'quantity_out',
            9=>'balance',
           
        ); 
        if($request->id){
            $invent=BinCard::where([['inventory_id','=',$request->id],['lab_id','=',auth()->user()->laboratory_id]])->select('item_id')->first();
      $totalData = Inventory::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->count();
     
      $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');
   
$terms=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->get();

        }
        else{
          $totalData = BinCard::where([['inventory_id','=',1],['lab_id','=',auth()->user()->laboratory_id]])->count();
    $totalRec = $totalData;
$terms=BinCard::where([['inventory_id','=',1],['lab_id','=',auth()->user()->laboratory_id]])->get();
          }
          $totalFiltered =  $totalRec ;
//  0 => 'id',
         
          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
 $total=0;
 $stocktaken=1;
 $consumed=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','consumed']])->sum('quantity');;
$item_name="";
 $issued_to=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','issued_out']])->sum('quantity');
  $order_sent=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','order_sent']])->sum('quantity');
 $stocktaken_date=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','stocktaken']])->select('date')->latest('id')->first();

$image_name="";
//dd($terms);
       $c=url('/').'/assets/icon/pdf.png'; 
      //dd($terms);

        $total_balance = Inventory::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->sum('quantity');
    
     //
        foreach ($terms as $term) {

         $inventory=Inventory::where('id',$term->inventory_id)->select('cost','quantity','expiry_date','recieved_id')->first();
        $received=ReceivedItem::where('id',$inventory->recieved_id)->select('supplier_id')->first();
        $supplier=Supplier::where('id',$received->supplier_id)->select('supplier_name')->first();
        $items=Item::where('id',$term->item_id)->select('item_name','item_image')->first();  

             $nestedData['id']=$x;
             $nestedData['date']= $term->date; 
             
     switch($term->transaction_type){
            case "supplier":
                    $nestedData['quantity_in'] = $term->quantity;
                    $nestedData['quantity_out']="";
                    $nestedData['balance']= $term->balance;
                break;
            case "issued_out":
                    $nestedData['quantity_in'] = '';
                    $nestedData['quantity_out']=$term->quantity;
                    $nestedData['balance']= $term->balance; 

                break;

            case "consumed":
                    $nestedData['quantity_in'] = '';
                    $nestedData['quantity_out']=$term->quantity;
                    $nestedData['balance']= $term->balance;

                break;
            case "adjusted":
                $nestedData['quantity_in'] = $term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;

                break;

            case "issue_in":
                $nestedData['quantity_in'] =$term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;

                break;
            case "stocktaken":
                $nestedData['quantity_in'] =$term->quantity;
                $nestedData['quantity_out']='';
                $nestedData['balance']= $term->balance;
                $stocktaken=0;
             break;

            case "order_sent":
                $nestedData['quantity_in'] = '';
                $nestedData['quantity_out']=$term->quantity;
                $nestedData['balance']= $term->balance;
             break;

             case "order_received":
                $nestedData['quantity_in'] = $term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;
                break;
                
                
            case "disposed":
                $nestedData['quantity_in'] = '';
                $nestedData['quantity_out']=$term->quantity;
                $nestedData['balance']= '';
                break;
             }
    if($stocktaken_date!=NULL){
    if($term->date < $stocktaken_date->date){
    $stocktaken=1;    
    }
    else{
         $stocktaken=0;  
    }
}
            $nestedData['batch_number']= $term->batch_number; 
            $nestedData['description']= $term->description; 
            $nestedData['supplier']= $supplier->supplier_name; 
            $nestedData['cost']  =$inventory->cost;
            $nestedData['expiry_date']=date('d M Y',strtotime($inventory->expiry_date));
                
              
               $t=$term->balance;
               $total= $total_balance;
              
               $image_name=$items->item_image;
               $item_name=$items->item_name;
             
                   $x++;
                $data[] = $nestedData;
           
      }
    }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "consumed"=>$consumed,
        "item_name"=>$item_name,
        "out"=>$issued_to+$order_sent,
        'stock_taken'=>$stocktaken,
        'image'=>$image_name,
    );

      echo json_encode($json_data);

}
public function searchItem(Request $request){
    if($request->search=="reload"){
 $inventory = DB::table('items as item')
                          ->join('inventories as inve','inve.item_id','=','item.id')
                          ->where('inve.lab_id',auth()->user()->laboratory_id)
                          ->select('inve.id','item.item_name')->distinct()->get();
                           

                              $data = array();
                              foreach ($inventory as $inv) {
$n['id']=$inv->id;
$n['item_name']=$inv->item_name;
$data[]=$n;
                              }
                            return response()->json([
'data'=>$data
                            ]);
    }
    else{
  $inventory = DB::table('items as item')
                          ->join('inventories as inve','inve.item_id','=','item.id')
                          ->where('inve.lab_id',auth()->user()->laboratory_id)
                          ->select('inve.id','item.item_name')
                            ->where('item.item_name','LIKE',"%{$request->search}%")->orderBy('item.item_name')->get();
//dd($inventory);
                            $name="";
                              $data = array();
                              foreach ($inventory as $inv) {
                if($name!=$inv->item_name){
$n['id']=$inv->id;
$n['item_name']=$inv->item_name;
$data[]=$n;
}
 $name= $inv->item_name;
                              }
                                                        return response()->json([
'data'=>$data
                            ]);
                        }
}
public function binCardFilter(Request $request){
   $date=date('Y-m-d');
   $start_date = date('Y-m-d 00:00:00', strtotime($request->start));
$end_date = date('Y-m-d 23:59:59', strtotime($request->end));
              $columns = array(
            0=>'id',
            1=>'date',
            2=>'quantity_in',
            3=>'batch_number',
            4=> 'description',
            5=>'supplier',
            6=>'cost',
            7=>'expiry_date',
            8=>'quantity_out',
            9=>'balance',
           
        ); 
      
            $invent=BinCard::where([['inventory_id','=',$request->id],['lab_id','=',auth()->user()->laboratory_id]])->select('item_id')->first();
      $totalData = Inventory::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->count();
     
      $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          //$order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');
   
$terms=BinCard::whereBetween(DB::raw('DATE(date)'), array( $start_date, $end_date))
->where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->get();

        
       
          $totalFiltered =  $totalRec ;
//  0 => 'id',
         
          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
 $total=0;
 $stocktaken=1;
 $consumed=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','consumed']])->sum('quantity');;
$item_name="";
 $issued_to=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','issued_out']])->sum('quantity');
 $stocktaken_date=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','stocktaken']])->select('date')->latest('id')->first();

$image_name="";
//dd($terms);
       $c=url('/').'/assets/icon/pdf.png'; 
      //dd($terms);

        $total_balance = Inventory::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->sum('quantity');
    
     //
        foreach ($terms as $term) {

         $inventory=Inventory::where('id',$term->inventory_id)->select('cost','quantity','expiry_date','recieved_id')->first();
        $received=ReceivedItem::where('id',$inventory->recieved_id)->select('supplier_id')->first();
        $supplier=Supplier::where('id',$received->supplier_id)->select('supplier_name')->first();
        $items=Item::where('id',$term->item_id)->select('item_name','item_image')->first();  

             $nestedData['id']=$x;
             $nestedData['date']= $term->date; 
             
     switch($term->transaction_type){
            case "supplier":
                    $nestedData['quantity_in'] = $term->quantity;
                    $nestedData['quantity_out']="";
                    $nestedData['balance']= $term->balance;
                break;
            case "issued_out":
                    $nestedData['quantity_in'] = '';
                    $nestedData['quantity_out']=$term->quantity;
                    $nestedData['balance']= $term->balance; 

                break;

            case "consumed":
                    $nestedData['quantity_in'] = '';
                    $nestedData['quantity_out']=$term->quantity;
                    $nestedData['balance']= $term->balance;

                break;
            case "adjusted":
                $nestedData['quantity_in'] = $term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;

                break;

            case "issue_in":
                $nestedData['quantity_in'] =$term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;

                break;
            case "stocktaken":
                $nestedData['quantity_in'] =$term->quantity;
                $nestedData['quantity_out']='';
                $nestedData['balance']= $term->balance;
                $stocktaken=0;
             break;

            case "order_sent":
                $nestedData['quantity_in'] = '';
                $nestedData['quantity_out']=$term->quantity;
                $nestedData['balance']= $term->balance;
             break;

             case "order_received":
                $nestedData['quantity_in'] = $term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;
                break;
                
                
            case "disposed":
                $nestedData['quantity_in'] = '';
                $nestedData['quantity_out']=$term->quantity;
                $nestedData['balance']= '';
                break;
             }
    if($stocktaken_date!=NULL){
    if($term->date > $stocktaken_date->date){
    $stocktaken=1;    
    }
}
            $nestedData['batch_number']= $term->batch_number; 
            $nestedData['description']= $term->description; 
            $nestedData['supplier']= $supplier->supplier_name; 
            $nestedData['cost']  =$inventory->cost;
            $nestedData['expiry_date']=date('d M Y',strtotime($inventory->expiry_date));
                
              
               $t=$term->balance;
               $total= $total_balance;
              
               $image_name=$items->item_image;
               $item_name=$items->item_name;
             
                   $x++;
                $data[] = $nestedData;
           
      }
    }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "consumed"=>$consumed,
        "item_name"=>$item_name,
        "out"=>$issued_to,
        'stock_taken'=>$stocktaken,
        'image'=>$image_name,
    );

      echo json_encode($json_data);
  
}
public function showDisposalModal(){
    return view('inventory.modal.disposal_modal');
}
public function disposalList(Request $request){

       
 $columns = array(
            0=>'check',
            1=>'code',
            2=> 'item',
            3=>'unit',
            4=>'quantity',
            5=>'expiry',
            6=>'id',
           
           
        ); 
   $totalData = DB::table('inventories as inv')
   ->join('items as i','i.id','=','inv.item_id')
          ->where([['inv.lab_id','=',auth()->user()->laboratory_id]])
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('inventories as inv')
   ->join('items as i','i.id','=','inv.item_id')
   ->select('inv.id as id','i.code','i.item_name','i.unit_issue','inv.batch_number','inv.quantity','inv.expiry_date')
          ->where([['inv.lab_id','=',auth()->user()->laboratory_id]])

                ->where(function ($query) use ($search){
                  return  $query->where('inv.batch_number', 'LIKE', "%{$search}%")
                  ->orWhere('i.code','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
         
            ->limit($limit)
            ->orderBy('id','asc')
            ->get(); 

          $totalFiltered =  $totalRec ;

      
          $data = array();
          if (!empty($terms)) {
$x=1;
 
  
         
            foreach ($terms as $term) {

   

                 $nestedData['id']= $term->id;
                $nestedData['check']="<input type='checkbox' id='$term->id' class='checkboxall' name='selected_check' value='$term->id'  onclick='selectItem(this.value)' />";
             
                 $nestedData['code']= $term->code;
                   
                  $nestedData['batch'] = $term->batch_number;
                       
                    $nestedData['item']= $term->item_name;
                
                     
                     $nestedData['unit']= $term->unit_issue;
               
                 $nestedData['quantity']  =$term->quantity;
                 $nestedData['expiry']=$term->expiry_date;
               
              
               
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
public function selectedForDisposal(Request $request){

  
             $columns = array(
            0=>'id',
            1=>'item',
            2=> 'code',
            3=>'unit',
            4=>'quantity',
            5=>'reason',
            6=>'batch',
             7=>'catalog',
             8=>'available'
           
           
        ); 
                           $disposals=array();
        for($i=0;$i<count($request->selected);$i++){
      // $consumption[]=ConsumptionDetail::where('item_id',$request->items[$i])->avg('consumed_quantity');
        $terms=DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')  
    
              ->select('t.id as id','s.code','s.catalog_number','s.item_name','t.grn_number','t.batch_number','s.unit_issue','t.cost','t.quantity')
             ->where([['t.id','=',$request->selected[$i]]])
             ->groupBy('s.item_name')
           // ->limit($limit)
            //->orderBy('s.id','asc')
            ->get(); 

       $disposals[]=$terms;
        }
   



            $totalData =  DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id') 
              ->join('consumption_details as d','d.item_id','=','t.id')    
             ->where([['t.lab_id','=',auth()->user()->laboratory_id]])->count();

            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

         // $limit = $request->input('length');
        // $start = $request->input('start');
          //$order = $columns[$request->input('order.0.column')];
          //$dir = $request->input('order.0.dir');

     

          $totalFiltered =  $totalRec ;
//  0 => 'id',
         
          $data = array();
          $d=array();
          if (!empty($disposals)) {
$x=1;
 
 
           
            for ($y=0;$y<count($disposals);$y++ ) {

for($n=0;$n<count($disposals[$y]);$n++){
$item_id=$disposals[$y][$n]->id;
                 $nestedData['id']=$x;
                 $nestedData['code'] = $disposals[$y][$n]->code;
                      $nestedData['catalog'] = $disposals[$y][$n]->catalog_number; 
                    $nestedData['item'] =  $disposals[$y][$n]->item_name;
                    $nestedData['unit'] =  $disposals[$y][$n]->unit_issue;
                    $nestedData['batch']= $disposals[$y][$n]->batch_number;
         $nestedData['available']='<strong>'. $disposals[$y][$n]->quantity.'</strong>';
                  $nestedData['quantity']  = "<input type='number' min='1' class='form-control' id='$item_id' name='ordered' size='3'   oninput='getQuantity(this.id,this.value)'/>";  
                     $nestedData['reason'] ="<select class='form-control form-control-sm' id='q_$item_id' name='$item_id' onchange='getReason(this.name,this.value)'>
  <option value='damaged'selected>Damaged</option>
   <option value='expired'>Expired</option>
      <option value='donated'>Donated</option>
</select>" ;       
                   $x++;
                $data[] = $nestedData;
               

           }
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
public function searchIssueByNumber(Request $request){
$date=date('Y-m-d');

         $columns = array(
            0 =>'id',
            1=>'issue_date',
            2=> 'issue_from',
            3=>'issue_to',
            4=>'action',
           
        ); 
   $totalData = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','l.lab_name','t.issuing_date')
          ->where('t.to_lab_id','=',auth()->user()->laboratory_id)
          ->where('t.siv_number',$request->siv)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','t.from_lab_id','t.to_lab_id','t.issuing_date','t.approve_status','t.issued_by')
          ->where('t.to_lab_id','=',auth()->user()->laboratory_id)
           ->where('t.siv_number', 'LIKE', "%{$request->siv}%")
                ->where(function ($query) use ($search){
                  return  $query->where('t.siv_number', 'LIKE', "%{$search}%")
                  ->orWhere('t.from_lab_id','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.issuing_date','desc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;

            foreach ($terms as $term) {

$download=route('download',['id'=>$term->siv_number,'action'=>'download','type'=>'issue']);
$print=route('download',['id'=>$term->siv_number,'action'=>'print','type'=>'issue']);

             
                $nestedData['id']="<a href='#'>".$term->siv_number."</a>";
             
                 $nestedData['issue_date']= date('d, M Y',strtotime($term->issuing_date));
                $lab=Laboratory::select('id','lab_name')->where('id',$term->from_lab_id)->first();
                    $nestedData['issue_from']= $lab->lab_name;
                    $lab=Laboratory::select('id','lab_name')->where('id',$term->to_lab_id)->first();
                 $nestedData['issue_to'] = $lab->lab_name;
               if($term->approve_status=="received"){
$nestedData['action']= "<a type='button'><i class='fa fa-check'> Received</i></a> | <a id='$term->id' class='btn btn-info' onclick='viewIssue(this.id)'<i class='fa fa-eye'</i> view</a> |";
                   $nestedData['action'].="<ul ><li class='nav-item dropdown' >
         <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fa fa-file'></i>   Export To
        </a>
         <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
          <a class='dropdown-item' href='$download'><i class='fa fa-file-pdf'></i> PDF</a>
          <a class='dropdown-item' href='#' hidden>Excel</a>
          <div class='dropdown-divider'></div>
          <a class='dropdown-item' href='$print'><i class='fa fa-print'></i> Print</a>
        </div>
      </li>
    
    </ul>";
               }
               else{
                $nestedData['action']= "<a id='$term->id' onclick='ReceiveIssue(this.id)'><i class='btn btn-success'> Accept</i></a> | <a id='$term->id' class='btn btn-info' onclick='viewIssue(this.id)'<i class='fa fa-eye'</i> view</a> ";
                   $nestedData['action'].="<ul class='navbar-nav'><li class='nav-item dropdown' >
         <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fa fa-file'></i>   Export To
        </a>
         <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
          <a class='dropdown-item' href='$download'>PDF</a>
          <a class='dropdown-item' href='#' hidden>Excel</a>
          <div class='dropdown-divider'></div>
          <a class='dropdown-item' href='$print'>Print</a>
        </div>
      </li>
    
    </ul>";
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
public function searchIssueByDateRange(Request $request){
    
   $start=Carbon::createFromFormat('Y-m-d', $request->start_date);
$end=Carbon::createFromFormat('Y-m-d',$request->end_date);
  
       $columns = array(
            0 =>'id',
            1=>'issue_date',
            2=> 'issue_from',
            3=>'issue_to',
            4=>'action',
           
        ); 
   $totalData = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','l.lab_name','t.issuing_date')
          ->where('t.to_lab_id','=',auth()->user()->laboratory_id)
          ->whereBetween('t.issuing_date',[$start,$end])
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','t.from_lab_id','t.to_lab_id','t.issuing_date','t.approve_status','t.issued_by')
       
           ->whereBetween('t.issuing_date', [$request->start_date, $request->end_date])
              ->where('t.to_lab_id','=',auth()->user()->laboratory_id)
                ->where(function ($query) use ($search){
                  return  $query->where('t.siv_number', 'LIKE', "%{$search}%")
                  ->orWhere('t.from_lab_id','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.issuing_date','desc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;

            foreach ($terms as $term) {

$download=route('download',['id'=>$term->siv_number,'action'=>'download','type'=>'issue']);
$print=route('download',['id'=>$term->siv_number,'action'=>'print','type'=>'issue']);

             
                $nestedData['id']="<a href='#'>".$term->siv_number."</a>";
             
                 $nestedData['issue_date']= date('d, M Y',strtotime($term->issuing_date));
                $lab=Laboratory::select('id','lab_name')->where('id',$term->from_lab_id)->first();
                    $nestedData['issue_from']= $lab->lab_name;
                    $lab=Laboratory::select('id','lab_name')->where('id',$term->to_lab_id)->first();
                 $nestedData['issue_to'] = $lab->lab_name;
               if($term->approve_status=="received"){
$nestedData['action']= "<a type='button'><i class='fa fa-check'> Received</i></a> | <a id='$term->id' class='btn btn-info' onclick='viewIssue(this.id)'<i class='fa fa-eye'</i> view</a> |";
                   $nestedData['action'].="<ul ><li class='nav-item dropdown' >
         <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fa fa-file'></i>   Export To
        </a>
         <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
          <a class='dropdown-item' href='$download'><i class='fa fa-file-pdf'></i> PDF</a>
          <a class='dropdown-item' href='#' hidden>Excel</a>
          <div class='dropdown-divider'></div>
          <a class='dropdown-item' href='$print'><i class='fa fa-print'></i> Print</a>
        </div>
      </li>
    
    </ul>";
               }
               else{
                $nestedData['action']= "<a id='$term->id' onclick='ReceiveIssue(this.id)'><i class='btn btn-success'> Accept</i></a> | <a id='$term->id' class='btn btn-info' onclick='viewIssue(this.id)'<i class='fa fa-eye'</i> view</a> ";
                   $nestedData['action'].="<ul class='navbar-nav'><li class='nav-item dropdown' >
         <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fa fa-file'></i>   Export To
        </a>
         <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
          <a class='dropdown-item' href='$download'>PDF</a>
          <a class='dropdown-item' href='#' hidden>Excel</a>
          <div class='dropdown-divider'></div>
          <a class='dropdown-item' href='$print'>Print</a>
        </div>
      </li>
    
    </ul>";
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

public function searchIssueByLabSent(Request $request){
$date=date('Y-m-d');

         $columns = array(
            0 =>'id',
            1=>'issue_date',
            2=> 'issue_from',
            3=>'issue_to',
            4=>'action',
           
        ); 
   $totalData = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','l.lab_name','t.issuing_date')
          ->where('t.to_lab_id','=',auth()->user()->laboratory_id)
           ->where('t.from_lab_id', '=', $request->from_lab)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','t.from_lab_id','t.to_lab_id','t.issuing_date','t.approve_status','t.issued_by')
          ->where('t.to_lab_id','=',auth()->user()->laboratory_id)
           ->where('t.from_lab_id', '=', $request->from_lab)
                ->where(function ($query) use ($search){
                  return  $query->where('t.siv_number', 'LIKE', "%{$search}%")
                  ->orWhere('t.from_lab_id','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.issuing_date','desc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;

            foreach ($terms as $term) {

$download=route('download',['id'=>$term->siv_number,'action'=>'download','type'=>'issue']);
$print=route('download',['id'=>$term->siv_number,'action'=>'print','type'=>'issue']);

             
                $nestedData['id']="<a href='#'>".$term->siv_number."</a>";
             
                 $nestedData['issue_date']= date('d, M Y',strtotime($term->issuing_date));
                $lab=Laboratory::select('id','lab_name')->where('id',$term->from_lab_id)->first();
                    $nestedData['issue_from']= $lab->lab_name;
                    $lab=Laboratory::select('id','lab_name')->where('id',$term->to_lab_id)->first();
                 $nestedData['issue_to'] = $lab->lab_name;
               if($term->approve_status=="received"){
$nestedData['action']= "<a type='button'><i class='fa fa-check'> Received</i></a> | <a id='$term->id' class='btn btn-info' onclick='viewIssue(this.id)'<i class='fa fa-eye'</i> view</a> |";
                   $nestedData['action'].="<ul ><li class='nav-item dropdown' >
         <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fa fa-file'></i>   Export To
        </a>
         <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
          <a class='dropdown-item' href='$download'><i class='fa fa-file-pdf'></i> PDF</a>
          <a class='dropdown-item' href='#' hidden>Excel</a>
          <div class='dropdown-divider'></div>
          <a class='dropdown-item' href='$print'><i class='fa fa-print'></i> Print</a>
        </div>
      </li>
    
    </ul>";
               }
               else{
                $nestedData['action']= "<a id='$term->id' onclick='ReceiveIssue(this.id)'><i class='btn btn-success'> Accept</i></a> | <a id='$term->id' class='btn btn-info' onclick='viewIssue(this.id)'<i class='fa fa-eye'</i> view</a> ";
                   $nestedData['action'].="<ul class='navbar-nav'><li class='nav-item dropdown' >
         <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fa fa-file'></i>   Export To
        </a>
         <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
          <a class='dropdown-item' href='$download'>PDF</a>
          <a class='dropdown-item' href='#' hidden>Excel</a>
          <div class='dropdown-divider'></div>
          <a class='dropdown-item' href='$print'>Print</a>
        </div>
      </li>
    
    </ul>";
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
