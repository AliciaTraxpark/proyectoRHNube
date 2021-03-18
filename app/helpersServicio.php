<?php
// * SERVICIO DE VERIFY

use App\asocs_servicio;
use App\crd_token;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

// * FUNCION DE LOGUEAR PARA OBTENER TOKEN
function loginServicioVerify($client, $credencial)
{
    // : OBTENIENDO USUARIO Y CLAVE
    $usuario = base64_decode($credencial->usuario);
    $clave = base64_decode($credencial->clave);
    // : LOGUEAR CREDENCIAL
    try {
        $response = $client->request('POST', '/api/v1/auth/login', ['form_params' => [
            'email' => $usuario,
            'password' => $clave
        ]]);
        $body = json_decode($response->getBody());
        // * BUSCAMOS CRD EN TABLA CDR TOKEN
        $buscarToken = crd_token::where('id_crd', '=', $credencial->id)->get()->first();
        if (!$buscarToken) {
            // : REGISTRAR TOKEN 
            $crd_token = new crd_token();
            $crd_token->token = $body->token;
            $crd_token->token_type = $body->token_type;
            $crd_token->id_crd = $credencial->id;
            $crd_token->fecha = Carbon::now();
            $crd_token->save();
        } else {
            // : ACTUALIZAR TOKEN
            $buscarToken->token = $body->token;
            $buscarToken->token_type = $body->token_type;
            $buscarToken->fecha = Carbon::now();
            $buscarToken->save();
        }
        // : DEVOLVEMOS EL TOKEN Y EL TIPO DE TOKEN
        return array("token" => $body->token, "token_type" => $body->token_type);
    } catch (RequestException $e) {
        if ($e->getResponse()->getStatusCode() == 401) {
            return response()->json(array(
                "mensaje" => "Credenciales incorrectas del servicio,comunicarse con nosotros.",
                "status" => 404,
                "mensaje_servicio" => $e->getMessage(),
                "status_servicio" => $e->getResponse()->getStatusCode()
            ), 404);
        }
        return response()->json(array(
            "mensaje" => "Problemas con el servicio de verificación, comunicarse con nosotros.",
            "status" => 404,
            "mensaje_servicio" => $e->getMessage(),
            "status_servicio" => $e->getResponse()->getStatusCode()
        ), 404);
    }
}

// * VALIDACION DE NUMERO DE DOCUMENTO
function validacionNumeroDocuemntoVerify($numero, $tipo)
{
    // : VALIDACION QUE NO SEA NULL EL TIPO
    if (is_null($tipo)) {
        return array("mensaje" => "Seleccionar el tipo documento.");
    }
    // : VALIDACION QUE NO SEA NULL EL NUMERO DE DOCUMENTO
    if (is_null($numero)) {
        return array("mensaje" => "Ingresar número de documento");
    }
    // : VALIDACION DE TIPO -> POR AHORA SOLO DNI
    if ($tipo == 1) {
        // : VALIDACION QUE SOLO SEA NUMERICO
        if (!is_numeric($numero)) {
            return array("mensaje" => "DNI invalido debe ser númerico.");
        }
        // : VALIDACION DE LENGTH DE DNI
        $numero_length = Str::length($numero);
        if ($numero_length != 8) {
            return array("mensaje" => "DNI invalido debe tener 8 digitos.");
        }
    }
    return true;
}

// * FUNCION DE VERIFICAR REGISTO EN SERVICIO - API DE CONSULTAR NUMERO DOCUMENTO
function verificarPersonVerify($client, $type_token, $token, $person)
{
    try {
        // * API DE CONSULTAR NUMERO DOCUMENTO
        $response = $client->request(
            'GET',
            '/api/v1/person/detailsByDocument/' . $person,
            [
                "headers" => [
                    "Authorization" => $type_token . " " . $token
                ],
            ]
        );
        $body = json_decode($response->getBody());
        return $body;
    } catch (RequestException $e) {
        return response()->json(array(
            "mensaje" => "Problemas con el servicio de verificación, comunicarse con nosotros.",
            "status" => 404,
            "mensaje_servicio" => $e->getMessage(),
            "status_servicio" => $e->getResponse()->getStatusCode()
        ), 404);
    }
}

