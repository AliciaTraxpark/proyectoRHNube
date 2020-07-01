<?php

namespace App\Mail;

use App\empleado;
use App\persona;
use App\vinculacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoEmpleadoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $vinculacion;
    public $persona;
    public function __construct(vinculacion $vinculacion, persona $persona)
    {
        $this->vinculacion = $vinculacion;
        $this->persona = $persona;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.vinculacion');
    }
}
