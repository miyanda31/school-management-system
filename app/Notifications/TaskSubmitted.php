<?php

namespace App\Notifications;

use App\Group;
use App\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class TaskSubmitted extends Notification
{
    use Queueable;
	/**
	 * @var Group
	 */
	private $group;
	/**
	 * @var Post
	 */
	private $post;
	private $task;

	/**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Group $group,Post $post,$task)
    {
	    $this->group = $group;
	    $this->post = $post;
	    $this->task = $task;
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
            'from'=> Auth::user()->fname.' '.Auth::user()->lname,
            'url'=>'/teachers/dashboard/assignment/'.$this->post->slug.'/'.$this->task,
            'mobile'=>'/show_task',
            'slug'=>$this->post->slug,
            'task'=> $this->task,
	        'message'=>$this->group?$this->group->name.' has submitted the assignment for '.$this->post->category:
		        Auth::user()->fname.' '.Auth::user()->lname.' has submitted the assignment',
        ];
    }
}
