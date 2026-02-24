<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $type;
    public $actionText;

    /**
     * Create a new message instance.
     */
    public function __construct($code, $type)
    {
        $this->code = $code;
        $this->type = $type;

        $this->actionText = 'complete your request';
        if (stripos($type, 'Registration') !== false) {
            $this->actionText = 'complete your registration';
        } elseif (stripos($type, 'Password') !== false) {
            $this->actionText = 'reset your password';
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->type} - Verification Code",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
        );
    }
}
