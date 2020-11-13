<?php

namespace App\Mail;

use App\controladores;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SugerenciaApiMovil extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $contenido;
    public $controlador;
    public $asunto;
    public $celular;
    public function __construct($contenido, controladores $controlador,$asunto,$celular)
    {
        $this->contenido = $contenido;
        $this->controlador = $controlador;
        $this->asunto = $asunto;
        $this->celular = $celular;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.ticketSugerenciaApiMo');
    }
}
