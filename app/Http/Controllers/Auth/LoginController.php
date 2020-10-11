<?php

namespace App\Http\Controllers\Auth;

header("Refresh:3600");

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\usuario_organizacion;
use App\User;
use App\invitado;
use Illuminate\Support\Facades\Crypt;
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
        if (Auth::check()) {
            Auth::logoutOtherDevices(request()->input('password'));
        }
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
            Auth::logoutOtherDevices(request()->input('password'));
            $usuario_organizacion=usuario_organizacion::where('user_id','=', Auth::user()->id)->get()->first();

            if($usuario_organizacion!=null){
                if($usuario_organizacion->rol_id==4){
                    return redirect('/superadmin');
                }
                else{
                    $comusuario_organizacion=usuario_organizacion::where('user_id','=', Auth::user()->id)->count();
                    if($comusuario_organizacion>1) {


                        return redirect(route('elegirorganizacion'));
                           /* ->with('elegirEmpresa',' Elige tu empresa')  */;
                    }else{

                        $vars=$usuario_organizacion->organi_id;
                        session(['sesionidorg' => $vars]);
                        ///
                        $invitado=invitado::where('user_Invitado','=', Auth::user()->id)
                        ->where('organi_id','=', session('sesionidorg'))
                        ->where('estado_condic','=', 0)->get()->first();
                        /////
                        if($invitado){
                            Auth::logout();
                            session()->forget('sesionidorg');
                            /* return view ('welcome'); */
                            return redirect()->route('login')
                            ->with('error', 'Usuario no activado.');
                        }else{
                            return redirect(route('dashboard'));
                        }

                    }
                }
            } else{
                $id = Auth::user()->id;
                $user1 = Crypt::encrypt($id);
                return redirect('/registro/organizacion/'+$user1);
            }




        } else {
            $user = User::where('email', '=', request()->get('email'))->get()->first();
            if ($user) {
                return redirect()->route('login')
                    ->with('error', 'Correo electronico o contraseña incorrecta');
            } else {
                return redirect()->route('login')
                    ->with('error', 'Usuario no registrado.');
            }
        }
    }

    public function logout()
    {

        Auth::logout();
        session()->forget('sesionidorg');
        session()->flush();
        /* return view ('welcome'); */
        return redirect('/');
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
