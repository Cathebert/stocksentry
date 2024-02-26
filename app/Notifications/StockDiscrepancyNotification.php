<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockDiscrepancyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $stockTake;
    protected $discrepancy;
    public function __construct($stockTake,$discrepancy)
    {
        //
        $this->stockTake=$stockTake;
        $this->discrepancy=$discrepancy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $count=count($this->discrepancy);
        return [
          'stock_take_date'=>$this->stockTake->stock_date,
          'stock_take_id'=>$this->stockTake->id,
          'count'=>$count,
         
        ];
    }
}
