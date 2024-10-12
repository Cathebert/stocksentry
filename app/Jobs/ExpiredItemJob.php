<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\ScheduleReport;
use App\Models\EmailReceipient;
use App\Models\SystemMail;
use App\Notifications\ExpiredItemNotification;
use App\Notifications\NoExpiredItemNotification;
use Carbon\Carbon;


use App\Models\Item;
use App\Models\Inventory;
use App\Models\User;
use App\Models\UserSetting;

use App\Models\Laboratory;
use PDF;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ExpiredItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
     protected $report_id;
     protected $lab_id;
     protected $start_date;
     protected $attach_as;
    public function __construct($report_id,$lab_id,$start_date,$attach_as)
    {
  
        //
         $this->report_id= $report_id;
         $this->lab_id=$lab_id;
         $this->start_date=$start_date;
         $this->$attach_as=$attach_as;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $report=ScheduleReport::where('id',$this->report_id)->first();
         $lab=Laboratory::where('id',$this->lab_id)->select('lab_name','id')->first();  
$lab_id = $this->lab_id;
$start = $this->start_date;
//$start=Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
$end   =   date('Y-m-d');
//foreach($labs as $lab){
       $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
        
     ->whereBetween('t.expiry_date', [$start,  $end])
     ->where('t.quantity','>',0)
     ->where('t.lab_id',$this->lab_id)
     ->get();
    $data['lab_name']=$lab->lab_name;
    $Lab_name=$lab->lab_name;
 if(count($data['info']) >0){
  switch($report->attach_as){
  case 1:
    $name=$lab->lab_name."_expired_items.pdf";
    $path=public_path('reports').'/'.$name;
        
        $pdf=PDF::loadView('pdf.reports.expired_report',$data);
        $pdf->save($path); 
   break;
   
   case 2:
   $spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
      $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
 $image = file_get_contents('https://stocksentry.org/assets/icon/logo_black.png');
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
$name=$lab->lab_name.'_expired_items.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save(public_path('reports').'/'.$name);
$path=public_path('reports').'/'.$name;

   
   break;
        
        
        
        }
        
           $approver_list=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();   
   if(!empty($approver_list) && count($approver_list)>0){
foreach ($approver_list as $list){  
 $user=User::find($list->user_id);
 $user->notify(new ExpiredItemNotification($path));
 }
 }

 }
   else{
 
  $approver_list=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();
   if(!empty($approver_list) && count($approver_list)>0){
foreach ($approver_list as $list){  
 $user=User::find($list->user_id);
 $user->notify(new NoExpiredItemNotification($Lab_name,$start,$end));
 }
 }

 }
 
 
 ScheduleReport::where('id',$this->report_id)->update([
    
        'start_date'=>now()
    ]); 
 
   
}
}