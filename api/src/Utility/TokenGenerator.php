<?php

namespace App\Utility;

use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class TokenGenerator
{
    private const string JWT_SECRET = JWT_SECRET;

    static public function generateToken(int $userId, string $userName): array
    {
        $iat = time();
        $exp = $iat + (60 * 60); // 1 hour expiration
        $payload = [
            'iss' => 'iliadtest.it', // Issuer
            'aud' => 'iliadtest.it',    // Audience
            'iat' => $iat,            // Issued At: current timestamp
            'exp' => $exp, // Expiration Time: 1 hour from now
            'data' => [                  // Custom data
                'userId' => $userId,
                'username' => $userName
            ]
        ];
        $token = JWT::encode($payload, self::JWT_SECRET, 'HS256');

        // Create DateTime objects from timestamps
        try {
            $iatDateTime = new DateTime("@$iat")->format('Y-m-d H:i:s');
            $expDateTime = new DateTime("@$exp")->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            error_log("Error creating DateTime from timestamp: " . $e->getMessage());
            return [
                'error' => 'Failed to create DateTime objects from timestamps.',
            ];
        }

        return [
            'token' => $token,
            'iat' => $iatDateTime,
            'exp' => $expDateTime,
        ];
    }

    static public function decodeToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::JWT_SECRET, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            error_log("Error decoding token: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}