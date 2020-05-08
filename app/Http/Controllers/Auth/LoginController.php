<?php

namespace App\Http\Controllers\Auth;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function showLoginForm(){
        $organizacion=organizacion::all();
        return view ('Welcome',['organizacion'=>$organizacion]);
    }
    public function login(){
        $credentials=$this->validate(request(),[
            'email'=>'required|string',
            'password'=>'required|string'
        ]);

        if(Auth::attempt($credentials)){
            return redirect(route('dashboard'));
        }
            return back()
            ->withInput(request(['email']));
    }

    public function logout(){
       Auth::logout();
       return redirect('/');
    }

}
