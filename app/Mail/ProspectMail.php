<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProspectMail extends Mailable
{
    use Queueable, SerializesModels;
    public $encryptedLink;
    protected $email;

    /**
     * Create a new message instance.
     */
    public function __construct($userId, $email_id)
    {
        // Encrypt user_id and email_id
        $this->email = $email_id;
        $encryptedData = encrypt(json_encode(['user_id' => $userId, 'email_id' => $email_id]));

        // Get the secure upload URL from .env
        $this->encryptedLink = env('SECURE_UPLOAD_URL') . "?data=" . urlencode($encryptedData);
    }
    
    public function build()
    {
        return $this->from('ashishyadav.avology@gmail.com')
            ->subject('Upload files mail')
            ->view('mail.prospect')
            ->with('secureUploadLink', $this->encryptedLink);
    }
}
