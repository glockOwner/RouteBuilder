<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function registerUser(AuthRequest $request)
    {
        $request->validated();

        $password = Hash::make($request->password);
        /** @var User $user */
        $user = User::firstOrCreate(['email' => $request->email], ['password' => $password]);
        list($accessToken, $refreshToken) = $this->getAccessAndRefreshToken($user);

        return new AuthResource($user, $accessToken, $refreshToken);
    }

    public function loginUser(AuthRequest $request)
    {
        $request->validated();
        $hashedPassword = Hash::make($request->password);

        /** @var User $user */
        $user = User::where([
            'email' => $request->email,
        ])->firstOrFail();
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) abort(403, 'Access denied');

        list($accessToken, $refreshToken) = $this->getAccessAndRefreshToken($user);

        return new AuthResource($user, $accessToken, $refreshToken);
    }

    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken();
        $refreshToken = PersonalAccessToken::findToken($token);
        /** @var User $user */
        $user = $refreshToken->tokenable;

        list($accessToken, $refreshToken) = $this->getAccessAndRefreshToken($user);

        return new AuthResource($user, $accessToken, $refreshToken);
    }

    private function getAccessAndRefreshToken(User $user)
    {
        $user->tokens()->delete();
        $accessToken = $user->createAuthToken('apiToken');
        $refreshToken = $user->createRefreshToken('apiToken');
        return [$accessToken, $refreshToken];
    }
}
