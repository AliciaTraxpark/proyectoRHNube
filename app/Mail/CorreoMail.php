<?php

namespace App\Mail;

use App\organizacion;
use App\persona;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $datos;
    public $persona;
    public $organizacion;
    public function __construct(User $datos, persona $persona, organizacion $organizacion)
    {
        $this->datos = $datos;
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
        return $this->view('mails.confirmation_code')->subject('RH nube');
    }
}
