<?php

namespace App\Http\Controllers\Api\V1;

use App\Enum\ProcessResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Tools\EndProcess;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ])) {
                return response()->json([
                    'message' => ProcessResponse::AUTH_FAILED
                ], Response::HTTP_UNAUTHORIZED);
            }

            $accessToken = $user->createToken(env('APP_TOKEN_NAME'))->plainTextToken;

            $response = [
                'user' => new UserResource($user),
                'access_token' => $accessToken
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error(
                "{$e->getMessage()}:
                 {$e->getFile()}:
                 {$e->getLine()}"
            );

            return response()->json([
                'message' => ProcessResponse::SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
