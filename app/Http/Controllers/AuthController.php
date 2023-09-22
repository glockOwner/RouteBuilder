<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="404",
 *     description="Not Found",
 * )
 *
 *
 * @OA\Response(
 *     response="403",
 *     description="Forbidden",
 * )
 *
 *
 *
 *
 * @OA\Response(
 *     response="422",
 *     description="Unprocessable Content",
 *     @OA\JsonContent(
 *         @OA\Property (property="message", type="string", example="The given data was invalid."),
 *         @OA\Property (property="errors", type="object", description="array of validation errors",
 *             @OA\Property (property="field", type="array", @OA\Items(type="string", example="desc of error"))
 *         )
 *     )
 * )
 */
class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/registration",
     *     tags={"Auth"},
     *     summary="Method for registration",
     *     @OA\RequestBody(
     *         description="The data for auth",
     *         @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property (property="email", type="string", example="test@mail.ru"),
     *              @OA\Property (property="password", type="string", example="123")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User is registered and got access and refresh tokens",
     *         @OA\Schema (
     *              @OA\Property (property="email", type="string"),
     *              @OA\Property (property="access_token", type="object", description="key for authorization",
     *                  @OA\Property (property="token", type="string", example="111|mejC8hk7lhsdt3fsVQ3mCWXSJG1YcTf5qjWHn3zGv"),
     *                  @OA\Property (property="expired_at", type="string")
     *              )
     *         )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/422")
     * )
     */
    public function registerUser(AuthRequest $request)
    {
        $request->validated();

        $password = Hash::make($request->password);
        /** @var User $user */
        $user = User::firstOrCreate(['email' => $request->email], ['password' => $password]);
        list($accessToken, $refreshToken) = $this->getAccessAndRefreshToken($user);

        return new AuthResource($user, $accessToken, $refreshToken);
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Method for login",
     *     @OA\RequestBody(
     *         description="The data for auth",
     *         @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property (property="email", type="string", example="test@mail.ru"),
     *              @OA\Property (property="password", type="string", example="123"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User is logined and got access and refresh tokens",
     *         @OA\JsonContent(
     *              @OA\Property (property="email", type="string"),
     *              @OA\Property (property="access_token", type="object", description="key for authorization",
     *                  @OA\Property (property="token", type="string", example="111|mejC8hk7lhsdt3fsVQ3mCWXSJG1YcTf5qjWHn3zGv"),
     *                  @OA\Property (property="expired_at", type="string")
     *              )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/404"),
     *     @OA\Response(response="403", ref="#/components/responses/403"),
     *     @OA\Response(response="422", ref="#/components/responses/422")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/refresh-token",
     *     tags={"Auth"},
     *     summary="Method for token refreshing",
     *     security={ {"bearerAuth": {}} },
     *     @OA\Response(
     *         response="200",
     *         description="Access token is refreshed",
     *         @OA\JsonContent(
     *              @OA\Property (property="email", type="string"),
     *              @OA\Property (property="access_token", type="object", description="key for authorization",
     *                  @OA\Property (property="token", type="string", example="111|mejC8hk7lhsdt3fsVQ3mCWXSJG1YcTf5qjWHn3zGv"),
     *                  @OA\Property (property="expired_at", type="string")
     *              )
     *         )
     *     ),
     *     @OA\Response(response="403", ref="#/components/responses/403")
     * )
     */
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
