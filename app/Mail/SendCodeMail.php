<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCodeMail extends Mailable
{
    
    use Queueable, SerializesModels;

    // private $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        $data = $this->details;
        return $this->view('welcome', $data);
    }
}
