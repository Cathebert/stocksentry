<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
use App\Models\StockTake;
use App\Models\StockTakeDetails;
use App\Models\StockTakeEmployeeInvolved;
use App\Models\Discrepancy;
use App\Models\Inventory;
use App\Jobs\updateStockTakenJob;
use App\Notifications\StockCreatedNotification;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class StockTakeController extends Controller
{
    //

     public function show(Request $request){
        $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
        $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
             ->where('t.lab_id','=',auth()->user()->laboratory_id);
         
   
        return view('inventory.modal.stock_take',$data);
    }
public function loadStockInventory(Request $request){
     
         $columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'brand',
            4=>'name',
            5=>'unit',
            6=>'consumed',
            7=>'status'
           
        ); 
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where('t.lab_id','=',auth()->user()->laboratory_id)
          ->where('t.quantity','>',0)
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
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
         ->where('t.lab_id','=',auth()->user()->laboratory_id)
           ->where('t.quantity','>',0)
          ->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {


$nestedData['item_id']=$term->id;
                $nestedData['id']="<input type='checkbox' id='se_$term->id' name='selected_check' onclick='AddIdToList(this.id)'/>";
                  $nestedData['batch_number']=$term->batch_number;
                    $nestedData['brand']= $term->brand;
                $nestedData['code']=$term->code;
             
                 $nestedData['name']= $term->item_name;
                  $nestedData['unit']= $term->unit_issue;
               
                 $nestedData['consumed'] = "<input type='number'  size='5' id='s_$term->id' min='0' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getPhysicalCount(this.id,this.name)'/>";
                  $nestedData['status']="<button class='btn btn-outline-primary' id='$term->id' onclick='saveConsumed(this.id)'> <i class='fa fa-save' arial-hidden='true' id='fa_$term->id'></i></button>";
                   
     
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
    public function uploadCVS(Request $request){
       if ($request->hasFile('fileToUpload')) {
        $file_name = $request->file('fileToUpload')->getClientOriginalName();

     //  $earn_proof = $request->file('fileToUpload')->storeAs("public/upload/cvs/", $file_name);
        $request->file('fileToUpload')-> move(public_path().'/upload/cvs',$file_name);
       
    }
      return response()->json(['result' => true, 'message'=> "File uploaded will notify you of the progress " .$file_name], 200);
    }

    public function export()
{
 $report= DB::table('items as t') 
              ->join('inventories AS s', 's.item_id', '=', 't.id')
          //->select('t.id as id','s.item_name','t.batch_number','t.cost')
             ->where('t.lab_id','=',auth()->user()->laboratory_id)->get();
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
$spreadsheet->getActiveSheet()->setCellValue('E8', 'INVENTORY LIST ');
$spreadsheet->getActiveSheet()->getStyle('E8')->getFont()->setBold(true);

$spreadsheet->getActiveSheet()
   ->setCellValue('A10', 'ULN')
     ->setCellValue('B10', 'CODE')
    ->setCellValue('C10', 'ITEM NAME')
    ->setCellValue('D10', 'BATCH NUMBER')
    ->setCellValue('E10', 'CATALOG NUMBER')
    ->setCellValue('F10', 'HAZARDOUS')
    ->setCellValue('G10', 'UNIT OF ISSUE')
    ->setCellValue('H10', 'STORAGE TEMP.')
    ->setCellValue('I10', 'QUANTITY AVAILABLE')
 ;

$num=11;
  for ($x=0; $x<count($report); $x++){


  $data=[

    [
    $report[$x]->uln,
    $report[$x]->code,
    $report[$x]->item_name,
    $report[$x]->batch_number,
    $report[$x]->catalog_number,
    $report[$x]->is_hazardous,
    $report[$x]->unit_issue,
    $report[$x]->store_temp,
    '',
  ],
 
  ];

$num++;
   // $spreadsheet->getActiveSheet()->getRowDimension($x)->setOutlineLevel(1);
  //  $spreadsheet->getActiveSheet()->getRowDimension($x)->setVisible(false);
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
   $spreadsheet->getActiveSheet()->getStyle('A'.$num.':I'.$num)->getFont()->setBold(true);

 


}


//$spreadsheet->getActiveSheet()->getRowDimension(81)->setCollapsed(true); 
$step=$num+1;
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

$table = new Table('A10:I'.$step, 'Exported');
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
$name='inventory_list.xlsx';

$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
//Update Requsition status
//Requisition::where('is_marked','yes')->update([
 //'is_marked'=>'done',
//]);
//$this->saveConsolidationHistory($orders,$db_name);
return response()->download($path,$name, $headers);
}
public function saveMany(Request $request){
 
   parse_str($request->consumed_form_data,$out);
 $stock_save_form= $out;
   $ids=array();
  $quantities=array();
  $data=array();
  if(!$request->consumed){
    return response()->json([
  'message'=>"Failed to update Stock. Failed to run selected data",
  'error'=>true,
]);  
  }
for($i= 0; $i<count($request->consumed); $i++){
$items= explode('_',$request->consumed[$i]);

$ids[]= $items[0];
$quantities[]= $items[1];


}

 try{
  if(!empty($ids) && count($ids)>0){

    DB::beginTransaction();
    $stock_take_id =$this->saveStock($stock_save_form);

    if (!empty($stock_save_form['employee_involved']) && count($stock_save_form['employee_involved'])>0){
      $this->saveEmployeesInvolved( $stock_take_id,$stock_save_form['employee_involved']);

  }
  for($x=0; $x<count($ids); $x++){
  $inventory=Inventory::where('id',$ids[$x])->select('quantity')->first();
     $details = new StockTakeDetails();
  $details->stock_take_id = $stock_take_id;
  $details->item_id=$ids[$x];
  $details->system_quantity=$inventory->quantity;
  $details->physical_count=$quantities[$x];
  $details->created_at=now();
  $details->updated_at=NULL;
  $details->save();

  $this->checkForDiscrepancies($stock_take_id,$ids[$x],$quantities[$x]);
  }
  DB::commit();
$approvers=User::where([['authority','=',2],['laboratory_id','=',auth()->user()->laboratory_id]])->get();
$disposed_by=auth()->user()->name.' '.auth()->user()->last_name;

foreach($approvers as $user){

  $user->notify(new StockCreatedNotification($disposed_by))
}
return response()->json([
  'message'=>"Stock take completed Successfully awaiting Approval",
  'error'=>false,
]);
  }

 }
 catch(Exception $e){
  DB::rollback();
  return response()->json([
  'message'=>"Operation Failed",
  'error'=>true,
]);
}
}
public function saveSelected(Request $request){
parse_str($request->stock_save_form,$out);
$stock_save_form=$out;
//  dd($request->id);
  try{
DB::beginTransaction();
   $stock_take_id =$this->saveStock($stock_save_form);

//people involved 
if (!empty($stock_save_form['employee_involved']) && count($stock_save_form['employee_involved'])>0){
  $this->saveEmployeesInvolved( $stock_take_id,$stock_save_form['employee_involved']);

  }
  $details = new StockTakeDetails();
  $details->stock_take_id = $stock_take_id;
  $details->item_id= $request->id;
  $details->physical_count=$request->consumed;
  $details->created_at=now();
  $details->updated_at=NULL;
  $details->save();
  DB::commit();
return response()->json([
  'message'=>config('stocksentry.stock_taken'),
  'error'=>false
]);
  }

  catch(Exception $e){
    DB::rollback();
return response()->json([
  'message'=>"Something went wrong",
  'error'=>true
]);
  }
}



protected function  saveStock($form_details){

  $stock=new StockTake();

$stock->lab_id= auth()->user()->laboratory_id;
$stock->section_id=  auth()->user()->section_id;
$stock->stock_date=$form_details['start_date'];
$stock->is_approved="no";
$stock->approved_by=NULL;
$stock->inventory_area='Store';
$stock->supervisor_id=$form_details['supervisor'];
$stock->created_at=now();
$stock->updated_at=NULL;
$stock->save();
$stock_take_id= $stock->id;
return $stock_take_id;
}

protected function saveEmployeesInvolved($stock_id,$stock_save_form){

  for($i=0;$i<count($stock_save_form); $i++){
$people= new StockTakeEmployeeInvolved();
$people->stock_take_id = $stock_id;
$people->user_id =$stock_save_form[$i];
$people->created_at = now();
$people->updated_at= NULL;
$people->save();
}

}
protected function checkForDiscrepancies($stock_id,$item_id,$quantity){

  //dd($quantity);

  $inventory=Inventory::where('id', $item_id)->select('quantity')->first();
  if($quantity>$inventory->quantity || $quantity < $inventory->quantity){
    $d_value= $inventory->quantity-$quantity;
    if($d_value>0){
    $remark="Overage";
  }
  else{
    $remark="Underage";
  }

  $discrepancies=new Discrepancy();
  $discrepancies->stock_id  =  $stock_id;
  $discrepancies->item_id = $item_id;
  $discrepancies->value = $d_value;
  $discrepancies->remark=$remark;
  $discrepancies->created_at=now();
  $discrepancies->updated_at=NULL;
  $discrepancies->save();

  }

}
public function stockViewHistory(){
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('inventory.inventory_tab.stock_history');
}
public function stockTakenLoad(Request $request){
  
    $columns = array(
            0 =>'id',
            1=>'date',
            2=>'supervisor',
            3=>'view',
            4=>'action',
          
          
        ); 
     $totalData = DB::table('stock_takes as t') 
          ->where('t.lab_id','=',auth()->user()->laboratory_id)
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');
$search = $request->input('search.value');
            $terms = DB::table('stock_takes as t') 
         ->where('t.lab_id','=',auth()->user()->laboratory_id)
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('supervisor_id','LIKE',"%{$search}%")
                   ->orWhere('stock_date','LIKE',"%{$search}%")
                  ->orWhere('is_approved','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.stock_date','desc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {

$supervisor=User::where('id',$term->supervisor_id)->select('name','last_name')->first();

                $nestedData['id']=$x;
                  $nestedData['date']=date('d, M Y',strtotime($term->stock_date));
                    $nestedData['supervisor']= $supervisor->name.'  '.$supervisor->last_name;
                $nestedData['view']="<a class='btn btn-info btn-sm' id='$term->id' onclick='ViewStockTakeDetails(this.id)'><i class='fa fa-eye'></i> View</a> ";
             if($term->is_approved=="no"){
                 $nestedData['action']= "<a class='btn btn-success btn-sm'id='$term->id' onclick='ApproveStockTaken(this.id)' ><i class='fa fa-check'> Approve</i></a> | <a  class='btn btn-danger btn-sm'   id='$term->id' onclick='CancelStockTaken(this.id)'><i class='fa fa-trash'> Cancel</i></a> ";
                 
               }
                if($term->is_approved=="yes"){
                 $nestedData['action']= "<a type='button' id='$term->id' onclick='ViewStockTakeDetails(this.id)'><i class='fa fa-check'> Approved</i></a>";
                }
                 if($term->is_approved=="cancel"){
                 $nestedData['action']= "<a type='button' id='$term->id' onclick='ViewStockTakeDetails(this.id)'><i class='fa fa-check'> Cancelled</i></a>";
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
public function stockViewDetails(Request $request){
  $details=DB::table('users as u')
                ->join('stock_takes as e','e.supervisor_id','=','u.id')
                ->select('name','last_name','signature','stock_date','inventory_area','is_approved','approved_by')
                ->where('e.id',$request->id)->first();
  $approver=User::where('id',$details->approved_by)->select('name','last_name','signature')->first();
$data['supervisor']=$details->name. ' '. $details->last_name;
$data['date']=date('d,M Y',strtotime($details->stock_date));
$data['area']=$details->inventory_area;
$data['signature']=$details->signature;
$data['state']=$details->is_approved;
$name=$approver->name??'';
$last=$approver->last_name??'';
$data['approved_by']=$name.' '.$last;
$data['approver_sign']=$approver->signature??'';
$data['id']=$request->id;
//$data['stock_details']=StockTakeDetails::where('stock_take_id',$request->id)->get();
 $employees=DB::table('users as u')
                ->join('stock_take_employee_involved as e','e.user_id','=','u.id')
                ->select('name','last_name')
                ->where('e.stock_take_id',$request->id)->get();
  $data['employees']=$employees;
 return view('inventory.modal.stock_take_details',$data);

}

public function loadStockTakenDetails(Request $request){

  
$columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'unit_issue',
            4=>'available',
            5=>'physical',
            6=>'status',
            7=>'name',
            8=>'brand',
          
        ); 
     $totalData = DB::table('items as t') 
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('stock_take_details as s','s.item_id','=','i.id')
          ->where('s.stock_take_id','=',$request->id)
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');
$search = $request->input('search.value');

            $terms = DB::table('items as t') 
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('stock_take_details as s','s.item_id','=','i.id')
                  ->select('s.id as id','t.brand','t.item_name','t.code','t.unit_issue','i.batch_number','s.system_quantity','s.physical_count')
          ->where('s.stock_take_id','=',$request->id)
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('i.batch_number','LIKE',"%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.id','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {
              $stock=$term->system_quantity-$term->physical_count;
if($stock>0){
  $status="<span class='badge badge-info'>Underage</span>";
}
if($stock<0){
 $status="<span class='badge badge-danger'>Overage</span>";
}
if($stock==0){
 $status="<span class='badge badge-success'>Balanced</span>";
}


                $nestedData['id']=$x;
                $nestedData['code']=$term->code;
                $nestedData['name']=$term->item_name;
                $nestedData['batch_number']= $term->batch_number;
                $nestedData['brand']= $term->brand;
                $nestedData['unit_issue']=$term->unit_issue;
             
                 $nestedData['available']= $term->system_quantity;
                  $nestedData['physical']= $term->physical_count;
                $nestedData['status']= $status;
                 
               
                 
                 
                   
     
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
public function approveStockTaken(Request $request){

$stockDetails=StockTakeDetails::where('stock_take_id',$request->id)->get();

updateStockTakenJob::dispatch($stockDetails,$request->id);
  return response()->json([
    'message'=>config('stocksentry.stock_taken_approved'),
    'error'=>false,
  ]);
}
public function labViewStockTaken(){
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('provider.inventory.inventory_tabs.lab_stocktake_history');
}

  protected function cancelStockTaken(Request $request){
        StockTake::where('id',$request->id)->update([
            'is_approved'=>'cancel',
            'approved_by'=>auth()->user()->id,
            'updated_at'=>now(),
        ]);
        
          return response()->json([
    'message'=>config('stocksentry.stock_taken_cancelled'),
    'error'=>false,
  ]);
    }

public function itemsLoadSelected(Request $request){

         $columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'brand',
            4=>'name',
            5=>'unit',
            6=>'consumed',
            7=>'status'
           
        ); 
        
        
        $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
              ->whereIn('s.laboratory_id',$request->values)
              ->count();

            

        

  /* $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
              ->where('s.laboratory_id','=',$request->id)
          
              ->count();*/





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
         ->whereIn('s.laboratory_id',$request->values)
          
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {


                $nestedData['item_id']=$term->id;
                $nestedData['id']="<input type='checkbox' id='se_$term->id' name='selected_check' onclick='AddIdToList(this.id)'/>";
                  $nestedData['batch_number']=$term->batch_number;
                    $nestedData['brand']= $term->brand;
                $nestedData['code']=$term->code;
             
                 $nestedData['name']= $term->item_name;
                  $nestedData['unit']= $term->unit_issue;
               
                 $nestedData['consumed'] = "<input type='number'  size='5' id='s_$term->id' min='0' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getPhysicalCount(this.id,this.name)'/>";
                  $nestedData['status']="<button class='btn btn-outline-primary' id='$term->id' onclick='saveConsumed(this.id)'> <i class='fa fa-save' arial-hidden='true' id='fa_$term->id'></i></button>";
                   
     
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
function downloadItemsSelected(Request $request){
if(count($request->labs)==0){
  return redirect()->back();
}
$labs=$request->labs;

/*return response()->json([
  'url'=>route('stock_download',['id'=>$request->id]),
]);*/
$report= DB::table('items as t') 
              ->join('inventories AS s', 's.item_id', '=', 't.id')
          //->select('t.id as id','s.item_name','t.batch_number','t.cost')
             ->whereIn('t.laboratory_id',$labs)->get();
if(count($report)==0){
  return redirect()->back()->with('error','data empty');
}
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
$spreadsheet->getActiveSheet()->setCellValue('E8', 'INVENTORY LIST ');
$spreadsheet->getActiveSheet()->getStyle('E8')->getFont()->setBold(true);

$spreadsheet->getActiveSheet()
   ->setCellValue('A10', 'ULN')
     ->setCellValue('B10', 'CODE')
    ->setCellValue('C10', 'ITEM NAME')
    ->setCellValue('D10', 'BATCH NUMBER')
    ->setCellValue('E10', 'CATALOG NUMBER')
    ->setCellValue('F10', 'HAZARDOUS')
    ->setCellValue('G10', 'UNIT OF ISSUE')
    ->setCellValue('H10', 'STORAGE TEMP.')
    ->setCellValue('I10', 'QUANTITY AVAILABLE')
 ;

$num=11;
  for ($x=0; $x<count($report); $x++){


  $data=[

    [
    $report[$x]->uln,
    $report[$x]->code,
    $report[$x]->item_name,
    $report[$x]->batch_number,
    $report[$x]->catalog_number,
    $report[$x]->is_hazardous,
    $report[$x]->unit_issue,
    $report[$x]->store_temp,
    '',
  ],
 
  ];

$num++;
   // $spreadsheet->getActiveSheet()->getRowDimension($x)->setOutlineLevel(1);
  //  $spreadsheet->getActiveSheet()->getRowDimension($x)->setVisible(false);
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
   $spreadsheet->getActiveSheet()->getStyle('A'.$num.':I'.$num)->getFont()->setBold(true);

 


}


//$spreadsheet->getActiveSheet()->getRowDimension(81)->setCollapsed(true); 
$step=$num+1;
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

$table = new Table('A10:I'.$step, 'Exported');
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
$name='inventory_list.xlsx';

$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
//Update Requsition status
//Requisition::where('is_marked','yes')->update([
 //'is_marked'=>'done',
//]);
//$this->saveConsolidationHistory($orders,$db_name);
return response()->download($path,$name, $headers);
 
}
function download(Request $request, $id){
  $report= DB::table('items as t') 
              ->join('inventories AS s', 's.item_id', '=', 't.id')
          //->select('t.id as id','s.item_name','t.batch_number','t.cost')
             ->where('s.lab_id','=',$id)->get();
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
$spreadsheet->getActiveSheet()->setCellValue('E8', 'INVENTORY LIST ');
$spreadsheet->getActiveSheet()->getStyle('E8')->getFont()->setBold(true);

$spreadsheet->getActiveSheet()
   ->setCellValue('A10', 'ULN')
     ->setCellValue('B10', 'CODE')
    ->setCellValue('C10', 'ITEM NAME')
    ->setCellValue('D10', 'BATCH NUMBER')
    ->setCellValue('E10', 'CATALOG NUMBER')
    ->setCellValue('F10', 'HAZARDOUS')
    ->setCellValue('G10', 'UNIT OF ISSUE')
    ->setCellValue('H10', 'STORAGE TEMP.')
    ->setCellValue('I10', 'QUANTITY AVAILABLE')
 ;

$num=11;
  for ($x=0; $x<count($report); $x++){


  $data=[

    [
    $report[$x]->uln,
    $report[$x]->code,
    $report[$x]->item_name,
    $report[$x]->batch_number,
    $report[$x]->catalog_number,
    $report[$x]->is_hazardous,
    $report[$x]->unit_issue,
    $report[$x]->store_temp,
    '',
  ],
 
  ];

$num++;
   // $spreadsheet->getActiveSheet()->getRowDimension($x)->setOutlineLevel(1);
  //  $spreadsheet->getActiveSheet()->getRowDimension($x)->setVisible(false);
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
   $spreadsheet->getActiveSheet()->getStyle('A'.$num.':I'.$num)->getFont()->setBold(true);

 


}


//$spreadsheet->getActiveSheet()->getRowDimension(81)->setCollapsed(true); 
$step=$num+1;
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

$table = new Table('A10:I'.$step, 'Exported');
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
$name='inventory_list.xlsx';

$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
//Update Requsition status
//Requisition::where('is_marked','yes')->update([
 //'is_marked'=>'done',
//]);
//$this->saveConsolidationHistory($orders,$db_name);
return response()->download($path,$name, $headers);
}
}
