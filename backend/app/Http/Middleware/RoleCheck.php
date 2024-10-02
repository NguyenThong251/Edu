<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Lấy user từ JWT token
        $user = Auth::user();

        // Kiểm tra nếu user có role phù hợp với role của route
        if ($user && $user->role === $role) {
            return $next($request);
        }

        // Nếu không đúng role, trả về lỗi
        return formatResponse(STATUS_FAIL, '', '', 'Bạn không có quyền thực hiện hành động này');
    }
}
