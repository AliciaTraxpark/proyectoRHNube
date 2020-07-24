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
use App\envio;
use App\horario_empleado;
use App\incidencias;
use App\licencia_empleado;
use App\eventos_usuario;
use App\eventos_empleado_temp;
use App\horario;
use App\eventos_empleado;
use App\incidencia_dias;
use App\horario_dias;
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
            $calendario = DB::table('calendario as ca')
            ->where('ca.users_id', '=', Auth::user()->id)
            ->get();
            $horario=horario::where('user_id', '=', Auth::user()->id)->get();
        //dd($tabla_empleado);
        return view('empleado.empleado', [
            'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
            'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
            'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
            'calendario'=>$calendario,'horario'=>$horario
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

        $usuario = DB::table('users')
            ->where('id', '=', Auth::user()->id)->get();

        return view('empleado.cargarEmpleado', ['empleado' => $empleado, 'usuario' => $usuario[0]->user_estado]);
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
        $empleado->emple_celular = '';
        if ($objEmpleado['celular'] != '') {
            $empleado->emple_celular = $objEmpleado['celular'];
        }
        $empleado->emple_telefono = $objEmpleado['telefono'];
        if ($objEmpleado['fechaI'] != '') {
            $empleado->emple_fechaIC = $objEmpleado['fechaI'];
        }
        if ($objEmpleado['fechaF'] != '') {
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
        $modo = new modo();
        $modo->idEmpleado = $idempleado;
        $modo->idTipoModo = 1;
        $modo->idTipoDispositivo = 1;
        $modo->save();

        $modo = new modo();
        $modo->idEmpleado = $idempleado;
        $modo->idTipoModo = 1;
        $modo->idTipoDispositivo = 2;
        $modo->save();
        ///CALENDARIO

        $eventos_empleado_tempEU= eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
        ->where('id_horario', '=', null)->where('color', '!=', '#9E9E9E')
        ->where('calendario_calen_id', '=', $objEmpleado['idca'])->get();
        foreach ($eventos_empleado_tempEU as $eventos_empleado_tempEUs) {
            $eventos_empleado=new eventos_empleado();
            $eventos_empleado->title=$eventos_empleado_tempEUs->title;
            $eventos_empleado->color=$eventos_empleado_tempEUs->color;
            $eventos_empleado->textColor=$eventos_empleado_tempEUs->textColor;
            $eventos_empleado->start=$eventos_empleado_tempEUs->start;
            $eventos_empleado->end=$eventos_empleado_tempEUs->end;
            $eventos_empleado->id_empleado=$idempleado;
            $eventos_empleado->tipo_ev=$eventos_empleado_tempEUs->tipo_ev;
            $eventos_empleado->save();
        }
        //INIDENC
        $eventos_empleado_tempInc= eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
        ->where('id_horario', '=', null)->where('color', '=', '#9E9E9E')->where('textColor', '=', '#313131')
        ->where('calendario_calen_id', '=', $objEmpleado['idca'])->get();

        foreach ($eventos_empleado_tempInc as $eventos_empleado_tempIncs) {
            $incidenciadias_dias=new incidencia_dias();
            $incidenciadias_dias->inciden_dias_fechaI=$eventos_empleado_tempIncs->start;
            $incidenciadias_dias->inciden_dias_fechaF=$eventos_empleado_tempIncs->end;
            $incidenciadias_dias->id_incidencia=$eventos_empleado_tempIncs->tipo_ev;
            $incidenciadias_dias->id_empleado=$idempleado;
            $incidenciadias_dias->save();
        }
        //HORARIO
        $eventos_empleado_tempHor= eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
        ->where('id_horario', '!=', null)->where('color', '=', '#ffffff')->where('textColor', '=', '111111')
        ->where('calendario_calen_id', '=', $objEmpleado['idca'])->get();

        foreach ($eventos_empleado_tempHor as $eventos_empleado_tempHors) {
            $horario_dias=new horario_dias();
            $horario_dias->title=$eventos_empleado_tempHors->title;
            $horario_dias->color=$eventos_empleado_tempHors->color;
            $horario_dias->textColor=$eventos_empleado_tempHors->textColor;
            $horario_dias->start=$eventos_empleado_tempHors->start;
            $horario_dias->end=$eventos_empleado_tempHors->end;
            $horario_dias->users_id=Auth::user()->id;
            $horario_dias->save();

            $horario_empleados=new horario_empleado();
            $horario_empleados->horario_horario_id=$eventos_empleado_tempHors->id_horario;
            $horario_empleados->empleado_emple_id=$idempleado;
            $horario_empleados->horario_dias_id=$horario_dias->id;
            $horario_empleados->save();


        }



        ///////////FIN CALENDARIO

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
            ->leftJoin('tipo_contrato as tp', 'e.emple_tipoContrato', '=', 'tp.contrato_id')
            ->leftJoin('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
            ->leftJoin('local as l', 'e.emple_local', '=', 'l.local_id')
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
                'depar.name as deparNo',
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
                'e.emple_Correo',
                'e.emple_codigo',
                'tp.contrato_descripcion',
                'n.nivel_descripcion',
                'l.local_descripcion',
                'md.idTipoDispositivo as dispositivo'
            )
            ->where('e.emple_id', '=', $idempleado)
            ->where('e.users_id', '=', Auth::user()->id)
            ->get();
        $cantidad = DB::table('licencia_empleado as le')
            ->select(DB::raw('COUNT(le.id) as total'), 'le.licencia')
            ->where('le.idEmpleado', '=', $idempleado)
            ->get();
        $licenciaE = DB::table('licencia_empleado as le')
            ->select('le.licencia', 'le.id', 'le.disponible')
            ->where('le.idEmpleado', '=', $idempleado)
            ->get();
        $licencia = [];
        foreach ($licenciaE as $lic) {
            array_push($licencia, array("id" => $lic->id, "licencia" => $lic->licencia, "disponible" => $lic->disponible));
        }
        $empleado[0]->total = $cantidad[0]->total;
        $empleado[0]->licencia = $licencia;
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
        }
        if ($objEmpleado['provincia_v'] != '') {
            $empleado->emple_provinciaN = $objEmpleado['provincia_v'];
        }
        if ($objEmpleado['distrito_v'] != '') {
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
        $empleado->emple_celular = '';
        if ($empleado->emple_celular != '') {
            $empleado->emple_celular = $objEmpleado['celular_v'];
        }
        $empleado->emple_telefono = $objEmpleado['telefono_v'];
        $empleado->emple_Correo = $objEmpleado['correo_v'];
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


        $actividad = actividad::whereIn('empleado_emple_id', explode(",", $ids))->get();
        $actividad->each->delete();

        $envio = envio::whereIn('idEmpleado', explode(",", $ids))->get();
        $envio->each->delete();

        $horario_empleado = horario_empleado::whereIn('empleado_emple_id', explode(",", $ids))->get();
        $horario_empleado->each->delete();

        $incidencias = incidencias::whereIn('emple_id', explode(",", $ids))->get();
        $incidencias->each->delete();

        $licencia_empleado = licencia_empleado::whereIn('idEmpleado', explode(",", $ids))->get();
        $licencia_empleado->each->delete();

        $proyecto_empleado = proyecto_empleado::whereIn('empleado_emple_id', explode(",", $ids))->get();
        $proyecto_empleado->each->delete();

        $tarea = tarea::whereIn('empleado_emple_id', explode(",", $ids))->get();
        $tarea->each->delete();

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
            $calendario = DB::table('calendario as ca')
            ->where('ca.users_id', '=', Auth::user()->id)
            ->get();
            $horario=horario::where('user_id', '=', Auth::user()->id)->get();
        //dd($tabla_empleado);
        return view('empleado.empleadoMenu', [
            'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
            'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
            'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
            'calendario'=>$calendario,'horario'=>$horario
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

    public function comprobarCorreoEditar(Request $request)
    {
        $email = $request->get('email');
        $empleado = $request->get('idE');
        $empleado = DB::table('empleado as e')
            ->where('emple_Correo', '=', $email)
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_id', '!=', $empleado)
            ->get()->first();
        if ($empleado != null) {
            return 1;
        }
    }

    public function calendarioEmpTemp(Request $request){
        $idcalendario=$request->idcalendario;

        $eventos_empleado_tempCop = eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
        ->where('calendario_calen_id','=',$idcalendario) ->get();
        if($eventos_empleado_tempCop->isEmpty()){
          $eventos_usuario = eventos_usuario::where('users_id', '=', Auth::user()->id)
        ->where('id_calendario','=',$idcalendario) ->get();
        if($eventos_usuario){
          foreach ($eventos_usuario as $eventos_usuarios) {
            $eventos_empleado_tempSave = new eventos_empleado_temp();
            $eventos_empleado_tempSave->users_id = Auth::user()->id;
            $eventos_empleado_tempSave->title =$eventos_usuarios->title;
            $eventos_empleado_tempSave->color =$eventos_usuarios->color;
            $eventos_empleado_tempSave->textColor =$eventos_usuarios->textColor;
            $eventos_empleado_tempSave->start =$eventos_usuarios->start;
            $eventos_empleado_tempSave->end =$eventos_usuarios->end;
            $eventos_empleado_tempSave->tipo_ev =$eventos_usuarios->tipo;
             $eventos_empleado_tempSave->calendario_calen_id =$idcalendario;
            $eventos_empleado_tempSave->save();
        }
        }
        }


        $eventos_empleado_temp = DB::table('eventos_empleado_temp as evt')
                ->select(['evEmpleadoT_id as id','title','color','textColor','start','end','tipo_ev','users_id','calendario_calen_id'])
                ->where('evt.users_id', '=', Auth::user()->id)
                ->where('evt.calendario_calen_id','=',$idcalendario)

                ->get();

        return $eventos_empleado_temp;
    }
    public function storeCalendarioTem(Request $request){
        $eventos_empleado_tempSave = new eventos_empleado_temp();
        $eventos_empleado_tempSave->users_id = Auth::user()->id;
        $eventos_empleado_tempSave->title =$request->get('title');
        $eventos_empleado_tempSave->color =$request->get('color');
        $eventos_empleado_tempSave->textColor =$request->get('textColor');
        $eventos_empleado_tempSave->start =$request->get('start');
        $eventos_empleado_tempSave->end =$request->get('end');
        $eventos_empleado_tempSave->tipo_ev =$request->get('tipo');
        $eventos_empleado_tempSave->calendario_calen_id =$request->get('id_calendario');
        $eventos_empleado_tempSave->save();



        $eventos_empleado_temp = DB::table('eventos_empleado_temp as evt')
                ->select(['evEmpleadoT_id as id','title','color','textColor','start','end','tipo_ev','users_id','calendario_calen_id'])
                ->where('evt.users_id', '=', Auth::user()->id)
                ->where('evt.calendario_calen_id','=',$request->get('id_calendario'))

                ->get();

        return $eventos_empleado_temp;

    }

    public function storeIncidTem(Request $request){

        $incidencia = new incidencias();
        $incidencia->inciden_descripcion =  $request->get('title');
        $incidencia->inciden_descuento =$request->get('descuentoI');
        $incidencia->inciden_hora =  $request->get('horaIn');
        $incidencia->users_id = Auth::user()->id;
        $incidencia->save();

        $eventos_empleado_tempSave = new eventos_empleado_temp();
        $eventos_empleado_tempSave->users_id = Auth::user()->id;
        $eventos_empleado_tempSave->title =$request->get('title');
        $eventos_empleado_tempSave->color ='#9E9E9E';
        $eventos_empleado_tempSave->textColor ='#313131';
        $eventos_empleado_tempSave->start =$request->get('start');
        $eventos_empleado_tempSave->end =$request->get('end');
        $eventos_empleado_tempSave->tipo_ev = $incidencia->inciden_id;
        $eventos_empleado_tempSave->calendario_calen_id =$request->get('id_calendario');
        $eventos_empleado_tempSave->save();



        $eventos_empleado_temp = DB::table('eventos_empleado_temp as evt')
                ->select(['evEmpleadoT_id as id','title','color','textColor','start','end','tipo_ev','users_id','calendario_calen_id'])
                ->where('evt.users_id', '=', Auth::user()->id)
                ->where('evt.calendario_calen_id','=',$request->get('id_calendario'))

                ->get();

        return $eventos_empleado_temp;

    }
    public function vaciarcalend(){
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
        ->delete();
    }
    public function vaciarcalendId(Request $request){
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
        ->where('calendario_calen_id','=',$request->get('idca'))

        ->delete();
    }
    public function registrarHorario(Request $request){
        $sobretiempo = $request->sobretiempo;
        $tipHorario = $request->tipHorario;
        $descripcion = $request->descripcion;
        $toleranciaH = $request->toleranciaH;
        $inicio = $request->inicio;
        $fin = $request->fin;

        $horario = new horario();
        $horario->horario_sobretiempo = $sobretiempo;
        $horario->horario_tipo = $tipHorario;
        $horario->horario_descripcion = $descripcion;
        $horario->horario_tolerancia = $toleranciaH;
        $horario->horaI = $inicio;
        $horario->horaF = $fin;
        $horario->user_id = Auth::user()->id;
        $horario->save();
        return $horario;
    }

    public function guardarhorarioTem(Request $request)
    {
        $datafecha = $request->fechasArray;
        $horas = $request->hora;
        $idhorar = $request->idhorar;
        $idca = $request->idca;
        $arrayeve = collect();
        foreach ($datafecha as $datafechas) {
            $eventos_empleado_tempSave = new eventos_empleado_temp();
            $eventos_empleado_tempSave->title = $horas;
            $eventos_empleado_tempSave->start = $datafechas;
            $eventos_empleado_tempSave->color = '#ffffff';
            $eventos_empleado_tempSave->textColor = '111111';
            $eventos_empleado_tempSave->users_id = Auth::user()->id;
            $eventos_empleado_tempSave->tipo_ev =5;
            $eventos_empleado_tempSave->id_horario = $idhorar;
            $eventos_empleado_tempSave->calendario_calen_id = $idca;
            $eventos_empleado_tempSave->save();
            $arrayeve->push($eventos_empleado_tempSave);
        }

    }

    public function vercalendarioEmpl(Request $request){



                $horario_empleado = DB::table('horario_empleado as he')->select(['id', 'title', 'color', 'textColor', 'start', 'end'])
                /*  ->where('users_id', '=', Auth::user()->id) */
                 ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                 ->where('he.empleado_emple_id', '=', $request->get('idempleado'));

        $eventos_empleado= DB::table('eventos_empleado')
                ->select(['evEmpleado_id as id','title','color','textColor','start','end'])
                ->where('id_empleado','=',$request->get('idempleado'))
                ->union($horario_empleado)
                ->get();

                return $eventos_empleado;

    }
}
