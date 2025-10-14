<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyAccountUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public $verifyUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $verifyUrl)
    {
        $this->student = $student;
        $this->verifyUrl = $verifyUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Verify Your Account Update')
                    ->view('emails.VerifyEmail2');
    }
}
