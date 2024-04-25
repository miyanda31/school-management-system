<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class ResultsPublished extends Notification
{
    use Queueable;

    public $message,$term,$parent,$wards,$form;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message,$term,$parent,$wards,$form)
    {
        $this->message = $message;
        $this->term = $term;
        $this->parent = $parent;
        $this->wards = $wards;
        $this->form = $form;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = [
            'message'=>$this->message,
            'term'=>$this->term ,
            'parent'=>$this->parent ,
            'user'=>$this->wards ,
            'form'=>$this->form ,
        ];

        return (new MailMessage)
            ->subject("Results have been published")
            ->markdown('emails.results',$data);

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
	        'message'=>$this->message,
            'mobile'=>'/view_report',
            'username'=>$this->wards->username,
            'term'=>$this->term->id,
	        'url'=>route('reports.show',[$this->wards->username,'t'=>$this->term->id])
        ];
    }
}
