<?php

namespace App\Notifications;

use App\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class AssignmentPublished extends Notification
{
    use Queueable;
    /**
     * @var Post
     */
    private $post;
    private $user;
	private $type;

	/**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Post $post,$user,$type)
    {
        //
        $this->post = $post;
        $this->user = $user;
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
    public function toArray($notifiable)
    {
    	if ($this->type == 'created') {
		    return [
                'icon'=>'fa-images',
                'from'=>Auth::user()->gender=="Male"?'Mr. ':'Ms. '.Auth::user()->lname,
                'slug'=>$this->post->slug,
                'mobile'=>'/view_task',
			    'url'=>route('tasks.show',$this->post->slug),
			    'message'=>'A new assignment from your teacher '.substr($this->user->fname,0,1).'. '.$this->user->lname.' has been published.'
			    //
		    ];
	    }
    	else {
		    return [
                'icon'=>'fa-images',
                'from'=>Auth::user()->gender=="Male"?'Mr. ':'Ms. '.Auth::user()->lname,
			    'url'=>route('tasks.show',$this->post->slug),
			    'message'=>substr($this->user->fname,0,1).'. '.$this->user->lname.' has updated the details of his/her assignment.'
			    //
		    ];
	    }

    }
}
