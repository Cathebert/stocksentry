<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\User;
use App\Models\SystemMail;
use PDF;

use DB;
use App\Notifications\AboutToExpireNotification;
class CheckAboutToExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-about-to-expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'checks items that are about to expire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        $expiryDate = Carbon::now()->addDays(30);

        // Find items expiring in 30 days
        

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
        
     ->where('t.expiry_date', '<',  $expiryDate)
     ->get();
      $name="thirty_expired.pdf";
    $path=public_path('reports').'/thirty_expired.pdf';
        
$pdf=PDF::loadView('pdf.reports.expired_report',$data);
$pdf->save($path); 
        
        $user=User::find(1);
 $user->notify(new AboutToExpireNotification($path));
 $system_mail=new SystemMail();
 $system_mail->lab_id=NULL;
 $system_mail->subject='About To Expire Notification';
 $system_mail->type="About To Expire";
 $system_mail->date=now();
 $system_mail->save();

    }
}
