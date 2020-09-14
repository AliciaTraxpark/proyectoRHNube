<?php

namespace App\Mail;

use App\organizacion;
use App\persona;
use App\User;
use App\invitado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoInvitado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $organizacion;
    public $invitado;
    public function __construct( organizacion $organizacion, invitado $invitado )
    {
       
        $this->organizacion = $organizacion;
        $this->invitado  = $invitado;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.confirmation_invitacion')->subject('RH nube');
    }
}
