<?php
// * SERVICIO DE VERIFY

use App\crd_token;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

function loginServicioVerify($body, $credencial)
{
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
}
