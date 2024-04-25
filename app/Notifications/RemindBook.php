<?php

namespace App\Notifications;

use App\Bookmeta;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class RemindBook extends Notification
{
    use Queueable;
	/**
	 * @var Bookmeta
	 */
	private $bookmeta;
	/**
	 * @var User
	 */
	private $user;

	/**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Bookmeta $bookmeta,User $user)
    {

	    $this->bookmeta = $bookmeta;
	    $this->user = $user;
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
                'icon'=>'fa-book',
                'from'=>Auth::user()->gender=='Male'?'Mr. '.Auth::user()->lname:'Ms. '.Auth::user()->lname,
                'message'=>request()->message,
                'url'=>$this->user->type == 'Student'?route('messaging.index'):'/teachers/notifications?r=1'
            ];


    }
}
