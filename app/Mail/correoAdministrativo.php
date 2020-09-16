<?php

namespace App\Mail;

use App\organizacion;
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
    public $user;
    public function __construct($contenido, string $asunto,organizacion $organizacion, User $user)
    {
        $this->contenido = $contenido;
        $this->asunto = $asunto;
        $this->organizacion = $organizacion;
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
