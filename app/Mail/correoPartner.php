<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
 
class correoPartner extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $ruc;
    public $razonSocial;
    public $contacto;
    public $correo;
    public $telefono;
    public $mensaje;
    public $fecha;

    public function __construct($ruc, $razonSocial, $contacto, $correo, $telefono, $mensaje, $fecha)
    {
        $this->ruc = $ruc;
        $this->razonSocial = $razonSocial;
        $this->contacto = $contacto;
        $this->correo = $correo;
        $this->telefono = $telefono;
        $this->mensaje = $mensaje;
        $this->fecha = $fecha;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.agendaPartner')->subject('Solicitud de Partner');
    }
}
