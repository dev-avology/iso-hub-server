<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyUserMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->subject("You have received a new user notification.")
            ->view('mail.notify_user')
            ->with([
                'data' => $this->data,
            ]);
    }
}
