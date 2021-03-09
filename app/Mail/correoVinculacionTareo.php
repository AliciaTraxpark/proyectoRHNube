<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\organizacion;
class correoVinculacionTareo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $codigo;
    public $numero;
    public $organizacion;
    public function __construct($codigo, $numero, organizacion $organizacion)
    {
        $this->codigo = $codigo;
        $this->numero = $numero;
        $this->organizacion = $organizacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.vinculacionTareo')->subject('Vinculación: Modo Tareo');
    }
}
