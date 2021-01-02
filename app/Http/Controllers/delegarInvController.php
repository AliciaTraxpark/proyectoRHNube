<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\empleado;
use App\persona;
use App\User;
use App\invitado;
use App\organizacion;
use App\invitado_empleado;
use App\usuario_organizacion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CorreoInvitado;
use App\Mail\CorreoActivacion;
use App\Mail\CorreoMail;
use App\permiso_invitado;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Mockery\Undefined;

class delegarInvController extends Controller
{   /*SEPARAR ENVIAR CORREO public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    // */

    public function index(){
        if(session('sesionidorg')==null || session('sesionidorg')=='null' ){
            return redirect('/elegirorganizacion');
        } else{
        if (Auth::check()) {
            $invitado=DB::table('invitado as i')
            ->where('i.organi_id', '=',session('sesionidorg'))
            ->join('rol as r', 'i.rol_id', '=', 'r.rol_id')
            ->get();

        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->select(
                'e.emple_id',
                'p.perso_id',
                'p.perso_nombre',
                'e.emple_nDoc',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'a.area_descripcion',
                'e.emple_id',
                'a.area_id'
            )
            ->get();
            $area = DB::table('area as ar')
           /*  ->join('empleado as em', 'ar.area_id', '=', 'em.emple_area') */
            ->where('ar.organi_id','=',session('sesionidorg'))
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();


            $invitadod=DB::table('invitado')
            ->where('user_Invitado','=',Auth::user()->id)
            ->where('organi_id','=',session('sesionidorg'))
            ->get()->first();

                if($invitadod){
                    if ($invitadod->rol_id!=1){
                        return redirect('/dashboard');
                    }
                    else{
                        return view('delegarInvitado.delegarControl',['empleado'=>$empleado,'area'=>$area,'invitado'=>$invitado]);
                    }
                }

            else{
        return view('delegarInvitado.delegarControl',['empleado'=>$empleado,'area'=>$area,'invitado'=>$invitado]);}
    }
    else {
        return redirect(route('principal'));
    }
}
}
    public function empleAreaIn(Request $request){
        $idarea=$request->idarea;
        $arrayem = collect();
        foreach($idarea as $idareas){
            $empleadosArea = DB::table('empleado')
        ->where('organi_id', '=', session('sesionidorg'))
        ->where('emple_area', '=', $idareas)
        ->where('emple_estado', '=', 1)
        ->get();
        $arrayem->push($empleadosArea);
        }
        return $arrayem->toArray();

    }

