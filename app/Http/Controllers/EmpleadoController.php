<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\actividad;
use Illuminate\Support\Facades\Hash;
use App\empleado;
use App\area;
use App\cargo;
use App\centro_costo;
use Illuminate\Http\Request;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_districts;
use App\tipo_documento;
use App\tipo_contrato;
use App\nivel;
use App\local;
use App\modo;
use App\persona;
use App\proyecto;
use App\proyecto_empleado;
use App\tarea;
use App\tipo_dispositivo;
use App\User;
use App\vinculacion;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illiminate\Support\Facades\File;


class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except('provincias', 'distritos', 'fechas');
    }
    public function fechas($id)
    {
        return tipo_contrato::where('contrato_id', $id)->get();
    }
    public function provincias($id)
    {
        return ubigeo_peru_provinces::where('departamento_id', $id)->get();
    }
    public function distritos($id)
    {
        return ubigeo_peru_districts::where('province_id', $id)->get();
    }
    public function index()
    {

        $departamento = ubigeo_peru_departments::all();
        $provincia = ubigeo_peru_provinces::all();
        $distrito = ubigeo_peru_districts::all();
        $tipo_doc = tipo_documento::all();
        $tipo_cont = tipo_contrato::all();
        $area = area::all();
        $cargo = cargo::all();
        $centro_costo = centro_costo::all();
        $nivel = nivel::all();
        $local = local::all();
        $empleado = empleado::all();
        $dispositivo = tipo_dispositivo::all();
        $tabla_empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select(
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'c.cargo_descripcion',
                'a.area_descripcion',
                'cc.centroC_descripcion',
                'e.emple_id'
            )
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();
        //dd($tabla_empleado);
        return view('empleado.empleado', [
            'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
            'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
            'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo
        ]);
    }
    public function cargarDatos()
    {   //DATOS DE TABLA PARA CARGAR EXCEL

        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('tipo_documento as tipoD', 'e.emple_tipoDoc', '=', 'tipoD.tipoDoc_id')
            ->leftJoin('ubigeo_peru_departments as depar', 'e.emple_departamento', '=', 'depar.id')
            ->leftJoin('ubigeo_peru_provinces as provi', 'e.emple_provincia', '=', 'provi.id')
            ->leftJoin('ubigeo_peru_districts as dist', 'e.emple_distrito', '=', 'dist.id')

            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('ubigeo_peru_departments as para', 'e.emple_departamentoN', '=', 'para.id')
            ->leftJoin('ubigeo_peru_provinces as proviN', 'e.emple_provinciaN', '=', 'proviN.id')
            ->leftJoin('ubigeo_peru_districts as distN', 'e.emple_distritoN', '=', 'distN.id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')


            ->select(
                'e.emple_id',
                'p.perso_id',
                'p.perso_nombre',
                'tipoD.tipoDoc_descripcion',
                'e.emple_nDoc',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'p.perso_fechaNacimiento',
                'p.perso_direccion',
                'p.perso_sexo',
                'depar.id as depar',
                'depar.name as deparNom',
                'provi.id as proviId',
                'provi.name as provi',
                'dist.id as distId',
                'dist.name as distNo',
                'c.cargo_descripcion',
                'a.area_descripcion',
                'cc.centroC_descripcion',
                'para.id as iddepaN',
                'para.name as depaN',
                'proviN.id as idproviN',
                'proviN.name as proviN',
                'distN.id as iddistN',
                'distN.name as distN',
                'e.emple_id',
                'c.cargo_id',
                'a.area_id',
                'cc.centroC_id',
                'e.emple_tipoContrato',
                'e.emple_local',
                'e.emple_nivel',
                'e.emple_departamento',
                'e.emple_provincia',
                'e.emple_distrito',
                'e.emple_foto as foto',
                'e.emple_celular',
                'e.emple_telefono',
                'e.emple_fechaIC',
                'e.emple_fechaFC',
                'e.emple_Correo'
            )

            ->get();

            $usuario=DB::table('users')
            ->where('id','=',Auth::user()->id)->get();

        return view('empleado.cargarEmpleado', ['empleado' => $empleado,'usuario'=>$usuario[0]->user_estado]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tabla()
    {
        function agruparEmpleados($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = $empleado;
                }
                if (!isset($resultado[$empleado->emple_id]->dispositivos)) {
                    $resultado[$empleado->emple_id]->dispositivos = array();
                }
                array_push($resultado[$empleado->emple_id]->dispositivos, $empleado->dispositivo);
            }
            return $resultado;
        }
        $tabla_empleado1 = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->leftJoin('modo as md', 'md.idEmpleado', '=', 'e.emple_id')
            ->leftJoin('tipo_dispositivo as td', 'td.id', '=', 'md.idTipoDispositivo')
            ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
            ->select(
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'c.cargo_descripcion',
                'a.area_descripcion',
                'cc.centroC_descripcion',
                'e.emple_id',
                'md.idTipoDispositivo as dispositivo',
                'v.envio',
                'v.reenvio'
            )
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();
        $result = agruparEmpleados($tabla_empleado1);
        return view('empleado.tablaEmpleado', ['tabla_empleado' => $result]);
    }

    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $objEmpleado = json_decode($request->get('objEmpleado'), true);

        $persona = new persona();
        $persona->perso_nombre = $objEmpleado['nombres'];
        $persona->perso_apPaterno = $objEmpleado['apPaterno'];
        $persona->perso_apMaterno = $objEmpleado['apMaterno'];
        $persona->perso_direccion = $objEmpleado['direccion'];
        $persona->perso_fechaNacimiento = $objEmpleado['fechaN'];
        $persona->perso_sexo = $objEmpleado['tipo'];
        $persona->save();
        $emple_persona = $persona->perso_id;


        $empleado = new empleado();
        $empleado->emple_tipoDoc = $objEmpleado['documento'];
        $empleado->emple_nDoc = $objEmpleado['numDocumento'];
        $empleado->emple_persona = $emple_persona;
        if ($objEmpleado['departamento'] != '') {
            $empleado->emple_departamentoN = $objEmpleado['departamento'];
            $empleado->emple_provinciaN = $objEmpleado['provincia'];
            $empleado->emple_distritoN = $objEmpleado['distrito'];
        }
        if ($objEmpleado['cargo'] != '') {
            $empleado->emple_cargo = $objEmpleado['cargo'];
        }
        if ($objEmpleado['area'] != '') {
            $empleado->emple_area = $objEmpleado['area'];
        }
        if ($objEmpleado['centroc'] != '') {
            $empleado->emple_centCosto = $objEmpleado['centroc'];
        }

        if ($objEmpleado['dep'] != '') {
            $empleado->emple_departamento = $objEmpleado['dep'];
        }
        if ($objEmpleado['prov'] != '') {
            $empleado->emple_provincia = $objEmpleado['prov'];
        }
        if ($objEmpleado['dist'] != '') {
            $empleado->emple_distrito = $objEmpleado['dist'];
        }
        if ($objEmpleado['contrato'] != '') {
            $empleado->emple_tipoContrato = $objEmpleado['contrato'];
        }
        if ($objEmpleado['local'] != '') {
            $empleado->emple_local = $objEmpleado['local'];
        }
        if ($objEmpleado['nivel'] != '') {
            $empleado->emple_nivel = $objEmpleado['nivel'];
        }
        $empleado->emple_celular = $objEmpleado['celular'];
        $empleado->emple_telefono = $objEmpleado['telefono'];
        if ($objEmpleado['fechaI'] != '' && $objEmpleado['fechaF'] != '') {
            $empleado->emple_fechaIC = $objEmpleado['fechaI'];
            $empleado->emple_fechaFC = $objEmpleado['fechaF'];
        }
        if ($objEmpleado['correo'] != '') {
            $empleado->emple_Correo = $objEmpleado['correo'];
        }
        $empleado->emple_foto = '';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = public_path() . '/fotosEmpleado';
            $fileName = uniqid() . $file->getClientOriginalName();
            $file->move($path, $fileName);
            $empleado->emple_foto = $fileName;
        }
        $empleado->emple_pasword = Hash::make($objEmpleado['numDocumento']);
        $empleado->emple_estado = '1';
        $empleado->emple_codigo = $objEmpleado['codigoEmpleado'];
        $empleado->users_id = Auth::user()->id;
        $empleado->save();
        $idempleado = $empleado->emple_id;
        if ($request->get('disp') != '') {
            $disp = $request->get('disp');
            foreach ($disp as $dispositivo) {
                $modo = new modo();
                $modo->idEmpleado = $idempleado;
                $modo->idTipoModo = 1;
                $modo->idTipoDispositivo = $dispositivo;
                $modo->save();
            }
        }


        return json_encode(array('status' => true));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $idempleado = $request->get('value');
        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('tipo_documento as tipoD', 'e.emple_tipoDoc', '=', 'tipoD.tipoDoc_id')
            ->leftJoin('ubigeo_peru_departments as depar', 'e.emple_departamento', '=', 'depar.id')
            ->leftJoin('ubigeo_peru_provinces as provi', 'e.emple_provincia', '=', 'provi.id')
            ->leftJoin('ubigeo_peru_districts as dist', 'e.emple_distrito', '=', 'dist.id')

            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('ubigeo_peru_departments as para', 'e.emple_departamentoN', '=', 'para.id')
            ->leftJoin('ubigeo_peru_provinces as proviN', 'e.emple_provinciaN', '=', 'proviN.id')
            ->leftJoin('ubigeo_peru_districts as distN', 'e.emple_distritoN', '=', 'distN.id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->leftJoin('modo as md', 'md.idEmpleado', '=', 'e.emple_id')
            ->leftJoin('tipo_dispositivo as td', 'td.id', '=', 'md.idTipoDispositivo')

            ->select(
                'e.emple_id',
                'p.perso_id',
                'p.perso_nombre',
                'tipoD.tipoDoc_descripcion',
                'e.emple_nDoc',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'p.perso_fechaNacimiento',
                'p.perso_direccion',
                'p.perso_sexo',
                'depar.id as depar',
                'depar.id as deparNo',
                'provi.id as proviId',
                'provi.name as provi',
                'dist.id as distId',
                'dist.name as distNo',
                'c.cargo_descripcion',
                'a.area_descripcion',
                'cc.centroC_descripcion',
                'para.id as iddepaN',
                'para.id as depaN',
                'proviN.id as idproviN',
                'proviN.name as proviN',
                'distN.id as iddistN',
                'distN.name as distN',
                'e.emple_id',
                'c.cargo_id',
                'a.area_id',
                'cc.centroC_id',
                'e.emple_tipoContrato',
                'e.emple_local',
                'e.emple_nivel',
                'e.emple_departamento',
                'e.emple_provincia',
                'e.emple_distrito',
                'e.emple_foto as foto',
                'e.emple_celular',
                'e.emple_telefono',
                'e.emple_fechaIC',
                'e.emple_fechaFC',
                'e.emple_Correo',
                'e.emple_codigo',
                'md.idTipoDispositivo as dispositivo'
            )
            ->where('e.emple_id', '=', $idempleado)
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();
        return $empleado;
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function edit(empleado $empleado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idE)
    {

        $objEmpleado = json_decode($request->get('objEmpleadoA'), true);
        if ($request == null) return false;
        $empleado = Empleado::findOrFail($idE);

        if ($objEmpleado['cargo_v'] != '') {
            $empleado->emple_cargo = $objEmpleado['cargo_v'];
        }
        if ($objEmpleado['area_v'] != '') {
            $empleado->emple_area = $objEmpleado['area_v'];
        }
        if ($objEmpleado['departamento_v'] != '') {
            $empleado->emple_departamentoN = $objEmpleado['departamento_v'];
            $empleado->emple_provinciaN = $objEmpleado['provincia_v'];
            $empleado->emple_distritoN = $objEmpleado['distrito_v'];
        }
        if ($objEmpleado['centroc_v'] != '') {
            $empleado->emple_centCosto = $objEmpleado['centroc_v'];
        }
        if ($objEmpleado['dep_v'] != '') {
            $empleado->emple_departamento = $objEmpleado['dep_v'];
        }
        if ($objEmpleado['prov_v'] != '') {
            $empleado->emple_provincia = $objEmpleado['prov_v'];
        }
        if ($objEmpleado['dist_v'] != '') {
            $empleado->emple_distrito = $objEmpleado['dist_v'];
        }
        if ($objEmpleado['contrato_v'] != '') {
            $empleado->emple_tipoContrato = $objEmpleado['contrato_v'];
        }
        if ($objEmpleado['local_v'] != '') {
            $empleado->emple_local = $objEmpleado['local_v'];
        }
        if ($objEmpleado['nivel_v'] != '') {
            $empleado->emple_nivel = $objEmpleado['nivel_v'];
        }
        $empleado->emple_celular = $objEmpleado['celular_v'];
        $empleado->emple_telefono = $objEmpleado['telefono_v'];
        if ($objEmpleado['correo_v'] != '') {
            $empleado->emple_Correo = $objEmpleado['correo_v'];
        }
        $empleado->emple_fechaIC = $objEmpleado['fechaI_v'];
        $empleado->emple_fechaFC = $objEmpleado['fechaF_v'];
        $empleado->emple_codigo = $objEmpleado['codigoEmpleado_v'];
        if ($request->hasfile('file')) {
            $file = $request->file('file');
            $path = public_path() . '/fotosEmpleado';
            $fileName = uniqid() . $file->getClientOriginalName();
            $file->move($path, $fileName);
            $empleado->emple_foto = $fileName;
        }
        $empleado->save();

        $idpersona = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_id')
            ->where('emple_id', '=', $idE)
            ->get();

        $persona = Persona::findOrFail($idpersona[0]->perso_id);
        $persona->perso_nombre = $objEmpleado['nombres_v'];
        $persona->perso_apPaterno = $objEmpleado['apPaterno_v'];
        $persona->perso_apMaterno = $objEmpleado['apMaterno_v'];
        $persona->perso_direccion = $objEmpleado['direccion_v'];
        $persona->perso_fechaNacimiento = $objEmpleado['fechaN_v'];
        $persona->perso_sexo = $objEmpleado['tipo_v'];
        $persona->save();

        $idDispositivo = DB::table('empleado as e')
            ->join('modo as md', 'md.idEmpleado', '=', 'e.emple_id')
            ->join('tipo_dispositivo as td', 'td.id', '=', 'md.idTipoDispositivo')
            ->select('md.idTipoDispositivo as idD')
            ->where('md.idEmpleado', '=', $idE)
            ->get();
        if ($request->get('disp') != '') {
            $valor = $request->get('disp');
            foreach ($idDispositivo as $idD) {
                $aux = true;
                foreach ($valor as $index => $val) {
                    if ($idD->idD == $val) {
                        unset($valor[$index]);
                        $aux = false;
                    }
                }
                if ($aux) {
                    $idModo = DB::table('empleado as e')
                        ->join('modo as md', 'md.idEmpleado', '=', 'e.emple_id')
                        ->select('md.id')
                        ->where('md.idEmpleado', '=', $idE)
                        ->where('md.idTipoDispositivo', '=', $idD->idD)
                        ->get();
                    $modo = modo::where('id', $idModo[0]->id)->get()->first();
                    $modo->delete();
                }
            }
            foreach ($valor as $val1) {
                $modoI = new modo();
                $modoI->idEmpleado = $idE;
                $modoI->idTipoModo = 1;
                $modoI->idTipoDispositivo = $val1;
                $modoI->save();
            }
        } else {
            foreach ($idDispositivo as $idD) {
                $idModo = DB::table('empleado as e')
                    ->join('modo as md', 'md.idEmpleado', '=', 'e.emple_id')
                    ->select('md.id')
                    ->where('md.idEmpleado', '=', $idE)
                    ->where('md.idTipoDispositivo', '=', $idD->idD)
                    ->get();
                $modo = modo::where('id', $idModo[0]->id)->get()->first();
                $modo->delete();
            }
        }
        return json_encode(array('status' => true));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {



        $empleado = empleado::find($request->get('id'));


        $empleado->delete();

        $persona = persona::where('perso_id', '=', $empleado->emple_persona)->delete();
    }

    public function eliminarFoto(Request $request, $v_id)
    {
        $empleado = Empleado::findOrFail($v_id);
        $idFoto = DB::table('empleado as e')
            ->select('e.emple_foto')
            ->where('emple_id', '=', $v_id)
            ->get();
        unlink(public_path() . '/fotosEmpleado/' . $idFoto[0]->emple_foto);
        $empleado->emple_foto = "";
        $empleado->save();
        return json_encode(array("result" => true));
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;

        $empleado = empleado::whereIn('emple_id', explode(",", $ids))->get();
        //$empleado = empleado::find(explode(",",$ids))->first();

        $array = array();
        foreach ($empleado as $t) {

            $array[] = $t->emple_persona;
        }
        $idem = implode(',', $array);


        //dd($idem);
        $modo = modo::whereIn('idEmpleado', explode(",", $ids))->get();
        $modo->each->delete();
        $modo = modo::whereIn('idEmpleado', explode(",", $ids))->get();
        $modo->each->delete();
        $vinculacion = vinculacion::whereIn('idEmpleado', explode(",", $ids))->get();
        $vinculacion->each->delete();
        $empleado->each->delete();
        $persona = persona::whereIn('perso_id', explode(",", $idem))->get();
        $persona->each->delete();
        //$persona= persona::where('perso_id','=',$empleado->emple_persona);
        //dd($empleado->emple_persona);

    }
    public function indexMenu()
    {
        $departamento = ubigeo_peru_departments::all();
        $provincia = ubigeo_peru_provinces::all();
        $distrito = ubigeo_peru_districts::all();
        $tipo_doc = tipo_documento::all();
        $tipo_cont = tipo_contrato::all();
        $area = area::all();
        $cargo = cargo::all();
        $centro_costo = centro_costo::all();
        $nivel = nivel::all();
        $local = local::all();
        $empleado = empleado::all();
        $dispositivo = tipo_dispositivo::all();
        $tabla_empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
            ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select(
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'c.cargo_descripcion',
                'a.area_descripcion',
                'cc.centroC_descripcion',
                'e.emple_id'
            )
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();
        //dd($tabla_empleado);
        return view('empleado.empleadoMenu', [
            'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
            'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
            'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo
        ]);
    }

    public function comprobarNumD(Request $request)
    {
        $numeroD = $request->get('numeroD');
        //$empleado = empleado::where('emple_nDoc', '=', $numeroD)->first();
        $empleado = DB::table('empleado as e')
            ->where('e.emple_nDoc', '=', $numeroD)
            ->where('e.users_id', '=', Auth::user()->id)
            ->get()->first();
        if ($empleado != null) {
            return 1;
        }
    }

    public function comprobarCorreo(Request $request)
    {
        $email = $request->get('email');
        //$empleado = empleado::where('emple_Correo', '=', $email)->first();
        $empleado = DB::table('empleado as e')
            ->where('emple_Correo', '=', $email)
            ->where('e.users_id', '=', Auth::user()->id)
            ->get()->first();
        if ($empleado != null) {
            return 1;
        }
    }
}
