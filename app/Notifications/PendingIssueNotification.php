<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PendingIssueNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $issuer;
    protected $lab_name;
    protected $stock_tranfer_no;
    public function __construct($issuer,$lab_name,$stock_transfer_no)
    {
        //
        $this->issuerer=$issuer;
        $this->lab_name=$lab_name;
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
                    ->line($this->issuerer. ' made a request that is waiting your approval to.')
                    ->line('issue to '.$this->lab_name.' with stock transfer number'.$this->stock_transfer_no)
                    ->action('Please login to approve!',url('/'))
                    ->line('Thank You');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'issuerer'=>$this->issuerer,
            'lab_name'=>$this->lab_name,
            'stock_transfer_no'=>$this->stock_transfer_no
            //
        ];
    }
}
