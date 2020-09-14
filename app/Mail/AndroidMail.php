<?php

namespace App\Mail;

use App\organizacion;
use App\persona;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AndroidMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $persona;
    public $organizacion;
    public function __construct(persona $persona, organizacion $organizacion)
    {
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
        return $this->view('mails.empleadoA')->subject('RH nube');
    }
}
