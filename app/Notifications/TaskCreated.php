<?php

namespace App\Notifications;

use App\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class TaskCreated extends Notification
{
    use Queueable;
	/**
	 * @var Event
	 */
	private $request;

	/**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $request)
    {
        //
	    $this->request = $request;
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
            'url'=>'/tasks/'.$this->request->id,
	        'message'=>'You have been assigned a new task in your department'
        ];
    }
}
