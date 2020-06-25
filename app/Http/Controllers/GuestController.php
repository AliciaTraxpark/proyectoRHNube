<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function verify($code)
    {
        $user = User::where('confirmation_code', $code)->first();

        if (!$user)
            return redirect('/');

        $user->email_verified_at = time();
        $user->confirmation_code = null;
        $user->save();

        return redirect('/dashboard')->with('notification', 'Has confirmado correctamente tu correo!');
    }
}
