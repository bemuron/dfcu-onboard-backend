<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $userName;
    protected $emailSubject;
    protected $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName, $subject, $message)
    {
        $this->userName = $userName;
        $this->emailSubject = $subject;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailSubject)
                ->markdown('emails.offer_notice')
                ->with([
                    'username'=> $this->userName,
                    'message' => $this->message,
        ]);
    }
}
