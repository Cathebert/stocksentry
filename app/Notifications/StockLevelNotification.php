<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockLevelNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
     protected $lab_name;
    protected $path;
    protected $start;
    public function __construct($lab_name,$path,$start)
    {
    	$this->lab_name=$lab_name;
        $this->path=$path;
        $this->start=$start;
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
                   ->line('This is a scheduled report for stock level from '.$this->lab_name. ' as of .'.$this->start)
                    ->attach($this->path)
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