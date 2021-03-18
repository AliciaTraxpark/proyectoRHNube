<?php

namespace App\Http\Controllers;

use App\asocs_servicio;
use App\crd;
use App\crd_token;
use App\Imports\ExcelServicioImport;
use App\organizacion_consultas;
use App\tipo_documento;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class servicioVerifyController extends Controller
{
    private $client;

    public function __construct()
    {
        // : ACCEDIENDO A CLIENTE
        $this->client = new Client(['base_uri' => 'http://dcgtec.verify.com.pe']);
        $this->middleware(['auth', 'verified']);
    }

    // * FUNCION DE REGISTO DE CREDENCIALES PARA LOGIN 
    public function nuevaCredencial(Request $request)
    {
        // : VALIDACION DE BACKEND 
        // ? usuario -> email de credencial a registrar
        // ? CLAVE -> clave de credencial a registrar
        $validacion = Validator::make($request->all(), [
            'usuario' => 'required',
            'confirmar_usuario' => 'required',
            'clave' => 'required|min:8',
            'confirmar_clave' => 'required|min:8'
        ], [
            'required' => ':attribute es obligatorio.',
            'min' => 'El valor :attribute debe ser mayor :min'
        ]);
        // * ARRAY DE ERRORES
        if ($validacion->fails()) {

            return response()->json($validacion->errors(), 404);
        }
        // * BUSCAR SI USUARIO YA SE ENCUENTRA REGISTRADO
        $credencial = crd::where('usuario', '=', base64_encode($request->get('usuario')))->get()->first();
        if (!$credencial) {                   //: CUANDO NO LOE ENCUENTRA REGISTRADO
            // * COMPARAR USUARIOS Y CLAVES
            if (!(strcmp($request->get('usuario'), $request->get('confirmar_usuario')) === 0)) {
                return response()->json(array("mensaje" => "Usuario no coinciden."), 404);
            }
            if (!(strcmp($request->get('clave'), $request->get('confirmar_clave')) === 0)) {
                return response()->json(array("mensaje" => "Clave no coinciden."), 404);
            }
            // * REGISTRAR NUEVAS CREDENCIALES
            $nuevaCredencial = new crd();
            $nuevaCredencial->fecha_registro = Carbon::now();
            $nuevaCredencial->usuario = base64_encode($request->get('usuario'));
            $nuevaCredencial->clave = base64_encode($request->get('clave'));
            $nuevaCredencial->save();

            return response()->json(array("mensaje" => "Registro exitoso."), 201);
        } else {
            return response()->json(array("mensaje" => "Usuario ya se encuentra registrado."), 404);
        }
    }
    // * FUNCION DE LOGUEO DEL SERVICIO DE VERIFY
    public function loginServicio()
    {
        // ! ------------------------ CONSULTAR API DE LOGIN DE SERVICIO -----------------------------
        $credencial = crd::where('estado', '=', 1)->get()->first();
        if ($credencial) {
            return loginServicioVerify($this->client, $credencial);
        } else {
            return response()->json(array("mensaje" => "No se encuentran credenciales disponibles para login"), 404);
        }
    }

    // * FUNCION DE VERIFICAR REGISTRO POR PARTE DEL SERVICIO
    // ! NOTA IMPORTANTE MIGUEL: LA INFORMACION DEBES ENVIARMELA EN FORMDATA
    // ! MOTIVO POR EL EXCEL MASIVO DE CONSULTAS
    // ! EL FORMDATA DEBE CONTENER DOS ARRAYS
    // ! EL PRIMER ARRAY LLAMADO DATOS DONDE SE VA ENVIAR LOS ESTADOS QUE SE VAN CONSULTAR
    // ! MAS EL DNI A BUSCAR SI ES QUE ES PARA UN SOLO DNI
    // ! Y EL SEGUNDO ARRAY LLAMADO FILE VA TENER EL EXCEL SI ES MASIVO
    public function consultarInformacionVerify(Request $request)
    {
        // * FUNCION DE BORRAR CARACTERES ESPECIALES
        function escape_like(string $value, string $char = '\\')
        {
            return str_replace(
                [$char, '%', '_'],
                [$char . $char, $char . '%', $char . '_'],
                $value
            );
        }
        // * VALIDACION DE BOLSAS POR ORGANIZACION
        $bolsaOrganizacion = organizacion_consultas::where('organi_id', '=', session('sesionidorg'))->get()->first();
        if ($bolsaOrganizacion) {
            // !-------------------------------------- PRIMER ARRAY -----------------------------------------------
            // $datos = json_decode($request->get('datos'), true);
            $datos = $request->get('datos');
            // !-------------------------------------- SEGUNDO ARRAY ---------------------------------------------------
            if ($request->hasFile('file')) {
                $import = new ExcelServicioImport;
                $path = $request->file('file');
                $data = Excel::toArray($import, $path);
                $data = Arr::first($data);
                $arrayConsultar = array();
                foreach ($data as $key => $d) {
                    // * SOLO NECESITAMOS LA COLUMNA 0 Y LA COLUMNA 1
                    $d = array_slice($d, 0, 2);
                    if ($key == 0) {
                        // : VALIDAR QUE NO ESTE VACIO LA HOJA DE EXCEL
                        if (empty($d[0])) {
                            return response()->json(array("mensaje" => "Archivo de carga vacío"), 404);
                        }
                        // : VALIDAR LAS CABECERAS
                        if (!in_array("tipo_documento", $d) && !in_array("numero_documento", $d)) {
                            return response()->json(array("mensaje" => "Formato incorrecto, Porfavor descargue la plantilla y actualize sus datos"), 404);
                        }
                    } else {
                        // * QUE NO SE ENCUENTRE VACIO EL CAMPO DE TIPO DE DOCUMENTO
                        if (!is_null($d[0])) {
                            // * QUE NO SE ENCUENTRE VACIO EL CAMPO DEL NUMERO DOCUMENTO
                            if (!is_null($d[1])) {
                                // ! ----------------------------------------------- VALIDACIÓN DE TIPO DE DOCUMENTO ---------------------------------------------------------
                                // : BUSCAR EL TIPO DE DOCUMENTO
                                $tipoDocumento = tipo_documento::where("tipoDoc_descripcion", "like", "%" . escape_like($d[0]) . "%")->first();
                                // : SI NO ENCUENTRA EL TIPO DE DOCUMENTO EN LA BD
                                if (is_null($tipoDocumento)) {
                                    return response()->json(array("mensaje" => "No se encontro el tipo de documento: " . escape_like($d[0]) . ". El proceso se interrumpio en la fila:" . $key), 404);
                                } else {
                                    // : POR AHORA HABILITADO SOLO PARA DNI
                                    if ($tipoDocumento->tipoDoc_id != 1) {
                                        return response()->json(array("mensaje" => "No se encontra habilitado este tipo documento: " . escape_like($d[0]) . ". El proceso se interrumpio en la fila:" . $key), 404);
                                    }
                                }
                            }
                        }
                        // ! ------------------------------------------------ VALIDACION DE NUMERO DE DOCUMENTO --------------------------------------------------------
                        if (!is_null($d[1])) {
                            if (!is_null($d[0])) {
                                // * SOLO DIGITOS NUMERICOS
                                if (!is_numeric($d[1])) {
                                    return response()->json(array("mensaje" => "numero de DNI " . $d[1] . " invalido en la importacion(Debe ser númerico)  .El proceso se interrumpio en la fila " . $key . " de excel"), 404);
                                }
                                // * TAMAÑO DEL DNI
                                $numero_length = Str::length($d[1]);
                                if ($numero_length != 8) {
                                    return response()->json(array("mensaje" => "numero de DNI " . $d[1] . " invalido en la importacion(Debe tener 8 digitos)  .El proceso se interrumpio en la fila " . $key . " de excel"), 404);
                                }
                            } else {
                                return response()->json(array("mensaje" => "Ingresar un tipo de documento. El proceso se interrumpio en la fila:" . $key), 404);
                            }
                        }
                        // * INSERTAR DATOS A CONSULTAR
                        array_push($arrayConsultar, array("type" => $tipoDocumento->tipoDoc_id, "document" => $d[1]));
                    }
                }
            } else {
                // ! NOTA : CUANDO SOLO QUIERE CONSULTAR DE UNO SOLO Y NO ENVIA NINGUN EXCEL
                // : VALIDACION DE CAMPOS
                $validacion_campos = validacionNumeroDocuemntoVerify($datos['tipo'], $datos['numeroDocumento']);
                // : SI CUMPLE TODAS LAS VALIDACIONES
                if (is_bool($validacion_campos)) {
                    // ! ---------------------------------- CONSUMIR SERVICIO ----------------------------------------------------
                    // : BUSCAR CREDENCIAL
                    $credencial = crd::where('estado', '=', 1)->get()->first();
                    if ($credencial) {
                        $token = crd_token::where('id_crd', '=', $credencial->id)->get()->first();
                        if ($token) {
                            // * CONSULTAR SI ENCUENTRA ACTIVO EL SERVICIO Y TIENE SALDO
                            $verifica_saldo = verificarYbolsaVerify(
                                $datos['consulta_identidad'],
                                $datos['consulta_policial'],
                                $datos['consulta_penal_judicial'],
                                $datos['consulta_crediticio'],
                                0,
                                $bolsaOrganizacion
                            );
                            $verifica_saldo["asocs"] = Arr::flatten($verifica_saldo["asocs"]);
                            dd($verifica_saldo);
                            $respuesta_verify = verificarPersonVerify($this->client, $token->token_type, $token->token, $datos['numeroDocumento']);
                            if (!isset($respuesta_verify->original)) {
                                // : CONDICIONAL SI LO ENCONTRO
                                if (!is_bool($respuesta_verify)) {
                                } else {
                                    // : REGISTRAR EL NUMERO DOCUMENTO
                                }
                            } else {
                                return $respuesta_verify;
                            }
                        } else {
                            // : LOGUAERNOS PARA OBTENER TOKEN
                            $login = loginServicioVerify($this->client, $credencial);
                        }
                    } else {
                        return response()->json(array("mensaje" => "No se encuentran credenciales disponibles para login"), 404);
                    }
                } else {
                    return response()->json($validacion_campos, 404);
                }
            }
        } else {
            return response()->json(array("mensaje" => "Recargar bolsas de identificación."), 404);
        }
    }
}
