<?php

namespace App\Util;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class JWTUtil
{
    private const ALG = 'HS256';
    public static function encode(string $secret, array $payload): string
    {
        return JWT::encode($payload, $secret, self::ALG);
    }

    public static function decode(string $token, string $secret): stdClass
    {
        return JWT::decode($token, new Key($secret, self::ALG));
    }
}