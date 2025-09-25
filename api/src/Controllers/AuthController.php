<?php

// \AuthController
namespace App\Controllers;

use App\Utility\_DataAccess;
use App\Utility\Result;
use App\Utility\TokenGenerator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends BaseController
{
    private _DataAccess $dataAccess;

    public function __construct(_DataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function login(Request $request, Response $response, array $args): Response
    {

        $result = new Result();
        $requestBody = $request->getParsedBody();

        if (isset($requestBody['username']) && isset($requestBody['password'])) {

            $username = $requestBody['username'];
            $password = $requestBody['password'];
            $hashPassword = hash('sha256', $password);

            $qRes = $this->dataAccess->get(
                table: "users",
                args: [
                    "user_name" => $username,
                    "password" => $hashPassword
                ],
                single: true,
                fields: [
                    'user_id',
                    'email',
                    'user_name'
                ],
            );

            if ($qRes && count($qRes) > 0) {

                $this->dataAccess->delete(
                    table: 'users_oauth_token',
                    args: [
                        'user_id' => $qRes['user_id']
                    ]
                );

                $tokenRes = TokenGenerator::generateToken(
                    userId: $qRes['user_id'],
                    email: $qRes['email'],
                    userName: $qRes['user_name'],
                );

                $this->dataAccess->add(
                    table: 'users_oauth_token',
                    requestData: [
                        'token' => $tokenRes['token'],
                        'issued_at' => $tokenRes['iat'],
                        'expires_at' => $tokenRes['exp'],
                        'user_id' => "" . $qRes['user_id'],
                    ],
                );

                $result->setSuccessResult(['token' => $tokenRes['token']]);

            } else {
                $result->setUnauthorized();
            }

        } else {
            $result->setInvalidParameters();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function register(Request $request, Response $response, array $args): Response
    {
        $result = new Result();
        $requestBody = $request->getParsedBody();

        if (isset($requestBody['username']) && isset($requestBody['password'])) {

            $username = $requestBody['username'];
            $password = $requestBody['password'];
            $hashPassword = hash('sha256', $password);

            // Check if user already exists
            $existingUser = $this->dataAccess->get(
                table: "users",
                args: [
                    "username" => $username
                ],
                single: true
            );

            if ($existingUser && count($existingUser) > 0) {
                $result->setError('User already exists', 409);
            } else {

                // Create new user
                $userId = $this->dataAccess->add(
                    table: 'users',
                    requestData: [
                        'username' => $username,
                        'password' => $hashPassword
                    ]
                );

                if ($userId) {
                    // Generate token for new user
                    $tokenRes = TokenGenerator::generateToken(
                        userId: $userId,
                        userName: $username,
                    );

                    $this->dataAccess->add(
                        table: 'users_oauth_tokens',
                        requestData: [
                            'token' => $tokenRes['token'],
                            'issued_at' => $tokenRes['iat'],
                            'expires_at' => $tokenRes['exp'],
                            'user_id' => "" . $userId,
                        ],
                    );

                    $result->setSuccessResult([
                        'token' => $tokenRes['token'],
                        'user' => [
                            'id' => $userId,
                            'username' => $username
                        ]
                    ]);
                } else {
                    $result->setError('Failed to create user', 500);
                }

            }

        } else {
            $result->setInvalidParameters();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}