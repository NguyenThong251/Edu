<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Xác thực token JWT
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            return formatResponse(STATUS_FAIL, '', '', 'Token không hợp lệ');
        } catch (TokenExpiredException $e) {
            return formatResponse(STATUS_FAIL, '', '', 'Token đã hết hạn');
        } catch (\Exception $e) {
            return formatResponse(STATUS_FAIL, '', '', 'Không tìm thấy Token');
        }
        return $next($request);
    }
}
