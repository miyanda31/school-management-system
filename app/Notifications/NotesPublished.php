<?php

namespace App\Notifications;

use App\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class NotesPublished extends Notification
{
    use Queueable;
    /**
     * @var Post
     */
    private $post;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Post $post,$user)
    {
        //
        $this->post = $post;
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
        return [
            'icon'=>'fa-images',
            'from'=>Auth::user()->gender=="Male"?'Mr. ':'Ms. '.Auth::user()->lname,
            'url'=>route('notes.show',$this->post->slug),
            'mobile'=>'/view_notes',
            'slug'=>$this->post->slug,
            'message'=>'Notes published or updated for '.$this->post->category.'.'
        ];

    }
}
