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
use App\Models\User;
use App\Notifications\StockLevelNotification;
use Carbon\Carbon;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class StockLevelJob implements ShouldQueue
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
                  ->where('i.lab_id',$lab_id)
                  ->groupBy('t.id','t.item_name')
                ->get();

        switch($report->attach_as){
            case 1:
    $name="scheduled_stock_level.pdf";
    $path=public_path('reports').'/'.$name;
    
    $pdf=PDF::loadView('pdf.reports.stock_level',['info'=>$terms])->save($path)
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
$spreadsheet->getActiveSheet()->setCellValue('B7', ' Stock Level ');
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
    ->setCellValue('A8', 'ULN')
    ->setCellValue('B8', 'Name')
    ->setCellValue('C8', 'Code')
    ->setCellValue('D8', 'Unit');
    ->setCellValue('E8', 'Minimum');
    ->setCellValue('F8', 'Maximum');
    ->setCellValue('G8', 'Available');

$num=9;
$total=0;
$overall_total=0;

  for ($x=0; $x<count($terms); $x++){

  $dat=[

    [
 $terms[$x]->uln,
    $terms[$x]->item_name,
  $terms[$x]->code,
  $terms[$x]->unit_issue,
  $terms[$x]->minimum_level,
  $terms[$x]->maximum_level,
  $terms[$x]->quantity,
   

]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');


// Create Table

$table = new Table('A8:G'.$num, 'Expired_Data');

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
$writer->save(public_path('reports').'/scheduled_stock_level.xlsx');
$path=public_path('reports').'/scheduled_stock_level.xlsx';
$name='scheduled_stock_level.xlsx';
            break;
$receivers=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();
if(!empty($receivers)){
    foreach($receivers as $user){
        $notifier=User::find($user->user_id);
        $notifier->notify(new StockLevelNotification($path,$end));
    }
$mail=new SystemMail();
$mail->lab_id=$lab_id;
$mail->subject="Stock Level Report";
$mail->type="Stock Level";
$mail->date=now();
$mail->save();
}
ScheduleReport::where('id',$this->report_id)->update([
    
        'start_date'=>now()
    ]);


}
        
                  
    }
}
