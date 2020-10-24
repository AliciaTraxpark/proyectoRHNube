<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class bibliotecaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function vista()
    {
        return view('videos.biblioteca');
    }
}
