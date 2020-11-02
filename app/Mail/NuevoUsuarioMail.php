<?php

namespace App\Mail;

use App\organizacion;
use App\persona;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $persona;
    public $organizacion;
    public $users;
    public function __construct(persona $persona, organizacion $organizacion, User $users)
    {
        $this->persona = $persona;
        $this->organizacion = $organizacion;
        $this->users = $users;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.nuevoUsuario');
    }
}
