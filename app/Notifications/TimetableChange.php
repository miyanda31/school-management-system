<?php

namespace App\Notifications;

use App\Timetable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class TimetableChange extends Notification
{
    use Queueable;

    /**
     * @var Timetable
     */
    private $timetable;
    private $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Timetable $timetable,$type)
    {

        $this->timetable = $timetable;
        $this->type = $type;
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
        if ($this->type == 'teacher') {
            return [
                'icon'=>'fa-table',
                'from'=> Auth::user()->fname.' '.Auth::user()->lname,
                'url'=>'/teachers/timetable/'.$this->timetable ->form->shift.'/'.$this->timetable ->form->number,
                'mobile'=>'/view_timetable',
                'form'=>$this->form->number.$this->form->name,
                'message'=>'The timetable for your class has been updated. '.$this->timetable ->subject->name.' has been moved to'
                    .$this->timetable->dayname.' from '.$this->timetable->time->start.' - '.$this->timetable->time->end
            ];
        }
        else {
            return [
                'icon'=>'fa-table',
                'from'=> Auth::user()->fname.' '.Auth::user()->lname,
                'url'=>route('timetable.show',[$this->timetable ->form->number,'s'=>$this->timetable ->form->shift]),
                  'mobile'=>'/view_timetable',
                'form'=>$this->form->number.$this->form->name,
                'message'=>'The timetable for your class has been updated. '.$this->timetable ->subject->name.' has been moved to'
                    .$this->timetable->dayname.' from '.$this->timetable->time->start.' - '.$this->timetable->time->end
            ];
        }
    }
}
