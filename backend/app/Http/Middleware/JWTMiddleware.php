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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Xác thực token JWT
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            // Token không hợp lệ
            return formatResponse(401, STATUS_FAIL, '', '', 'Token không hợp lệ');
        } catch (TokenExpiredException $e) {
            // Token hết hạn
            return formatResponse(401, STATUS_FAIL, '', '', 'Token đã hết hạn');
        } catch (\Exception $e) {
            // Lỗi khác (Token không tìm thấy hoặc lỗi không xác định)
            return formatResponse(401, STATUS_FAIL, '', '', 'Không tìm thấy Token');
        }
        // Tiếp tục xử lý request
        return $next($request);
    }
}
