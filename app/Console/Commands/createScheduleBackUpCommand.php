<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
 

class createScheduleBackUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-schedule-back-up-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Back up database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         if (! Storage::exists('backup')) {
            Storage::makeDirectory('backup');
        }
 
        $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".gz";
    
        $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD')
                . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') 
                . "  | gzip > " . storage_path() . "/app/backup/" . $filename;
 
        $returnVar = NULL;
        $output  = NULL;
 
        exec($command, $output, $returnVar);
    }
}