<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUpdate extends Mailable
{
    use Queueable, SerializesModels;

    protected $userName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Exciting things in the FixApp latest update.")
                ->markdown('emails.new_updates')
                ->with([
                    'username'=> $this->userName,
        ]);
    }
}
