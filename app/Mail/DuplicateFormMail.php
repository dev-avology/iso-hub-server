<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DuplicateFormMail extends Mailable
{
    use Queueable, SerializesModels;
    public $encryptedLink;
    protected $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        // Encrypt user_id and email_id
        $this->data = $data;
        $encryptedData = encrypt(json_encode($this->data));

        // Get the secure upload URL from .env
        $this->encryptedLink = env('WEBSITE_URL').'jot-forms?data=' . urlencode($encryptedData);
    }
    
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->subject('Replicated prospect form on ISO HUB')
            ->view('mail.duplicate_prospect_form')
            ->with([
                'secureUploadLink' => $this->encryptedLink,
                'data' => $this->data, // Pass prospect name
        ]);
    }
}
