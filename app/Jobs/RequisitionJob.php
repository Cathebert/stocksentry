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
use App\Notifications\RequisitionNotification;
use Carbon\Carbon;
use App\Models\ItemOrder;
use App\Models\User;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class RequisitionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $report_id;
    public function __construct($report_id)
    {
        $this->report_id=$report_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $report=ScheduleReport::where('id',$this->report_id)->first(); 
$lab_id = $report->lab_id;
$start = $report->start_date;
$end   =   date('Y-m-d');
$terms=DB::table('item_orders as i')
                ->join('users as u','u.id','i.ordered_by')
                ->where('lab_id',$lab_id)
                ->select('i.order_number','is_consolidated','i.is_approved','i.is_delivered','u.name','u.last_name')
                     ->whereBetween('created_at',[$start,$end])->get();
if(count($terms)>0){ 
    switch ($report->attach_as) {
        case 1:
           $name="requisition_level.pdf";
    $path=public_path('reports').'/'.$name;
    
    $pdf=PDF::loadView('pdf.reports.requisition',['info'=>$terms])->save($path)
            break;
        
       case 2:
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
$spreadsheet->getActiveSheet()->setCellValue('B7', ' Orders Summary List ');
$spreadsheet->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

        \

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Order #')
     ->setCellValue('B8','Ordered By')
    ->setCellValue('C8', 'Order Approved')
    ->setCellValue('D8', 'Order Consolidated')
    ->setCellValue('E8', 'Order Delivered');

$num=9;
$total=0;
$overall_total=0;

  for ($x=0; $x<count($terms); $x++){
$name= $terms[$x]->name.' '.$terms[$x]->name;
  $dat=[

    [
    $terms[$x]->order_number,
  $name,
  $terms[$x]->is_approved,
  $terms[$x]->is_consolidated,
  $terms[$x]->consumed_quantity,
   

]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');


// Create Table

$table = new Table('A8:E'.$num, 'Expired_Data');

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
    //
    $receivers=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();
    if(!empty($receivers)){
    foreach($receivers as $user){
        $notifier=User::find($user->user_id);
       $notifier->notify(new RequisitionNotification($path,$start,$end));
    }
$mail=new SystemMail();
$mail->lab_id=$lab_id;
$mail->subject="Order to Supplier Report";
$mail->type="Order";
$mail->date=now();
$mail->save();
}
ScheduleReport::where('id',$this->report_id)->update([
    
        'start_date'=>now()
    ]);

     }
    }
}
