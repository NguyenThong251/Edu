<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailWelcome;
use App\Models\User;
use App\Jobs\SendEmailForgotPassword;
use App\Jobs\SendEmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use function Termwind\render;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',
            [
                'except' => [
                    'login',
                    'register',
                    'refresh',
                    'verifyEmail',
                    'forgotPassword',
                    'resetPassword',
                    'getGoogleSignInUrl',
                    'loginGoogleCallback',
                    'test',
                ]
            ]);
    }

    public function test()
    {
        render('emails.welcome');
    }

    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,instructor,student',
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), 'Xác thực thất bại');
        }

        $currentUser = auth()->user();
        $role = request()->input('role');

        if ($currentUser) {
            if ($currentUser->role !== 'admin') {
                return formatResponse(STATUS_FAIL, '', '', 'Không được phép tạo vai trò này');
            }
        } else {
            if (!in_array($role, ['instructor', 'student'])) {
                return formatResponse(STATUS_FAIL, '', '', 'Không được phép tạo vai trò này');
            }
        }

        $user = User::create([
            'email' => request()->input('email'),
            'password' => Hash::make(request()->input('password')),
            'role' => $role,
            'verification_token' => Str::random(60),
        ]);

        SendEmailVerification::dispatch($user);

        return formatResponse(STATUS_OK, $user, '', 'Đăng ký thành công');
    }

    public function getGoogleSignInUrl(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role' => 'required|in:instructor,student',
            ]);

            if ($validator->fails()) {
                return formatResponse(STATUS_FAIL, '', $validator->errors(), 'xác thực thất bại');
            }
            $role = $request->input('role');

            $url = Socialite::driver('google')->stateless()->with(['state' => http_build_query(['role' => $role])])
                ->redirect()->getTargetUrl();
            return formatResponse(STATUS_OK, ['url' => $url], '', 'Lấy đường dẫn đăng ký thành công !', CODE_OK);
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function loginGoogleCallback(Request $request)
    {
        try {
            $state = $request->input('state');
            parse_str($state, $result);
            $googleUser = Socialite::driver('google')->stateless()->user();

            $role = $result['role'] ?? User::ROLE_STUDENT;

            $user = User::where('email', $googleUser->email)->first();
            if ($user) {
                if (!$token = auth('api')->login($user)) {
                    return formatResponse(STATUS_FAIL, '', '', 'Không thể tạo token cho tài khoản đã tồn tại',
                        CODE_FAIL);
                }
                $refreshToken = $this->createRefreshToken();
                return formatResponse(STATUS_OK, $user, '', 'Đăng nhập thành công', CODE_OK, $token, $refreshToken);
            }

            $user = User::create(
                [
                    'avatar' => $googleUser->avatar,
                    'email' => $googleUser->email,
                    'full_name' => $googleUser->name,
                    'role' => $role,
                    'status' => User::USER_ACTIVE,
                    'email_verified' => $googleUser->user['email_verified'],
                    'provider' => 'google',
                    'provider_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(12)),
                ]
            );

            if (!$token = auth('api')->login($user)) {
                return formatResponse(STATUS_FAIL, '', '', 'Không thể tạo token cho tài khoản đã tồn tại',
                    CODE_FAIL);
            }
            $refreshToken = $this->createRefreshToken();

            SendEmailWelcome::dispatch($user);
            return formatResponse(STATUS_OK, $user, '', 'Đăng nhập thành công', CODE_OK, $token, $refreshToken);
        } catch (\Exception $exception) {
            return formatResponse(STATUS_FAIL, '', $exception, 'Đăng nhập tài khoản google thất bại', CODE_BAD);
        }
    }

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();
        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', 'Đường dẫn không tồn tại', CODE_NOT_FOUND);
        }
        $user->email_verified = true;
        $user->status = USER::USER_ACTIVE;
        $user->verification_token = null;
        $user->save();
        return formatResponse(STATUS_OK, $user, '', 'Xác thực tài khoản thành công');
    }


    public function forgotPassword()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|string|email|max:100',
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), 'Xác thực thất bại');
        }

        $user = User::where('email', request()->input('email'))->first();
        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', 'Không có tài khoản nào thuộc email này');
        }

        $user->reset_token = Str::random(60);
        $user->save();
        SendEmailForgotPassword::dispatch($user);
        return formatResponse(STATUS_OK, '', '', 'Gửi email thành công', CODE_OK);
    }

    public function resetPassword($token)
    {
        $user = User::where('reset_token', $token)->first();
        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', 'Đường dẫn không tồn tại', CODE_NOT_FOUND);
        }

        $validator = Validator::make(request()->all(), [
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), 'Xác thực thất bại');
        }

        $user->reset_token = null;
        $user->password = Hash::make(request()->input('password'));
        $user->save();
        return formatResponse(STATUS_OK, $user, '', 'Thay đổi mật khẩu thành công', CODE_OK);
    }

        public function login()
        {
    //        dd(__('messages.welcome'));
            $validator = Validator::make(request()->all(), [
                'email' => 'required|string|email|max:100',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return formatResponse(STATUS_FAIL, '', $validator->errors(), 'Xác thực thất bại');
            }

            $user = User::where(['email' => request()->input('email')])->first();
            if (!$user) {
                return formatResponse(STATUS_FAIL, '', '', 'Email không tồn tại');
            }
            if (!$user->email_verified) {
                return formatResponse(STATUS_FAIL, '', '',
                    'Email chưa được xác thực, vui lòng kiểm tra email để xác thực tài khoản.');
            }
            $credentials = request(['email', 'password']);
            if (!$token = auth('api')->attempt($credentials)) {
                return formatResponse(STATUS_FAIL, '', '', 'Mật khẩu không chính xác');
            }
            $refreshToken = $this->createRefreshToken();

            return formatResponse(STATUS_OK, $user, '', 'Đăng nhập thành công', CODE_OK, $token, $refreshToken);
        }

    public function logout()
    {
        auth('api')->logout();
        return formatResponse(STATUS_OK, '', '', 'Đăng xuất thành công');
    }

    public function profile()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return formatResponse(STATUS_OK, $user, '', 'Lấy thông tin tài khoản thành công');
    }


    public function refresh()
    {
        try {
            $refresh_token = request()->input('refresh_token');
            if (!$refresh_token) {
                return formatResponse(STATUS_FAIL, '', '', 'Vui lòng nhập refresh token');
            }

            $decode = JWTAuth::getJWTProvider()->decode($refresh_token);

            // Invalidate current access token
//            auth('api')->invalidate();

            $user = User::find($decode['user_id']);
            if (!$user) {
                return formatResponse(STATUS_FAIL, '', '', 'Tài khoản không tồn tại');
            }
            // Generate new tokens
            $token = auth('api')->login($user);
            $refreshToken = $this->createRefreshToken();

            return formatResponse(STATUS_OK, $user, '', 'Refresh access token thành công', CODE_OK, $token,
                $refreshToken);

        } catch (TokenExpiredException $e) {
            return formatResponse(STATUS_FAIL, '', '', 'Refresh token đã hết hạn');
        } catch (TokenInvalidException $e) {
            return formatResponse(STATUS_FAIL, '', '', 'Refresh token không hợp lệ');
        } catch (Exception $e) {
            return formatResponse(STATUS_FAIL, '', '', 'Lỗi xảy ra trong quá trình làm mới token');
        }
    }

    public function updateProfile($id)
    {
        $user1 = auth()->user();
        $user = User::where('id', $id)->first();
        $validator = Validator::make(request()->all(), [
            'username' => 'string|max:50|unique:users',
            'email' => 'string|email|max:100|unique:users',
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), 'Xác thực thất bại');
        }
        $data = request()->except(['role', 'password', 'email_verified', 'reset_token', 'status']);

        if (!$user->update($data)) {
            return formatResponse(STATUS_FAIL, '', '', 'Cập nhật thông tin thất bại');
        }

        return formatResponse(STATUS_OK, $user, '', 'Cập nhật thông tin thành công');
    }

    public function adminUpdateUser()
    {
//        $auth = auth()->user();
//        if ($auth->role != 'admin') {
//            return formatResponse(STATUS_FAIL, '', '', 'Không có quyền chỉnh sửa tài khoản');
//        }
        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|integer',
            'username' => 'string|max:50|unique:users',
            'email' => 'string|email|max:100|unique:users',
            'password' => 'string|min:8',
            'role' => 'in:admin,instructor,student',
            'status' => 'in:active,inactive',
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), 'Xác thực thất bại');
        }
        $data = request()->all();

        $user = User::where('id', $data['user_id'])->first();
        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', 'Tài khoản không tồn tại');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make(request()->input('password'));
        }

        if (!$user->update($data)) {
            return formatResponse(STATUS_FAIL, '', '', 'Cập nhật thông tin thất bại');
        }
        return formatResponse(STATUS_OK, $user, '', 'Cập nhật thông tin thành công');
    }


    public function deleteUser($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', 'Tài khoản không tồn tại');
        }

        if ($user->delete()) {
            $user->is_deleted = User::STATUS_DELETED;
            $user->save();
            return formatResponse(STATUS_OK, '', '', 'Xóa tài khoản thành công');
        }
        return formatResponse(STATUS_FAIL, '', '', 'Xóa tài khoản thất bại');

    }

    public function restoreUser($id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', 'Tài khoản không tồn tại');
        }

        if ($user->trashed()) {
            $user->restore();
            $user->is_deleted = User::STATUS_DEFAULT;
            $user->save();
            return formatResponse(STATUS_OK, $user, '', 'Khôi phục thành công');
        }
        return formatResponse(STATUS_FAIL, '', '', 'Khôi phục thất bại');
    }

    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', 'Tài khoản không tồn tại');
        }

        // Xóa hoàn toàn khỏi DB
        $user->forceDelete();
        return formatResponse(STATUS_OK, $user, '', 'Xóa hoàn toàn thành công');
    }


    private
    function createRefreshToken()
    {
        $data = [
            'user_id' => auth('api')->user()->id,
            'random' => rand() . time(),
            'exp' => time() + config('jwt.refresh_ttl') * 60,
        ];
        $refreshToken = JWTAuth::getJWTProvider()->encode($data);
        return $refreshToken;
    }

    protected
    function respondWithToken(
        $token,
        $refreshToken
    ) {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
