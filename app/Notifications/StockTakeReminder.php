<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
class StockTakeReminder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $days;
    protected  $lab_name;
    public function __construct($days, $lab_name)
    {
        $this->days=$days;
        $this->lab_name=$lab_name;
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
                    ->line(new HtmlString('It has been <strong>'.$this->days. '</strong> since Stock take was conducted in <strong>'.$this->lab_name.'</strong>'))
                    ->line(new HtmlString('Please plan to take stock for <strong>'.$this->lab_name.'</strong>'))
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