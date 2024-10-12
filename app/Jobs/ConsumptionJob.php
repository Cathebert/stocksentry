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
use App\Models\Laboratory;
use App\Models\User;
use DB;
use PDF;
use App\Notifications\ConsumptionNotification;
use App\Notifications\NoConsumptionNotification;
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
   protected $lab_id;
   protected $start_date;
   protected $attach_as;
    public function __construct($report_id,$lab_id, $start_date, $attach_as)
    {
        $this->report_id=$report_id;
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

    if(count($terms)>0){
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
    
    switch($report->attach_as){
        case 1:
            $name="consumption_report.pdf";
            $path=public_path('reports').'/'.$name;
            $pdf=PDF::loadView('pdf.reports.consumed_report',['items'=>$terms,'lab_name'=>$lab_name])->save($path);
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
$spreadsheet->getActiveSheet()->setCellValue('B7', $lab_name.' Consumed List ');
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
break;

}
ScheduleReport::where('id',$this->report_id)->update([
    
        'start_date'=>now()
    ]);

$receivers=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();
if(!empty($receivers)){
    foreach($receivers as $user){
    $notified=User::find($user->user_id);
       $notified->notify(new ConsumptionNotification($path,$start,$end));
    }
$mail=new SystemMail();
$mail->lab_id=$lab_id;
$mail->subject="Consumption Report";
$mail->type="Consumption";
$mail->date=now();
$mail->save();
}

}
else{
ScheduleReport::where('id',$this->report_id)->update([
    
        'start_date'=>now()
    ]);

  $receivers=EmailReceipient::where('report_id',$this->report_id)->select('user_id')->get();
if(!empty($receivers)){
    foreach($receivers as $user){
    $notified=User::find($user->user_id);
       $notified->notify(new NoConsumptionNotification($lab_name,$start,$end));
       
    }  
}
}





    }
}