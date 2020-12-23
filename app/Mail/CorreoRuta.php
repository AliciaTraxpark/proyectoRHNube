<?php

namespace App\Mail;

use App\persona;
use App\vinculacion_ruta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoRuta extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $persona;
    public $vinculacion_ruta;
    public function __construct(persona $persona, vinculacion_ruta $vinculacion_ruta)
    {
        $this->persona = $persona;
        $this->vinculacion_ruta = $vinculacion_ruta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.vinculacionRuta')->subject('RH nube');
    }
}
