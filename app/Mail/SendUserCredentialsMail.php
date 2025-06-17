<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendUserCredentialsMail extends Mailable
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
            ->subject("You have received your " . $this->data['website_name'] . " login credentials.")
            ->view('mail.user_credentials_mail')
            ->with([
                'data' => $this->data,
            ]);
    }
}
