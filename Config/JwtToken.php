<?php

namespace Config;

use Config\Redis\RedisModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtToken
{
    private string $secretKey;
    private string $algorithm;
    private EnvLoader $env;
    private RedisModel $redis;

    public function __construct()
    {
        $this->env = new EnvLoader(__DIR__ . '/../.env');
        $this->redis = new RedisModel();

        $this->secretKey = $this->env->get('APP_SECRETE_TOKEN');
        $this->algorithm = 'HS256';
    }

    public function generateToken($payload)
    {
        try {
            $token = JWT::encode($payload, $this->secretKey, $this->algorithm);
            $this->redis->set('TOKEN_KEY', $token, 2592000); // Armazena o token no cache com 30 dias de expiração
            return $token;
        } catch (\Exception $e) {
            echo "<br /> Erro ao gerar o token: " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function validateToken($token)
    {
        try {
            $decoded = $this->decodeToken($token);

            if ($decoded === false) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            echo "<br /> Erro ao verificar o token: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function decodeToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return $decoded;
        } catch (\Exception $e) {
            echo "<br/ > Erro ao decodificar o token: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function invalidateToken($token)
    {
        try {
            $decoded = $this->decodeToken($token);

            if ($decoded === false) {
                return false;
            }

            $payloadArray = (array) $decoded;
            $invalidToken = JWT::encode($payloadArray, $this->secretKey, $this->algorithm);
            // $this->redis->del('TOKEN_KEY');
            // $this->redis->set('TOKEN_KEY', $invalidToken, 2592000);
            return $invalidToken;
        } catch (\Exception $e) {
            echo "<br /> Erro ao invalidar o token: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function isVerifyToken($token)
    {
        try {
            $tokenKey = 'TOKEN_KEY';
            $exists = $this->redis->exists($tokenKey);

            if ($exists) {
                $storedToken = $this->redis->get($tokenKey);

                if ($storedToken === $token) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            echo "<br /> Erro ao verificar o token: " . $e->getMessage() . "<br>";
            return false;
        }
    }
}
