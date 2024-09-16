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
            return response()->json(['error' => 'Token không hợp lệ'], 401);
        } catch (TokenExpiredException $e) {
            // Token hết hạn
            return response()->json(['error' => 'Token đã hết hạn'], 401);
        } catch (\Exception $e) {
            // Lỗi khác (Token không tìm thấy hoặc lỗi không xác định)
            return response()->json(['error' => 'Không tìm thấy Token'], 401);
        }
        // Tiếp tục xử lý request
        return $next($request);
    }
}
