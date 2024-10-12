<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contract;
use App\Models\User;
use App\Models\ContractUser;
use Carbon\Carbon;
use App\Notifications\ContractScheduleNotification;
class ContractManagementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $contract_id;
    public function __construct($contract_id)
    {
       $this->contract_id=$contract_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $contract= Contract::where('id',$this->contract_id)->first();
        $contract_name=$contract->contract_name;
        $contract_number=$contract->contract_number;
        $end_date=Carbon::parse($contract->contract_enddate);
        $date=Carbon::parse(date('Y-m-d'));
$differenceInDays = $date->diffInDays($end_date);
        $cont_users=ContractUser::where('contract_id',$this->contract_id)->select('user_id')->get();
foreach ( $cont_users as $cont_user) {
    $user=User::find($cont_user->user_id);
    $user->notify(new ContractScheduleNotification($contract_name,$contract_number,$end_date,$differenceInDays));
}
    }
}