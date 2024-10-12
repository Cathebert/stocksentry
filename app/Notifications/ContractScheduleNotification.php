<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
class ContractScheduleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $contract_name;
    protected $contract_number;
    protected $end_date;
    protected $days;
    public function __construct($contract_name,$contract_number,$end_date,$days)
    {
        $this->contract_name=$contract_name;
        $this->contract_number=$contract_number;
        $this->end_date=$end_date;
        $this->days=$days;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line(new HtmlString('We would like to remind you that contract with name <strong>'.$this->contract_name.'</strong> and contract number <strong>'.$this->contract_number.'</strong> has <stron>'.$this->days.'</strong> days before expiring on <strong>'.$this->end_date.'</strong>'))
                    ->line('Please start the process of renewing this contract or finding a replacement service provider')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}