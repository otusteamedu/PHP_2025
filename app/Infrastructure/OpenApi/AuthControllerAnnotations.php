<?php

namespace App\Infrastructure\OpenApi;

use OpenApi\Attributes as OA;

class AuthControllerAnnotations
{
    #[OA\Post(
        path: '/api/v1/register',
        tags: ['Auth'],
        description: 'Register new user',
        requestBody: new OA\RequestBody(
            description: 'User registration data',
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Registration successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'jwt', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Невалидный email')
                    ]
                )
            )
        ]
    )]
    public function register() {}

    #[OA\Post(
        path: '/api/v1/login',
        tags: ['Auth'],
        description: 'User login',
        requestBody: new OA\RequestBody(
            description: 'User login credentials',
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'jwt', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid credentials',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Неверные email/пароль')
                    ]
                )
            )
        ]
    )]
    public function login() {}
}