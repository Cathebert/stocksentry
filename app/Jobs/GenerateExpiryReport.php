<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PDF;
use DB;
use App\Models\ScheduleReport;


class GenerateExpiryReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $report_id;
    public function __construct($report_id)
    {
        //
        $this->report_id=$report_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       $id=$this->report_id;
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

          $path=public_path('reports').'/Inventory Expiry.pdf';
        
    $pdf=PDF::loadView('pdf.reports.expiry_report',['info'=>$report]);
     // dd("done");
     $pdf->save($path); 
     
      /*   ScheduleReport::where('id',$id)->update([
            'updated_at'=>now(),
        ]); */
$scheduled=ScheduleReport::where('id',$id)->select('attach_as','email_list')->first();
    $email=json_decode($scheduled->email_list);
 try {
       
        $user->notify(new IssueApproved($path, count($report)));
    } catch (Exception $e) {
        logger('Error in job: ' . $e->getMessage());
    }
    }
}
