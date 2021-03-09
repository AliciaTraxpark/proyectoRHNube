<?php

namespace App\Mail;

use App\persona;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\organizacion;
class SoporteApi extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $persona;
     public $contenido;
     public $asunto;
     public $celular;
     public $organizacion;
    public function __construct($contenido, persona $persona,$asunto,$celular, organizacion $organizacion)
    {
        $this->contenido = $contenido;
        $this->persona = $persona;
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
        return $this->view('mails.ticketSoporteApi');
    }
}