    public function registrarInvitado(Request $request){

        $emailInv=$request->emailInv;
        $idEmpleado=$request->idEmpleado;
        $dash=$request->dash;
        $permisoEmp=$request->permisoEmp;
        $switchActividades=$request->switchActividades;
        $switchasisPuerta=$request->switchasisPuerta;
        $switchCRemo=$request->switchCRemo;
        $switchCRuta=$request->switchCRuta;
        $checkTodoEmp=$request->checkTodoEmp;
        $swReporteAsis=$request->swReporteAsis;
        $swMoReporteAsis=$request->swMoReporteAsis;

        $agregarEmp=$request->agregarEmp;
        $modifEmp=$request->modifEmp;
        $bajaEmp=$request->bajaEmp;
        $gActiEmp=$request->gActiEmp;
        $agregarActi=$request->agregarActi;
        $modifActi=$request->modifActi;
        $bajaActi=$request->bajaActi;
        $verPuerta=$request->verPuerta;
        $agregPuerta=$request->agregPuerta;
        $ModifPuerta=$request->ModifPuerta;

        $organi = organizacion::find(session('sesionidorg'));
        $invitado = new invitado();
        $invitado->organi_id =  session('sesionidorg');
        $invitado->rol_id =3;
        $invitado->email_inv = $emailInv;
        $invitado->estado =0;
        $invitado->users_id =Auth::user()->id;
        $invitado->dashboard =$dash;
        $invitado->estado_condic=1;
        $invitado->permiso_Emp=$permisoEmp;
        $invitado->modoCR=$switchCRemo;
        $invitado->ControlRuta=$switchCRuta;
        $invitado->gestionActiv=$switchActividades;
        $invitado->asistePuerta=$switchasisPuerta;
        $invitado->verTodosEmps=$checkTodoEmp;
        $invitado->reporteAsisten=$swReporteAsis;
        $invitado->ModificarReportePuerta=$swMoReporteAsis;
        $invitado->save();
        if($checkTodoEmp!=1){
        foreach($idEmpleado as $idEmpleados){
            $invitado_empleado = new invitado_empleado();
            $invitado_empleado->idinvitado = $invitado->idinvitado;
            $invitado_empleado->emple_id = $idEmpleados;
            $invitado_empleado->save();
        }
       }
        $permiso_invitado= new permiso_invitado();
        $permiso_invitado->idinvitado =$invitado->idinvitado;
        if($permisoEmp==1){
        $permiso_invitado->agregarEmp=$agregarEmp;
        $permiso_invitado->modifEmp=$modifEmp;
        $permiso_invitado->bajaEmp=$bajaEmp;
        $permiso_invitado->GestActEmp=$gActiEmp;
        }
        else{
            $permiso_invitado->agregarEmp=0;
            $permiso_invitado->modifEmp=0;
            $permiso_invitado->bajaEmp=0;
            $permiso_invitado->GestActEmp=0;
        }

        if($switchActividades==1){
        $permiso_invitado->agregarActi=$agregarActi;
        $permiso_invitado->modifActi=$modifActi;
        $permiso_invitado->bajaActi=$bajaActi;
        } else{
        $permiso_invitado->agregarActi=0;
        $permiso_invitado->modifActi=0;
        $permiso_invitado->bajaActi=0;
        }

        if($switchasisPuerta==1){
            $permiso_invitado->verPuerta=$verPuerta;
            $permiso_invitado->agregarPuerta=$agregPuerta;
            $permiso_invitado->modifPuerta=$ModifPuerta;
        } else{
            $permiso_invitado->verPuerta=0;
            $permiso_invitado->agregarPuerta=0;
            $permiso_invitado->modifPuerta=0;
        }

        $permiso_invitado->save();

        Mail::to($emailInv)->queue(new CorreoInvitado($organi,$invitado));

    }
    public function registrarInvitadoAdm(Request $request){

        $emailInv=$request->emailInv;


        $organi = organizacion::find(session('sesionidorg'));
        $invitado = new invitado();
        $invitado->organi_id =  session('sesionidorg');
        $invitado->rol_id =1;
        $invitado->email_inv = $emailInv;
        $invitado->estado =0;
        $invitado->dashboard =1;
        $invitado->users_id =Auth::user()->id;
        $invitado->estado_condic=1;
        $invitado->save();

        Mail::to($emailInv)->queue(new CorreoInvitado($organi,$invitado));

    }

    public function vistaRegistroInv($idinEncr){

        $idInvit = Crypt::decrypt($idinEncr);
        return view('registro.registroInvitado',['idInvit'=>$idInvit]);
    }

