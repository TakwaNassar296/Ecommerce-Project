<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\password;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $accessTokenTTLMinutes = 15; 
    protected $refreshTokenTTLDays = 30;   

    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($v->fails()) {
            return $this->sendResponse('Validation error', $v->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $tokens = $this->createTokensForUser($user, $request);

        return $this->sendResponse('Registered successfully', $tokens, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($v->fails()) {
            return $this->sendResponse('Validation error', $v->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->sendResponse('Invalid credentials', null, Response::HTTP_UNAUTHORIZED);
        }

        $tokens = $this->createTokensForUser($user, $request);

        return $this->sendResponse('Logged in successfully', $tokens, Response::HTTP_OK);
    }

    public function user(Request $request)
    {
        return $this->sendResponse('User retrieved', $request->user(), Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        // revoke current access token
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        // revoke all refresh tokens for this user (optional)
        RefreshToken::where('user_id', $request->user()->id)->update(['revoked' => true]);

        return $this->sendResponse('Logged out', null, Response::HTTP_OK);
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $provided = $request->refresh_token;
        $hash = hash('sha256', $provided);

        $record = RefreshToken::where('token_hash', $hash)->first();

        if (! $record || ! $record->isValid()) {
            return $this->sendResponse('Refresh token invalid or expired', null, Response::HTTP_UNAUTHORIZED);
        }

        $user = $record->user;

        // revoke old access tokens? (optional)
        // $user->tokens()->delete();

        // create new access token + set expiry
        $accessToken = $user->createToken('access_token');
        $plainAccess = $accessToken->plainTextToken;

        $tokenModel = $accessToken->accessToken;
        $tokenModel->expires_at = now()->addMinutes($this->accessTokenTTLMinutes);
        $tokenModel->save();

        $record->revoked = true;
        $record->save();

        $newRefreshPlain = Str::random(80);
        $newHash = hash('sha256', $newRefreshPlain);
        $refresh = RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => $newHash,
            'expires_at' => now()->addDays($this->refreshTokenTTLDays),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
        ]);

        $data = [
            'access_token' => $plainAccess,
            'token_type' => 'Bearer',
            'expires_at' => $tokenModel->expires_at->toDateTimeString(),
            'refresh_token' => $newRefreshPlain,
            'refresh_expires_at' => $refresh->expires_at->toDateTimeString(),
        ];

        return $this->sendResponse('Token refreshed', $data, Response::HTTP_OK);
    }

    protected function createTokensForUser(User $user, Request $request)
    {
        // create access token
        $accessToken = $user->createToken('access_token');
        $plainAccess = $accessToken->plainTextToken;
        $tokenModel = $accessToken->accessToken;
        $tokenModel->expires_at = now()->addMinutes($this->accessTokenTTLMinutes);
        $tokenModel->save();

        // create refresh token 
        $refreshPlain = Str::random(80);
        $refreshHash = hash('sha256', $refreshPlain);

        $refresh = RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => $refreshHash,
            'expires_at' => now()->addDays($this->refreshTokenTTLDays),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
        ]);

        return [
            'access_token' => $plainAccess,
            'token_type' => 'Bearer',
            'expires_at' => $tokenModel->expires_at->toDateTimeString(),
            'refresh_token' => $refreshPlain,
            'refresh_expires_at' => $refresh->expires_at->toDateTimeString(),
            'user' => $user,
        ];
    }

    /*public function forgetpassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT){
           
            return response()->json([
                'status' => 'success',
                'message' => 'Reset link sent to your email' ,
                'code' => 200 ,

            ] , 200);
        }

            return response()->json([
                'status' => 'error',
                'message' => $status ,
                'code' => 500 ,

            ] , 500);

    }

     public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully',
                'code' => 200,
            ], 200);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    */

    public function forgetpassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email' , $request->email)->first();

        if (!$user)
        {
           return $this->sendResponse('User not found', null , Response::HTTP_NOT_FOUND); 
        }

        $otp = rand(100000, 999999);

        $user->otp = $otp ;
        $user->otp_expires = now()->addMinutes(10);

        $user->save();

        return $this->sendResponse('Otp generated successfully',['otp' , $otp] , Response::HTTP_OK);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email' ,
            'otp' => 'required|numeric|min:6',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::where('email' , $request->email)->first();

        if (!$user)
        {
           return $this->sendResponse('User not found', null , Response::HTTP_NOT_FOUND); 
        }

        if ($user->otp !== $request->otp || $user->otp_expires < now() )
        {
           return $this->sendResponse('Invalid or expired Otp', null , 400); 
        }

        $user->password = bcrypt($request->password) ;
        $user->otp = null ;
        $user->otp_expires = null;

        $user->save();

        return $this->sendResponse('Password reset successfully', null , Response::HTTP_OK);

    }

    public function sendotp(Request $request)
    {
        $user = User::where('email' , $request->email)->firstOrFail();

        $otp = rand(100000, 999999);

        $user->otp = $otp ;
        $user->otp_expires = now()->addMinutes(10);

        $user->save();

        return $this->sendResponse('Otp generated successfully', $otp, Response::HTTP_OK);

    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric'
        ]);

        $user = User::where('email' , $request->email)->firstOrFail();

        if($user->otp !== $request->otp || $user->otp_expirse < now())
        {
           return $this->sendResponse('Invalid or expired Otp', null , 400); 
        }

        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expirse = null;

        $user->save();

        
        return $this->sendResponse('Email verified successfully', null, Response::HTTP_OK);

    }
}
