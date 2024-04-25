<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class FeesBalances extends Notification
{
    use Queueable;
    private $message;
    /**
     * @var User
     */
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message,User $user)
    {
        //
        $this->message = $message;
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
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Fees Balances Reminder')
                    ->line($this->message)
                    ->action('Fees Profile',route('profile.show',[$this->user->username,'c'=>'payments']));
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
            'icon'=>'fa-coins',
            'fees'=>'/payments',
            'username'=>$this->user->username,
            'from'=> Auth::user()->fname.' '.Auth::user()->lname,
            'url'=>route('profile.show',[$this->user->username,'c'=>'payments']),
            'message'=>$this->message
        ];
    }
}
