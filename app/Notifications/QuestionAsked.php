<?php

namespace App\Notifications;

use App\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class QuestionAsked extends Notification
{
    use Queueable;
    /**
     * @var Post
     */
    private $post;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Post $post)
    {
        //
        $this->post = $post;
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
            'url'=>route('forum.show',$this->post->slug),
            'message'=>'A new question in your subject area has been asked. Please help answer the student',
            'mobile'=>'/view_forum',
            'slug'=>$this->post->slug,
            'from'=>Auth::user()->fname.' '.Auth::user()->lname,
            'icon'=>'fa-help',
        ];
    }
}