    public function registroInvitado(Request $request){

        $idinvitado=$request->idinvita;
        //  registro persona y user
        $persona = new persona();
        $persona->perso_nombre = $request->get('nombres');
        $persona->perso_apPaterno = $request->get('apPaterno');
        $persona->perso_apMaterno = $request->get('apMaterno');
        $persona->perso_direccion = $request->get('direccion');
        $diaf = $request->get('dia_fecha');
        $mesf = $request->get('mes_fecha');
        $anof = $request->get('ano_fecha');

        $fechaN = $anof . "-" . $mesf . "-" . $diaf;
        $persona->perso_fechaNacimiento = $fechaN;

        $persona->perso_sexo = $request->get('sexo');
        $persona->perso_celular = $request->get('n_celular');

        $persona->save();
        $user_persona = $persona->perso_id;

        $data['confirmation_code'] = STR::random(25);

        $User = new User();
        $User->email = $request->get('email');

        $User->perso_id = $user_persona;
        $User->user_estado = 1;
        $User->password = Hash::make($request->get('password'));
        $User->confirmation_code = $data['confirmation_code'];
        $User->save();

        //registro en organizacion
        $invitado = DB::table('invitado')
            ->where('idinvitado', '=',  $idinvitado)
            ->get()->first();
        $usuario_organizacion = new usuario_organizacion();
            $usuario_organizacion->user_id =$User->id;
            $usuario_organizacion->organi_id = $invitado->organi_id;
            $usuario_organizacion->rol_id =$invitado->rol_id;
            $usuario_organizacion->save();

         //actualiza invitado
         $invitadoAct  = DB::table('invitado')
         ->where('idinvitado', '=',  $idinvitado)
            ->update(['estado' => 1,'user_Invitado'=> $User->id]);
        //////////////





            $data = DB::table('users as u')
                ->select('u.email', 'u.email_verified_at', 'confirmation_code')
                ->where('u.id', '=', $User->id)
                ->get();
            $idPersona = DB::table('users as u')
                ->join('persona as p', 'u.perso_id', 'p.perso_id', 'p.')
                ->select('p.perso_id')
                ->where('u.id', '=', $User->id)
                ->get();
            $datos = [];
            $persona = [];
            $persona["id"] = $idPersona[0]->perso_id;
            $datos["email"] = $data[0]->email;
            $datos["email_verified_at"] = $data[0]->email_verified_at;
            $datos["confirmation_code"] = $data[0]->confirmation_code;
            $persona = persona::find($persona["id"]);
            $users = User::find($User->id);
            $organi = organizacion::find($invitado->organi_id);
            $correo = array($datos['email']);
            $datoNuevo = explode("@", $data[0]->email);
                Mail::to($correo)->queue(new CorreoMail($users, $persona, $organi));
               /*  return view('home')->with('mensaje', "Bien hecho, estas registrado! Te hemos enviado un correo de verificación."); */

    }

    public function validaremailC(Request $request){
        $emailUser = $request->get('email');
        $cantigua=$request->get('clave');

       /*  $cnueva= $request->get('cnueva'); */
        $user = User::where('email', '=',$emailUser)->get()->first();

        $id = $user->id;
        $user1 = Crypt::encrypt($id);
       /*  if (Auth::attempt(['email' => $user->email, 'password' => $cantigua])) { */
            if (Hash::check($cantigua,$user->password)) {
            // Authentication passed...
           /*  $user =DB::table('users')
            ->where('id', '=', Auth::user()->id)
            ->update(['password' => Hash::make($cnueva)]); */
            return [1,$user1];
        } else { return [0,$user1];}

    }
    public function validaremailCInvita(Request $request){
        $emailUser = $request->get('email');
        $cantigua=$request->get('clave');

       /*  $cnueva= $request->get('cnueva'); */
        $user = User::where('email', '=',$emailUser)->get()->first();

        $id = $user->id;

            if (Hash::check($cantigua,$user->password)) {

            return [1,$id];
        } else { return [0,$id];}

    }

    public function registrarEmailBD(Request $request){
        $idinvitado=$request->idinvitado;
        $iduser=$request->iduser;
         //registro en organizacion
         $invitado = DB::table('invitado')
         ->where('idinvitado', '=',  $idinvitado)
         ->get()->first();
     $usuario_organizacion = new usuario_organizacion();
         $usuario_organizacion->user_id =$iduser;
         $usuario_organizacion->organi_id = $invitado->organi_id;
         $usuario_organizacion->rol_id =  $invitado->rol_id;
         $usuario_organizacion->save();

      //actualiza invitado
      $invitadoAct  = DB::table('invitado')
      ->where('idinvitado', '=',  $idinvitado)
      ->update(['estado' => 1,'user_Invitado'=> $iduser]);
    }

    public function verificarEmaD(Request $request){
        $email=$request->email;
        $invitado=DB::table('invitado')
        ->where('organi_id','=',session('sesionidorg'))
        ->where('email_inv','=',  $email)
        ->get();

        $usuario_organizacion=DB::table('usuario_organizacion')
        ->join('users','usuario_organizacion.user_id','=','users.id')
        ->where('organi_id','=',session('sesionidorg'))
        ->where('users.email','=',  $email)
        ->get();
        if(count($invitado) || count($usuario_organizacion)){
            return 1;
        } else{
            return 0;
        }
    }

