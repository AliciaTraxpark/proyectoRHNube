<?php

namespace App\Mail;

use App\controladores;
use App\persona;
use App\organizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SoporteApiMovil extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $controlador;
     public $contenido;
     public $asunto;
     public $celular;
     public $organizacion;
    public function __construct($contenido, controladores $controlador,$asunto,$celular, organizacion $organizacion)
    {
        $this->contenido = $contenido;
        $this->controlador = $controlador;
        $this->asunto = $asunto;
        $this->celular = $celular;
        $this->organizacion = $organizacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.ticketSoporteApiMo');
    }
}
