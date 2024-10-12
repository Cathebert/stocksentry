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
use App\Models\Laboratory;
use App\Notifications\DisposalSheduleNotification;
use App\Notifications\NoStockDisposalNotification;
use Carbon\Carbon;
use App\Models\ItemOrder;
use App\Models\User;
use PDF;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class DisposalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
     protected $report_id;
     protected $lab_id;
     protected $start_date;
     protected $attach_as;
    public function __construct($report_id, $lab_id,$start_date,$attach_as)
    {
        $this->report_id= $report_id;
        $this->lab_id=$lab_id;
        $this->start_date=$start_date;
        $this->attach_as=$attach_as;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
$report=ScheduleReport::where('id',$this->report_id)->first(); 
$lab_id = $this->lab_id;
$start = $this->start_date;
$end   =   date('Y-m-d');
$lab=Laboratory::where('id',$lab_id)->select('id','lab_name')->first();
$lab_name=$lab->lab_name;
 $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_name','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                ->where('d.lab_id',$lab_id)
          //->whereBetween('d.created_at', [$start, $end])
          ->get();

if(count($terms)>0){

    switch($report->attach_as){
        case 1:
            $name="disposal.pdf";
            $path=public_path('reports').'/'.$name;
            $pdf=PDF::loadView('pdf.reports.disposal',['info'=>$terms,'lab_name'=>$lab_name])->save($path);
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
$drawing->setCoordinates('A2');
$drawing->setOffsetX(110);


$drawing->getShadow()->setDirection(45);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('B7', $lab_name.' Disposed Item List ');
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
    ->setCellValue('A8', 'Item Name')
    ->setCellValue('B8', 'Code')
    ->setCellValue('C8', 'Batch Number')
    ->setCellValue('D8', 'Disposed Date')
    ->setCellValue('E8', 'Disposed Quantity')
    ->setCellValue('F8', 'Cost')
    ->setCellValue('G8', 'Total')
    ->setCellValue('H8', 'Remark');

$num=9;
$total=0;
$overall_total=0;

  for ($x=0; $x<count($terms); $x++){
$total= $terms[$x]->dispose_quantity* $terms[$x]->cost;
$overall_total=$overall_total+$total;
  $dat=[

    [
    $terms[$x]->item_name,
  
  $terms[$x]->code,
  $terms[$x]->batch_number,
  $terms[$x]->created_at,
  $terms[$x]->created_at,
  $terms[$x]->dispose_quantity,
  $terms[$x]->cost,
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
$writer->save(public_path('reports').'/consumption_scheduled.xlsx');
$path=public_path('reports').'/consumption_scheduled.xlsx';
$name='consumption_scheduled.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];

        break;
            }
$receivers=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();
if(!empty($receivers)){
    foreach($receivers as $user){
        $notifier=User::find($user->user_id);
      $notifier->notify(new DisposalSheduleNotification($path,$start, $end));
    }
$mail=new SystemMail();
$mail->lab_id=$lab_id;
$mail->subject="Disposal Report";
$mail->type="Disposal";
$mail->date=now();
$mail->save();
}
ScheduleReport::where('id',$this->report_id)->update([
    
        'start_date'=>now()
    ]);


          }
          else{
          $receivers=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();
if(!empty($receivers)){
    foreach($receivers as $user){
        $notifier=User::find($user->user_id);
      $notifier->notify(new NoStockDisposalNotification($lab_name,$start, $end));
    }

}
          ScheduleReport::where('id',$this->report_id)->update([
    
        'start_date'=>now()
    ]);
    }
    }
}