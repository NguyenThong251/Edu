<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,instructor,student',
        ]);

        if ($validator->fails()) {
            return formatResponse(422, STATUS_FAIL, '', $validator->errors(), 'Validation failed');
        }

        $currentUser = auth()->user();
        $role = request()->input('role');

        if ($currentUser) {
            if ($currentUser->role !== 'admin') {
                return formatResponse(403 , STATUS_FAIL, '', '', 'Unauthorized role assignment');
            }
        } else {
            if (!in_array($role, ['instructor', 'student'])) {
                return formatResponse(403 ,STATUS_FAIL, '', '', 'Unauthorized role assignment');
            }
        }

        $user = User::create([
            'username' => request()->input('username'),
            'email' => request()->input('email'),
            'password' => Hash::make(request()->input('password')),
            'role' => $role,
        ]);
        return formatResponse(200, STATUS_OK, $user, '', 'Register successfully');
    }

    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return formatResponse(422, STATUS_FAIL, '', $validator->errors(), 'Validation failed');
        }

        $user = User::where(['username' => request()->input('username')])->first();
        if (!$user) {
            return formatResponse(404, STATUS_FAIL, '', '', 'Username does not exist');
        }

        $credentials = request(['username', 'password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return formatResponse(401, STATUS_FAIL, '', '', 'Incorrect password');
        }
        $refreshToken = $this->createRefreshToken();
        return formatResponse(200 ,STATUS_OK, $user, '', 'Login successfully', $token, $refreshToken);
        //còn gửi mail verify và queue bổ sung sau
    }

    public function profile()
    {
        $user = JWTAuth::parseToken()->authenticate();     
        if (!$user) {
            return formatResponse(404, STATUS_FAIL, '', '', 'User not found');
        }
        return formatResponse(200, STATUS_OK, $user, '', 'Get user info successfully');
    }


    public function logout()
    {
        auth('api')->logout();
        return formatResponse(200, STATUS_OK, '', '', 'Logged out successfully');
    }


    public function refresh()
    {
        $refresh_token = request()->input('refresh_token');
//        dd($refresh_token);
        $decode = JWTAuth::getJWTProvider()->decode($refresh_token);


        auth('api')->invalidate();
        $user = User::find($decode['user_id']);

        $token = auth('api')->login($user);
        $refreshToken = $this->createRefreshToken();

        return formatResponse(200, STATUS_OK, $user, '', 'Login successfully', $token, $refreshToken);
    }

    private function createRefreshToken()
    {
        $data = [
            'user_id' => auth('api')->user()->id,
            'random' => rand() . time(),
            'exp' => time() . config('jwt.refresh.ttl'),
        ];
        $refreshToken = JWTAuth::getJWTProvider()->encode($data);
        return $refreshToken;
    }

    protected function respondWithToken($token, $refreshToken)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
