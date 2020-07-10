<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenVerificar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        try {
            JWTAuth::setToken($token); // setToken and check
            if (!$claim = JWTAuth::getPayload()) {
                return response()->json(array('message' => 'user_not_found'), 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(array('message' => 'token_expired'), 404);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(array('message' => 'token_invalid'), 404);
        } catch (\Tymon\JWTAuth\Exceptions\TokenException $e) {
            return response()->json(array('message' => 'token_absent'), 404);
        }
        return $next($request);
    }
}
