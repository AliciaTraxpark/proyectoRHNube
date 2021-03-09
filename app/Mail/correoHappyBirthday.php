<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\organizacion;
class correoHappyBirthday extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $datos;
    public $organizacion;
    public function __construct($datos, organizacion $organizacion)
    {
        $this->datos = $datos;
        $this->organizacion = $organizacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.happyBirthday')->subject('CUMPLEAÑOS DE COMPAÑEROS');
    }
}