// * FUNCION  DE REGISTAR PERSONA EN SERVICIO
function registrarPersonVerify($client, $type_token, $token, $array)
{
    try {
        // * API DE REGISTRAR PERSONA
        $response = $client->request(
            'POST',
            '/api/v1/person',
            [
                "headers" => [
                    "Authorization" => $type_token . " " . $token
                ],
                "form_params" => [
                    $array
                ]
            ]
        );
        return true;
    } catch (RequestException $e) {
        // * ERROS CUANDO YA SE ENCUENTRA REGISTRADO LA PERSONA
        if ($e->getResponse()->getStatusCode() == 422) {
            return false;
        }
        // * OTRO ERROR QUE AUN NO ESTA CONTROLADO
        return response()->json(array(
            "mensaje" => "Problemas con el servicio de verificación, comunicarse con nosotros.",
            "status" => 404,
            "mensaje_servicio" => $e->getMessage(),
            "status_servicio" => $e->getResponse()->getStatusCode()
        ), 404);
    }
}

// * GUARDAR IMAGEN DE RENIEC
function rutaFotoVerify($foto, $organizacion)
{
    // : NOMBRE DE LA CARPETA
    $nombre = "f" . $organizacion;
    // : ENCRIPTAR NOMBRE
    $encode = intval($nombre, 36);
    // : BUSCAMOS QUE NO EXISTA CARPETA PARA CREAR CARPETA
    if (!file_exists(app_path() . '/images_verify/' . $encode)) {
        File::makeDirectory(app_path() . '/images_verify/' . $encode, $mode = 0777, true, true);
    }
    $data = $foto;
    // : DECODIFICAR IMAGEN
    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    $path = app_path();
    $image = base64_decode($data);
    $fileName = '/images_verify/' . $encode . '/' . uniqid() . '.jpeg';
    $success = file_put_contents($path . $fileName, $image);                // : GUARDAMOS IMAGEN EN LA CARPETA

    return $fileName;
}

// * VERIFICAR BOLSAS DE VERIFY
function bolsaSaldoVerify($tipo, $contador, $bolsa)
{
    // : ESTADOS DE DEVOLUCION -> false: NO SE ENCUENTRA ACTIVO, true: NO CUENTA CON SALDO
    switch ($tipo) {
            // ? -------------------------------- CONSULTA DE IDENTIDAD ------------------------------------
        case 1:
            // : VERIFICAMOS QUE SERVICIO TIENE ACTIVO
            $asoc = asocs_servicio::where('tipo', '=', 'consulta_identidad')->where('activo', '=', 1)->get();
            // : SI NO ENCONTRAMOS SERVICIO ACTIVO DEVOLVEMOS FALSE
            if (sizeof($asoc) == 0) return "Verificación de identidad inactivo";
            // : VERIFICAMOS SALDO DE IDENTIDAD
            if ($bolsa == 0) return "Verificación de identidad sin saldo";
            // : RESTAMOS BOLSA CON CONTADOR PARA OBTENER EL SALDO POR AHORA
            $resta_saldo = $bolsa - $contador;
            // : VERIFICAMOS DENUEVO SALDO
            if ($resta_saldo == 0) return "Verificación de identidad sin saldo";
            // : DEVOLVEMOS ARRAY DE ASOCS
            $arrayAsoc = [];
            foreach ($asoc as $a) {
                array_push($arrayAsoc, $a->codigo);
            }
            return $arrayAsoc;
            break;
            // ? -------------------------------- CONSULTA DE POLICIAL ------------------------------------
        case 2:
            // : VERIFICAMOS QUE SERVICIO TIENE ACTIVO
            $asoc = asocs_servicio::where('tipo', '=', 'consulta_policial')->where('activo', '=', 1)->get();
            // : SI NO ENCONTRAMOS SERVICIO ACTIVO DEVOLVEMOS FALSE
            if (sizeof($asoc) == 0) return "Antecendetes policiales inactivo";
            // : VERIFICAMOS SALDO DE IDENTIDAD
            if ($bolsa == 0) return "Antecendetes policiales sin saldo";
            // : RESTAMOS BOLSA CON CONTADOR PARA OBTENER EL SALDO POR AHORA
            $resta_saldo = $bolsa - $contador;
            // : VERIFICAMOS DENUEVO SALDO
            if ($resta_saldo == 0) return "Antecendetes policiales sin saldo";
            // : DEVOLVEMOS ARRAY DE ASOCS
            $arrayAsoc = [];
            foreach ($asoc as $a) {
                array_push($arrayAsoc, $a->codigo);
            }
            return $arrayAsoc;
            break;
            // ? -------------------------------- CONSULTA DE PENAL ------------------------------------
        case 3:
            // : VERIFICAMOS QUE SERVICIO TIENE ACTIVO
            $asoc = asocs_servicio::where('tipo', '=', 'consulta_penal_judicial')->where('activo', '=', 1)->get();
            // : SI NO ENCONTRAMOS SERVICIO ACTIVO DEVOLVEMOS FALSE
            if (sizeof($asoc) == 0) return "Antecendetes penales y judiciales inactivo";
            // : VERIFICAMOS SALDO DE IDENTIDAD
            if ($bolsa == 0) return "Antecendetes penales y judiciales sin saldo";
            // : RESTAMOS BOLSA CON CONTADOR PARA OBTENER EL SALDO POR AHORA
            $resta_saldo = $bolsa - $contador;
            // : VERIFICAMOS DENUEVO SALDO
            if ($resta_saldo == 0) return "Antecendetes penales y judiciales sin saldo";
            // : DEVOLVEMOS ARRAY DE ASOCS
            $arrayAsoc = [];
            foreach ($asoc as $a) {
                array_push($arrayAsoc, $a->codigo);
            }
            return $arrayAsoc;
            break;
            // ? -------------------------------- CONSULTA DE CREDITICIO ------------------------------------
        case 4:
            // : VERIFICAMOS QUE SERVICIO TIENE ACTIVO
            $asoc = asocs_servicio::where('tipo', '=', 'consulta_crediticio')->where('activo', '=', 1)->get();
            // : SI NO ENCONTRAMOS SERVICIO ACTIVO DEVOLVEMOS FALSE
            if (sizeof($asoc) == 0) return "Record crediticio inactivo";
            // : VERIFICAMOS SALDO DE IDENTIDAD
            if ($bolsa == 0) return "Record crediticio sin saldo";
            // : RESTAMOS BOLSA CON CONTADOR PARA OBTENER EL SALDO POR AHORA
            $resta_saldo = $bolsa - $contador;
            // : VERIFICAMOS DENUEVO SALDO
            if ($resta_saldo == 0) return "Record crediticio sin saldo";
            // : DEVOLVEMOS ARRAY DE ASOCS
            $arrayAsoc = [];
            foreach ($asoc as $a) {
                array_push($arrayAsoc, $a->codigo);
            }
            return $arrayAsoc;
            break;
    }
}

