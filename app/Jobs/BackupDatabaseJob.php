<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\SystemBackup;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Notifications\BackUpNotification;
class BackupDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
     protected $user_id;
     protected $type;
    public function __construct($user_id,$type)
    {
        
        $this->user_id=$user_id;
        $this->type= $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! Storage::exists('backup')) {
            Storage::makeDirectory('backup');
        }
    $rand=mt_rand(0, 999999);
         $filename = "backup-" . Carbon::now()->format('Y-m-d').'_' .$rand. ".gz";
    
        $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD')
                . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') 
                . "  | gzip > " . storage_path() . "/app/backup/" . $filename;
 
        $returnVar = NULL;
        $output  = NULL;
 
        exec($command, $output, $returnVar);
        $full_path=storage_path() . "/app/backup/" . $filename;
        $name="backup-" . Carbon::now()->format('Y-m-d').'_'.$rand ;
        $fileSize=1024;
       $this->updateBackupInformation($name,$filename,$fileSize);
        
        $user=User::where('id',$this->user_id)->first();
        $user->notify(new BackUpNotification($full_path,$this->type));
    }
    
    protected function updateBackupInformation($file_name,$internal_name,$fileSize){
    
    $backup=new SystemBackup();
    $backup->name=$file_name;
     $backup->internal_name=$internal_name;
    $backup->file_size=$fileSize;
    $backup->back_type="scheduled";
    $backup->scheduled_by=$this->user_id;
    $backup->backup_by="System";
    $backup->created_at	=now();
    $backup->updated_at=NULL;
    $backup->save();
    
    
    }
}