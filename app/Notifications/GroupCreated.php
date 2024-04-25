<?php

namespace App\Notifications;

use App\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class GroupCreated extends Notification
{
    use Queueable;
	/**
	 * @var Group
	 */
	private $group;

	/**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Group $group)
    {
        //
	    $this->group = $group;
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
            'icon'=>'fa-users',
            'from'=>Auth::user()->gender=="Male"?'Mr. ':'Ms. '.Auth::user()->lname,
        	'url'=>route('groups.index'),
            'mobile'=>'groups',
            'message'=>'You have been assigned by '.Auth::user()->gender == 'Male'?'Mr ':'Mrs/Ms '.Auth::user()->lname.' to a group called '.$this->group->name
        ];
    }
}
