<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratory;
use App\Models\ScheduleReport;
use App\Models\User;
use App\Models\LaboratorySection;
use App\Models\Supplier;
use DB;
use PDF;
use Carbon\Carbon;
use DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LabManagerTableReportController extends Controller
{
    //

 public function showLabReport(){
          $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.reports.show',$data);
}



    public function showExpiryReport(Request $request){
             $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
           
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
return view('provider.reports.list.expiry',$data);
    }

    public function loadExpiryTable(Request $request){
            if($request->selected){
           
         $this->getTableData($request);
        }
        else{
       $date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays(90);
    $from_date=date('Y-m-d');
         $columns = array(
         0 =>'id',
            1=>'item',
            2=>'batch_number',
            3=>'expire_date',
            4=>'quantity',
            5=>'cost',
            6=>'est_loss',
            7=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')

              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
         ->where('t.lab_id',auth()->user()->laboratory_id)
           ->where('t.quantity','>',0)
             ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
         ->where('t.lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))
          //->where('t.expiry_date', '<', $date )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->groupBy('s.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->item_name;
              
                    $nestedData['batch_number']= $term->batch_number;
                // $nestedData['name']= $term->item_name;
                    //$nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
               $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 if($diff_in_days<0){
                    $nestedData['status']="<span class='text-danger'>  Expired</span>";  
                 }
                  if($diff_in_days==0){
                    $nestedData['status']="<span class='text-danger'> Expiring Today</span>";  
                 }
                elseif( $diff_in_days>=1 && $diff_in_days <30){
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
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);

} 
}
  private function getTableData($request) {
     // dd($request);
    parse_str($request->expiry_form,$out);
 $expiry_details= $out;
  //dd($expiry_details['lab']);
         $columns = array(
          0 =>'id',
            1=>'item',
            2=>'batch_number',
            3=>'expire_date',
            4=>'quantity',
            5=>'cost',
            6=>'est_loss',
            7=>'status',
        ); 
   if($expiry_details['lab']!=99 ||  $expiry_details['period']!=99){
    switch ($expiry_details['period']) {
        case 1:
           
              $from_date=  date('Y-m-d', strtotime("-30 days"));
           
                $to_date=date('Y-m-d');
           
            break;
        case 2:
            $days=30;
               $to_date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($days);
                 $from_date=date('Y-m-d');
            break;
        case 3:
            $days=60;
                $to_date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($days);
                 $from_date=date('Y-m-d');
            break;
        case 4:
            $days=90;
        $to_date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($days);
         $from_date=date('Y-m-d');
            break;
        default:
        $days=90;
            $to_date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($days);
             $from_date=date('Y-m-d');
    }
  
   
      $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
         ->where('t.lab_id','=',$expiry_details['lab'])
             ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $to_date))
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
          ->where('t.lab_id','=',$expiry_details['lab'])
        ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $to_date))
          //->where('t.expiry_date', '<', $date )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
           $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->item_name;
               
                    $nestedData['batch_number']= $term->batch_number;
                 //$nestedData['name']= $term->item_name;
                    //$nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
           
                 switch ($expiry_details['period']) {
                  case 1:
                     $to =strtotime( $term->expiry_date);
      $from = strtotime(date('Y-m-d'));
                 $diff_in_days =(($to-$from)/86400);
            
                    break;
                  
                  default:
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                    break;
                 }
                 if($diff_in_days < 0){
                    $nestedData['status']="<span class='text-danger'>  Expired</span>";  
                 }
                 elseif($diff_in_days==0){
  $nestedData['status']="<span class='text-danger'>  Expiring Today</span>"; 
                 }
                elseif( $diff_in_days>1 && $diff_in_days <30){
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
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);
   
    }
    //end if
    else{

         $date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays(90);
    $from_date=date('Y-m-d');
         $columns = array(
            0 =>'id',
            1=>'item',
            2=>'batch_number',
            3=>'expire_date',
            4=>'quantity',
            5=>'cost',
            6=>'est_loss',
            7=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
         ->where('t.lab_id',auth()->user()->laboratory_id)
             ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
        ->where('t.lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))
          //->where('t.expiry_date', '<', $date )
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->item_name;
                    $nestedData['batch_number']= $term->batch_number;
                 
                    //$nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
                    switch ($expiry_details['period']) {
                  case 1:
                     $to =strtotime( $term->expiry_date);
      $from = strtotime(date('Y-m-d'));
                 $diff_in_days =(($to-$from)/86400);
            
                    break;
                  
                  default:
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                    break;
                 }
                 if($diff_in_days <1){
                    $nestedData['status']="<span class='text-danger'>  Expired</span>";  
                 }
                elseif( $diff_in_days >=1 && $diff_in_days <30){
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
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);
    }






   ///finalisze
  
    
  } 
  public function scheduleReport(Request $request, $type){
   
    try{
      DB::beginTransaction();
   $schedule= new  ScheduleReport();
   $schedule->user_id = auth()->user()->id;
   $schedule->type=$type;
   $schedule->start_date= $request->start_date;
   $schedule->next_run_date=NULL;
   $schedule->time_run=$request->time;
   $schedule->frequency=$request->frequency;
   $schedule->attach_as= $request->attach_as;
   $schedule->email_list= json_encode($request->employee_involved);
   $schedule->created_at=now();
   $schedule->updated_at=NULL;
   $schedule->save();
  DB::commit();
  return  redirect()->back()->with('success', "Scheduled successfully.");
    }
    catch(Exception $e){
      DB::rollback();
        return  redirect()->back()->with('message',"Failed");
    }
  }

  public function downloadReport(Request $request){


          $date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays(90);
            
     $from_date=date('Y-m-d');
      
        //  dd("done");

            $report = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
         ->where('t.lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))->get();
          //->where('t.expiry_date', '<', $date )

          //$path=public_path('reports').'/Inventory Expiry.pdf';
        
   // $pdf=PDF::loadView('pdf.reports.expiry_report',['info'=>$report]);
     // dd("done");
  

   if($request->action=="download"){
       $path=public_path('reports').'/Inventory Expiry.pdf';
        
    $pdf=PDF::loadView('pdf.reports.expiry_report',['info'=>$report]);
                  return $pdf->download("Inventory Expiry.pdf"); 
}
if($request->action=="print"){
 $pdf=PDF::loadView('pdf.reports.expiry_report',['info'=>$report]);
                  return $pdf->stream();
}
if($request->action=="excel"){
$spreadsheet = new Spreadsheet();

        
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('cathebert muyila')
    ->setTitle('Inventory Expiry Report')
    ->setSubject('Inventory Expiry')
    ->setDescription('Inventory Expiry Report')
    ->setKeywords('Expiry Inventory')
    ->setCategory('Table');

// Create the worksheet

    

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A3', 'Code')
    ->setCellValue('B3', 'Brand')
    ->setCellValue('C3', 'Batch Number')
    ->setCellValue('D3', 'Expiration')
    ->setCellValue('E3', 'Quantity')
    ->setCellValue('F3', 'Cost')
    ->setCellValue('G3', 'Estimated Loss');

$num=4;
$total=0;
$overall_total=0;
  for ($x=0; $x<count($report); $x++){
$total=$report[$x]->cost*$report[$x]->quantity;
$overall_total=$overall_total+$total;
  $data=[

    [$report[$x]->code,$report[$x]->brand, $report[$x]->batch_number,$report[$x]->expiry_date,$report[$x]->quantity,$report[$x]->cost,$total]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
$spreadsheet->getActiveSheet()
    ->setCellValue('A'.$step, 'Total');
  $spreadsheet->getActiveSheet()->getStyle('A'.$step)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()
    ->setCellValue('G'.$step, $overall_total);
    $spreadsheet->getActiveSheet()->getStyle('G'.$step)->getNumberFormat()
    ->setFormatCode('#,##0.00');
    $spreadsheet->getActiveSheet()->getStyle('G'.$step)->getFont()->setBold(true);

// Create Table

$table = new Table('A3:G'.$num, 'About_To_Expire_Data');

// Create Columns
$table->getColumn('G')->setShowFilterButton(false);
$table->getAutoFilter()->getColumn('A')
    ->setFilterType(AutoFilter\Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER)
    ->createRule()
    ->setRule(AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL, 2011)
    ->setRuleType(AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

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
$writer->save(public_path('reports').'/expiry_report.xlsx');
$path=public_path('reports').'/expiry_report.xlsx';
$name='expiry_report.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 

return response()->download($path,$name, $headers);
}
   
  }

  public function showIssueReport(Request $request){
     $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.reports.list.issue',$data);
  }



 public function loadIssueTable(Request $request){
     
  $lab=auth()->user()->laboratory_id;
         $columns = array(
            0 =>'id',
            1=>'siv',
            2=>'from_lab',
            3=>'to_lab',
            4=>'issued_by',
            5=>'approved_by',
            6=>'received_by',
            7=>'issue_date',
            8=>'status',
           
        ); 
    
   $totalData = DB::table('issues as t') 
              //->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
            ->where('t.from_lab_id',$lab)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('issues as t') 
             // ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->where('t.from_lab_id',$lab)
              ->select(
                 't.id as id',
                 't.siv_number',
                 't.issued_by' ,
                 't.from_lab_id',
                 't.from_section_id',
                 't.to_lab_id  as to_lab',
                 't.to_section_id',
                 't.approved_by',
                 't.received_by',
                
                 't.issuing_date',
                 't.approve_status')
                    //->where('t.expiry_date', '<', $date )
                ->where(function ($query) use ($search){
                  return  $query->where('t.siv_number', 'LIKE', "%{$search}%")
                  ->orWhere('t.issuing_date','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.issuing_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 


            foreach ($terms as $term) {
//$route=route('siv-details',['siv'=>$term->id]);
$user=User::where('id',$term->issued_by)->select('name','last_name','id')->first();
if($term->approved_by!=NULL){
    $approver=User::where('id',$term->approved_by)->select('name','last_name')->first();
    $approver_name=$approver->name." ".$approver->lab_name;
}
else{
    $approver_name="";
}
$from_lab=Laboratory::where('id',$term->from_lab_id)->select('lab_name')->first();
$from_section_id=LaboratorySection::where('id',$term->from_section_id)->select('section_name')->first();
$to_lab=Laboratory::where('id',$term->to_lab)->select('lab_name')->first();
$to_section=LaboratorySection::where('id',$term->from_section_id)->select('section_name')->first();
if(!$from_section_id){
    $lab=$from_lab->lab_name;

}
else{
  $lab=$from_lab->lab_name.'/'.$from_section_id->section_name;  
}
if(!$to_section){
    $sentTo=$to_lab->lab_name;
}
else{
    $sentTo=$to_lab->lab_name.'/'.$to_section->section_name;
}

                $nestedData['id']=$x;
                $nestedData['siv']="<a id='$term->siv_number' type='button' href='#' onclick='showTransfer(this.id)'>$term->siv_number</a>";
                $nestedData['from_lab']=$lab;

                $nestedData['to_lab']= $sentTo;
                $nestedData['issued_by']= $user->name.' '.$user->last_name;
                $nestedData['issue_date']= $term->issuing_date;
                $nestedData['status'] = $term->approve_status;
             
               
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
public function showConsumptionReport(){
     $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.reports.list.consumption_report',$data);
}

public  function loadConsumptionTable(Request $request){
   
         $columns = array(
            0 =>'id',
            1=>'item_name',
            2=>'catalog_number',
            3=>'unit_issue',
            4=>'consumed',
         
           
        );

          $totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
              ->where('c.lab_id',auth()->user()->laboratory_id)     
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
          //->where('t.expiry_date', '<', $date )
              ->where('c.lab_id',auth()->user()->laboratory_id)
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 


            foreach ($terms as $term) {



               $nestedData['item_id']= $term->item_id;
                $nestedData['id']=$x;
                $nestedData['item_name']=$term->item_name;
               
                $nestedData['catalog_number']= $term->catalog_number;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['consumed']= $term->consumed_quantity;
                
             
               
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
public function consumptionMoreDetails(Request $request){
   
    if($request->period!=-1){
switch($request->period){
    //today
    case 0:
    $date=date('Y-m-d');

$consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->where('rd.created_at','=',$date)
                  ->where('t.id',$request->id)

                   ->get();
    break;
    //yesterday
case 1:
//format 
   $date=date('Y-m-d', strtotime("-1 days"));

   $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->where('rd.created_at','=',$date)
                  ->where('t.id',$request->id)
                   ->get();
        break;
//this week
case 2:

$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();
   $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();
  
break;
//this month
case 3:
 $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');

 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();

break;

//this quarter
case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');

 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();

break;
//this year
case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');

 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();
break;
//previous week
case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();

$consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();
break;
//previous month
case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();
break;
//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();    

break;
//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();    

break;

case 10:
$start=Carbon::createFromFormat('Y-m-d', $request->start_date);
$end=Carbon::createFromFormat('Y-m-d',$request->end_date);
$consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )->whereBetween('rd.created_at', [$start, $end])
                  ->where('t.id',$request->id)
                   ->get();  
break;
}
    }
    else{
    $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$request->id)
                   ->get();
               }
$x=1;
         $data= array();
             foreach($consum as $r)  { 
  
   if($r->section_id!=NULL){
         $section=LaboratorySection::where('id',$r->section_id)->select('section_name')->first();
         $lab=$r->lab_name.' | '.$section->section_name;
     }
     else{
       $lab=$r->lab_name;   
     }
   $nested['id']=$x;
   $nested['lab_name']=$lab;
   $nested['batch_number']=$r->batch_number;
  $nested['consumed']=$r->consumed_quantity;
  
$data[]= $nested;
$x++;
}

 return response()->json([
   'data'=>$data,
  ]);               
}

public function filterConsumption(Request $request){
    //dd($request->value);
      $columns = array(
            0 =>'id',
            1=>'item_name',
            2=>'catalog_number',
            3=>'unit_issue',
            4=>'consumed',
         
           
        );
   switch($request->value){

    //today
case 0:

$date=Carbon::now()->format('Y-m-d');
 

          $totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id') 
               ->where('c.created_at', '=', $date )  
               ->where('c.lab_id',auth()->user()->laboratory_id)   
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                'c.created_at',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
          ->where('c.created_at', '=', $date )
          ->where('c.lab_id',auth()->user()->laboratory_id)
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

          $totalFiltered =  $totalRec ;

    break;
//yesterday consumption
case 1:
$date=date('Y-m-d', strtotime("-1 days"));

//dd($date);
 

          $totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->where('c.created_at', '=', date('Y-m-d', strtotime("-1 days")))   
               ->where('c.lab_id',auth()->user()->laboratory_id) 
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                'c.created_at',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
          ->where('c.created_at', '=', date('Y-m-d', strtotime("-1 days")))
           ->where('c.lab_id',auth()->user()->laboratory_id) 
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;
break;

// this week
case 2:
///previous week $start = Carbon::now()->subWeek()->startOfWeek();
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();

  $totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->where('c.lab_id',auth()->user()->laboratory_id) 
               ->whereBetween('c.created_at', [$start, $end])

              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
                  ->whereBetween('c.created_at', [$start, $end])
              ->where('c.lab_id',auth()->user()->laboratory_id) 
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;

break;

//this month
case 3:

 $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');

  $totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->whereBetween('c.created_at', [$start, $end])
                ->where('c.lab_id',auth()->user()->laboratory_id) 
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
                  ->whereBetween('c.created_at', [$start, $end])
 ->where('c.lab_id',auth()->user()->laboratory_id) 
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;


break;

case 4:
//quarter 
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');

  $totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->whereBetween('c.created_at', [$start, $end])
                ->where('c.lab_id',auth()->user()->laboratory_id) 
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
                  ->whereBetween('c.created_at', [$start, $end])
 ->where('c.lab_id',auth()->user()->laboratory_id) 
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;
  break;
//this year
  case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');

  $totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->whereBetween('c.created_at', [$start, $end])
                ->where('c.lab_id',auth()->user()->laboratory_id) 
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
                  ->whereBetween('c.created_at', [$start, $end])
 ->where('c.lab_id',auth()->user()->laboratory_id) 
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;

  break;


  case 6:

  //previous week
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();

$totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->whereBetween('c.created_at', [$start, $end])
                ->where('c.lab_id',auth()->user()->laboratory_id) 
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
                  ->whereBetween('c.created_at', [$start, $end])
                ->where('c.lab_id',auth()->user()->laboratory_id) 
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;

  break;

  //previous month
  case 7:

  $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();

$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();

$totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->where('c.lab_id',auth()->user()->laboratory_id) 
               ->whereBetween('c.created_at', [$start, $end])
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('c.created_at', [$start, $end])

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;


  break;

  //previous quarter
  case 8:

$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
     
     $totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->where('c.lab_id',auth()->user()->laboratory_id) 
               ->whereBetween('c.created_at', [$start, $end])
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('c.created_at', [$start, $end])

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;

  break;

  //previous year

  case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

$totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id') 
               ->where('c.lab_id',auth()->user()->laboratory_id) 
               ->whereBetween('c.created_at', [$start, $end])
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('c.created_at', [$start, $end])

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;
  break;
// custom range
  case 10:
$start=Carbon::createFromFormat('Y-m-d', $request->start_date);
$end=Carbon::createFromFormat('Y-m-d',$request->end_date);
//dd($end);
$totalData = DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
               ->where('c.lab_id',auth()->user()->laboratory_id)  
               ->whereBetween('c.created_at', [$start, $end])
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('c.created_at', [$start, $end])

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->get();

  $totalFiltered =  $totalRec ;
  break;
   }
 
        

          
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 


            foreach ($terms as $term) {



$nestedData['item_id']= $term->item_id;
                $nestedData['id']=$x;
                $nestedData['item_name']=$term->item_name;
               
                $nestedData['catalog_number']= $term->catalog_number;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['consumed']= $term->consumed_quantity;
                
             
               
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


public function changeFrequency(Request $request){
    switch($request->id){
 case 0:

        $date=date('Y-m-d');
        return response()->json([
           'date'=>$date,
        ]);

 break;

 case 1:  
        $week= Carbon::now()->addWeek()->startOfWeek()->format('Y-m-d');
        return response()->json([
           'date'=>$week,
            ]);
break;

case 2:
        $myDate = date('Y-m-d');
        $month= Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');

        return response()->json([
           'date'=>$month,
            ]);
break;

case 3:
        $quarter= Carbon::now()->endOfQuarter()->format('Y-m-d');
         return response()->json([
           'date'=>$quarter,
            ]);
break;

case 4:
$year=Carbon::now()->endOfYear()->format('Y-m-d');
 return response()->json([
           'date'=>$year,
            ]);

break;

    }
}

public function downloadConsumptionReport(Request $request){

   parse_str($request->form_data,$out);
 $expired= $out;

$lab=auth()->user()->laboratory_id;
 $period=$expired['period'];
  
switch($period){
    case -1:
 $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                 DB::raw('GROUP_CONCAT(c.id) as requisitions_ids'),
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 
       
       $data=array();
                 foreach($terms as $term){
             $ids=explode(',',$term->requisitions_ids);  
             for($x=0;$x<count($ids);$x++){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                      't.item_name',
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('rd.id',$ids[$x])
                   ->get();
                   
                   $data[]=$consum;
                 }


                
}
               
    break;
//download today
case 0:
$date=Carbon::now()->format('Y-m-d');
$consum=array();
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->where('c.created_at','=',$date)
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
             
                   ->get();
}

  

 break;

//yesterday
case 1:

  $date=date('Y-m-d', strtotime("-1 days"));
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->where('c.created_at','=',$date)
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}
 
break;

case 2:
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}
break;


case 3:
 $myDate = date('Y-m-d');
$start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');


$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}

break;


case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}

break;

case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}

break;

case 6:
$start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}

break;

case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}

break;

case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}

break;

case 9:
$start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
               ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}

break;

case 10:
$start= $expired['start'];
$end=   $expired['end'];
$terms =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
              ->where('c.lab_id',$lab)
              ->whereBetween('c.created_at',[$start,$end])
              ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 foreach($terms as $term){
 $consum=DB::table('items as t')
                  ->join('inventories  as r','r.item_id','=','t.id')
                  ->join('consumption_details as rd','rd.item_id','=','r.id')
                  ->join('consumptions as u','u.id','=','rd.consumption_id')
                  ->join('laboratories as l','l.id','=','u.lab_id')
                  ->select(
                    'l.lab_name',
                    'u.section_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('t.id',$term->item_id)
                   ->get();
}

break;
//end  inner switch
}
if(count($terms)==0){
    return response()->json([
        'message'=>"empty data",
        'error'=>true
    ]);

}
dd(count($terms));
 switch ($request->type) {
     case 'download':

        $name="consumed_items.pdf";
    $path=public_path('reports').'/'.$name;
      $url=route('redirect_download',['name'=>$name]) ; 
    $pdf=PDF::loadView('pdf.reports.consumed_report',['items'=>$terms,'consumed'=>$consum])->save($path);

                  return response()->json([
                    'path'=>$url,
                    
                  ]); 
         break;
     
     case 'excel':
       $spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
      $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
 $image = file_get_contents(url('/').'/assets/icon/logo_black.png');
$imageName = 'logo.png';
$temp_image=tempnam(sys_get_temp_dir(), $imageName);
file_put_contents($temp_image, $image);
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath($temp_image); 
$drawing->setHeight(70);
$drawing->setCoordinates('A2');
$drawing->setOffsetX(110);


$drawing->getShadow()->setDirection(45);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('B7', ' Consumed List ');
$spreadsheet->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

    

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Name')
    ->setCellValue('B8', 'Catalog Number')
    ->setCellValue('C8', 'Unit Issue')
    ->setCellValue('D8', 'Total Consumed');

$num=9;
$total=0;
$overall_total=0;

  for ($x=0; $x<count($terms); $x++){

  $dat=[

    [
    $terms[$x]->item_name,
  
  $terms[$x]->catalog_number,
  $terms[$x]->unit_issue,
  $terms[$x]->consumed_quantity,
   

]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');


// Create Table

$table = new Table('A8:D'.$num, 'Expired_Data');

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
$writer->save(public_path('reports').'/consumed_itrems.xlsx');
$path=public_path('reports').'/consumed_itrems.xlsx';
$name='consumed_itrems.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
$url=route('report.consumed_download-excel',['name'=>$name]); 
return response()->json([
    'path'=>$name,
    'url'=>$url,
]);

         break;
 }




}
public function download($name){
    $path=public_path('reports').'/'.$name;
$name='consumption_report.pdf';

$headers = [
  'Content-type' => 'application/pdf', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 

return response()->download($path,$name, $headers);
       
}

public function showRequisitionReport(){
    $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.reports.list.requisition_report',$data);
}
public function showSupplierOrderReport(){
     $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->get();
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
    return view('provider.reports.list.supplier_order_report',$data);  
}

public function loadRequisitionReport(Request $request){

    $columns = array(
            0 =>'id',
            1=>'item_name',
            2=>'code',
            
            3=>'catalog_number',
            4=>'is_hazardous',
            5=>'unit_issue',
            6=>'store_temp',
            7=>'total',
            8=>'status',
          
        ); 

        
          $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   //->where([['r.status','=','approved']])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  // ->where([['r.status','=','approved']])
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
 $nestedData['id']=$x;
 
                $nestedData['item_name']=$term->item_name;
                $nestedData['code']=$term->code;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= '<strong>'.$term->quantity_requested.'</strong>';
                $nestedData['status']=$term->status;
      
                
               
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
public function getRequestedData(Request $request){
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
                    'inv.cost',
                    'r.quantity_requested'
                    )->where('r.id',$ids[$i])
                   
                  
                   // ->where('rd.status','approved')
                    ->get();
                    
             
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
  $nested['approved_name']=$r->approved_name??"";
  $nested['approved_lastname']=$r->approved_lastname??'';
  $nested['requested_date']=$r->requested_date;
  $nested['cost']=$r->cost;
$nested['quantity_requested']=$r->quantity_requested;
$data[]= $nested;

}
} 
//dd($data);
  return response()->json([
   'data'=>$data,
  ]);
}

public function loadByLocation(Request $request){
   
   $columns = array(
            0 =>'id',
            1=>'code',
            2=>'item_name',
            
            4=>'catalog_number',
            5=>'is_hazardous',
            6=>'unit_issue',
            7=>'store_temp',
            8=>'total',
            9=>'status',

          
        ); 

        
          $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                  
                   ->where([['r.lab_id','=',$request->id]])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                  ->where([['r.lab_id','=',$request->id]])
                  // ->where([['r.status','=','approved']])
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
              $nestedData['id']=$x;
                   $nestedData['code']=$term->code;
                $nestedData['item_name']=$term->item_name;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= '<strong>'.$term->quantity_requested.'</strong>';
                $nestedData['status']=$term->status;
      
                
               
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
  public function loadByPeriod(Request $request){

      $columns = array(
            0 =>'id',
            1=>'code',
            2=>'item_name',
            
            4=>'catalog_number',
            5=>'is_hazardous',
            6=>'unit_issue',
            7=>'store_temp',
            8=>'total',
            9=>'status',
          
        ); 

    switch($request->period){
        case 0:
$date=date('Y-m-d');
             $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->where([['r.requested_date','=',$date]])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->where([['r.requested_date','=',$date]])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
            break;

    //yesterday

            case 1:
 $date=date('Y-m-d', strtotime("-1 days"));

             $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->where([['r.requested_date','=',$date]])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->where([['r.requested_date','=',$date]])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
            break;

         //this week
         case 2:
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();


             $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
         break;  
// this month
case 3:
 $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

      $end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');

 $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
    break;

    //this quarter 
    case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');

 $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
    break ;

    //This year;

    case 5:
   $start = Carbon::now()->startOfYear()->format('Y-m-d');
   $end = Carbon::now()->endOfYear()->format('Y-m-d');

        $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
    break;

//previous week

    case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();

 $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
    break;
//previous month
case 7:
$start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();

$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
 
break;

//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
 
break;

//previous year;
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
 
break;
// custom
case 10:
$start=Carbon::createFromFormat('Y-m-d', $request->start_date);
$end=Carbon::createFromFormat('Y-m-d',$request->end_date);
//dd($end);

$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('requisition_details as rd','rd.item_id','=','i.id')
                  ->join('requisitions as r','r.id','=','rd.requisition_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->whereBetween('r.requested_date', [$start, $end])
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
                    'r.status',
                     DB::raw('GROUP_CONCAT(rd.id) as requisitions_ids'),
                     DB::raw('SUM(rd.quantity_requested) as quantity_requested'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.requested_date', [$start, $end])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
 
break;
    }


      $data = array();
          if (!empty($terms)) {
$x=1;


            foreach ($terms as $term) {

               $nestedData['item_id']=$term->requisitions_ids;
               $nestedData['id']=$x;
                $nestedData['code']=$term->code;
                $nestedData['item_name']=$term->item_name;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= '<strong>'.$term->quantity_requested.'</strong>';
                $nestedData['status']=$term->status;
      
                
               
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

  public function loadOrdersToSupplier(Request $request){

    $columns = array(
            0 =>'id',
            1=>'code',
            2=>'item_name',
            4=>'catalog_number',
            5=>'is_hazardous',
            6=>'unit_issue',
            7=>'store_temp',
            8=>'total',
            9=>'status',
          
        ); 

        
          $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   //->where([['r.status','=','approved']])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                  // ->where([['r.status','=','approved']])
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

               $nestedData['item_id']=$term->orders_ids;
               $nestedData['id']=$x;
                $nestedData['code']=$term->code;
                $nestedData['item_name']=$term->item_name;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= '<strong>'.$term->quantity_ordered.'</strong>';
                $nestedData['status']=$term->is_delivered;
      
                
               
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

  public function getOrderDetails(Request $request){
    $ids=explode(',',$request->id);
    
    $data = array();
    for($i=0;$i<count($ids); $i++){
$record= DB::table('inventories as inv')
                  ->join('item_order_details  as r','r.inventory_id','=','inv.id')
                  ->leftjoin('item_orders as rd','rd.id','=','r.order_id')
                  ->leftjoin('users as u','u.id','=','rd.ordered_by')
                  ->leftjoin('users as y','y.id','=','rd.approved_by')
                  ->leftjoin('laboratories as l','l.id','=','rd.lab_id')
                  ->leftjoin('suppliers as s','s.id','=','r.supplier_id')
                  
                  ->select(
                    'rd.order_number',
                     'rd.id',
                     'rd.section_id',
                    'l.lab_name',
                    'u.name',
                    'u.last_name',
                    'y.name as approved_name',
                    'y.last_name as approved_lastname',
                    's.supplier_name',
                    'rd.delivery_date',
                    'inv.cost',
                    'r.ordered_quantity'
                    )->where('r.id',$ids[$i])
                   
                  
                   // ->where('rd.status','approved')
                    ->get();
         
             
             foreach($record as $r)  { 
  //dd($record);
   if($r->section_id!=NULL){
         $section=LaboratorySection::where('id',$r->section_id)->select('section_name')->first();
         $lab=$r->lab_name.' | '.$section->section_name;
     }
     else{
       $lab=$r->lab_name;   
     }
  $nested['sr_number']=$r->order_number;
   $nested['lab_name']=$lab;
   $nested['name']=$r->name;
  $nested['last_name']=$r->last_name;
  $nested['approved_name']=$r->approved_name??"";
  $nested['supplier']=$r->supplier_name??"";
  $nested['approved_lastname']=$r->approved_lastname??'';
  $nested['requested_date']=$r->delivery_date;
  $nested['cost']=$r->cost;
$nested['quantity_requested']=$r->ordered_quantity;
$data[]= $nested;

}
} 

  return response()->json([
   'data'=>$data,
  ]);
  }


  public function loadOrderByPeriod(Request $request){



    $columns = array(
            0 =>'id',
            1=>'code',
            2=>'item_name',
            
            4=>'catalog_number',
            5=>'is_hazardous',
            6=>'unit_issue',
            7=>'store_temp',
            8=>'total',
            9=>'status',
          
        ); 
switch($request->period){
    //today

        case 0:

         $date=date('Y-m-d');
          $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->where('r.created_at',$date)
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->where('r.created_at',$date)
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;

break;

//yeesterday

case 1:
 $date=date('Y-m-d', strtotime("-1 days"));
//dd($date);
          $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->where('r.created_at',$date)
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->where('r.created_at',$date)
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
break;



//this week
case 2:
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();

$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                     ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;

break;

//this month
case 3:
 $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');

                        $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                     ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;

break;

//this quarter

case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');

$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                     ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
break;

//this Year
case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');

$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                     ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
break;



//previous week


case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
break;

//previous month

case 7;
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                   ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
break;


//previous quarter

case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                  ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
break;

//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                    ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
break;

//custom
case 10:
$start=Carbon::createFromFormat('Y-m-d', $request->start_date);
$end=Carbon::createFromFormat('Y-m-d',$request->end_date);

$totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                     ->whereBetween('r.created_at',[$start,$end])
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                    ->whereBetween('r.created_at',[$start,$end])
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
break;

}
        $data = array();
          if (!empty($terms)) {
$x=1;


            foreach ($terms as $term) {

               $nestedData['item_id']=$term->orders_ids;
                $nestedData['id']=$x;
                $nestedData['code']=$term->code;
                $nestedData['item_name']=$term->item_name;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= '<strong>'.$term->quantity_ordered.'</strong>';
                $nestedData['status']=$term->is_delivered;
      
                
               
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

  public function  loadOrderByLocation(Request $request){
    $location=$request->location;

     $columns = array(
            0 =>'id',
            1=>'code',
            2=>'item_name',
            
            4=>'catalog_number',
            5=>'is_hazardous',
            6=>'unit_issue',
            7=>'store_temp',
            8=>'total',
            9=>'status',
            10=>'lab'
          
        );
    $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                     ->where('r.lab_id',$location)
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                    ->where('r.lab_id',$location)
                  // ->where([['r.status','=','approved']])
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

               $nestedData['item_id']=$term->orders_ids;
                $nestedData['id']=$x;
                $nestedData['code']=$term->code;
                $nestedData['item_name']=$term->item_name;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= '<strong>'.$term->quantity_ordered.'</strong>';
                $nestedData['status']=$term->is_delivered;
      
                
               
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

  public function loadOrderBySupplier(Request $request){
    $supplier=$request->supplier;

     $columns = array(
            0 =>'id',
            1=>'code',
            2=>'item_name',
            
            4=>'catalog_number',
            5=>'is_hazardous',
            6=>'unit_issue',
            7=>'store_temp',
            8=>'total',
            9=>'status',
          
        );
    $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
                     ->where('rd.supplier_id',$supplier)
                     ->where('r.lab_id',auth()->user()->laboratory_id) 
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
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                    'r.section_id',
                    'r.ordered_by',
                    'r.approved_by',
                    'r.is_delivered',
                     DB::raw('GROUP_CONCAT(rd.id) as orders_ids'),
                     DB::raw('SUM(rd.ordered_quantity) as quantity_ordered'))
                  ->where('r.lab_id',auth()->user()->laboratory_id) 
                   ->where('rd.supplier_id',$supplier)
                  // ->where([['r.status','=','approved']])
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

               $nestedData['item_id']=$term->orders_ids;
                $nestedData['id']=$x;
                $nestedData['code']=$term->code;
                $nestedData['item_name']=$term->item_name;
               $nestedData['catalog_number']= $term->catalog_number;
               $nestedData['is_hazardous']= $term->is_hazardous;
                $nestedData['unit_issue']= $term->unit_issue;
                $nestedData['store_temp']= $term->store_temp;
                 $nestedData['total']= '<strong>'.$term->quantity_ordered.'</strong>';
                $nestedData['status']=$term->is_delivered;
      
                
               
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

  public function stockLevelReport(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.reports.list.stock_level');
  }

  public function loadStockLevelReport(Request $request){
      
    /*$columns = array(
            0 =>'id',
            1=>'item_name',
            2=>'catalog_number',
            4=>'place_purchase',
            5=>'unit_issue',
            6=>'min',
            7=>'max',
            8=>'available',
            9=>'status',
          
        );
    $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->where('i.lab_id',auth()->user()->laboratory_id) 
                   ->where('i.expiry_date', '>', date('Y-m-d') )
                    ->select(DB::raw('SUM(i.quantity) as stock_on_hand'))
                   ->groupBy('t.id','t.item_name')
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
                  ->select(
                    't.id as id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.place_of_purchase',
                    't.unit_issue',
                    't.minimum_level',
                    't.maximum_level',
                    'i.quantity',
                    'i.expiry_date',
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
                  ->where('i.lab_id',auth()->user()->laboratory_id) 
                  // ->where([['r.status','=','approved']])
          ->where('i.expiry_date', '>', date('Y-m-d') )
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
                    $nestedData['id']=$x;
               $nestedData['item_id']=$term->id;
                $nestedData['item_name']=$term->item_name;
                $nestedData['catalog_number']=$term->catalog_number;
                $nestedData['place_purchase']=$term->place_of_purchase;
                 $nestedData['unit_issue']= $term->unit_issue;
               $nestedData['min']= $term->minimum_level;
                $nestedData['max']= $term->maximum_level;
                 $nestedData['available']= '<strong>'.$term->stock_on_hand.'</strong>';
                if($term->minimum_level>$term->stock_on_hand){
                $nestedData['status']='<span class="badge badge-danger">Below Minimum</span>';
      }
      if($term->stock_on_hand>$term->minimum_level){
         $nestedData['status']= '<span class="badge badge-warning">Good</span>';
      }
       if($term->stock_on_hand>$term->maximum_level){
        $nestedData['status']=  '<span class="badge badge-success">More than enough</span>';
      }
      else{
          $nestedData['status']='<span class="badge badge-success">Good</span>';
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
      */
        if ($request->ajax()) {
    $stock_level = DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->select(
                    't.id as id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.place_of_purchase',
                    't.unit_issue',
                    't.minimum_level',
                    't.maximum_level',
                    'i.quantity',
                    'i.expiry_date',
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
                  ->where('i.lab_id',auth()->user()->laboratory_id)
                  ->where('i.quantity','>',0)
                  ->groupBy('t.id','t.item_name')
                  ->get();
 return Datatables::of($stock_level)
                ->addIndexColumn()
                
                ->addColumn('status', function($row) {
                    if($row->stock_on_hand>$row->minimum_level){
                    return   '<span class="badge badge-info">Good</span>' ;
            }
            if ($row->stock_on_hand >$row->maximum_level) {
                return '<span class="badge badge-success">More than enough</span>';
            }
             if ($row->stock_on_hand <$row->minimum_level) {
                return '<span class="badge badge-danger">Bad</span>';
            }
if ($row->stock_on_hand <$row->maximum_level) {
                return '<span class="badge badge-info">Good</span>';
            }
                })
                
                   ->rawColumns(['status'])
                ->make(true);

}
            
  }
  public function loadStockLevelDetails(Request $request){
   
    //dd($request->id);
    $data = array();
   
$record= DB::table('inventories as inv')
                  ->join('laboratories  as r','r.id','=','inv.lab_id')
                 
                  ->select(
                    'r.lab_name',
                    'inv.section_id',
                    'inv.quantity',
                     DB::raw('SUM(inv.quantity) as stock_on_hand'))
                  ->where('inv.lab_id',auth()->user()->laboratory_id) 
                    ->where('inv.item_id',$request->id)
                   
                  
                   // ->where('rd.status','approved')
                    ->groupBy('r.lab_name')
                    ->get();
         //dd($record);
             
             foreach($record as $r)  { 
  //dd($record);
   if($r->section_id!=NULL){
         $section=LaboratorySection::where('id',$r->section_id)->select('section_name')->first();
         $lab=$r->lab_name.' | '.$section->section_name;
     }
     else{
       $lab=$r->lab_name;   
     }
 
   $nested['lab_name']=$lab;

$nested['quantity']=$r->stock_on_hand;
$data[]= $nested;

}


  return response()->json([
   'data'=>$data,
  ]);
  }

  public function showOutOfStockReport(){
      
           $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.reports.list.out_of_stock');
  }

  public function loadOutOfStock(Request $request){
        $columns = array(
            0 =>'id',
            1=>'item_name',
            2=>'catalog_number',
            4=>'place_purchase',
            5=>'unit_issue',
            6=>'status',
          
        );
    $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->where('i.lab_id',auth()->user()->laboratory_id) 
                   ->where([['i.quantity','<=',0]])
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
                  ->select(
                    't.id as id',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.place_of_purchase',
                    't.unit_issue',
                    DB::raw('GROUP_CONCAT(i.id) as item_ids')
                   )
                  ->where('i.lab_id',auth()->user()->laboratory_id) 
                  ->where([['i.quantity','<=',0]])
                  // ->where([['r.status','=','approved']])
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
                    $nestedData['id']=$term->id;
               $nestedData['item_id']=$term->item_ids;
                $nestedData['item_name']=$term->item_name;
                $nestedData['catalog_number']=$term->catalog_number;

                $nestedData['place_purchase']=$term->place_of_purchase;
                 $nestedData['unit_issue']= $term->unit_issue;
             
               
                $nestedData['status']="";
      
                
               
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
  public function loadOutOfStockDetails(Request $request){
 $ids=explode(',',$request->id);
    
    $data = array();
    for($i=0;$i<count($ids); $i++){
$record= DB::table('items as i')
                  ->join('inventories as r','r.item_id','=','i.id')
                  ->join('laboratories as l','l.id','r.lab_id')
                  ->select(
                   
                     'r.lab_id',
                     'r.section_id',
                    'l.lab_name',
                    'r.quantity',
                    'r.updated_at',
                    'r.batch_number'
                    )->where('r.id',$ids[$i])
                   
                  
                   // ->where('rd.status','approved')
                    ->get();
         
             
             foreach($record as $r)  { 
  //dd($record);
   if($r->section_id!=NULL){
         $section=LaboratorySection::where('id',$r->section_id)->select('section_name')->first();
         $lab=$r->lab_name.' | '.$section->section_name;
     }
     else{
       $lab=$r->lab_name;   
     }
 
   $nested['lab_name']=$lab;
   $nested['batch_number']=$r->batch_number;
  $nested['quantity']=$r->quantity;
  $nested['date']=$r->updated_at??"";
 
$data[]= $nested;

}
} 

  return response()->json([
   'data'=>$data,
  ]);
  }

  public function showExpiredReport(){
$data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
           
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   
$data['lab_name']='Logged Into: '.$lab->lab_name;
return view('provider.reports.list.expired',$data);

  }
  public function loadExpired(Request $request){
    $lab=auth()->user()->laboratory_id;


     $date=Carbon::now();
  $columns = array(
            0=>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
            11=>'lab'
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
           ->where('t.lab_id',$lab)
             ->where('t.expiry_date', '<', $date)
             
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
        ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '<', $date)
          
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->location??'';
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);

  }

  public function loadExpiredItemByRange(Request $request){
     parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab'];
$range=$expired['period'];
    switch ($range) {
        case -1:
        
$this->getDefaultData($request);
        break;
        case 0:
             $date=date('Y-m-d');
              $this->getRangePrevious($request,$date);
        break;
        //yesterday
        case 1:
             $date=date('Y-m-d', strtotime("-1 days"));
           $this->getRangePrevious($request,$date);
            break;
            //this week
        case 2:
           $start = Carbon::now()->subWeek()->endOfWeek();
            $end = Carbon::now();
            $this->getRangeData($request,$start, $end);
            break;
            //this month
        case 3:
        $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

        $end = Carbon::now();
            $this->getRangeData($request,$start, $end);
            break;
            //this quarter
        case 4:
            $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now();
$this->getRangeData($request,$start, $end);
            break;
            //this year
    case 5:
 $start = Carbon::now()->startOfYear()->format('Y-m-d');
   $end = Carbon::now();
   $this->getRangeData($request,$start, $end);
    break;
    //previous week
 case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$this->getRangeData($request,$start, $end);
        break;
   //previous month     
case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$this->getRangeData($request,$start, $end);
break;

//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$this->getRangeData($request,$start, $end);
break;

//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $this->getRangeData($request,$start, $end);
break;
//custom select
case 10:

$start = $expired['start'];
 $end =$expired['end'];
$this->getRangeData($request,$start, $end);
break;
    }

}
protected function getRangePrevious($request,$date){
    $date=$date;
 
    $date=Carbon::now();
         $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
            11=>'lab'
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
           ->where('t.quantity','>',0)
             ->where('t.expiry_date', '=', $date)
             ->where('t.lab_id',auth()->user()->laboratory_id)
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
        ->where('t.lab_id',auth()->user()->laboratory_id)
     ->where('t.expiry_date', '=', $date)
            ->where('t.quantity','>',0)
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->location??'';
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);


}
protected function getRangeData($request,$start_date, $end){
   


     $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
 $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
        ->where('t.lab_id',auth()->user()->laboratory_id)
             ->whereBetween('t.expiry_date', [$start_date, $end])
           
          ->count();



            $totalRec = $totalData;
$limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
       ->where('t.lab_id',auth()->user()->laboratory_id)
     ->whereBetween('t.expiry_date', [$start_date, $end])
 
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;

          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->location??'Store';
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);


}
// download files


public function downloadExpired(Request $request){
   
  parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=auth()->user()->laboratory_id;

 $period=$expired['period'];
  $date=date('Y-m-d');
if( $period==-1){
    
   $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
          ->where('t.quantity','>',0)
     ->where('t.expiry_date', '<', $date)
     ->get();
}
if($lab!=-1 && $period==-1){
 $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
      ->where('t.quantity','>',0)
     ->where('t.expiry_date', '<', $date)
     ->get();
}

if($lab==-1 && $period!=-1){
    switch ($period) {
        case 0:
         $date=date('Y-m-d');
         $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '=', $date)
     ->get();
        break;
        case 1:
     $date=date('Y-m-d', strtotime("-1 days"));

     $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
      ->where('t.quantity','>',0)
     ->where('t.expiry_date', '=', $date)
     ->get();
            break;
        
        case 2:
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();


$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start,$end])
     ->get();
            break;

        case 3:
 $myDate = date('Y-m-d');
$start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;

        case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 6:
$start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
        break;


        case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;

        case 9:
$start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

 $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 10:
$start= $expired['start'];
$end=   $expired['end'];
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
    }
}

if($period!=-1){
    $lab=auth()->user()->laboratory_id;
 switch ($period) {
        case 0:
         $date=date('Y-m-d');
         $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '=', $date)
     ->get();
        break;
        case 1:
     $date=date('Y-m-d', strtotime("-1 days"));

     $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
      ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '=', $date)
     ->get();
            break;
        
        case 2:
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();


$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();

    break;

        case 3:
 $myDate = date('Y-m-d');
$start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;

        case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 6:
$start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
        break;


        case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;

        case 9:
$start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

 $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 10:
$start= $expired['start'];
$end=   $expired['end'];
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
    }
   
}
if($request->type=="download"){
 
    $name="expired.pdf";
    $path=public_path('reports').'/expired.pdf';
        
    $pdf=PDF::loadView('pdf.reports.expired_report',$data);
        $pdf->save($path); 
$url=route('report.get_expired_download',['name'=>$name]);

return response()->json([
    'path'=>$name,
    'url'=>$url,

]);

}
if($request->type=="excel"){
$spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
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
$spreadsheet->getActiveSheet()->setCellValue('D7', ' EXPIRED ITEM LIST ');
$spreadsheet->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

    

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Name')
    ->setCellValue('B8', 'Code')
    ->setCellValue('C8', 'Batch Number')
    ->setCellValue('D8', 'Location')
    ->setCellValue('E8', 'Expiration')
    ->setCellValue('F8', 'Quantity')
    ->setCellValue('G8', 'Cost')
    ->setCellValue('H8', 'Loss');

$num=9;
$total=0;
$overall_total=0;

  for ($x=0; $x<count($data['info']); $x++){
$total=$data['info'][$x]->cost*$data['info'][$x]->quantity;
$overall_total=$overall_total+$total;
  $dat=[

    [
    $data['info'][$x]->item_name,
    $data['info'][$x]->code, 
    $data['info'][$x]->batch_number,
    $data['info'][$x]->lab_name,
    $data['info'][$x]->expiry_date,
    $data['info'][$x]->quantity,
    $data['info'][$x]->cost,
    $total
]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
$spreadsheet->getActiveSheet()
    ->setCellValue('A'.$step, 'Total');
  $spreadsheet->getActiveSheet()->getStyle('A'.$step)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()
    ->setCellValue('H'.$step, $overall_total);
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getNumberFormat()
    ->setFormatCode('#,##0.00');
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getFont()->setBold(true);

// Create Table

$table = new Table('A8:H'.$num, 'Expired_Data');

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
$writer->save(public_path('reports').'/expired_report.xlsx');
$path=public_path('reports').'/expiry_report.xlsx';
$name='expired_report.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
$url=route('report.expired_download-excel',['name'=>$name]); 
return response()->json([
    'path'=>$name,
    'url'=>$url,
]);

}
}
public function downloadAboutToExpire(Request $request){
    parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=auth()->user()->laboratory_id;

 $period=$expired['period'];
if($period==-1){
            $date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays(90);
    $from_date=date('Y-m-d');
      
        //  dd("done");

            $report = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
        ->where('t.lab_id','=',$lab)
        ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))->get();
    }


if($period!=-1){
switch ($period) {

        case 1:
           
              $from_date=  date('Y-m-d', strtotime("-30 days"));
               $to_date=Carbon::now();
            break;
        case 2:
            $days=30;
               $to_date= Carbon::now()->addDays($days);
               $from_date=Carbon::now();
            break;
        case 3:
            $days=60;
                $to_date= Carbon::now()->addDays($days);
                $from_date=Carbon::now()->addDays(31);
            break;
        case 4:
            $days=90;
          $to_date=Carbon::now()->addDays($days);
          $from_date=Carbon::now()->addDays(60);
            break;
        default:
        $days=90;
            $to_date=Carbon::now()->addDays($days);
             $from_date=Carbon::now();
    }
    $report = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
         ->where('t.lab_id','=',$lab)
        ->whereBetween('t.expiry_date', [$from_date,  $to_date])->get(); 

}

          //->where('t.expiry_date', '<', $date )

          //$path=public_path('reports').'/Inventory Expiry.pdf';
        
   // $pdf=PDF::loadView('pdf.reports.expiry_report',['info'=>$report]);
     // dd("done");
  

   if($request->type=="download"){
   
    $name="Inventory Expiry.pdf";
       $path=public_path('reports').'/Inventory Expiry.pdf';
        
$pdf=PDF::loadView('pdf.reports.expiry_report',['info'=>$report]);
        $pdf->save($path); 
$url=route('report.expiry_download',['name'=>$name]);

return response()->json([
    'path'=>$name,
    'url'=>$url,

]);

}
if($request->type=="print"){
 $pdf=PDF::loadView('pdf.reports.expiry_report',['info'=>$report]);
 return $pdf->stream();
}
if($request->type=="excel"){
$spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
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
$spreadsheet->getActiveSheet()->setCellValue('D7', 'ABOUT TO EXPIRE LIST ');
$spreadsheet->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

    

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Name')
    ->setCellValue('B8', 'Code')
    ->setCellValue('C8', 'Batch Number')
    ->setCellValue('D8', 'Location')
    ->setCellValue('E8', 'Expiration')
    ->setCellValue('F8', 'Quantity')
    ->setCellValue('G8', 'Cost')
    ->setCellValue('H8', 'Estimated Loss');

$num=9;
$total=0;
$overall_total=0;

  for ($x=0; $x<count($report); $x++){
$total=$report[$x]->cost*$report[$x]->quantity;
$overall_total=$overall_total+$total;
  $data=[

    [$report[$x]->item_name,$report[$x]->code, $report[$x]->batch_number,$report[$x]->lab_name,$report[$x]->expiry_date,$report[$x]->quantity,$report[$x]->cost,$total]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
$spreadsheet->getActiveSheet()
    ->setCellValue('A'.$step, 'Total');
  $spreadsheet->getActiveSheet()->getStyle('A'.$step)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()
    ->setCellValue('H'.$step, $overall_total);
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getNumberFormat()
    ->setFormatCode('#,##0.00');
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getFont()->setBold(true);

// Create Table

$table = new Table('A8:H'.$num, 'About_To_Expire_Data');

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
$writer->save(public_path('reports').'/expiry_report.xlsx');
$path=public_path('reports').'/expiry_report.xlsx';
$name='expiry_report.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
$url=route('report.expiry_download-excel',['name'=>$name]); 
return response()->json([
    'path'=>$name,
    'url'=>$url,
]); 
}
}
public function stockLevelDownload(Request $request, $name){

$lab=auth()->user()->laboratory_id;
               $terms = DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->where('i.lab_id',$lab)
                  ->select(
                    't.id as id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.place_of_purchase',
                    't.unit_issue',
                    't.minimum_level',
                    't.maximum_level',
                    'i.quantity',
                    'i.cost',
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
            ->groupBy('t.id','t.item_name')
            ->get();



       if($name=='pdf'){
  $name="out_of_stock.pdf";
    $path=public_path('reports').'/'.$name;
        
    $pdf=PDF::loadView('pdf.reports.stock_level',['info'=>$terms]);
      return  $pdf->download(now().'_Stock_level.pdf'); 
    }       
if($name=='excel'){
     $spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
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
$spreadsheet->getActiveSheet()->setCellValue('D7', ' STOCK LEVEL');
$spreadsheet->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

    

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Name')
    ->setCellValue('B8', 'Code')
    ->setCellValue('C8', 'Batch Number')
    ->setCellValue('D8', 'Place of Purchase')
    ->setCellValue('E8', 'Unit')
    ->setCellValue('F8', 'Minimum')
    ->setCellValue('G8', 'Maximum')
    ->setCellValue('H8', 'Quantity');


      

$num=9;
$total=0;
$overall_total=0;

  foreach ($terms as $term){

  $dat=[

    [
   $term->item_name,
    $term->code, 
    $term->batch_number,
    $term->unit_issue,
    $term->place_of_purchase,
    $term->minimum_level,
     $term->maximum_level,
     $term->stock_on_hand
   
]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');

// Create Table

$table = new Table('A8:H'.$num, 'Expired_Data');

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
$writer->save(public_path('reports').'/stock_level_report.xlsx');
$path=public_path('reports').'/stock_level_report.xlsx';
$name='stock_level_report.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
   return response()->download($path,$name, $headers);
    }
   
  }
  public function downloadOutOfStock(Request $request ,$name){
    $terms = DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->select(
                    't.id as id',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.place_of_purchase',
                    't.minimum_level',
                    't.maximum_level',

                    't.unit_issue',
                    DB::raw('GROUP_CONCAT(i.id) as item_ids')
                   )
                  ->where('i.lab_id',auth()->user()->laboratory_id) 
                  ->where([['i.quantity','<=',0]])
                  ->groupBy('t.id','t.item_name')
            ->get();
       if($name=='pdf'){
  $name="out_of_stock.pdf";
    $path=public_path('reports').'/out_of_stock.pdf';
        
    $pdf=PDF::loadView('pdf.reports.out_of_stock',['info'=>$terms]);
      return  $pdf->download(now().'_out_of_stock.pdf'); 
    }       
if($name=='excel'){
     $spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
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
$spreadsheet->getActiveSheet()->setCellValue('D7', ' STOCK LEVEL');
$spreadsheet->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

    

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Name')
    ->setCellValue('B8', 'Code')
    ->setCellValue('C8', 'Batch Number')
    ->setCellValue('D8', 'Place of Purchase')
    ->setCellValue('E8', 'Unit')
    ->setCellValue('F8', 'Minimum')
    ->setCellValue('G8', 'Maximum')
    ->setCellValue('H8', 'Quantity');


      

$num=9;
$total=0;
$overall_total=0;

  foreach ($terms as $term){

  $dat=[

    [
   $term->item_name,
    $term->code, 
    $term->batch_number,
    $term->unit_issue,
    $term->place_of_purchase,
    $term->minimum_level,
     $term->maximum_level,
     $term->stock_on_hand
   
]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');

// Create Table

$table = new Table('A8:H'.$num, 'Expired_Data');

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
$writer->save(public_path('reports').'/out_of_stock.xlsx');
$path=public_path('reports').'/out_of_stock.xlsx';
$name='out_of_stock.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
   return response()->download($path,$name, $headers);
    }
   
  }

   public function showDisposal(){
              $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
  
   
$data['lab_name']='Logged Into: '.$lab->lab_name;

$data['laboratories']=Laboratory::get();
           
return view('provider.reports.list.disposal',$data);
        
    }
public function  loadDisposal(Request $request){
   $lab=auth()->user()->laboratory_id;  
    $columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
    
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->where('d.lab_id',$lab)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
      
           ->where('d.lab_id',$lab)
         
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
   $nestedData['date']= $term->created_at;
                    $nestedData['remark']=$term->remarks;  
                 
                  
 
    
            
                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);
   
}
public function labDownloadDisposed(Request $request){
   parse_str($request->expiry_form,$out);
 $expired= $out;
$lab=auth()->user()->laboratory_id;
 $period=$expired['period'];
  switch ($period) { 
  case -1:

 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                  ->orderBy('s.item_name','asc')
                 ->get();
  break;
 case 0:
  $date=date('Y-m-d');

 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->where('d.created_at',$date)
                  ->orderBy('s.item_name','asc')
                 ->get();
 break;

case 1:
 $date=date('Y-m-d', strtotime("-1 days"));

 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                  ->where('d.created_at','=',$date)  
                 ->where('d.lab_id',$lab) 
                  ->orderBy('s.item_name','asc')
                 ->get();

 break;

case 2:
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();


 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
 break;
case 3:
 $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
 break;

case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
break;
 case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
 break;  

 case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
 break;
 case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
 break;

 case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');

 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
 break;
 case 9:
  $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
  $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
 break;

 case 10:
$start=Carbon::createFromFormat('Y-m-d', $request->start_date);
$end=Carbon::createFromFormat('Y-m-d',$request->end_date);
 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                    
                 ->where('d.lab_id',$lab) 
                 ->whereBetween('d.created_at',[$start,$end])
                  ->orderBy('s.item_name','asc')
                 ->get();
 break; 
}
if(count($terms)>0){
    switch($request->type){
        case 'download':

            $name="disposed_items.pdf";
    $path=public_path('reports').'/'.$name;
        
    $pdf=PDF::loadView('pdf.reports.disposal',['info'=>$terms]);
        $pdf->save($path); 
$url=route('report.get_expired_download',['name'=>$name]);

return response()->json([
    'path'=>$name,
    'url'=>$url,

]);
        break;


        case 'excel':
$spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
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
$spreadsheet->getActiveSheet()->setCellValue('D7', ' EXPIRED ITEM LIST ');
$spreadsheet->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

    

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', '#')
    ->setCellValue('B8', 'Name')
    ->setCellValue('C8', 'Code')
    ->setCellValue('D8', 'Batch #')
    ->setCellValue('E8', 'Disposed Date')
    ->setCellValue('F8', 'Disposed Quantity')
    ->setCellValue('G8', 'Cost')
    ->setCellValue('H8', 'Remark')
    ->setCellValue('I8', 'Total');
    -

$num=9;
$total=0;
$overall_total=0;
$x=1;
  foreach ($terms as $term){
$total=$term->cost*$term->dispose_quantity;
$overall_total=$overall_total+$total;
  $dat=[

    [
   $x,
   $term->item_name,
   $term->code, 
   $term->batch_number,
   $term->created_at,
   $term->dispose_quantity,
   $term->cost,
   $term->remarks,
    $total,
   
]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
$spreadsheet->getActiveSheet()
    ->setCellValue('A'.$step, 'Total');
  $spreadsheet->getActiveSheet()->getStyle('A'.$step)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()
    ->setCellValue('I'.$step, $overall_total);
    $spreadsheet->getActiveSheet()->getStyle('I'.$step)->getNumberFormat()
    ->setFormatCode('#,##0.00');
    $spreadsheet->getActiveSheet()->getStyle('I'.$step)->getFont()->setBold(true);

// Create Table

$table = new Table('A8:I'.$num, 'Expired_Data');

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
$name='disposed_items.xlsx';
$writer->save(public_path('reports').'/'.$name);
$path=public_path('reports').'/'.$name;

$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
$url=route('report.expired_download-excel',['name'=>$name]); 
return response()->json([
    'path'=>$name,
    'url'=>$url,
]);
        break;
    }
}
else{
    return response()->json([
        'message'=>"data not available",
        'error'=>true,
    ]);
}
}
public function filterByPeriod(Request $request){
parse_str($request->expiry_form,$out);
 $expired= $out;

 $period=$expired['period'];
  switch ($period) {
    case -1:
$this->loadDefault($request);
    break;
        case 0:
         $date=date('Y-m-d');
            $this->getDisposedRangePrevious($request,$date);
        break;
        //yesterday
        case 1:
             $date=date('Y-m-d', strtotime("-1 days"));
           $this->getDisposedRangePrevious($request,$date);
            break;
            //this week
        case 2:
           $start = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
            $end = Carbon::now()->addWeek()->startOfWeek()->format('Y-m-d');
            //dd($start->format('Y-m-d'));
            $this->getDisposedRangeData($request,$start, $end);
            break;
            //this month
        case 3:
        $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

        $end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
    $this->getDisposedRangeData($request,$start, $end);
            break;
            //this quarter
        case 4:
            $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
$this->getDisposedRangeData($request,$start, $end);
            break;
            //this year
    case 5:
 $start = Carbon::now()->startOfYear()->format('Y-m-d');
   $end = Carbon::now()->endOfYear()->format('Y-m-d');
   $this->getDisposedRangeData($request,$start, $end);
    break;
    //previous week
 case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$this->getDisposedRangeData($request,$start, $end);
        break;
   //previous month     
case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$this->getDisposedRangeData($request,$start, $end);
break;

//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$this->getDisposedRangeData($request,$start, $end);
break;

//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $this->getDisposedRangeData($request,$start, $end);
break;
case 10:
$start=$expired['start'];
$end=$expired['end'];
$this->getDisposedRangeData($request,$start, $end);
break;
    }
  
      
}
protected function loadDefault($request){
$lab=auth()->user()->laboratory_id;
     $columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
           ->where('d.lab_id',$lab)  
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
  
                 ->where('d.lab_id',$lab) 
         
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
                 $nestedData['date']= $term->created_at;
                $nestedData['remark']=$term->remarks;  

                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);
      


}
protected function getDisposedRangeData($request,$start_date, $end){

$columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  $lab=auth()->user()->laboratory_id;
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->where('d.lab_id',$lab)
             ->whereBetween('d.created_at', [$start_date, $end])->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
               ->where('d.lab_id',$lab)
      ->whereBetween('d.created_at', [$start_date, $end])
  
       
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
   $nestedData['date']= $term->created_at;
                    $nestedData['remark']=$term->remarks;  

                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data); 

}

public function getDisposedRangePrevious($request,$date){
$lab=auth()->user()->laboratory_id;
$columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->where('d.lab_id',$lab)
              ->where('d.created_at',$date)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
               ->where('d.lab_id',$lab)
      ->where('d.created_at',$date)
  
         
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
   $nestedData['date']= $term->created_at;
                    $nestedData['remark']=$term->remarks;  

                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);    
}
public function labIssueDownload(Request $request, $name){
    $lab=auth()->user()->laboratory_id;
   $terms = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.to_lab_id')
              ->where('t.from_lab_id',$lab)
              ->select(
                 't.id as id',
                 't.siv_number',
                 't.issued_by' ,
                 't.from_lab_id',
                 't.from_section_id',
                 'l.lab_name as lab_name',
                 't.to_section_id',
                 't.approved_by',
                 't.received_by',
                 't.issuing_date',
                 't.approve_status')
            ->orderBy('t.issuing_date','asc')
            ->get();
            switch ($name) {
                case 'pdf':
    $name="stock_transfer.pdf";
    $path=public_path('reports').'/'.$name;
        
    $pdf=PDF::loadView('pdf.reports.stock_transfer',['info'=>$terms]);
      return  $pdf->download(now().'stock_transfer.pdf'); 
       
                    break;
                
                case 'excel':
                    // code...
                    break;
            }

}
protected function getDefaultData($request ){
     $lab=auth()->user()->laboratory_id;


     $date=Carbon::now();
  $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
            11=>'lab',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
           ->where('t.lab_id',$lab)
             ->where('t.expiry_date', '<', $date)
             
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
        ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '<', $date)
          
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->location??'Store';
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);


}
public function showStockVariance(Request $request){
   $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.reports.list.variance',$data);    
}
}