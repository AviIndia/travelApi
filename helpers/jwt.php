<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../vendor/autoload.php';

class JwtHelper {

    //private static $secret = "travel_secret_key_2026";
private static $secret = "travel_tourism_application_super_secret_jwt_key_2026_@#123456789";
    public static function generate($user){

        $payload = [

            "id" => $user['id'],
            "name" => $user['name'],
            "email" => $user['email'],
            "role" => $user['role'],
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24 * 7)

        ];

        return JWT::encode(
            $payload,
            self::$secret,
            'HS256'
        );
    }

    public static function verify($token){

        return JWT::decode(
            $token,
            new Key(self::$secret,'HS256')
        );
    }

}