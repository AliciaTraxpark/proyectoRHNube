<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class correoVinculacionPuerta extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $codigo;
    public $numero;

    public function __construct($codigo, $numero)
    {
        $this->codigo = $codigo;
        $this->numero = $numero;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.vinculacionPuerta')->subject('Vinculación: Asistencia en puerta');
    }
}
