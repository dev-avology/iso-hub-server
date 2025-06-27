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
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct($userId, $email_id, $name)
    {
        // Encrypt user_id and email_id
        $this->email = $email_id;
        $this->name = $name;
        // $encryptedData = encrypt(json_encode(['user_id' => $userId, 'email_id' => $email_id, 'name' => $name]));

        // Get the secure upload URL from .env
        // $this->encryptedLink = env('SECURE_UPLOAD_URL') . "?data=" . urlencode($encryptedData);


        $type_segment = $userId. "&" .$this->name . "&" .$this->email;
        $this->encryptedLink = env('SECURE_UPLOADS_URL') . "/" .$type_segment ;


    }
    
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->subject('Upload files on CoCard')
            ->view('mail.prospect')
            // ->with('secureUploadLink', $this->encryptedLink);
            ->with([
                'secureUploadLink' => $this->encryptedLink,
                'name' => $this->name, // Pass prospect name
            ]);
    }
}
