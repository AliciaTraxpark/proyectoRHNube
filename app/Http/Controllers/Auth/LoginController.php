<?php

namespace App\Http\Controllers\Auth;

header("Refresh:7200");

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\organizacion;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect(route('dashboard'));
        } else {
            return view('Welcome');
        }
    }
    public function login()
    {
        $credentials = $this->validate(request(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect(route('dashboard'));
        } else {
            //return view('Welcome');
            return redirect()->route('login')
                ->with('error', 'Correo electronico o contraseña incorrecta');
        }
    }

    public function logout()
    {
        Auth::logout();
        /* return view ('welcome'); */
        return redirect(route('principal'));
    }

    public function principal()
    {

        if (Auth::check()) {
            Auth::logoutOtherDevices(request()->input('password'));
            return redirect('/dashboard');
        } else {
            return view('welcome');
        }
    }
}
