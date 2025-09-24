<?php

namespace App\Utility;

use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class TokenGenerator
{
    private const string JWT_SECRET = JWT_SECRET;

    static public function generateToken(int $userId, string $email, string $userName): array
    {
        $iat = time();
        $exp = $iat + (60 * 60); // 1 hour expiration
        $payload = [
            'iss'  => 'ibarbierilissone.it', // Issuer
            'aud'  => 'ibarbierilissone.it',    // Audience
            'iat'  => $iat,            // Issued At: current timestamp
            'exp'  => $exp, // Expiration Time: 1 hour from now
            'data' => [                  // Custom data
                'userId' => $userId,
                'username' => $userName,
                'role' => $email
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

    static public function generateRefreshToken(int $userId, string $email, string $userName): array
    {
        $iat = time();
        $exp = $iat + (60 * 60 * 24 * 30); // 30 days expiration for refresh token
        $payload = [
            'iss'  => 'ibarbierilissone.it', // Issuer
            'aud'  => 'ibarbierilissone.it',    // Audience
            'iat'  => $iat,            // Issued At: current timestamp
            'exp'  => $exp, // Expiration Time: 1 hour from now
            'data' => [                  // Custom data
                'userId' => $userId,
                'username' => $userName,
                'role' => $email
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
            'refreshToken' => $token,
            'iat' => $iatDateTime,
            'exp' => $expDateTime,
        ];
    }

    static public function decodeToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::JWT_SECRET, 'HS256'));
            return (array)$decoded;
        } catch (Exception $e) {
            error_log("Error decoding token: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}