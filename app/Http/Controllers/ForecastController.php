<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Consumption;
use App\Models\ConsumptionDetail;
use App\Models\ReceivedItem;
use App\Models\ItemOrder;
use App\Models\ItemOrderDetail;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\ConsolidateHistory;
use App\Models\User;
use Carbon\Carbon;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ForecastController extends Controller
{
    //load items selection for forecasting

    public function showForecasted(){
        return view('inventory.modal.forecast');
    }
    public function loadForecastItem(Request $request){
         
         $columns = array(
            0=>'check',
            1=>'code',
            2=> 'item',
            3=>'unit',
            4=>'cost',
            5=>'available',
            6=>'id',
            7=>'in_store',
         
           
           
        ); 
   $totalData =  DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')     
             ->where([['t.lab_id','=',auth()->user()->laboratory_id]])->count();

            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();
            $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
           $store1Id=auth()->user()->laboratory_id;
$store2Id=0;
            if(auth()->user()->laboratory_id!=0){

          
            $terms =DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.id as item_id','s.code','s.item_name','s.unit_issue','t.cost','t.quantity',
           DB::raw('SUM(CASE WHEN t.lab_id = ? THEN t.quantity ELSE 0 END) AS lab_total'),
        DB::raw('SUM(CASE WHEN t.lab_id = ? THEN t.quantity ELSE 0 END) AS store_total')
    )
    ->setBindings([$store1Id, $store2Id])
            //->offset($start)
           ->groupBy('s.item_name')
           // ->limit($limit)
            //->orderBy('s.id','asc')
            ->get(); 

        }
        else{
$terms =DB::table('inventories as t') 
        ->join('items AS s', 's.id', '=', 't.item_id')
        ->select('t.id as id','s.id as item_id','s.code','s.item_name','s.unit_issue','t.cost','t.quantity',
        DB::raw('SUM(t.quantity) AS lab_total'),
        DB::raw('SUM(CASE WHEN t.lab_id = ? THEN t.quantity ELSE 0 END) AS store_total')
    )
    ->setBindings([$store2Id])
            //->offset($start)
           ->groupBy('s.item_name')
           // ->limit($limit)
            //->orderBy('s.id','asc')
            ->get();
        }

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
               
                 $nestedData['available']  =$term->lab_total; 
                 $nestedData['in_store']  =$term->store_total;               
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
    public function generateForecast(Request $request){
      $order=$request->order;
      $lead_time=$request->lead;

    $columns = array(
            0=>'code',
            1=>'supplier',
            2=> 'item',
            3=>'unit',
            4=>'on_hand',
            5=>'average',
            6=>'forecasted',
            7=>'order',
            8=>'reorder_point',
            9=>'id'
             
           
           
        ); 
///start editing from here///
                    $consumption=array();
               //dd($request->items) ;   
        for($i=0;$i<count($request->items);$i++){
            $inv=Inventory::where('id',$request->items[$i])->select('item_id')->first();
      // $consumption[]=ConsumptionDetail::where('item_id',$request->items[$i])->avg('consumed_quantity');
        $terms=DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')  
    
              ->select('t.id as id','s.id as item_id','s.code','s.item_name','t.grn_number','s.unit_issue','t.cost','t.quantity',
               DB::raw('SUM(t.quantity) as quantity_requested'))
             ->where([['t.item_id','=',$inv->item_id]])
                ->where([['t.lab_id','=',auth()->user()->laboratory_id]])
             ->groupBy('s.item_name')
           // ->limit($limit)
            //->orderBy('s.id','asc')
            ->get(); 

      $consumption[]=$terms;
        }

 //dd( $consumption);
    
  
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
          if (!empty($consumption)) {
$x=1;
 
 $currentDateTime = Carbon::now();
        $newDateTime = Carbon::now()->subMonths($order);
           
            for ($y=0;$y<count($consumption);$y++ ) {

for($n=0;$n<count($consumption[$y]);$n++){
$con=ConsumptionDetail::where('item_id',$consumption[$y][$n]->id)
->where('lab_id',auth()->user()->laboratory_id)
->whereBetween(DB::raw('DATE(created_at)'), array($newDateTime,  $currentDateTime))->sum('consumed_quantity');
$avg_consumption=ConsumptionDetail::where('item_id',$consumption[$y][$n]->id)
->where('lab_id',auth()->user()->laboratory_id)
->whereBetween(DB::raw('DATE(created_at)'), array($newDateTime,  $currentDateTime))->avg('consumed_quantity');

$max_min=ConsumptionDetail::where('item_id',$consumption[$y][$n]->id)->whereBetween(DB::raw('DATE(created_at)'), array($newDateTime,  $currentDateTime))
->select( DB::raw('MAX(consumed_quantity) as max_consumption'),
DB::raw('MIN(consumed_quantity) as min_consumption')
)->first();


$avr_consum= round($con/$order,0)??0;
$maximum_lead_time=30;
$minimum_lead_time=20;
$max_consum=$max_min->max_consumption??0;
$min_consum=$max_min->min_consumption??0;
$avg_c=round(($max_consum+$min_consum)/2,0);

$av_lead_time=($maximum_lead_time+$minimum_lead_time)/2;
$lead_demand=$av_lead_time*$avg_c;

$safety_stock=($max_consum*$maximum_lead_time)-($avg_c*$av_lead_time);
$reorder_point=($avg_c*30)+$safety_stock;
      $supplier_by=DB::table('received_items as r')
                            ->join('suppliers as s','s.id','=','r.supplier_id')
                            ->where('grn_number',$consumption[$y][$n]->grn_number)->select('supplier_name')->first();
 $nestedData['id'] = $x;
                 $nestedData['code'] = $consumption[$y][$n]->code;
                     $nestedData['supplier']  = $supplier_by->supplier_name;   
                    $nestedData['item'] = $consumption[$y][$n]->item_name;
                     $nestedData['unit'] = $consumption[$y][$n]->unit_issue;
                $nestedData['on_hand'] = $consumption[$y][$n]->quantity_requested;
                     $nestedData['average'] =$avg_c;
                     $nestedData['reorder_point']=$reorder_point;
              // $showForecasted=($con*$order)+$lead_time;
                $showForecasted=($lead_time+$order)-$avg_c;
               $forecast=$showForecasted+$safety_stock;
                 $nestedData['forecasted'] = $showForecasted; 
                 $ordercasted=$showForecasted*$lead_time; 
                 $item_id=$consumption[$y][$n]->id;
                  $nestedData['order']  = "<input type='number' class='form-control' id='$item_id' name='ordered' size='3' value='$ordercasted'  oninput='getOrdered(this.id,this.name)'/>";  
                       $quanties[$item_id] =$ordercasted;         
                   $x++;
                $data[] = $nestedData;
                $d[]=$quanties;

           }
       }
      }
      

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "quantities"=>$d,
    );

      echo json_encode($json_data);
      
}
public function forecastOrder(Request $request){

  //dd($request->quantity);
    $ids=array();
  $quantities=array();
  $data=array();
for($i= 0; $i<count($request->quantity); $i++){
$items= explode('_',$request->quantity[$i]);


  $ids[]= $items[0];
$quantities[]= $items[1];
}
try{
    DB::beginTransaction();
$delivery_date = Carbon::now()->addMonths($request->lead);
$order=new ItemOrder();
$order->order_number=$request->order_number;
$order->lab_id=auth()->user()->laboratory_id;
$order->section_id=auth()->user()->section_id;
$order->delivery_date=$delivery_date;
$order->ordered_by=auth()->user()->id;
$order->approved_by=NULL;
$order->is_delivered="pending";
$order->is_approved="no";
$order->is_consolidated="no";
$order->created_at=now();
$order->updated_at=NULL;
$order->save();
$order_id=$order->id;
  
for($y=0;$y<count($ids);$y++){
  $details=new ItemOrderDetail();
  $details->order_id=$order_id;
  $details->order_number=$request->order_number;
  $details->inventory_id=$ids[$y];
  $inventory=Inventory::where('id',$ids[$y])->select('grn_number','item_id')->first();
  $details->item_id=$inventory->item_id;
 $supplier_by=DB::table('received_items as r')
                            ->join('suppliers as s','s.id','=','r.supplier_id')
                            ->where('grn_number',$inventory->grn_number)->select('supplier_id')->first();
$details->supplier_id=$supplier_by->supplier_id;
$details->ordered_quantity=$quantities[$y];
$details->created_at=now();
$details->updated_at=NULL;
$details->save();
}
DB::commit();
return response()->json([
    'message'=>'order placed successfully',
    'error'=>false,
]);
}
catch(Exception $e){
DB::rollback();
return response()->json([
    'message'=>'Failed to place order',
    'error'=>true,
]);
}
}
public function viewOrders(){
    $data['suppliers']=Supplier::select('id','supplier_name')->get();
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('inventory.orders.view',$data);
}
public function loadNewOrders(Request $request){

 $columns = array(
            0=>'check',
            1=>'lab',
            2=> 'delivery',
            3=>'ordered_by',
            4=>'approved_by',
            5=>'action',
            6=>'cons',
            7=>'order',
           
           
           
        ); 
   $totalData =  ItemOrder::count();

            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =ItemOrder::select('id','order_number','is_delivered','is_marked','is_consolidated','lab_id','section_id','delivery_date')
                   
                   

                ->where(function ($query) use ($search){
                  return  $query->where('order_number', 'LIKE', "%{$search}%");
                          
            })
            ->offset($start)
           //->groupBy('s.item_name')
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 

          $totalFiltered =  $totalRec ;
//  0 => 'id',
         
          $data = array();
          if (!empty($terms)) {
$x=1;
   
 
           
            foreach ($terms as $term) {


             $nestedData['check']="<input type='checkbox' id='cons_$term->id' name='$term->id' disabled/>";
             $nestedData['order']="<a   id='$term->id' onclick='viewOrder(this.id)'>".$term->order_number." </a>";

             $lab=Laboratory::where('id',$term->lab_id)->select('lab_name')->first();
             $user=User::where('id',$term->ordered_by)->select('name','last_name')->first();
               $approver=User::where('id',$term->approved_by)->select('name','last_name')->first();
                $nestedData['lab']=$lab->lab_name;
             $order_first_name=$user->name??'';
              $order_last=$user->last_name??'';
             $approver_first=$approver->name??' ';
              $approver_last=$approver->last_name??'';
                 $nestedData['delivery']= date('M. d,Y', strtotime($term->delivery_date??''));
                     
                    $nestedData['ordered_by']= $order_first_name.' '.$order_last;
                     $nestedData['approved_by']= $approver_first.' '.$approver_last;
                     if($term->is_delivered!="yes"){
              $nestedData['action']=" <a class='btn btn-info btn-sm' id='$term->id' onclick='viewOrder(this.id)'><i class='fa fa-eye'></i>View</a> | <a class='btn btn-success btn-sm' id='$term->id' onclick='MarkAsReceived(this.id)'><i class='fa fa-check'></i>Mark As Received</a>";
      }
      else{
       $nestedData['action']=" <a class='btn btn-info btn-sm' id='$term->id' onclick='viewOrder(this.id)'><i class='fa fa-eye'></i>View</a> | <span class='badge badge-success'>Order Received</span>"; 
      }

            switch($term->is_marked){
            case 'no':
                $nestedData['cons']="<a class='btn btn-success btn-sm' name='add'  id='$term->id' onclick='MarkForConsolidation(this.id,this.name)' ><i class='fa fa-plus'></i> add</a> ";
              break;
             
           case 'yes':
             
 $nestedData['cons']="<a class='btn btn-secondary btn-sm' name='remove'  id='$term->id' onclick='MarkForConsolidation(this.id,this.name)' ><i class='fa fa-minus'></i> Remove </a> ";
            break;
          case 'done':
  $nestedData['cons']="<span class='badge badge-info'>Consolidated</span> ";
             }

              /*if($term->is_consolidated=="no"){
                     $nestedData['cons']= "<a class='btn btn-success btn-sm' id='$term->id' name='cons_$term->id' onclick='consolidateItem(this.id,this.name)'><i class='fa fa-check'></i>Mark To Consolidate</a>  ";
                    
               }
               else{
 $nestedData['cons']="<span class='badge badge-info'>Order Consolidated</span>  ";
               }*/
                            
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

public function viewReceivedOrders(Request $request){
    $data['suppliers']=Supplier::select('id','supplier_name')->get();
    
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('inventory.orders.received_orders',$data);
}
public function showOrderDetails(Request $request){
    $data['id']=$request->id;
    return view('inventory.modal.order_details_modal',$data);
}
public function loadOrderDetails(Request $request){



        $columns = array(
            0=>'item',
            1=>'supplier',
            2=> 'unit',
            3=>'ordered',
           4=>'id',
           
           
           
        ); 
   $totalData =DB::table('item_order_details as iod')
                    ->join('items as i','i.id','=','iod.item_id')
                    ->where('iod.order_id',$request->id)->count();

            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('item_order_details as iod')
                    ->join('items as i','i.id','=','iod.item_id')
                    
                    ->select('iod.id as id','iod.order_number','iod.supplier_id','iod.ordered_quantity','i.item_name','i.unit_issue')
                ->where('iod.order_id',$request->id)
             

                ->where(function ($query) use ($search){
                  return  $query->where('iod.order_number', 'LIKE', "%{$search}%");
                          
            })
            ->offset($start)
           //->groupBy('s.item_name')
            ->limit($limit)
            ->orderBy('iod.id','desc')
            ->get(); 

          $totalFiltered =  $totalRec ;
//  0 => 'id',
         
          $data = array();
          if (!empty($terms)) {
$x=1;
   
 
           
           
            foreach ($terms as $term) {


 $nestedData['id']=$x;
             $nestedData['item']=$term->item_name;

             $supplier=Supplier::where('id',$term->supplier_id)->select('supplier_name')->first();
             
                 //$nestedData['delivery']= date('M. d,Y', strtotime($term->delivery_date??''));
                     
                    $nestedData['supplier']= $supplier->supplier_name;
                     $nestedData['unit']= $term->unit_issue;
              $nestedData['ordered']='<strong>'.$term->ordered_quantity.'</strong>';
              
                     
               
                            
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
function consolidateOrder(Request $request){
    //dd($request->item_ids[0]);
    $consolidated=array();
for($i=0;$i<count($request->item_ids);$i++){
     $report = DB::table('item_orders as r')
                      ->join('item_order_details as d','d.order_id','=','r.id')
                      ->join('inventories as inv','inv.id','=','d.inventory_id')
                      ->join('items as i','i.id','=','d.item_id')
                      ->where('r.id',$request->item_ids[$i])

                      ->get();
        $consolidated[]=$report;
}

     $spreadsheet = new Spreadsheet();

    
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

  

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A3', 'ULN NUMBER')
    ->setCellValue('B3', 'CODE')
    ->setCellValue('C3', 'Supplier')
    ->setCellValue('D3', 'Item Name')
     ->setCellValue('E3', 'Requested By')
    ->setCellValue('F3', 'Catalog Number')
    ->setCellValue('G3', 'Quantity Ordered')
    ->setCellValue('H3', 'Cost')
    ->setCellValue('I3', 'Total Cost')
     ->setCellValue('J3', 'Hazardous')
    ->setCellValue('K3', 'Storage Temp');

$num=4;
  for ($x=0; $x<count($consolidated); $x++){
    for($y=0; $y<count($consolidated[$x]);$y++){
$lab=Laboratory::where('id',$consolidated[$x][$y]->lab_id)->select('lab_name')->first();
$supplier=Supplier::where('id',$consolidated[$x][$y]->supplier_id)->select('supplier_name')->first();
 $total_cost=$consolidated[$x][$y]->ordered_quantity*$consolidated[$x][$y]->cost;
 $data=[

    [$consolidated[$x][$y]->order_number,
    $consolidated[$x][$y]->code, 
    $supplier->supplier_name,
    $consolidated[$x][$y]->item_name,
    $lab->lab_name,
    $consolidated[$x][$y]->catalog_number,
   
    $consolidated[$x][$y]->ordered_quantity,
    $consolidated[$x][$y]->cost,
    $total_cost,
       $consolidated[$x][$y]->is_hazardous,
    $consolidated[$x][$y]->store_temp
  ]
  ];
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
$num++;
}
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
/*$spreadsheet->getActiveSheet()
    ->setCellValue('A'.$step, 'Total');
  $spreadsheet->getActiveSheet()->getStyle('A'.$step)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()
    ->setCellValue('H'.$step, $overall_total);
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getNumberFormat()
    ->setFormatCode('#,##0.00');
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getFont()->setBold(true);*/

// Create Table

$table = new Table('A3:J'.$num, 'Exported');

// Create Columns

// Create Table Style

$tableStyle = new TableStyle();
$tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
$tableStyle->setShowRowStripes(true);
$tableStyle->setShowColumnStripes(true);
$tableStyle->setShowFirstColumn(true);
$tableStyle->setShowLastColumn(true);
$table->setStyle($tableStyle);

// Add Table to Worksheet

$spreadsheet->getActiveSheet()->addTable($table);



// Save

$writer = new Xlsx($spreadsheet);
$name="consolidated_orders.xlsx";
$writer->save(public_path('reports').'/'.$name);

/*for($index=0;$index<count($request->item_ids);$index++){
ItemOrder::where('id',$request->item_ids[$index])->update([
    'is_consolidated'=>'yes',
]);
}*/
return response()->json([
    'return_url'=>route('download_order',['name'=>$name]),
    'message'=>'Orders Consolidated successfully'
]);
}
public function downloadOrder($name){

$path=public_path('reports').'/'.$name;

$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 

return response()->download($path,$name, $headers);
}
public function loadFilterForecastItemByLocation(Request $request){
      
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
             ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.place_of_purchase','=',$request->value]])->count();

            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.code','s.item_name','s.unit_issue','t.cost','t.quantity',
            DB::raw('SUM(t.quantity) as quantity_requested'))
 
             ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.place_of_purchase','=',$request->value]])

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
               
                 $nestedData['available']  =$term->quantity_requested;              
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
function markAsReceived(Request $request){

    ItemOrder::where('id',$request->id)->update([
        'is_delivered'=>'yes',
        'received_by'=>auth()->user()->id,
        'updated_at'=>now(),
    ]);

    return response()->json([
        'message'=>"Order marked as Received",
    ]);
}
public function markForConsolidation(Request $request){

  switch ($request->type) {
    case 'add':
      ItemOrder::where('id',$request->id)->update([
        'is_marked'=>'yes'
      ]);
      return response()->json([
        'message'=>'Added to order consolidation list'
      ]);
      break;
    
    case 'remove':
      ItemOrder::where('id',$request->id)->update([
        'is_marked'=>'no'
      ]);

        return response()->json([
        'message'=>'Removed from order consolidation list'
      ]);
      break;
  }

}
public function consolidateOrders(Request $request){
return view('inventory.modal.view_order_modal_consolidate');
}

public function showOrdersMarked(Request $request){

$columns = array(
            0 =>'id',
            1=>'code',
            2=>'item_name',
            3=>'batch_number',
            4=>'catalog_number',
            5=>'is_hazardous',
            6=>'unit_issue',
            7=>'store_temp',
            8=>'total',

            9=>'options',
            10=>'request',
            11=>'cost',
            12=>'quantity'


          
        ); 

        
          $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->join('laboratories as l','l.id','=','r.lab_id')
                  ->select(
                    'i.id as id',
                    'r.id as order_id',
                    'r.lab_id',
                    'rd.supplier_id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.is_hazardous',
                    't.unit_issue',
                    't.warehouse_size',
                    'i.cost',
                    'i.quantity',
                    't.store_temp',
                    'l.lab_name',
                    'r.lab_id',
                    'r.ordered_by',
                    'r.approved_by',
                     DB::raw('SUM(rd.ordered_quantity) as quantity_requested'))
                   ->where([['r.is_approved','=','no'],['r.is_marked','=','yes']])
                   ->groupBy('t.item_name','r.id')
                   ->count();
           // ->where('t.expiry_date', '>', date('Y-m-d') )
         


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                   ->join('laboratories as l','l.id','=','r.lab_id')
                  ->select(
                    'i.id as id',
                    'r.id as order_id',
                    'r.lab_id',
                    'rd.supplier_id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.is_hazardous',
                    't.unit_issue',
                    't.warehouse_size',
                    'i.cost',
                    'i.quantity',
                    't.store_temp',
                    'l.lab_name',
                    'r.lab_id',
                    'r.ordered_by',
                    'r.approved_by',
                     DB::raw('GROUP_CONCAT(i.id) as requisitions_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_requested'))
                   ->where([['r.is_approved','=','no'],['r.is_marked','=','yes']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.item_name','r.lab_id')
            ->get();

          $totalFiltered =  $totalRec ;


        $data = array();
          if (!empty($terms)) {
$x=1;


            foreach ($terms as $term) {
$total=$term->quantity_requested*$term->cost;
 $nestedData['item_id']=$term->requisitions_ids;
                $nestedData['id']=$x;
                
                $nestedData['item_name']=$term->item_name;
                 $nestedData['request']=$term->lab_name;
                    $nestedData['code']=$term->code;
                $nestedData['batch_number']= $term->batch_number;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= $term->quantity_requested;
                 $nestedData['cost']=$term->cost;
           $nestedData['quantity']='<strong>'.$total.'</strong>';
                /*$nestedData['options']= " <a class='btn btn-danger btn-sm'   id='$term->id' onclick='RemoveForConsolidation(this.id)' ><i class='fa fa-trash'></i> remove</a> ";/*/
      
                
               
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
public function orderGetData(Request $request){
 //dd($request);
    $ids=explode(',',$request->id);
    
    $data = array();
 
    for($i=0;$i<count($ids); $i++){
$record= DB::table('inventories as inv')
                  ->join('item_order_details  as r','inv.id','=','r.inventory_id')
                  ->join('item_orders as rd','rd.id','=','r.order_id')
                  ->leftjoin('users as u','u.id','=','rd.ordered_by')
                  ->leftjoin('users as y','y.id','=','rd.approved_by')
                  ->join('laboratories as l','l.id','=','rd.lab_id')
                  
                  ->select(
                    'rd.order_number',
                    'rd.id',
                    'rd.section_id',
                    'l.lab_name',
                    'u.name',
                    'u.last_name',
                    'y.name as approved_name',
                    'y.last_name as approved_lastname',
                    'rd.created_at',
                    'inv.cost',
                    'r.ordered_quantity'
                    )->where('inv.id',$ids[$i])
                    ->where('rd.is_marked','yes')
                    ->where('rd.is_approved','no')->get();
                    
             
             foreach($record as $r)  { 
  //dd($record);
  
  $nested['sr_number']=$r->order_number;
   $nested['lab_name']=$r->lab_name;
   $nested['name']=$r->name;
  $nested['last_name']=$r->last_name;
  $nested['approved_name']=$r->approved_name;
  $nested['approved_lastname']=$r->approved_lastname;
  $nested['requested_date']=$r->created_at;
  $nested['cost']=$r->cost;
$nested['quantity_requested']=$r->ordered_quantity;
$data[]= $nested;

}
} 

  return response()->json([
   'data'=>$data,
  ]);
}

//export order to excel file
 public function exportOrderList(Request $request){
      $report =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                   ->join('item_orders as r','r.id','=','rd.order_id')
                   ->join('laboratories as l','l.id','=','r.lab_id')
                  ->select(
                    'i.id as id',
                    'r.id as order_id',
                    'r.lab_id',
                    'rd.supplier_id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.is_hazardous',
                    't.unit_issue',
                    't.warehouse_size',
                    'i.cost',
                    'i.quantity',
                    't.store_temp',
                    'l.lab_name',
                    'r.lab_id',
                    'r.ordered_by',
                    'r.approved_by',
                     //DB::raw('GROUP_CONCAT(r.id) as ids'),
                      DB::raw('SUM(rd.ordered_quantity) as quantity_requested'))
                   ->where([['r.is_approved','=','no'],['r.is_marked','=','yes']])
                   ->where('t.item_category','ambient goods')
            ->groupBy('t.item_name','r.lab_id')
                   ->get();


    $perishable =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                   ->join('item_orders as r','r.id','=','rd.order_id')
                   ->join('laboratories as l','l.id','=','r.lab_id')
                  ->select(
                    'i.id as id',
                    'r.id as order_id',
                    'r.lab_id',
                    'rd.supplier_id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.is_hazardous',
                    't.unit_issue',
                    't.warehouse_size',
                    'i.cost',
                    'i.quantity',
                    't.store_temp',
                    'l.lab_name',
                    'r.lab_id',
                    'r.ordered_by',
                    'r.approved_by',
                     //DB::raw('GROUP_CONCAT(r.id) as ids'),
                   DB::raw('SUM(rd.ordered_quantity) as quantity_requested'))
                   ->where([['r.is_approved','=','no'],['r.is_marked','=','yes']])
                   ->where('t.item_category','perishable')
                   ->groupBy('t.item_name','r.lab_id')
                   ->get();


$orders=ItemOrder::where([['is_approved','=','no'],['is_marked','=','yes']])->count();
     // dd($test);
                   if(count($report)==0 && count($perishable)==0){
                 return  back()->with('error',' Data was not found! ');
                   }
      /*$report = DB::table('requisitions as r')
                      ->join('requisition_details as d','d.requisition_id','=','r.id')
                      ->join('items as i','i.id','=','d.item_id')
                      ->where([['r.status','=','approved'],['r.is_marked','=','yes']])

                      ->get();*/

         $spreadsheet = new Spreadsheet();

    $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->mergeCells('D6:E6');
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

  $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
 $image = file_get_contents(url('/').'/assets/icon/logo_black.png');
$imageName = 'logo.png';
$temp_image=tempnam(sys_get_temp_dir(), $imageName);
file_put_contents($temp_image, $image);
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath($temp_image); 
$drawing->setHeight(70);
$drawing->setCoordinates('C2');
$drawing->setOffsetX(110);


$drawing->getShadow()->setDirection(45);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('D7', 'SUPPLIER ORDERS CONSOLIDATION LIST ');
$spreadsheet->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);

$spreadsheet->getActiveSheet()
   
    ->setCellValue('A10', 'ULN')
    ->setCellValue('B10','REQUESTED BY')
     ->setCellValue('C10', 'CODE')
      ->setCellValue('D10', 'SUPPLIER')
      ->setCellValue('E10', 'CATALOG NUMBER')
    ->setCellValue('F10', 'ITEM NAME')
    ->setCellValue('G10', 'PACK SIZE')
    ->setCellValue('H10', 'QUANTITY ORDERED')
    ->setCellValue('I10', 'UNIT COST')
    ->setCellValue('J10', 'TOTAL COST')
    ->setCellValue('K10', 'HAZARDOUS')
    ->setCellValue('L10', 'UNIT OF ISSUE')
    ->setCellValue('M10', 'STORAGE TEMP.')
   
   ;

$num=11;
$t=11;
  for ($x=0; $x<count($report); $x++){
//$requester=Laboratory::where('id', $report[$x]->lab_id)->select("lab_name")->first();
$supplier=Supplier::where('id',$report[$x]->supplier_id)->select('supplier_name')->first();
$total=$report[$x]->quantity_requested*$report[$x]->cost;
  $data=[

    [
    $report[$x]->uln,
     $report[$x]->lab_name,
    $report[$x]->code,
    $supplier->supplier_name,
    $report[$x]->catalog_number,
    $report[$x]->item_name,
    $report[$x]->warehouse_size,
    $report[$x]->quantity_requested,
    $report[$x]->cost,
    $total,
    $report[$x]->is_hazardous,
    $report[$x]->unit_issue,
    $report[$x]->store_temp,
    
  ],
 
  ];

$num++;
   // $spreadsheet->getActiveSheet()->getRowDimension($x)->setOutlineLevel(1);
   // $spreadsheet->getActiveSheet()->getRowDimension($x)->setVisible(false);
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
   $spreadsheet->getActiveSheet()->getStyle('A'.$num.':M'.$num)->getFont()->setBold(true);

 



 

//$t=$num;

}
$t=$num+2;
if(count($perishable)>0){
 $spreadsheet->getActiveSheet()->setCellValue('A'.$t, 'Perishable');   
for ($x=0; $x<count($perishable); $x++){
//$requester=Laboratory::where('id', $perishable[$x]->lab_id)->select("lab_name")->first();
$supplier=Supplier::where('id',$perishable[$x]->supplier_id)->select('supplier_name')->first();
$total=$perishable[$x]->quantity_requested*$perishable[$x]->cost;
  $data=[

    [
    $perishable[$x]->uln,
     $perishable[$x]->lab_name,
    $perishable[$x]->code,
    $supplier->supplier_name,
    $perishable[$x]->catalog_number,
    $perishable[$x]->item_name,
    $perishable[$x]->warehouse_size,
    $perishable[$x]->quantity_requested,
    $perishable[$x]->cost,
    $total,
    $perishable[$x]->is_hazardous,
    $perishable[$x]->unit_issue,
    $perishable[$x]->store_temp,
    
  ],
 
  ];

$t++;
    //$spreadsheet->getActiveSheet()->getRowDimension($x)->setOutlineLevel(1);
    //$spreadsheet->getActiveSheet()->getRowDimension($x)->setVisible(false);
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$t);
   $spreadsheet->getActiveSheet()->getStyle('A'.$t.':M'.$t)->getFont()->setBold(true);

 



 


}
}
//$spreadsheet->getActiveSheet()->getRowDimension(81)->setCollapsed(true); 
$step=$t+1;
//$spreadsheet->getActiveSheet()->getRowDimension($step)->setCollapsed(true); 
//$spreadsheet->getActiveSheet()->setShowSummaryBelow(false);

//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
/*$spreadsheet->getActiveSheet()
    ->setCellValue('A'.$step, 'Total');
  $spreadsheet->getActiveSheet()->getStyle('A'.$step)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()
    ->setCellValue('H'.$step, $overall_total);
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getNumberFormat()
    ->setFormatCode('#,##0.00');
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getFont()->setBold(true);*/

// Create Table

$table = new Table('A10:M'.$step, 'Exported');
$spreadsheet->getActiveSheet()->setCellValue('A'.$step+3, 'Consolidated By: '.auth()->user()->name.' '.auth()->user()->last_name);
$spreadsheet->getActiveSheet()->setCellValue('A'.$step+4, 'Consolidated Date: '.date('d,M Y',strtotime(now())));
$spreadsheet->getActiveSheet()->setCellValue('A'.$step+5, 'Orders Affected: '.$orders);
// Create Columns

// Create Table Style

$tableStyle = new TableStyle();
$tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
$tableStyle->setShowRowStripes(true);
$tableStyle->setShowColumnStripes(true);
//$tableStyle->setShowFirstColumn(true);
//$tableStyle->setShowLastColumn(true);
$table->setStyle($tableStyle);

// Add Table to Worksheet

$spreadsheet->getActiveSheet()->addTable($table);




// Save

$writer = new Xlsx($spreadsheet);
$db_name='consolidated_orders_'.date('d_M_Y').'.xlsx';
$writer->save(public_path('reports').'/'.$db_name);
$path=public_path('reports').'/'.$db_name;
$name='exports.xlsx';

$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
//Update order status
//return response()->download($path,$name, $headers);
try{
    DB::beginTransaction();
    ItemOrder::where('is_marked','yes')->update([
 'is_marked'=>'done',
]);
$this->saveOrdersHistory($orders,$db_name); 
DB::commit();
}
catch(Exception $e){
DB::rollback();
return  back()->with('error',' Something went  wrong! ');
                      
}
return response()->download($path,$name, $headers);
       
   }

   public function receivedOrders(Request $request){
       
 $columns= array(
    0 => 'id', 
    1=>'order',
    2=>'lab',
    3=>'delivery',
    4=>'ordered_by',
    5=>'approved_by',
    6=>'received_by',
    7=>'action'
    );
   
   $totalData = ItemOrder::where('is_delivered','yes')
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');


            $terms =ItemOrder::where('is_delivered','yes')

                ->where(function ($query) use ($search){
                  return  $query->where('order_number', 'LIKE', "%{$search}%");
                  //->orWhere('to_lab_id','LIKE',"%{$search}%");
                      
                     
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

 $lab=Laboratory::select('id','lab_name')->where('id',$term->lab_id)->first();

             $nestedData['id']=$x;
                $nestedData['order']=$term->order_number;
             
                 $nestedData['lab']= $lab->lab_name;
                    $nestedData['delivery']= date('d,M Y',strtotime($term->created_at));
                   
                 $nestedData['ordered_by'] = $term->ordered_by;
                 $nestedData['approved_by']  =$term->approved_by;
                  $nestedData['received_by']  =$term->approved_by;
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
private function saveOrdersHistory($orders,$db_name){
     $name=auth()->user()->name.' '.auth()->user()->last_name;
  $date=now();
  $consolidated=new ConsolidateHistory();
  $consolidated->consolidated_by=$name;
 $consolidated->date=$date;
 $consolidated->orders=$orders;
  $consolidated->type='supplier';

   $consolidated->path=$db_name;
   $consolidated->created_at=now();
   $consolidated->updated_at=NULL;
   $consolidated->save();
}
public function showOrderHistory(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('inventory.modal.orders_history');
}

public function loadOrderHistory(Request $request){

   $columns = array(
            0 =>'id',
            1=>'date',
            2=>'consolidated_by',
            3=>'orders',
            4=>'document',
          
        ); 

         $totalData = DB::table('consolidate_histories')->where('type','=','supplier')
         // ->where('status','=','approved')
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('consolidate_histories') 
          ->where('type','=','supplier')
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('consolidated_by', 'LIKE', "%{$search}%");
                 
                      
                     
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
$path=route('consolidated.document',['id'=>$term->id]);


                $nestedData['id']=$x;
                $nestedData['date']=$term->date;
                $nestedData['consolidated_by']= $term->consolidated_by;
             $nestedData['orders']= $term->orders;
                $nestedData['document']= " <a class='btn btn-primary btn-sm' id='$term->id' href='$path'><i class='fa fa-download'></i> Download</a> ";
    
               
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
public function downloadOrdersDocument($id){
  $cons=ConsolidateHistory::where('id',$id)->select('path')->first();
  $path=public_path('reports').'/'.$cons->path;
$name=$cons->path;

$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
//Update Requsition status
//Requisition::where('is_marked','yes')->update([
  //'is_marked'=>'done',
//]);

return response()->download($path,$name, $headers);


}
    


}
