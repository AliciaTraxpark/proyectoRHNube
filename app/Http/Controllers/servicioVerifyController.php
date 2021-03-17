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
use GuzzleHttp\Exception\RequestException;
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
                return response()->json(array("respuesta" => "Usuario no coinciden."), 404);
            }
            if (!(strcmp($request->get('clave'), $request->get('confirmar_clave')) === 0)) {
                return response()->json(array("respuesta" => "Clave no coinciden."), 404);
            }
            // * REGISTRAR NUEVAS CREDENCIALES
            $nuevaCredencial = new crd();
            $nuevaCredencial->fecha_registro = Carbon::now();
            $nuevaCredencial->usuario = base64_encode($request->get('usuario'));
            $nuevaCredencial->clave = base64_encode($request->get('clave'));
            $nuevaCredencial->save();

            return response()->json(array("respuesta" => "Registro exitoso."), 201);
        } else {
            return response()->json(array("respuesta" => "Usuario ya se encuentra registrado."), 404);
        }
    }
    // * FUNCION DE LOGUEO DEL SERVICIO DE VERIFY
    public function loginServicio()
    {
        // ! ------------------------ CONSULTAR API DE LOGIN DE SERVICIO -----------------------------
        $credencial = crd::where('estado', '=', 1)->get()->first();
        if ($credencial) {
            // : OBTENIENDO USUARIO Y CLAVE
            $usuario = base64_decode($credencial->usuario);
            $clave = base64_decode($credencial->clave);
            // : LOGUEAR CREDENCIAL
            try {
                $response = $this->client->request('POST', '/api/v1/auth/login', ['form_params' => [
                    'email' => $usuario,
                    'password' => $clave
                ]]);
                $body = json_decode($response->getBody());
                // : GUARDAR EN LA BD
                loginServicioVerify($body, $credencial);
                // : DEVOLVEMOS EL TOKEN Y EL TIPO DE TOKEN
                return array("token" => $body->token, "token_type" => $body->token_type);
            } catch (RequestException $e) {
                if ($e->getResponse()->getStatusCode() == 401) {
                    return response()->json(array("respuesta" => "Credenciales incorrectas."), 404);
                }
                return response()->json(array("respuesta" => "Problemas con el servicio de verificación, comunicarse con nosotros."), 404);
            }
        } else {
            return response()->json(array("respuesta" => "No se encuentran credenciales disponibles para login"), 404);
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
            // : TIPOS DE DATOS  A CONSULTAR
            $consulta_identidad = $datos['consulta_identidad'];
            $consulta_policial = $datos['consulta_policial'];
            $consulta_penal = $datos['consulta_penal'];
            $consulta_crediticio = $datos['consulta_crediticio'];
            $arrayParaBolsaYConsulta = [];
            // * ---------------------------------- TIPO DE CONSULTA HACIA EL SERVICIO ------------------------------
            // : CONSULTA DE IDENTIDAD
            if ($consulta_identidad == 1) {
                $asoc = asocs_servicio::where('tipo', '=', 'consulta_identidad')->where('activo', '=', 1)->get();
                $arrayAsoc = [];
                // * ASOCS DE DE SERVICIO
                foreach ($asoc as $a) {
                    array_push($arrayAsoc, $a->codigo);
                }
                if (sizeof($asoc) != 0) {
                    // * BOLSA DE IDENTIDAD
                    $bolsaIdentidad = $bolsaOrganizacion->saldo_identidad;
                    array_push($arrayParaBolsaYConsulta, array("bolsa" => $bolsaIdentidad, "asoc" => $arrayAsoc));
                }
            }
            // : CONSULTA DE POLICIALES
            if ($consulta_policial == 1) {
                $asoc = asocs_servicio::where('tipo', '=', 'consulta_policial')->where('activo', '=', 1)->get();
                $arrayAsoc = [];
                // * ASOCS DE DE SERVICIO
                foreach ($asoc as $a) {
                    array_push($arrayAsoc, $a->codigo);
                }
                if (sizeof($asoc) != 0) {
                    // * BOLSA DE POLICIAL
                    $bolsaPolicial = $bolsaOrganizacion->saldo_policial;
                    array_push($arrayParaBolsaYConsulta, array("bolsa" => $bolsaPolicial, "asoc" => $arrayAsoc));
                }
            }
            // : CONSULTA DE PENAL
            if ($consulta_penal == 1) {
                $asoc = asocs_servicio::where('tipo', '=', 'consulta_penal')->where('activo', '=', 1)->get();
                $arrayAsoc = [];
                // * ASOCS DE DE SERVICIO
                foreach ($asoc as $a) {
                    array_push($arrayAsoc, $a->codigo);
                }
                if (sizeof($asoc) != 0) {
                    // * BOLSA PENAL
                    $bolsaPenal = $bolsaOrganizacion->saldo_penal;
                    array_push($arrayParaBolsaYConsulta, array("bolsa" => $bolsaPenal, "asoc" => $arrayAsoc));
                }
            }
            // : CONSULTA DE CREDITICIO
            if ($consulta_crediticio == 1) {
                $asoc = asocs_servicio::where('tipo', '=', 'consulta_crediticio')->where('activo', '=', 1)->get();
                $arrayAsoc = [];
                // * ASOCS DE DE SERVICIO
                foreach ($asoc as $a) {
                    array_push($arrayAsoc, $a->codigo);
                }
                if (sizeof($asoc) != 0) {
                    // * BOLSA PENAL
                    $bolsaCrediticio = $bolsaOrganizacion->saldo_crediticio;
                    array_push($arrayParaBolsaYConsulta, array("bolsa" => $bolsaCrediticio, "asoc" => $arrayAsoc));
                }
            }
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
                            return response()->json(array("respuesta" => "Archivo de carga vacío"), 404);
                        }
                        // : VALIDAR LAS CABECERAS
                        if (!in_array("tipo_documento", $d) && !in_array("numero_documento", $d)) {
                            return response()->json(array("respuesta" => "Formato incorrecto, Porfavor descargue la plantilla y actualize sus datos"), 404);
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
                                    return response()->json(array("respuesta" => "No se encontro el tipo de documento: " . escape_like($d[0]) . ". El proceso se interrumpio en la fila:" . $key), 404);
                                } else {
                                    // : POR AHORA HABILITADO SOLO PARA DNI
                                    if ($tipoDocumento->tipoDoc_id != 1) {
                                        return response()->json(array("respuesta" => "No se encontra habilitado este tipo documento: " . escape_like($d[0]) . ". El proceso se interrumpio en la fila:" . $key), 404);
                                    }
                                }
                            }
                        }
                        // ! ------------------------------------------------ VALIDACION DE NUMERO DE DOCUMENTO --------------------------------------------------------
                        if (!is_null($d[1])) {
                            if (!is_null($d[0])) {
                                // * SOLO DIGITOS NUMERICOS
                                if (!is_numeric($d[1])) {
                                    return response()->json(array("respuesta" => "numero de DNI " . $d[1] . " invalido en la importacion(Debe ser númerico)  .El proceso se interrumpio en la fila " . $key . " de excel"), 404);
                                }
                                // * TAMAÑO DEL DNI
                                $numero_length = Str::length($d[1]);
                                if ($numero_length != 8) {
                                    return response()->json(array("respuesta" => "numero de DNI " . $d[1] . " invalido en la importacion(Debe tener 8 digitos)  .El proceso se interrumpio en la fila " . $key . " de excel"), 404);
                                }
                            } else {
                                return response()->json(array("respuesta" => "Ingresar un tipo de documento. El proceso se interrumpio en la fila:" . $key), 404);
                            }
                        }
                        // * INSERTAR DATOS A CONSULTAR
                        array_push($arrayConsultar, array("type" => $tipoDocumento->tipoDoc_id, "document" => $d[1]));
                    }
                }
            } else {
                // ! NOTA : CUANDO SOLO QUIERE CONSULTAR DE UNO SOLO Y NO ENVIA NINGUN EXCEL
                // : VALIDACION QUE EXISTA PARAMETRO DE NUMERO DE DOCUMENTO
                if (!isset($datos['numeroDocumento'])) {
                    return response()->json(array("respuesta" => "DNI invalido debe ser númerico"), 404);
                }
                // : VALIDACION QUE EXISTA PARAMETRO DE TIPO DE DOCUMENTO
                if (!isset($datos['tipo'])) {
                    return response()->json(array("respuesta" => "Seleccionar el tipo documento."), 404);
                }
                // : QUE NO SE CAMPO NULL
                if (!is_null($datos['numeroDocumento']) && !is_null($datos['tipo'])) {
                    if (!is_numeric($datos['numeroDocumento'])) {
                        return response()->json(array("respuesta" => "DNI invalido debe tener 8 digitos."), 404);
                    }
                    // : VALIDACION DE LENGTH DE DNI
                    $numero_length = Str::length($datos['numeroDocumento']);
                    if ($numero_length == 8) {
                    } else {
                        return response()->json(array("respuesta" => "DNI invalido debe tener 8 digitos."), 404);
                    }
                } else {
                    return response()->json(array("respuesta" => "Ingresar número de documento o seleccionar el tipo documento."), 404);
                }
            }
        } else {
            return response()->json(array("respuesta" => "Recargar bolsas de identificación."), 404);
        }
    }
}
