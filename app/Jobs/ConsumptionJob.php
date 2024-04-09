<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduleReport;
use App\Models\Consumption;
use App\Models\ConsumptionDetails;
use App\Models\EmailReceipient;
use App\Models\SystemMail;
use App\Models\Notification\ConsumptionNotification;
use Carbon\Carbon;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ConsumptionJob implements ShouldQueue
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
              ->whereBetween('c.created_at',[$start,$end])
              ->where('c.lab_id',$lab_id)
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

    if(count($terms)>0){
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
$writer->save(public_path('reports').'/consumption_scheduled.xlsx');
$path=public_path('reports').'/consumption_scheduled.xlsx';
$name='consumption_scheduled.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
$receivers=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();
if(!empty($receivers)){
    foreach($receivers as $user){
        $user->notify(new ConsumptionNotification($path));
    }
$mail=new SystemMail();
$mail->lab_id=$lab_id;
$mail->subject="Consumption Report";
$mail->type="Consumption";
$mail->date=now();
$mail->save();
}
ScheduleReport::where('id',$this->report_id)->update([
    
        'start_date'=>now()
    ]);


}
}


    }
}
