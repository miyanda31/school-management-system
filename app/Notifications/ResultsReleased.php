<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class ResultsReleased extends Notification
{
    use Queueable;
    private $year;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($year)
    {
        //
        $this->year = $year;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'icon'=>'fa-layers',
            'from'=> Auth::user()->fname.' '.Auth::user()->lname,
            'url'=>url('/maneb/'.$this->year),
            'mobile'=>'view_results',
            'year'=>$this->year,
            'message'=>'This is to inform you that results for the year '.$this->year. ' are now available'
        ];
    }
}