    public function verificarInvitadoreg(Request $request){
        $idinvitado=$request->idinvitado;
        $invitado=DB::table('invitado')
        ->where('idinvitado','=', $idinvitado)
        ->get();
        if($invitado[0]->estado==1){
            return 1;
        } else{
            return 0;
        }
    }

    public function datosInvitado(Request $request){
        $idinvitado=$request->idi;

        $invitadoG=DB::table('invitado as i')
        ->where('i.idinvitado','=', $idinvitado)->get()->first();

        $invitado=DB::table('invitado as i')
        ->where('i.idinvitado','=', $idinvitado)
        ->join('invitado_empleado as inve','i.idinvitado','=','inve.idinvitado')
        ->join('permiso_invitado as pi', 'i.idinvitado','=','pi.idinvitado')
        ->get();
        $invitado2=DB::table('invitado as i')
        ->where('i.idinvitado','=', $idinvitado)
        ->get();
        $invitado3=DB::table('invitado as i')
        ->where('i.idinvitado','=', $idinvitado)
        ->join('permiso_invitado as pi', 'i.idinvitado','=','pi.idinvitado')
        ->get();


        if($invitadoG->rol_id==1){
            return $invitado2;

        } else{
            if($invitadoG->verTodosEmps==1){
                return $invitado3;
            } else{
                return $invitado;
            }
        }

    }


    public function editarInviAdm(Request $request){
        $idinvitado=$request->idinvitado;

        $invitado = invitado::find( $idinvitado);
        if($invitado->rol_id==1){

        }
        else{
            $invitado_empleado = invitado_empleado::where('idinvitado','=', $idinvitado)->get();
            $invitado_empleado->each->delete();

            DB::table('permiso_invitado')->where('idinvitado', '=', $idinvitado)->delete();

            $invitadoAct  = DB::table('invitado')
            ->where('idinvitado', '=',  $idinvitado)
               ->update(['rol_id' => 1,'users_id'=>Auth::user()->id,'dashboard'=> 1]);



               $usuario_organizacion =DB::table('usuario_organizacion')
               ->where('user_id', '=', $invitado->user_Invitado )
               ->where('organi_id', '=', session('sesionidorg'))
               ->update(['rol_id' => 1]);
        }

    }

