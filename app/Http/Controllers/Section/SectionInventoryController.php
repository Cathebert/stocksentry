<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\Inventory;
use App\Models\Issue;
use App\Models\IssueDetails;
use App\Models\User;
use App\Models\ItemOrder;
use App\Models\Setting;
use App\Models\Requisition;
use App\Models\RequisitionDetails;
use App\Services\UpdateInventoryService;
use App\Services\AcceptIssueService;
use Validator;
use DB;

use PDF;
class SectionInventoryController extends Controller
{
    //

     public function showSectionBinCard(){
         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
        
         
      $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
          ->groupBy('s.id')
             ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id]])
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
        return view('clerk.inventory.tabs.section_bincard', $data);
    }

 public function showSectionStockTake(){
   $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
          $data['count']=Inventory::where('section_id',auth()->user()->section_id)->count(); 
          
          
               $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('clerk.inventory.tabs.section_stocktaking',$data);
    }

    public function showSectionConsumption(){
         $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
             ->where([['t.lab_id','=',auth()->user()->laboratory_id],
                ['t.section_id','=',auth()->user()->section_id]]);
                
                     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('clerk.inventory.tabs.section_consumption',$data);
    }

    public function showSectionStockForecasting(){
        $order=ItemOrder::select('id')->latest('id')->first();

  $settings=Setting::find(1);
  if($order->id){
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
        return view('clerk.inventory.tabs.section_stockforecating',$data);
    }
    public function showSectionAdjustment(){
        
             $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
         return view('clerk.inventory.tabs.section_stockadjustment');

    }

    public function showSectionDisposal(){
    $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('clerk.inventory.tabs.section_stockdisposal');

    }


    public function showSectionRequest(){
        $data['laboratories']=Laboratory::get();
        $data['sections']=LaboratorySection::where('id',auth()->user()->section_id)->get();
        $sr_number=Requisition::select('id','sr_number')->orderBy('id', 'desc')->first();

if($sr_number){

             $data['sr_number']=$this->get_order_number($sr_number->id);
}
else{
       
             $data['sr_number']=   $this->get_order_number(1);
}
      
    
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
        return view('clerk.issue.tabs.section_request',$data);
    }

         public function showSectionIssue(){
        $data['laboratories']=Laboratory::get();
        $data['sections']=LaboratorySection::get();
        $sr_number=Requisition::select('id','sr_number')->orderBy('id', 'desc')->first();

if($sr_number){

             $data['sr_number']=$this->get_order_number($sr_number->id);
}
else{
       
             $data['sr_number']=   $this->get_order_number(1);
}
      
    
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
        return view('clerk.issue.tabs.section_issue',$data);
    }
public function showSectionReceived(){
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
    return view('clerk.issue.tabs.section_received',$data);
}

    private function get_order_number($id)

{
    return 'SR' . str_pad($id, 4, "0", STR_PAD_LEFT);
}

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
        return view('clerk.receive.tabs.new_receipt',$data);
    }
      public function allReceivedInventory(Request $request){
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
        return view('clerk.receive.tabs.section_received_receipts',$data);
    }


