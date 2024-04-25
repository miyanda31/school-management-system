<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class AttendanceReport extends Notification
{
    use Queueable;
	private $item;
	/**
	 * @var User
	 */
	private $user;

	/**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($item,User $user)
    {
        //
	    $this->item = $item;
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
    public function toArray($notifiable)
    {
    	if ($this->item == 'Sick') {
		    return [
                'icon'=>'fa-user',
                'from'=>Auth::user()->gender=="Male"?'Mr. ':'Ms. '.Auth::user()->lname,
                'url'=>'#',
                'mobile'=>'attendance',
			    'message' => 'The class teacher '.Auth::user()->fname.' '.Auth::user()->lname.' wishes to notify you that '.$this->user->fname.' '.$this->user->lname.' is reported sick'
		    ];
	    }
    	else {
		    return [
                'icon'=>'fa-user',
                'from'=>Auth::user()->gender=="Male"?'Mr. ':'Ms. '.Auth::user()->lname,
                'url'=>'#',
                'mobile'=>'attendance',
			    'message' => 'The class teacher '.Auth::user()->fname.' '.Auth::user()->lname.' wishes to notify you that '.$this->user->fname.' '.$this->user->lname.' is reported as transferred'
		    ];
	    }

    }
}
