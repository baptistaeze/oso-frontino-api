<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: 'Oso Frontino API',
        version: '1.0',
        description: 'API for iPhone store - Products, Baskets and Invoices. Use POST /auth/login or POST /auth/register to get a Bearer token. Include it in the Authorization header for create, update and delete operations.'
    ),
    servers: [
        new OA\Server(url: '/api', description: 'API (relative - same origin)'),
        new OA\Server(url: 'http://127.0.0.1:8002/api', description: 'Local - 127.0.0.1'),
        new OA\Server(url: 'http://localhost:8002/api', description: 'Local - localhost'),
    ],
    components: new OA\Components(
        securitySchemes: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'Token',
                description: 'Enter your Bearer token from login/register'
            ),
        ]
    )
)]
class OpenApiSpec
{
}
