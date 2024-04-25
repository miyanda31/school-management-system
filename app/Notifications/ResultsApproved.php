<?php

namespace App\Notifications;

use App\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class ResultsApproved extends Notification
{
    use Queueable;
    protected $grade;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Form $grade)
    {
        $this->grade = $grade;
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



    public function toDatabase()
    {
        return [
            'icon'=>'fa-layers',
            'from'=> Auth::user()->fname.' '.Auth::user()->lname,
            'url'=>'#',
            'message'=>'Results for Form '.$this->grade->number.' '.$this->grade->name.' have been approved and will be published shortly. Once published, they can be accessed from your panel.'
        ];
    }
}
