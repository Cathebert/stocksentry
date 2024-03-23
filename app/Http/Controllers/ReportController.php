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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ReportController extends Controller
{
    //
    public function show(Request $request){
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
        $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('reports.show',$data);
    }

    public function showExpiryReport(Request $request){
             $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
           $data['laboratories']=Laboratory::get();
           
return view('reports.list.expiry',$data);
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
        // ->where('t.lab_id','=',auth()->user()->laboratory_id)
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
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
        // ->where('t.lab_id','=',auth()->user()->laboratory_id)
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
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
        // ->where('t.lab_id','=',auth()->user()->laboratory_id)
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
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

	

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A3', 'Code')
    ->setCellValue('B3', 'Brand')
    ->setCellValue('C3', 'Batch Number')
    ->setCellValue('D3', 'Location')
    ->setCellValue('E3', 'Expiration')
    ->setCellValue('F3', 'Quantity')
    ->setCellValue('G3', 'Cost')
    ->setCellValue('H3', 'Estimated Loss');

$num=4;
$total=0;
$overall_total=0;
  for ($x=0; $x<count($report); $x++){
$total=$report[$x]->cost*$report[$x]->quantity;
$overall_total=$overall_total+$total;
  $data=[

    [$report[$x]->code,$report[$x]->brand, $report[$x]->batch_number,$report[$x]->lab_name,$report[$x]->expiry_date,$report[$x]->quantity,$report[$x]->cost,$total]
  

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

$table = new Table('A3:H'.$num, 'About_To_Expire_Data');

// Create Columns
$table->getColumn('H')->setShowFilterButton(false);
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

    return view('reports.list.issue',$data);
  }



 public function loadIssueTable(Request $request){
     
  
         $columns = array(
            0 =>'id',
            1=>'siv',
            2=>'to_lab',
            3=>'issued_by',
            4=>'approved_by',
            5=>'received_by',
            6=>'issue_date',
            7=>'status',
           
        ); 
    
   $totalData = DB::table('issues as t') 
              ->join('laboratories AS l', 'l.id', '=', 't.from_lab_id')
              ->select('t.id as id','t.siv_number','l.lab_name','t.issuing_date')
          ->where('t.from_lab_id','=',auth()->user()->laboratory_id)
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
              ->select('t.id as id','t.siv_number', 't.issued_by' ,'t.to_lab_id','t.approved_by','t.received_by','l.lab_name','t.issuing_date','t.approve_status')
          ->where('t.from_lab_id','=',auth()->user()->laboratory_id)
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

 $user=User::where('id',$term->issued_by)->select('name','last_name','id')->first();


                $nestedData['id']=$x;
                $nestedData['siv']=$term->siv_number;
                $nestedData['to_lab']= $term->lab_name;
                $nestedData['issued_by']= $user->name.' '.$user->last_name;
                $nestedData['approved_by']= $term->approved_by;
                $nestedData['received_by']= $term->received_by;
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
    return view('reports.list.consumption_report',$data);
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
              ->select(
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                'unit_issue',
                DB::raw('SUM(c.consumed_quantity) as consumed_quantity'))
          //->where('t.expiry_date', '<', $date )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
   
    if($request->period!=99){
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
                  ->groupBy('r.batch_number')
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
                   ->groupBy('r.batch_number')
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
                    'u.consumption_id',
                    'rd.consumed_quantity',
                    'r.batch_number'
                )
                  ->where('r.id',$request->id)
                   ->groupBy('u.batch_number')
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
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
           ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
               ->whereBetween('c.created_at', [$start, $end])
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
     ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
           ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
            
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
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
         ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
           ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
       ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
               ->whereBetween('c.created_at', [$start, $end])
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
           ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
               ->whereBetween('c.created_at', [$start, $end])
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
               ->whereBetween('c.created_at', [$start, $end])
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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

                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('l.batch_number','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
           ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
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
   switch($request->type){

    case 'download':
switch($request->period){
    case 99:
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
                ->orderBy('t.item_name','asc')
            ->groupBy('t.item_name')
                 ->get();
                 
       // dd($terms);
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
// dd($data);
$name="consumption_Expiry.pdf";
    $path=public_path('reports').'/'.$name;
      $url=route('redirect_download',['name'=>$name]) ; 
    $pdf=PDF::loadView('pdf.reports.consumed_report',['items'=>$terms,'consumed'=>$consum])->save($path);

                  return response()->json([
                    'path'=>$url,
                    
                  ]);  
    //dd($terms);               
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

$name="consumption_Expiry.pdf";
    $path=public_path('reports').'/'.$name;
      $url=route('redirect_download',['name'=>$name]) ; 
    $pdf=PDF::loadView('pdf.reports.consumed_report',['items'=>$terms,'consumed'=>$consum])->save($path);

                  return response()->json([
                    'path'=>$url,
                    
                  ]);  

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
$name="consumption_Expiry.pdf";
    $path=public_path('reports').'/'.$name;
      $url=route('redirect_download',['name'=>$name]) ; 
    $pdf=PDF::loadView('pdf.reports.consumed_report',['items'=>$terms,'consumed'=>$consum])->save($path);

                  return response()->json([
                    'path'=>$url,
                    
                  ]);  
break;

//end  inner switch
}

   //outer switch
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
    return view('reports.list.requisition_report',$data);
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
    return view('reports.list.supplier_order_report',$data);  
}

public function loadRequisitionReport(Request $request){

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
                $nestedData['id']=$term->id;
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
         $lab=$r->lab_name;//.' | '.$section->section_name;
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
                $nestedData['id']=$term->id;
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
                $nestedData['id']=$term->id;
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
                $nestedData['id']=$term->id;
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
                $nestedData['id']=$term->id;
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
          
        );
    $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->join('item_order_details as rd','rd.inventory_id','=','i.id')
                  ->join('item_orders as r','r.id','=','rd.order_id')
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
                $nestedData['id']=$term->id;
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
                $nestedData['id']=$term->id;
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
    return view('reports.list.stock_level',$data);
  }

  public function loadStockLevelReport(Request $request){
      
    $columns = array(
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
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
                  
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

               $nestedData['item_id']=$term->id;
                $nestedData['item_name']=$term->item_name;
                $nestedData['catalog_number']=$term->catalog_number;
                $nestedData['place_purchase']=$term->place_of_purchase;
                 $nestedData['unit_issue']= $term->unit_issue;
               $nestedData['min']= $term->minimum_level;
                $nestedData['max']= $term->maximum_level;
                 $nestedData['available']= '<strong>'.$term->stock_on_hand.'</strong>';
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
                    ->where('inv.item_id',$request->id)
                   
                  
                   // ->where('rd.status','approved')
                    ->groupBy('r.lab_name')
                    ->get();
         //dd($record);
             
             foreach($record as $r)  { 
  //dd($record);
   if(auth()->user()->section_id!=NULL){
         $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
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
    return view('reports.list.out_of_stock',$data);
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
   if(auth()->user()->section_id!=NULL){
         $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
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
}


