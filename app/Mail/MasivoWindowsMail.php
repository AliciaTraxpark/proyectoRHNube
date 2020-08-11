<?php

namespace App\Mail;

use App\organizacion;
use App\persona;
use App\vinculacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MasivoWindowsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $vinculacion;
    public $persona;
    public $organizacion;
    public function __construct(array $vinculacion, persona $persona, organizacion $organizacion)
    {
        $this->vinculacion = $vinculacion;
        $this->persona = $persona;
        $this->organizacion = $organizacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.masivoWindows')->subject('RH SOLUTION');
    }
}
