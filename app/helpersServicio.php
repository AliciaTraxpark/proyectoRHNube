<?php
// * SERVICIO DE VERIFY

use App\crd_token;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;

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
                "respuesta" => "Credenciales incorrectas del servicio,comunicarse con nosotros.",
                "status" => 404,
                "respuesta_servicio" => $e->getMessage(),
                "status_servicio" => $e->getResponse()->getStatusCode()
            ), 404);
        }
        return response()->json(array(
            "respuesta" => "Problemas con el servicio de verificación, comunicarse con nosotros.",
            "status" => 404,
            "respuesta_servicio" => $e->getMessage(),
            "status_servicio" => $e->getResponse()->getStatusCode()
        ), 404);
    }
}

// * FUNCION DE VERIFICAR REGISTO EN SERVICIO
function verificarPerson($client, $type_token, $token, $person)
{
    try {
        // * API DE CONSULTAR NUMERO DOCUMENTO
        $response = $client->request(
            'POST',
            '/api/v1/person/detailsByDocument/' . $person,
            [
                "headers" => [
                    "Authorization" => $type_token . " " . $token
                ],
            ]
        );
        $body = json_decode($response->getBody());
        // * SI NO ENCUENTRA REGISTRADO EL DOCUMENTO
        if (isset($body->message)) {
            return false;
        }
        // * SI LO ENCUENTRA REGISTRAMOS O ACTUALIZAMOS DATOS
        
    } catch (RequestException $e) {
        return response()->json(array(
            "respuesta" => "Problemas con el servicio de verificación, comunicarse con nosotros.",
            "status" => 404,
            "respuesta_servicio" => $e->getMessage(),
            "status_servicio" => $e->getResponse()->getStatusCode()
        ), 404);
    }
}
