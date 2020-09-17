<?php

namespace App\Mail;

use App\organizacion;
use App\persona;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class correoAdministrativo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $contenido;
    public $asunto;
    public $organizacion;
    public $persona;
    public $user;
    public function __construct($contenido, string $asunto,organizacion $organizacion, persona $persona, User $user)
    {
        $this->contenido = $contenido;
        $this->asunto = $asunto;
        $this->organizacion = $organizacion;
        $this->persona = $persona;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.ticketReclamo')->subject('Ticket de Soporte');
    }
}