public function loadReceivedItems(Request $request){
              $columns = array(
            0 =>'id',
            1=>'receiving_date',
            2=> 'Supplier',
            3=>'Received_by',
            4=>'action' ,
        ); 
   $totalData = DB::table('received_items as t') 
              ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
        ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id]])
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('received_items as t') 
                ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
               ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id]])
          
                ->where(function ($query) use ($search){
                  return  $query->where('t.grn_number', 'LIKE', "%{$search}%")
                  ->orWhere('s.supplier_name','LIKE',"%{$search}%");
                      
                     
            })
            //->offset($start)
            //->limit($limit)
            ->orderBy($order, $dir)
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 

            foreach ($terms as $term) {

$print=route('received.generateprint',['id'=>$term->grn_number]);
$download=route('received.generatepdf',['id'=>$term->grn_number]);

                $nestedData['id']=$term->grn_number;
                $nestedData['receiving_date']=$term->receiving_date;
             
                 $nestedData['Supplier']= $term->supplier_name;
                $nestedData['Received_by']= $term->received_by;
               
                $nestedData['action']= "<a href='#' id='$term->grn_number' onclick=' showItemDetails(this.id)' ><i class='fa fa-eye'></i><u>View</u></a>";
                 $nestedData['action'].='<ul class="navbar-nav"><li class="nav-item dropdown" >
         <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-file"></i>   Export To
        </a>
         <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href='.$download.' name="download" id='.$term->grn_number.' onclick=generatePDF(this.id,this.name)><i class="fa fa-file-pdf" aria-hidden="true"></i> PDF</a>
        
          <div class="dropdown-divider"></div>
          <a class="dropdown-item"  href='.$print.' id='.$term->grn_number.' ><i class="fa fa-print" aria-hidden="true"></i> Print</a>
        </div>
      </li></ul>';
               
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

public function getSectionReceivedFiltered(Request $request){

      parse_str($request->all_receipts,$out);
  $filtered=$out;

    $columns = array(
            0 =>'id',
            1=>'receiving_date',
            2=> 'Supplier',
            3=>'Received_by',
            4=>'action' ,
        ); 

  if($filtered['start_date']&& $filtered['end_date']){
      $totalData = DB::table('received_items as t') 
              ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
              ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.id','=',$filtered['supplier']]])
             ->whereBetween(DB::raw('DATE(t.receiving_date)'), array($filtered['start_date'],  $filtered['end_date']))
          ->count();


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
           
          $totalFiltered =  $totalRec ;
//  0 => 'id',
    

           $terms = DB::table('received_items as t') 
                ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
               ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.id','=',$filtered['supplier']]])
             ->whereBetween(DB::raw('DATE(t.receiving_date)'), array($filtered['start_date'],  $filtered['end_date']))
                ->where(function ($query) use ($search){
                  return  $query->where('t.grn_number', 'LIKE', "%{$search}%")
                  ->orWhere('s.supplier_name','LIKE',"%{$search}%");
                      
                     
            })
            //->offset($start)
            //->limit($limit)
            ->orderBy($order, $dir)
            ->get();

  }



  else{
    $totalData = DB::table('received_items as t') 
              ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
              ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.id','=',$filtered['supplier']],['t.section_id','=',auth()->user()->section_id]])
          
          ->count();


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
           
          $totalFiltered =  $totalRec ;
//  0 => 'id',
    

           $terms = DB::table('received_items as t') 
                ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
               ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.id','=',$filtered['supplier']]
            ,['t.section_id',auth()->user()->section_id]])
          
                ->where(function ($query) use ($search){
                  return  $query->where('t.grn_number', 'LIKE', "%{$search}%")
                  ->orWhere('s.supplier_name','LIKE',"%{$search}%");
                      
                     
            })
            //->offset($start)
            //->limit($limit)
            ->orderBy($order, $dir)
            ->get();
  }
        


          $data = array();
          if (!empty($terms)) {
$x=1;
 

            foreach ($terms as $term) {
$print=route('received.generateprint',['id'=>$term->grn_number]);
$download=route('received.generatepdf',['id'=>$term->grn_number]);



                $nestedData['id']=$term->grn_number;
                $nestedData['receiving_date']=$term->receiving_date;
             
                 $nestedData['Supplier']= $term->supplier_name;
                $nestedData['Received_by']= $term->received_by;
               
                $nestedData['action']= "<a href='#' id='$term->grn_number' onclick=' showItemDetails(this.id)' ><i class='fa fa-eye'></i><u>View</u></a>";
               $nestedData['action'].='<ul class="navbar-nav"><li class="nav-item dropdown" >
         <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-file"></i>   Export To
        </a>
         <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href='.$download.' name="download" id='.$term->grn_number.' onclick=generatePDF(this.id,this.name)><i class="fa fa-file-pdf" aria-hidden="true"></i> PDF</a>
        
          <div class="dropdown-divider"></div>
          <a class="dropdown-item"  href='.$print.' id='.$term->grn_number.' ><i class="fa fa-print" aria-hidden="true"></i> Print</a>
        </div>
      </li></ul>';
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

public function searchItem(Request $request){
    if($request->search=="reload"){
 $inventory = DB::table('items as item')
                          ->join('inventories as inve','inve.item_id','=','item.id')
                          ->where([['inve.lab_id','=',auth()->user()->laboratory_id],['inve.section_id','=',auth()->user()->section_id]])
                          ->select('inve.id','item.item_name')->get();
                           

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
                        ->where([['inve.lab_id','=',auth()->user()->laboratory_id],['inve.section_id','=',auth()->user()->section_id]])
                          ->select('inve.id','item.item_name')
                            ->where('item.item_name','LIKE',"%{$request->search}%")->get();

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
}

public function loadSectionStockTake(Request $request){
  
         $columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'brand',
            4=>'name',
            5=>'unit',
            6=>'consumed',
           
        ); 
         switch ($request->value) {
             case 1:
                 $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],
            ['t.section_id','=',auth()->user()->section_id]])
          ->where('t.storage_location',$request->value)
           // ->where('t.expiry_date', '>', date('Y-m-d') )
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
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],
            ['t.section_id','=',auth()->user()->section_id]])
          ->where('t.storage_location',$request->value)
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
                 break;
            case 3:

                 $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],
            ['t.section_id','=',auth()->user()->section_id]])
          ->where('t.storage_location',$request->value)
           // ->where('t.expiry_date', '>', date('Y-m-d') )
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
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],
            ['t.section_id','=',auth()->user()->section_id]])
          ->where('t.storage_location',$request->value)
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
             break;
             default:
                 $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],
            ['t.section_id','=',auth()->user()->section_id]])
          ->where('t.storage_location',3)
           // ->where('t.expiry_date', '>', date('Y-m-d') )
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
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],
            ['t.section_id','=',auth()->user()->section_id]])
          ->where('t.storage_location',3)
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
                 break;
         }
   



           
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {



                $nestedData['id']="<input type='checkbox' id='se_$term->id' name='selected_check' onclick='AddIdToList(this.id)'/>";
                  $nestedData['batch_number']=$term->batch_number;
                    $nestedData['brand']= $term->brand;
                $nestedData['code']=$term->code;
             
                 $nestedData['name']= $term->item_name;
                  $nestedData['unit']= $term->unit_issue;
               
                 $nestedData['consumed'] = "<input type='number'  size='5' id='s_$term->id' size='4' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getPhysicalCount(this.id,this.name)'/>";
                 
                   
     
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

 public function loadSectionForecastItem(Request $request){
       
         $columns = array(
            0=>'check',
            1=>'code',
            2=> 'item',
            3=>'unit',
            4=>'cost',
            5=>'available',
            6=>'id'
           
           
        ); 
   $totalData =  DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')     
             ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id]])->count();

            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.code','s.item_name','s.unit_issue','t.cost','t.quantity')
 
             ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id]])

                ->where(function ($query) use ($search){
                  return  $query->where('s.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('s.code','LIKE',"%{$search}%");
                      
                     
            })
            //->offset($start)
           ->groupBy('s.item_name')
           // ->limit($limit)
            //->orderBy('s.id','asc')
            ->get(); 

          $totalFiltered =  $totalRec ;
//  0 => 'id',
         
          $data = array();
          if (!empty($terms)) {
$x=1;
   
  
           
            foreach ($terms as $term) {



             $nestedData['id']=$term->id;
                $nestedData['check']="<input type='checkbox' id='$term->id' class='checkboxall' name='selected_check' value='$term->id'  onclick='selectItem(this.value)'/>";
             
                 $nestedData['code']= $term->code;
                     
                    $nestedData['item']= $term->item_name;
                     $nestedData['unit']= $term->unit_issue;
             
                     $nestedData['cost']= $term->cost;
               
                 $nestedData['available']  =$term->quantity;              
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