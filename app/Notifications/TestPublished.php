<?php

namespace App\Notifications;

use App\QuestionMeta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class TestPublished extends Notification
{
    use Queueable;
    /**
     * @var QuestionMeta
     */
    private $questionMeta;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(QuestionMeta $questionMeta)
    {
        //
        $this->questionMeta = $questionMeta;
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
            'icon'=>'fa-question',
            'from'=>Auth::user()->gender=="Male"?'Mr. ':'Ms. '.Auth::user()->lname,
            'url'=>route('tests.show',$this->questionMeta->id),
            'mobile'=>'view_test',
            'id'=>$this->questionMeta->id,
            'message'=>'A new '.$this->questionMeta->subject->name.' test was published '
        ];
    }
}