   public function editarInviI(Request $request ){
    $idinvitado=$request->idinvitado;
    $idEmpleado=$request->idEmpleado;
    $dash_ed=$request->dash_ed;
    $permisoEmp_ed=$request->permisoEmp_ed;

    $switchActividades_ed=$request->switchActividades_ed;
    $switchasisPuerta_ed=$request->switchasisPuerta_ed;
    $switchCRemo_ed=$request->switchCRemo_ed;
    $switchCRuta_ed=$request->switchCRuta_ed;
    $swReporteAsis_ed=$request->swReporteAsis_ed;
    $swMoReporteAsis_ed=$request->swMoReporteAsis_ed; //aqui me quede falta buscar y agregar
    $checkTodoEmp_ed=$request->checkTodoEmp_ed;

    $agregarEmp_ed=$request->agregarEmp_ed;
    $modifEmp_ed=$request->modifEmp_ed;
    $bajaEmp_ed=$request->bajaEmp_ed;
    $gActiEmp_ed=$request->gActiEmp_ed;
    $agregarActi_ed=$request->agregarActi_ed;
    $modifActi_ed=$request->modifActi_ed;
    $bajaActi_ed=$request->bajaActi_ed;
    $verPuerta_ed=$request->verPuerta_ed;
    $agregPuerta_ed=$request->agregPuerta_ed;
    $ModifPuerta_ed=$request->ModifPuerta_ed;

    $invitado = invitado::find( $idinvitado);
    if($invitado->rol_id==3){
        ///delete all emp
       $invitado_empleado = invitado_empleado::where('idinvitado','=', $idinvitado)->get();
       $invitado_empleado->each->delete();
       if($checkTodoEmp_ed==0){
        ////copiar empleado
       foreach($idEmpleado as $idEmpleados){
        $invitado_empleado = new invitado_empleado();
        $invitado_empleado->idinvitado = $invitado->idinvitado;
        $invitado_empleado->emple_id = $idEmpleados;
        $invitado->estado_condic=1;
        $invitado_empleado->save();
        }
      }

        ///
        $invitadoAct  = DB::table('invitado')
        ->where('idinvitado', '=',  $idinvitado)
           ->update(['users_id'=>Auth::user()->id,'dashboard'=> $dash_ed,'permiso_Emp'=>$permisoEmp_ed,
           'modoCR'=> $switchCRemo_ed,'ControlRuta'=>$switchCRuta_ed,'gestionActiv'=>$switchActividades_ed,'asistePuerta'=> $switchasisPuerta_ed,
           'verTodosEmps'=>$checkTodoEmp_ed,
           'reporteAsisten'=> $swReporteAsis_ed, 'ModificarReportePuerta'=> $swMoReporteAsis_ed ]);

           //actualizar permiso invitado
           $permisoActu  = DB::table('permiso_invitado')
        ->where('idinvitado', '=',  $idinvitado)
           ->update(['agregarEmp'=>$agregarEmp_ed,'modifEmp'=> $modifEmp_ed,'bajaEmp'=>$bajaEmp_ed,
           'GestActEmp'=>$gActiEmp_ed,'agregarActi'=>$agregarActi_ed,'modifActi'=> $modifActi_ed,
           'bajaActi'=>$bajaActi_ed,
           'verPuerta'=> $verPuerta_ed, 'agregarPuerta'=> $agregPuerta_ed, 'modifPuerta'=> $ModifPuerta_ed ]);
    }
    else{
        if($checkTodoEmp_ed==0){
        foreach($idEmpleado as $idEmpleados){
            $invitado_empleado = new invitado_empleado();
            $invitado_empleado->idinvitado = $invitado->idinvitado;
            $invitado_empleado->emple_id = $idEmpleados;
            $invitado_empleado->save();
        }
       }
        $invitadoAct  = DB::table('invitado')
        ->where('idinvitado', '=',  $idinvitado)
           ->update(['rol_id' => 3,'users_id'=>Auth::user()->id,'dashboard'=> $dash_ed, 'permiso_Emp'=>$permisoEmp_ed,
           'modoCR'=> $switchCRemo_ed,'ControlRuta'=>$switchCRuta_ed,'gestionActiv'=>$switchActividades_ed,'asistePuerta'=> $switchasisPuerta_ed,
           'verTodosEmps'=>$checkTodoEmp_ed,
           'reporteAsisten'=> $swReporteAsis_ed, 'ModificarReportePuerta'=> $swMoReporteAsis_ed]);

           $usuario_organizacion =DB::table('usuario_organizacion')
           ->where('user_id', '=', $invitado->user_Invitado )
           ->where('organi_id', '=', session('sesionidorg'))
           ->update(['rol_id' => 3]);
           ////////////////////////////////////////////////////////////////
           $permiso_invitado= new permiso_invitado();
        $permiso_invitado->idinvitado =$invitado->idinvitado;
        if($permisoEmp_ed==1){
        $permiso_invitado->agregarEmp=$agregarEmp_ed;
        $permiso_invitado->modifEmp=$modifEmp_ed;
        $permiso_invitado->bajaEmp=$bajaEmp_ed;
        $permiso_invitado->GestActEmp=$gActiEmp_ed;
        }
        else{
            $permiso_invitado->agregarEmp=0;
            $permiso_invitado->modifEmp=0;
            $permiso_invitado->bajaEmp=0;
            $permiso_invitado->GestActEmp=0;
        }

        if($switchActividades_ed==1){
        $permiso_invitado->agregarActi=$agregarActi_ed;
        $permiso_invitado->modifActi=$modifActi_ed;
        $permiso_invitado->bajaActi=$bajaActi_ed;
        } else{
        $permiso_invitado->agregarActi=0;
        $permiso_invitado->modifActi=0;
        $permiso_invitado->bajaActi=0;
        }

        if($switchasisPuerta_ed==1){
            $permiso_invitado->verPuerta=$verPuerta_ed;
            $permiso_invitado->agregarPuerta=$agregPuerta_ed;
            $permiso_invitado->modifPuerta=$ModifPuerta_ed;
        } else{
            $permiso_invitado->verPuerta=0;
            $permiso_invitado->agregarPuerta=0;
            $permiso_invitado->modifPuerta=0;
        }

        $permiso_invitado->save();
           ////////////////////////////////////////////////////////////////
    }
   }

