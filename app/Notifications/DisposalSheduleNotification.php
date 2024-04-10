<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisposalSheduleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $path;
    protected $start;
    protected $end;
    public function __construct($path,$start,$end)
    {
        $this->path=$path;
        $this->start=$start;
        $this->end=$end;
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
                   ->line('This is a scheduled report for disposed items from period.'.$this->start.' to '.$this->end)
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
