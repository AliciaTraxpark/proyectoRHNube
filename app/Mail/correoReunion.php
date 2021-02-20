<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
 
class correoReunion extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $nombres;
    public $apellidos;
    public $telefono;
    public $email;
    public $empresa;
    public $cargo;
    public $nTrabajadores;
    public $fecha;
    public $comentario;
    public $horario;
    
    public function __construct($nombres, $apellidos, $telefono, $email, $empresa, $cargo, $nTrabajadores, $comment, $fecha, $horario)
    {
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->empresa = $empresa;
        $this->cargo = $cargo;
        $this->nTrabajadores = $nTrabajadores;
        $this->fecha = $fecha;
        $this->comentario = $comment;
        $this->horario = $horario;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.agendaReunion')->subject('Agenda reunión');
    }
}