// * VERIFICAR CONSULTA
function verificarServicioSolicitarVerify($consulta)
{
    switch ($consulta) {
        case 1:
            return true;
            break;
    }
}

// * RESPUESTA DE VERIFICACION QUE SE PIDA DESDE EL FRONTEND
function verificarYbolsaVerify($identidad, $policial, $penal_judicial, $crediticio, $contador, $bolsa)
{
    $array_asoc = [];
    $sin_consultar = [];
    // ? ----------------------- CONSULTA DE IDENTIDAD ------------------------
    $consulta_identidad = verificarServicioSolicitarVerify($identidad);
    if (!is_null($consulta_identidad)) {
        $respuesta_identidad = bolsaSaldoVerify(1, $contador, $bolsa->saldo_identidad);
        if (is_array($respuesta_identidad)) {
            array_push($array_asoc, $respuesta_identidad);
        } else {
            array_push($sin_consultar, $respuesta_identidad);
        }
    }
    // ? ------------------------- CONSULTA DE POLICIAL -----------------------------
    $consulta_policial = verificarServicioSolicitarVerify($policial);
    if (!is_null($consulta_policial)) {
        $respuesta_policial = bolsaSaldoVerify(2, $contador, $bolsa->saldo_policial);
        if (is_array($respuesta_policial)) {
            array_push($array_asoc, $respuesta_policial);
        } else {
            array_push($sin_consultar, $respuesta_policial);
        }
    }
    // ? ----------------------------- CONSULTA DE PENAL ----------------------------
    $consulta_penal_judicial = verificarServicioSolicitarVerify($penal_judicial);
    if (!is_null($consulta_penal_judicial)) {
        $respuesta_penal_judicial = bolsaSaldoVerify(3, $contador, $bolsa->saldo_penal_judicial);
        if (is_array($respuesta_penal_judicial)) {
            array_push($array_asoc, $respuesta_penal_judicial);
        } else {
            array_push($sin_consultar, $respuesta_penal_judicial);
        }
    }
    // ? ----------------------------- CONSULTA DE CREDITICIO ----------------------------
    $consulta_crediticio = verificarServicioSolicitarVerify($crediticio);
    if (!is_null($consulta_crediticio)) {
        $respuesta_crediticio = bolsaSaldoVerify(4, $contador, $bolsa->saldo_crediticio);
        if (is_array($respuesta_crediticio)) {
            array_push($array_asoc, $respuesta_crediticio);
        } else {
            array_push($sin_consultar, $respuesta_crediticio);
        }
    }

    return array("asocs" => $array_asoc, "mensaje" => $sin_consultar);
}
