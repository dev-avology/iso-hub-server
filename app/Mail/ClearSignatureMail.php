<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClearSignatureMail extends Mailable
{
    use Queueable, SerializesModels;
    public $encryptedLink;
    protected $email;
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $type_segment = $this->data['clear_signature']. "-".$this->data['personal_guarantee_required'];
        $this->data = $data;
        $encryptedData = encrypt(json_encode($this->data));
        $this->encryptedLink = env('SECURE_UPLOAD_URL') . "/" . $this->data['user_id'] . "/" .$type_segment;
        \Log::info($this->encryptedLink);
        \Log::info('$this->encryptedLink');
    }
    
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->subject('Clear Signature, Upload Docs on ISO HUB')
            ->view('mail.clear_signature')
            // ->with('secureUploadLink', $this->encryptedLink);
            ->with([
                'secureUploadLink' => $this->encryptedLink,
                'data' => $this->data, // Pass prospect name
            ]);
    }
}
