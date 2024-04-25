<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class GalleryPublished extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        //
        $this->id = $id;
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
            'from'=>Auth::user()->gender=="Male"?'Mr. '.Auth::user()->lname:'Ms. '.Auth::user()->lname,
            'message'=>'A photo gallery has been published and you were tagged. Check it out',
            'id'=>$this->id,
            'mobile'=>"/view_gallery",
            'url'=>route('photos.show',$this->id)
        ];
    }
}
