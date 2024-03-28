<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\RequisitionDetails;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\updateStore;
use DB;
use App\Notifications\PendingRequsitionNotification;
use App\Notifications\ApprovedRequestNotification;
use App\Notifications\StoreRequestNotification;
use App\Models\ConsolidateHistory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
   public function exportRequisitionList(Request $request){
      $report =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                   ->leftjoin('requisitions as r','r.id','=','rd.requisition_id')
                  ->select(
                    'i.id as id',
                    'r.id as requisition_id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.is_hazardous',
                    't.unit_issue',
                    't.store_temp',
                    'r.lab_id',
                    'r.requested_by',
                    'r.approved_by',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where([['r.status','=','approved'],['r.is_marked','=','yes']])
                   ->groupBy('t.item_name')
                   ->get();

$orders=Requisition::where([['status','=','approved'],['is_marked','=','yes']])->count();
     // dd($test);
                   if(count($report)==0){
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
$spreadsheet->getActiveSheet()->setCellValue('D8', 'STORE ORDERS CONSOLIDATION LIST ');
$spreadsheet->getActiveSheet()->getStyle('D8')->getFont()->setBold(true);

$spreadsheet->getActiveSheet()
   
    ->setCellValue('A10', 'ULN')
     ->setCellValue('B10', 'CODE')
    ->setCellValue('C10', 'ITEM NAME')
    ->setCellValue('D10', 'CATALOG NUMBER')
    ->setCellValue('E10', 'HAZARDOUS')
    ->setCellValue('F10', 'UNIT OF ISSUE')
    ->setCellValue('G10', 'STORAGE TEMP.')
    ->setCellValue('H10', 'QUANTITY REQUESTED')
   ;

$num=11;
  for ($x=0; $x<count($report); $x++){


  $data=[

    [
    $report[$x]->uln,
    $report[$x]->code,
    $report[$x]->item_name,
    $report[$x]->catalog_number,
    $report[$x]->is_hazardous,
    $report[$x]->unit_issue,
    $report[$x]->store_temp,
    $report[$x]->quantity_requested,
  ],
 
  ];

$num++;
    $spreadsheet->getActiveSheet()->getRowDimension($x)->setOutlineLevel(1);
    $spreadsheet->getActiveSheet()->getRowDimension($x)->setVisible(false);
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
   $spreadsheet->getActiveSheet()->getStyle('A'.$num.':I'.$num)->getFont()->setBold(true);

 



 

$t=$num;
 $ids=explode(',',$report[$x]->requisitions_ids);
    
    $data = array();
    for($i=0;$i<count($ids); $i++){
$record= DB::table('inventories as inv')
                  ->join('requisition_details  as r','r.item_id','=','inv.id')
                  ->join('requisitions as rd','rd.id','=','r.requisition_id')
                  ->join('users as u','u.id','=','rd.requested_by')
                  ->join('users as y','y.id','=','rd.approved_by')
                  ->join('laboratories as l','l.id','=','rd.lab_id')
                  
                  ->select(
                    'rd.sr_number',
                     'rd.id',
                     'rd.section_id',
                     'inv.batch_number',
                    'l.lab_name',
                    'u.name',
                    'u.last_name',
                    'y.name as approved_name',
                    'y.last_name as approved_lastname',
                    'rd.requested_date',
                    'inv.cost',
                    'r.quantity_requested'
                    )->where('r.id',$ids[$i])
                   
                  
                    ->where('rd.status','approved')->get();
                    
 foreach($record as $record)  { 
     if($record->section_id!=NULL){
         $section=LaboratorySection::where('id',$record->section_id)->select('section_name')->first();
         $lab=$record->lab_name.' | '.$section->section_name;
     }
     else{
       $lab=$record->lab_name;   
     }
  $da=[
    [
    'Lab Name:'.$lab,
    'Requested By:'.$record->name,
     'Approved By:'.$record->approved_name,
     'Batch Number:'.$record->batch_number,
  
    ],
    
  ];
$t++;

    $spreadsheet->getActiveSheet()->fromArray($da, null, 'A'.$t);
    $spreadsheet->getActiveSheet()->setCellValue('H'.$t,  $record->quantity_requested);
     $spreadsheet->getActiveSheet()->getStyle('H'.$t)->getFont()->setBold(false);
    
    $spreadsheet->getActiveSheet()->getRowDimension($t)->setOutlineLevel(1);
   $spreadsheet->getActiveSheet()->getRowDimension($t)->setVisible(false);
   
    }
}
$spreadsheet->getActiveSheet()->getRowDimension($t+1)->setCollapsed(true);
$num=$t;
}


//$spreadsheet->getActiveSheet()->getRowDimension(81)->setCollapsed(true); 
$step=$t+1;
$spreadsheet->getActiveSheet()->getRowDimension($step)->setCollapsed(true); 
$spreadsheet->getActiveSheet()->setShowSummaryBelow(false);

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

$table = new Table('A10:H'.$step, 'Exported');
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
//Update Requsition status
Requisition::where('is_marked','yes')->update([
 'is_marked'=>'done',
]);
$this->saveConsolidationHistory($orders,$db_name);
return response()->download($path,$name, $headers);
       
   }
private function saveConsolidationHistory($orders,$db_name){
  $name=auth()->user()->name.' '.auth()->user()->last_name;
  $date=now();
  $consolidated=new ConsolidateHistory();
  $consolidated->consolidated_by=$name;
   $consolidated->date=$date;
   $consolidated->orders=$orders;
   $consolidated->path=$db_name;
   $consolidated->created_at=now();
   $consolidated->updated_at=NULL;
   $consolidated->save();
}
    
public function showConsolidationHistory(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('inventory.modal.consolidation_history');
}

public function loadHistory(Request $request){

   $columns = array(
            0 =>'id',
            1=>'date',
            2=>'consolidated_by',
            3=>'orders',
            4=>'document',
          
        ); 

         $totalData = DB::table('consolidate_histories') 
         ->where('type','=','store')
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
          ->where('type','=','store')
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
public function downloadConsolidatedDocument($id){
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
    

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       $date=date('Y-m-d');

         $columns = array(
            0 =>'id',
            1=>'code',
            2=> 'name',
            3=>'available',
            4=>'quantity',
            5=>'brand',
            6=>'status',
        ); 
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
          ->where('t.lab_id','=',0)
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
              ->select('t.id as id','s.code','t.batch_number','s.item_description','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
         ->where('t.lab_id','=',0)
          ->where('t.expiry_date', '>', date('Y-m-d') )
          ->where('t.quantity','>',0)
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
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
  //$nestedData['id']=$term->id;
                $nestedData['id']="<input type='checkbox' id='$term->id' name='selected_check'  disabled/> ";
                $nestedData['code']=$term->code;
             
                 $nestedData['name']= $term->item_name;
                    $nestedData['available']= $term->quantity;
                 $nestedData['quantity'] = "<input type='number' min='1' id='q_$term->id' size='4' class='form-control' placeholder='Enter Quantity' name='$term->id' onchange='getText(this.id,this.name)'/> <span id='l_$term->id' hidden>Checking...</span>";
                $nestedData['brand']= $term->batch_number;
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




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(array_key_exists('section_id',$request->form_data)){
    
         $section=$request->form_data['section_id'];
       }
       else{
           $section=NULL;
       }
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
$issue=new Requisition();
$issue->sr_number=$request->form_data['sr_number'];
$issue->lab_id	=$request->form_data['lab_id'];
$issue->section_id=$section;
$issue->requested_by=auth()->user()->id;
$issue->approved_by=NULL;
$issue->requested_date=$request->form_data['request_date'];
$issue->created_at=now();
$issue->updated_at=NULL;
$issue->status="not approved";
$issue->save();
$issue_id=$issue->id;
for($i= 0; $i<count($ids); $i++){
$items= explode('_',$request->quantity[$i]);
$issueDetails=new RequisitionDetails();
$issueDetails->requisition_id=$issue_id;
$issueDetails->sr_number=$request->form_data['sr_number'];
$issueDetails->item_id=$ids[$i];
$issueDetails->quantity_requested=$quantities[$i];
$issueDetails->created_at=now();
$issueDetails->updated_at=NULL;
$issueDetails->save();
$number=str_pad($issue_id, 4, '0', STR_PAD_LEFT);
}

DB::commit();

$approvers=User::where([['authority','=',2],['laboratory_id','=',auth()->user()->laboratory_id]])->get();
$requested_by=auth()->user()->name.' '.auth()->user()->last_name;
$request_no=$request->form_data['sr_number'];
foreach($approvers as $user){

  $user->notify(new PendingRequsitionNotification($request_no,$requested_by))
}
$test="Request  has been made, pending Approval";
  return response([
'message'=> $test,
'sr_number'=>'SR '.$number,
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

    /**
     * Display the specified resource.
     */
    public function show(Request $request,Requisition $requisition)
    {
         
    $columns = array(
            0 =>'sr',
            1=>'request_lab',
            2=>'request_date',
            3=>'options',
            4=>'marked',
          
        ); 

         $totalData = DB::table('requisitions') 
          ->where('status','=','approved')
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('requisitions') 
          ->where('status','=','approved')
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('sr_number', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('id','desc')
            ->get();

          $totalFiltered =  $totalRec ;




        $data = array();
          if (!empty($terms)) {
$x=1;
  $section_name="";

            foreach ($terms as $term) {
$lab=Laboratory::where('id',$term->lab_id)->select('lab_name','has_section')->first();

if($lab->has_section=="yes"){
$section=LaboratorySection::where('id',$term->section_id)->select('section_name')->first();

$section_name=$section->section_name??'';

}
else{
  $section_name='';
}
                $nestedData['sr']=$term->sr_number;
                $nestedData['request_lab']=$lab->lab_name.' | '.$section_name;
                $nestedData['request_date']= date('d, M Y',strtotime($term->requested_date));
             
                $nestedData['options']= " <a class='btn btn-primary btn-sm' id='$term->id' onclick='AcceptApprovedRequest(this.id)'><i class='fa fa-check'></i> Accept</a> |  <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewApprovedRequest(this.id)'><i class='fa fa-eye'></i> View</a>  ";
      switch($term->is_marked){
            case 'no':
                $nestedData['marked']="<a class='btn btn-success btn-sm'   id='$term->id' onclick='MarkForConsolidation(this.id)' ><i class='fa fa-plus'></i> add</a> ";
              break;
             
           case 'yes':
             
 $nestedData['marked']="<a class='btn btn-secondary btn-sm'   id='$term->id' onclick='MarkForConsolidation(this.id)' ><i class='fa fa-minus'></i> Remove </a> ";
            break;
          case 'done':
  $nestedData['marked']="<span class='badge badge-info'>Consolidated</span> ";
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

    /**
     * Show the form for editing the specified resource.
     */
    public function viewRequest(Request $request )
    {
       
$requisition=DB::table('requisitions as r')
                  ->join('users as u','u.id','=','r.requested_by')
                  ->join('laboratories as l','l.id','=','r.lab_id')
                  ->select('r.status','u.name','u.last_name','u.signature','r.sr_number','r.section_id','l.lab_name','r.requested_date','r.approved_by')
                  ->where('r.id',$request->id)
                  ->first();
$data['requested_by']=$requisition->name.' '.$requisition->last_name;
$data['request_signature']=$requisition->signature??'';
$data['status']= $requisition->status;
$data['sr']=$requisition->sr_number;
$data['id']=$request->id;
$data['date_requested']=date('d, M Y',strtotime($requisition->requested_date));
$data['signature']=$requisition->signature;
$approver=User::where('id',$requisition->approved_by)->select('name','last_name','signature')->first();
if($approver){
$data['approved_by']=$approver->name.' '.$approver->last_name;
$data['approver_sign']=$approver->signature??'';
}
$section=LaboratorySection::where('id',$requisition->section_id)->select('section_name')->first();
if($section){
    $section_name=$section->section_name;
    $data['lab']=$requisition->lab_name.'/'.$section_name;
    
}
else{
   $data['lab']=$requisition->lab_name;  
}


/* $data['print_data']= DB::table('items as t') 
              ->join('inventories AS i', 'i.item_id', '=', 't.id')
              ->select('i.id as id','t.item_name','t.unit_issue','i.quantity','i.batch_number','i.cost')
              ->where('i.grn_number',$request->id)
              // ->groupBy('t.item_name')
              ->get(); */
    $data['requests']=DB::table('items as itm')
     //
      ->join('inventories  as d','d.item_id','=','itm.id')
      ->join('requisition_details as iss','iss.item_id','=','d.id')
       ->join('requisitions as inv','inv.id','=','iss.requisition_id')
      ->select('iss.sr_number','itm.item_name','itm.unit_issue','iss.quantity_requested','d.cost','d.batch_number')
     ->where([['inv.id','=',$request->id],['inv.lab_id','=',auth()->user()->laboratory_id]])
     // ->groupBy('.id')
     
      ->get();


  return view('provider.issues.modals.view_request',$data);
    }

    public function viewApprovedRequest(Request $request )
    {
       
$requisition=DB::table('requisitions as r')
                  ->join('users as u','u.id','=','r.approved_by')
                    ->join('laboratories as l','l.id','=','r.lab_id')
                  ->select('r.status','u.name','u.last_name','u.signature','r.sr_number','r.section_id','l.lab_name','r.requested_date','r.requested_by','r.approved_by')
                  ->where('r.id',$request->id)
                  ->first();
$data['approved_by']=$requisition->name.' '.$requisition->last_name;
$data['approver_sign']=$requisition->signature??'';


$data['status']= $requisition->status;
$data['sr']=$requisition->sr_number;
$data['id']=$request->id;
$data['date_requested']=date('d, M Y',strtotime($requisition->requested_date));
$data['signature']=$requisition->signature;
$requested=User::where('id',$requisition->requested_by)->select('name','last_name','signature')->first();
if($requested){
$data['requested_by']=$requested->name.' '.$requested->last_name;
$data['request_signature']=$requested->signature??'';
}
else{
  $data['requested_by']='';
$data['requested_by']='';  
}
$section=LaboratorySection::where('id',$requisition->section_id)->select('section_name')->first();
if($section){
    $section_name=$section->section_name;
    $data['lab']=$requisition->lab_name.'/'.$section_name;
    
}
else{
   $data['lab']=$requisition->lab_name;  
}
  $data['requests']=DB::table('requisitions as itm')
     //
      ->join('requisition_details  as d','itm.id','=','d.requisition_id')
       ->join('inventories as inv','inv.id','=','d.item_id')
       ->join('items as iss','iss.id','=','inv.item_id')
      ->select('itm.sr_number','iss.item_name','iss.unit_issue','d.quantity_requested','inv.cost','inv.batch_number')
     ->where('itm.id','=',$request->id)
      ->groupBy('d.item_id')
     
      ->get();


  return view('provider.issues.modals.view_request',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
     Requisition::where('id',$request->id)
  ->update([
'status'=>'approved',
'approved_by'=>auth()->user()->id,
'updated_at'=>now()
  ]);
  $lab=Laboratory::where('id',auth()->user()->laboratory_id)->first();
  $requested_by=Requisition::where('id',$request->id)->select('requested_by','sr_number')->first();
  $user=User::where('id',auth()->user()->id)->select('name','last_name')->first();
  $approved_by=$user->name.' '.$user->last_name;
  $user->notify(new ApprovedRequestNotification($requested_by->sr_number,$approved_by));
  $store_users=User::where([['laboratory_id','=',0],['authority','=',1]])->get();

  for($store_users as $user){
    $user->notify(new StoreRequestNotification($requested_by->sr_number,$approved_by,$lab->lab_name));
  }
  $requisition= Requisition::where([['lab_id','=',auth()->user()->laboratory_id],['status','=','approved']])->count();
  return response()->json([
'message'=>"Order  been  approved  successfully",
'count'=>$requisition,
  ]);


    }
public function updateApproved(Request $request){
  $data['lab_requested'] = Requisition::where('id',$request->id)->select('id','sr_number','lab_id','section_id')->first();
 $data['requisition'] = RequisitionDetails::where('requisition_id',$request->id)->select('item_id','quantity_requested')->get();
try{
  //dd($requisition);
 updateStore::dispatch($data);
 return response()->json([
  'message'=>"Order has been processed successfully",
  'error'=>false
 ]);
}
catch(Exception $e){
  return response()->json([
    'message'=>$e.message(),
    'error'=>true,
  ]);
}
 /*  $requisition= Requisition ::where([['lab_id','=',auth()->user()->laboratory_id],['status','=','not approved']])->count();
  return response()->json([
'message'=>"Order  been  approved  successfully",
'count'=>$requisition,
  ]); */

}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
       Requisition::where('id',$request->id)
  ->update([
'status'=>'void',
'approved_by'=>auth()->user()->id,
'updated_at'=>now()
  ]);
  $requisition= Requisition ::where([['lab_id','=',auth()->user()->laboratory_id],['status','=','not approved']])->count();
  return response()->json([
'message'=>"Order  been  Cancelled Successfully",
'count'=>$requisition,
  ]);

    }

public function getSelectedItems(Request $request){

$ids=array();
$requested=array();
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
             ->where([['t.lab_id','=',0],['t.id','=',$ids[$i]]])->first();

$data[]=$dat;


}
return response()->json([
  'data'=>$data,
  'quantity'=>$requested,
]);
}
public function pendingRequests(Request $request){
  return view('provider.issues.modals.requested_items_modal');
}
public function loadRequests(Request $request){
  
 $columns = array(
            0=>'id',
            1 =>'sr_number',
            2=>'lab_id',
            3=>'requested_by',
            4=>'requested_date',
            5=>'status'
          
        ); 

         $totalData = DB::table('requisitions') 
          ->where('status','=','not approved')
            ->where('lab_id', '=', auth()->user()->laboratory_id )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('requisitions') 
          ->where('status','=','not approved')
           ->where('lab_id', '=', auth()->user()->laboratory_id )
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('sr_number', 'LIKE', "%{$search}%");
                 
                      
                     
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

$user=User::where('id',$term->requested_by)->select('name','last_name')->first();
$lab=Laboratory::where('id',$term->lab_id)->select('lab_name')->first();
$section=LaboratorySection::where('id',$term->section_id)->select('section_name')->first();
 $nestedData['id']=$term->id;
                $nestedData['sr_number']=$term->sr_number;
                $nestedData['lab_id']=$lab->lab_name.'|';
                $nestedData['requested_by']= $user->name.' '.$user->last_name;
                $nestedData['requested_date']= $term->requested_date;
                $nestedData['status']= $term->status=='not approved' ?"<a class='btn btn-success btn-sm' id='$term->id' onclick='ApproveRequest(this.id)'><i class='fa fa-check'></i> Approve</a> |<a class='btn btn-danger btn-sm'   id='$term->id' onclick='VoidRequest(this.id)'><i class='fa fa-times'></i> Void</a> |" : "";
                $nestedData['status'].= " <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewRequest(this.id)'><i class='fa fa-eye'></i> View</a>  ";
              
             
             
               
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

//load approved
public function loadApproved(Request $request){
  
 $columns = array(
            0=>'id',
            1 =>'sr_number',
            2=>'lab_id',
            3=>'requested_by',
            4=>'requested_date',
            5=>'status'
          
        ); 

         $totalData = DB::table('requisitions') 
          ->where('status','=','approved')
            ->where('lab_id', '=', auth()->user()->laboratory_id )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('requisitions') 
          ->where('status','=','approved')
           ->where('lab_id', '=', auth()->user()->laboratory_id )
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('sr_number', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('requested_date','desc')
            ->get();

          $totalFiltered =  $totalRec ;




        $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {

$user=User::where('id',$term->approved_by)->select('name','last_name')->first();
$lab=Laboratory::where('id',$term->lab_id)->select('lab_name')->first();
$section=LaboratorySection::where('id',$term->section_id)->select('section_name')->first();
 $nestedData['id']=$term->id;
                $nestedData['sr_number']=$term->sr_number;
                $nestedData['lab_id']=$lab->lab_name.'|';
                $nestedData['requested_by']= $user->name.' '.$user->last_name;
                $nestedData['requested_date']= $term->requested_date;
                $nestedData['status']= " <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewRequest(this.id)'><i class='fa fa-eye'></i> View</a>  ";
             
             
               
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
public function searchRequisition(Request $request){
 
$columns = array(
            0 =>'sr',
            1=>'request_lab',
            2=>'request_date',
            3=>'options',
            4=>'marked'
        ); 
        switch ($request->type) {
          case 'SRNUMBER':
            $totalData = DB::table('requisitions') 
          ->where('status','=','approved')
            ->where('sr_number', 'LIKE', "%{$request->value}%")
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('requisitions') 
          ->where('status','=','approved')
       ->where('sr_number', 'LIKE',"%{$request->value}%" )
            
            ->offset($start)
            ->limit($limit)
            ->orderBy('requested_date','desc')
            ->get();
            break;

        case 'LAB' :
 $totalData = DB::table('requisitions') 
          ->where('status','=','approved')
            ->where('lab_id', '=', $request->value)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('requisitions') 
          ->where('status','=','approved')
       ->where('lab_id', '=',$request->value )
            
            ->offset($start)
            ->limit($limit)
            ->orderBy('requested_date','desc')
            ->get();
        
            break;
         case 'SECTION':
$totalData = DB::table('requisitions') 
          ->where('status','=','approved')
            ->where([['lab_id','=',$request->lab_id],['section_id', '=', $request->value]])
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('requisitions') 
          ->where('status','=','approved')
    ->where([['lab_id','=',$request->lab_id],['section_id', '=', $request->value]])
            
            ->offset($start)
            ->limit($limit)
            ->orderBy('requested_date','desc')
            ->get();
         
          break;
          
        }



          $totalFiltered =  $totalRec ;




        $data = array();
          if (!empty($terms)) {
$x=1;
  $section_name="";

            foreach ($terms as $term) {

$lab=Laboratory::where('id',$term->lab_id)->select('lab_name')->first();
$section=LaboratorySection::where('id',$term->section_id)->select('section_name')->first();

if($section){
$section_name=$section->section_name;
}

                $nestedData['sr']=$term->sr_number;
                $nestedData['request_lab']=$lab->lab_name.' | '.$section_name;
                $nestedData['request_date']= $term->requested_date;
             
                $nestedData['options']= " <a class='btn btn-success btn-sm' id='$term->id' onclick='AcceptApprovedRequest(this.id)'><i class='fa fa-check'></i> Accept</a> |<a class='btn btn-danger btn-sm'   id='$term->id' onclick='VoidRequest(this.id)' hidden><i class='fa fa-times'></i> Void</a> |<a class='btn btn-info btn-sm' id='$term->id' onclick='ViewApprovedRequest(this.id)'><i class='fa fa-eye'></i> View</a>  ";
              
               switch($term->is_marked){
            case 'no':
                $nestedData['marked']="<a class='btn btn-success btn-sm'   id='$term->id' onclick='MarkForConsolidation(this.id)' ><i class='fa fa-plus'></i> add</a> ";
              break;
             
           case 'yes':
             
 $nestedData['marked']="<a class='btn btn-secondary btn-sm'   id='$term->id' onclick='MarkForConsolidation(this.id)' ><i class='fa fa-minus'></i> Remove </a> ";
            break;
          case 'done':
  $nestedData['marked']="<span class='badge badge-info'>Consolidated</span> ";
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
public function viewList(Request $request){
  return view('provider.issues.modals.requisition_list');
}
public function loadRequsitionList(Request $request){
 $columns = array(
            0=>'id',
            1 =>'sr_number',
            2=>'lab_id',
            3=>'requested_by',
            4=>'requested_date',
            5=>'status',
            6=>'view'

          
        ); 

         $totalData = DB::table('requisitions') 
      
            ->where('lab_id', '=', auth()->user()->laboratory_id )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('requisitions') 
    
           ->where('lab_id', '=', auth()->user()->laboratory_id )
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('sr_number', 'LIKE', "%{$search}%");
                 
                      
                     
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

$user=User::where('id',$term->requested_by)->select('name','last_name')->first();
$lab=Laboratory::where('id',$term->lab_id)->select('lab_name')->first();
$section=LaboratorySection::where('id',$term->section_id)->select('section_name')->first();
if($section!=NULL){
    $lab_name=$lab->lab_name.'|'.$section->section_name;
    
}
else{
    $lab_name=$lab->lab_name; 
}
 $nestedData['id']=$term->id;
                $nestedData['sr_number']=$term->sr_number;
                $nestedData['lab_id']=$lab_name;
                $nestedData['requested_by']= $user->name.' '.$user->last_name;
                $nestedData['requested_date']= $term->requested_date;
                $nestedData['status']=$term->status;
                $nestedData['view']= " <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewRequest(this.id)'><i class='fa fa-eye'></i> ViewS</a>  ";
              
             
             
               
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
public function showApproved(){
    return view('provider.issues.modals.approved_items_modal');
}

public function getLabSections(Request $request){
    $lab=Laboratory::where('id',$request->id)->select('has_section')->first();
    if($lab->has_section=="yes"){
        $sections=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->select('s.id as id','s.section_name')
                    ->where('l.lab_id',$request->id)->get();

return response()->json([
            'sections'=>$sections,
            'status'=>0
        ]);
    }
    else{
        return response()->json([
            'message'=>"",
            'status'=>1
        ]);
    }
}
public function consolidateOrders(Request $request){
return view('inventory.modal.view_marked_consolidate');
}


public function orderItemList(){
  return view ('inventory.modal.load_request_selection_modal');
}

public function markToConsolidate(Request $request){
  $requisition=Requisition::where('id',$request->id)->select('is_marked')->first();
  if($requisition->is_marked=='no'){
    Requisition::where('id',$request->id)->update([
      'is_marked'=>'yes'
    ]);
  }
  else{
     Requisition::where('id',$request->id)->update([
      'is_marked'=>'no'
    ]);
  }
  return response()->json([
    'message'=>"oky",
    'error' =>false
  ]);
}

public function removeToConsolidate(Request $request){


     Requisition::where('id',$request->id)->update([
      'is_marked'=>'no'
    ]);
  
  return response()->json([
    'message'=>"oky",
    'error' =>false
  ]);
}
public function showRequisitions(Request $request){

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
          
        ); 

        
          $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                  ->select(
                    'i.id as id',
                    'r.id as requisition_id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.is_hazardous',
                    't.unit_issue',
                    't.store_temp',
                    'r.lab_id',
                    'r.requested_by',
                    'r.approved_by',
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where([['r.status','=','approved'],['r.is_marked','=','yes']])
                   ->groupBy('t.item_name')
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
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->leftjoin('requisitions as r','r.id','=','rd.requisition_id')
                  ->select(
                    'i.id as id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.is_hazardous',
                    't.unit_issue',
                    't.store_temp',
                    'r.lab_id',
                    'r.requested_by',
                    'r.approved_by',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where([['r.status','=','approved'],['r.is_marked','=','yes']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;


        $data = array();
          if (!empty($terms)) {
$x=1;


            foreach ($terms as $term) {

 $nestedData['item_id']=$term->requisitions_ids;
                $nestedData['id']=$term->id;
                   $nestedData['code']=$term->code;
                $nestedData['item_name']=$term->item_name;
                $nestedData['batch_number']= $term->batch_number;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= '<strong>'.$term->quantity_requested.'</strong>';
                $nestedData['options']= " <a class='btn btn-danger btn-sm'   id='$term->id' onclick='RemoveForConsolidation(this.id)' ><i class='fa fa-trash'></i> remove</a> ";
      
                
               
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
public function requisitionGetData(Request $request){
   // dd($request->id);
    $ids=explode(',',$request->id);
    
    $data = array();
    for($i=0;$i<count($ids); $i++){
$record= DB::table('inventories as inv')
                  ->join('requisition_details  as r','r.item_id','=','inv.id')
                  ->join('requisitions as rd','rd.id','=','r.requisition_id')
                  ->join('users as u','u.id','=','rd.requested_by')
                  ->join('users as y','y.id','=','rd.approved_by')
                  ->join('laboratories as l','l.id','=','rd.lab_id')
                  
                  ->select(
                    'rd.sr_number',
                     'rd.id',
                     'rd.section_id',
                    'l.lab_name',
                    'u.name',
                    'u.last_name',
                    'y.name as approved_name',
                    'y.last_name as approved_lastname',
                    'rd.requested_date',
                    'inv.batch_number',
                    'inv.cost',
                    'r.quantity_requested'
                    )->where('r.id',$ids[$i])
                   
                  
                    ->where('rd.status','approved')->get();
                    
             
             foreach($record as $r)  { 
  //dd($record);
   if($r->section_id!=NULL){
         $section=LaboratorySection::where('id',$r->section_id)->select('section_name')->first();
         $lab=$r->lab_name.' | '.$section->section_name;
     }
     else{
       $lab=$r->lab_name;   
     }
  $nested['sr_number']=$r->sr_number;
   $nested['lab_name']=$lab;
   $nested['name']=$r->name;
  $nested['last_name']=$r->last_name;
  $nested['approved_name']=$r->approved_name;
  $nested['approved_lastname']=$r->approved_lastname;
  $nested['requested_date']=$r->requested_date;
  $nested['cost']=$r->cost;
  $nested['batch_number']=$r->batch_number;
$nested['quantity_requested']=$r->quantity_requested;
$data[]= $nested;

}
} 
//dd($data);
  return response()->json([
   'data'=>$data,
  ]);
}
}