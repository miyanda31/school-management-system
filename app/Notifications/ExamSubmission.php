<?php

namespace App\Notifications;

use App\Timetable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class ExamSubmission extends Notification
{
    use Queueable;
    /**
     * @var Timetable
     */
    private $allocations;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Timetable $allocations)
    {
        //
        $this->allocations = $allocations;
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
    public function toArray($notifiable)
    {
        return [
            'icon'=>'fa-layers',
            'from'=> Auth::user()->fname.' '.Auth::user()->lname,
            'url'=>'/admin/assessments/'.$this->allocations->subject_id.'/'.$this->allocations->form_id,
            'message'=>substr($this->allocations->user->fname,0,1).'. '.$this->allocations->user->lname
                .' has submitted exams for this term. You may check out for approval or publication'
        ];
    }
}
