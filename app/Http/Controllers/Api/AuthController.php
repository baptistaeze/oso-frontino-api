<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    #[OA\Post(
        path: '/auth/register',
        tags: ['Auth'],
        summary: 'Register and get Bearer token',
        operationId: 'auth.register',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                example: [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User registered, token returned',
                content: new OA\JsonContent(example: [
                    'user' => [
                        'id' => 1,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                    ],
                    'token' => '1|abc123...',
                    'tokenType' => 'Bearer',
                ])
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
            ],
            'token' => $result['token'],
            'tokenType' => $result['tokenType'],
        ], 201);
    }

    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Login and get Bearer token',
        operationId: 'auth.login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                example: [
                    'email' => 'john@example.com',
                    'password' => 'password123',
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful, token returned',
                content: new OA\JsonContent(example: [
                    'user' => [
                        'id' => 1,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                    ],
                    'token' => '1|abc123...',
                    'tokenType' => 'Bearer',
                ])
            ),
            new OA\Response(response: 422, description: 'Invalid credentials'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated('email'),
            $request->validated('password')
        );

        return response()->json([
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
            ],
            'token' => $result['token'],
            'tokenType' => $result['tokenType'],
        ]);
    }

    #[OA\Post(
        path: '/auth/logout',
        tags: ['Auth'],
        summary: 'Logout (revoke current token)',
        operationId: 'auth.logout',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logged out'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out']);
    }
}
