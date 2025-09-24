<?php

namespace App\Controllers;

use App\Utility\TokenGenerator;

class BaseController
{
    protected ?int $userId = null;
    protected ?string $username = null;
    protected ?string $email = null;

    protected function validateToken($request): bool
    {
        $authorizationHeader = $request->getHeaderLine('Authorization');

        if (empty($authorizationHeader)) {
            return false;
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        if (empty($token)) {
            return false;
        }

        $decodeToken = TokenGenerator::decodeToken($token);
        if (!isset($decodeToken['error'])) {
            $this->userId = $decodeToken['data']->userId ?? null;
            $this->username = $decodeToken['data']->username ?? null;
            $this->email = $decodeToken['data']->role ?? null;
        }

        return !isset($decodeToken['error']);
    }
}