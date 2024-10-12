<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdjustmentCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $disposed_by;
     protected $data;
     protected $lab_name;
    public function __construct($disposed_by,$data,$lab_name)
    {
        //
        $this->disposed_by= $disposed_by;
        $this->data= $data;
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
        ->subject('Adjustment Created')
        ->markdown('mail.notification.adjustment',['items' => $this->data,'adjuster'=>$this->disposed_by,'lab_name'=> $this->lab_name,'url'=>route('user.login')]);
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
