<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
class verificarUsuarioController extends Controller
{
    //
    public function checkSession()
    {
        return Response::json(['guest' => Auth::check()]);
    }

}
