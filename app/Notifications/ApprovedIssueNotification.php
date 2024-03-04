<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovedIssueNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
        protected $stock_tranfer_no;
    public function __construct($stock_tranfer_no)
    {
        //
        $this->stock_transfer_no=$stock_transfer_no;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */

    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Your request to issue out  stock has been approved.')
                    ->line('Stock Transfer #:'.$this->stock_transfer_no)
                   // ->action('Notification Action', url('/'))
                    ->line('Thank you  !');
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
            'issue_approved'=>$this->stock_transfer_no,
            'message'=>'Issue has been approved'
        ];
    }
}
