<?php

namespace App\Mail;

use App\licencia_empleado;
use App\persona;
use App\vinculacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoMasivoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $vinculacion;
    public $persona;
    public $licencia_empleado;
    public function __construct(vinculacion $vinculacion, persona $persona, licencia_empleado $licencia_empleado)
    {
        $this->vinculacion = $vinculacion;
        $this->persona = $persona;
        $this->licencia_empleado = $licencia_empleado;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.correoMasivo')->subject('RH SOLUTION');
    }
}