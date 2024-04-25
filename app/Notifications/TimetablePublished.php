<?php

namespace App\Notifications;

use App\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class TimetablePublished extends Notification
{
    use Queueable;
    private $type;
    private $form;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type,$form)
    {

        $this->type = $type;
        $this->form = $form;
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
                'icon'=>'fa-table',
                'from'=>Auth::user()->gender=="Male"?'Mr. '.Auth::user()->lname:'Ms. '.Auth::user()->lname,
                'message'=>'The timetable for your class has been published. Please check in your panel',
                'mobile'=>'/view_timetable',
                'form'=>$this->form->number.$this->form->name,
                'url'=>$this->type == 'student'?route('timetable.index'):'/teachers/timetable/'.$this->form->shift.'/'.$this->form->number.'?n=1'
            ];
    }
}
