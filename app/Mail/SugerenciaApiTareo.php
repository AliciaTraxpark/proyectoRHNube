<?php

namespace App\Mail;

use App\controladores_tareo;
use App\organizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SugerenciaApiTareo extends Mailable
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
    public $organizacion;
    public function __construct($contenido, controladores_tareo $controlador,$asunto,$celular, organizacion $organizacion)
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
        return $this->view('mails.ticketSugerenciaApiTareo');
    }
}