   public function cambInvitadoswit(Request $request){
    $idinvitado=$request->idinvitado;
    $estadosw=$request->estadosw;

    $invitadoAct  = DB::table('invitado')
        ->where('idinvitado', '=',  $idinvitado)
           ->update(['estado_condic' => $estadosw]);



   }

   public function notificarInv(Request $request){
    $idinvitado=$request->idinvitado;
    $estadosw=$request->estadosw;


    if($estadosw==1){
        $invitado  = invitado::find($idinvitado);
           $emailInv=$invitado->email_inv;

           $organi = organizacion::find(session('sesionidorg'));
        Mail::to($emailInv)->queue(new CorreoActivacion($organi,$invitado));

    }

   }

   public function registrarInvitadoAreas(Request $request){

    $emailInv=$request->emailInv;
    $idarea=$request->idareas;
    $dash=$request->dash;
    $permisoEmp=$request->permisoEmp;
    $switchActividades=$request->switchActividades;
    $switchasisPuerta=$request->switchasisPuerta;
    $switchCRemo=$request->switchCRemo;
    $switchCRuta=$request->switchCRuta;
    $checkTodoEmp=$request->checkTodoEmp;
    $swReporteAsis=$request->swReporteAsis;
    $swMoReporteAsis=$request->swMoReporteAsis;

    $agregarEmp=$request->agregarEmp;
    $modifEmp=$request->modifEmp;
    $bajaEmp=$request->bajaEmp;
    $gActiEmp=$request->gActiEmp;
    $agregarActi=$request->agregarActi;
    $modifActi=$request->modifActi;
    $bajaActi=$request->bajaActi;
    $verPuerta=$request->verPuerta;
    $agregPuerta=$request->agregPuerta;
    $ModifPuerta=$request->ModifPuerta;

    $organi = organizacion::find(session('sesionidorg'));
    $invitado = new invitado();
    $invitado->organi_id =  session('sesionidorg');
    $invitado->rol_id =3;
    $invitado->email_inv = $emailInv;
    $invitado->estado =0;
    $invitado->users_id =Auth::user()->id;
    $invitado->dashboard =$dash;
    $invitado->estado_condic=1;
    $invitado->permiso_Emp=$permisoEmp;
    $invitado->modoCR=$switchCRemo;
    $invitado->ControlRuta=$switchCRuta;
    $invitado->gestionActiv=$switchActividades;
    $invitado->asistePuerta=$switchasisPuerta;
    $invitado->verTodosEmps=$checkTodoEmp;
    $invitado->reporteAsisten=$swReporteAsis;
    $invitado->ModificarReportePuerta=$swMoReporteAsis;
    $invitado->save();

    if($checkTodoEmp!=1){
    foreach($idarea as $idareas){
        $invitado_empleado = new invitado_empleado();
        $invitado_empleado->idinvitado = $invitado->idinvitado;
        $invitado_empleado->area_id = $idareas;
        $invitado_empleado->save();
    }
  }

    $permiso_invitado= new permiso_invitado();
        $permiso_invitado->idinvitado =$invitado->idinvitado;
        if($permisoEmp==1){
            $permiso_invitado->agregarEmp=$agregarEmp;
            $permiso_invitado->modifEmp=$modifEmp;
            $permiso_invitado->bajaEmp=$bajaEmp;
            $permiso_invitado->GestActEmp=$gActiEmp;
            }
            else{
                $permiso_invitado->agregarEmp=0;
                $permiso_invitado->modifEmp=0;
                $permiso_invitado->bajaEmp=0;
                $permiso_invitado->GestActEmp=0;
            }

            if($switchActividades==1){
                $permiso_invitado->agregarActi=$agregarActi;
                $permiso_invitado->modifActi=$modifActi;
                $permiso_invitado->bajaActi=$bajaActi;
                } else{
                $permiso_invitado->agregarActi=0;
                $permiso_invitado->modifActi=0;
                $permiso_invitado->bajaActi=0;
                }
                if($switchasisPuerta==1){
                    $permiso_invitado->verPuerta=$verPuerta;
                    $permiso_invitado->agregarPuerta=$agregPuerta;
                    $permiso_invitado->modifPuerta=$ModifPuerta;
                } else{
                    $permiso_invitado->verPuerta=0;
                    $permiso_invitado->agregarPuerta=0;
                    $permiso_invitado->modifPuerta=0;
                }
        $permiso_invitado->save();

    Mail::to($emailInv)->queue(new CorreoInvitado($organi,$invitado));

}
public function editarInviArea(Request $request){
    $idinvitado=$request->idinvitado;
    $idareas_edit=$request->idareas_edit;
    $dash_ed=$request->dash_ed;
    $permisoEmp_ed=$request->permisoEmp_ed;

    $switchActividades_ed=$request->switchActividades_ed;
    $switchasisPuerta_ed=$request->switchasisPuerta_ed;
    $switchCRemo_ed=$request->switchCRemo_ed;
    $switchCRuta_ed=$request->switchCRuta_ed;
    $swReporteAsis_ed=$request->swReporteAsis_ed;
    $swMoReporteAsis_ed=$request->swMoReporteAsis_ed;
    $checkTodoEmp_ed=$request->checkTodoEmp_ed;

    $agregarEmp_ed=$request->agregarEmp_ed;
    $modifEmp_ed=$request->modifEmp_ed;
    $bajaEmp_ed=$request->bajaEmp_ed;
    $gActiEmp_ed=$request->gActiEmp_ed;
    $agregarActi_ed=$request->agregarActi_ed;
    $modifActi_ed=$request->modifActi_ed;
    $bajaActi_ed=$request->bajaActi_ed;
    $verPuerta_ed=$request->verPuerta_ed;
    $agregPuerta_ed=$request->agregPuerta_ed;
    $ModifPuerta_ed=$request->ModifPuerta_ed;

    $invitado = invitado::find( $idinvitado);
    if($invitado->rol_id==3){
        ///delete all emp
       $invitado_empleado = invitado_empleado::where('idinvitado','=', $idinvitado)->get();
       $invitado_empleado->each->delete();
       if($checkTodoEmp_ed==0){
        ////copiar empleado
       foreach($idareas_edit as $idareas_edits){
        $invitado_empleado = new invitado_empleado();
        $invitado_empleado->idinvitado = $invitado->idinvitado;
        $invitado_empleado->area_id = $idareas_edits;

        $invitado_empleado->save();
        }
    }
        ///
        $invitadoAct  = DB::table('invitado')
        ->where('idinvitado', '=',  $idinvitado)
           ->update(['users_id'=>Auth::user()->id,'dashboard'=> $dash_ed,'permiso_Emp'=>$permisoEmp_ed,
           'modoCR'=> $switchCRemo_ed, 'ControlRuta'=>$switchCRuta_ed,'gestionActiv'=>$switchActividades_ed,'asistePuerta'=> $switchasisPuerta_ed,
           'verTodosEmps'=>$checkTodoEmp_ed,
           'reporteAsisten'=> $swReporteAsis_ed, 'ModificarReportePuerta'=> $swMoReporteAsis_ed ]);
           //actualizar permiso invitado
           $permisoActu  = DB::table('permiso_invitado')
        ->where('idinvitado', '=',  $idinvitado)
           ->update(['agregarEmp'=>$agregarEmp_ed,'modifEmp'=> $modifEmp_ed,'bajaEmp'=>$bajaEmp_ed,
           'GestActEmp'=>$gActiEmp_ed,'agregarActi'=>$agregarActi_ed,'modifActi'=> $modifActi_ed,
           'bajaActi'=>$bajaActi_ed,
           'verPuerta'=> $verPuerta_ed, 'agregarPuerta'=> $agregPuerta_ed, 'modifPuerta'=> $ModifPuerta_ed ]);

    }
    else{
        if($checkTodoEmp_ed==0){
        foreach($idareas_edit as $idareas_edits){
            $invitado_empleado = new invitado_empleado();
            $invitado_empleado->idinvitado = $invitado->idinvitado;
            $invitado_empleado->area_id = $idareas_edits;
            $invitado_empleado->save();
        }
    }

        $invitadoAct  = DB::table('invitado')
        ->where('idinvitado', '=',  $idinvitado)
           ->update(['rol_id' => 3,'users_id'=>Auth::user()->id,'dashboard'=> $dash_ed, 'permiso_Emp'=>$permisoEmp_ed,
           'modoCR'=> $switchCRemo_ed, 'ControlRuta'=>$switchCRuta_ed, 'gestionActiv'=>$switchActividades_ed,'asistePuerta'=> $switchasisPuerta_ed,
           'verTodosEmps'=>$checkTodoEmp_ed,
           'reporteAsisten'=> $swReporteAsis_ed, 'ModificarReportePuerta'=> $swMoReporteAsis_ed]);

           $usuario_organizacion =DB::table('usuario_organizacion')
           ->where('user_id', '=', $invitado->user_Invitado )
           ->where('organi_id', '=', session('sesionidorg'))
           ->update(['rol_id' => 3]);
           ////////////////////////////////////////////////////////////////
           $permiso_invitado= new permiso_invitado();
        $permiso_invitado->idinvitado =$invitado->idinvitado;
        if($permisoEmp_ed==1){
        $permiso_invitado->agregarEmp=$agregarEmp_ed;
        $permiso_invitado->modifEmp=$modifEmp_ed;
        $permiso_invitado->bajaEmp=$bajaEmp_ed;
        $permiso_invitado->GestActEmp=$gActiEmp_ed;
        }
        else{
            $permiso_invitado->agregarEmp=0;
            $permiso_invitado->modifEmp=0;
            $permiso_invitado->bajaEmp=0;
            $permiso_invitado->GestActEmp=0;
        }

        if($switchActividades_ed==1){
        $permiso_invitado->agregarActi=$agregarActi_ed;
        $permiso_invitado->modifActi=$modifActi_ed;
        $permiso_invitado->bajaActi=$bajaActi_ed;
        } else{
        $permiso_invitado->agregarActi=0;
        $permiso_invitado->modifActi=0;
        $permiso_invitado->bajaActi=0;
        }

        if($switchasisPuerta_ed==1){
            $permiso_invitado->verPuerta=$verPuerta_ed;
            $permiso_invitado->agregarPuerta=$agregPuerta_ed;
            $permiso_invitado->modifPuerta=$ModifPuerta_ed;
        } else{
            $permiso_invitado->verPuerta=0;
            $permiso_invitado->agregarPuerta=0;
            $permiso_invitado->modifPuerta=0;
        }

        $permiso_invitado->save();
           ////////////////////////////////////////////////////////////////
    }
}

public function verificarEmaDSiEdi(Request $request){
    $email=$request->email;
    $invitado=DB::table('invitado')
    ->where('organi_id','=',session('sesionidorg'))
    ->where('email_inv','=',  $email)
    ->get();

    $usuario_organizacion=DB::table('usuario_organizacion')
    ->join('users','usuario_organizacion.user_id','=','users.id')
    ->where('organi_id','=',session('sesionidorg'))
    ->where('users.email','=',  $email)
    ->get();
    if(count($invitado) || count($usuario_organizacion)){
        return [1, $invitado[0]->idinvitado];
    } else{
        return 0;
    }
}
}
