<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // <-- implement this
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $subjectLine;
    public $view;

    public function __construct($name, $subjectLine, $view)
    {
        $this->name = $name;
        $this->subjectLine = $subjectLine;
        $this->view = $view;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view($this->view)
                    ->with([
                        'name' => $this->name,
                        'subject' => $this->subjectLine, // 👈 send to Blade
                    ]);
    }
}

