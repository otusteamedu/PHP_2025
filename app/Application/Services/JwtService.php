<?php

namespace App\Application\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private $key;
    private $url;
    private $ttl;

    public function __construct()
    {
        $this->key = getenv('JWT_KEY');
        $this->url = getenv('APP_URL');
        $this->ttl = getenv('JWT_TTL');
    }

    public function getJwt(int $userId): string
    {
        $payload = [
            'iss' => $this->url,
            'iat' => time(),
            'userId' => $userId,
        ];

        $jwt = JWT::encode($payload, $this->key, 'HS256');
        return $jwt;
    }

    public function verifyJwt($header): int
    {
        if (!preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return 0;
        }

        $token = $matches[1];
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
        $data = (array) $decoded;

        if (empty($data['iss']) || $data['iss'] != $this->url) {
            return 0;
        }
        if (empty($data['userId'])) {
            return 0;
        }
        $time = (int) $data['iat'];
        if (empty($time) || time() - $time > $this->ttl) {
            return 0;
        }

        return (int) $data['userId'];
    }
}