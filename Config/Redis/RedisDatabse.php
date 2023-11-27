<?php

namespace Config\Redis;

use Config\EnvLoader;
use Predis\Client;

class RedisDatabse
{
    private static $client;
    private string $hostname;
    private string $database;
    private string $password;
    private string $port;
    private $env;

    public function __construct()
    {
        $this->env = new EnvLoader(__DIR__ . '/../../.env');
        $this->hostname = $this->env->get('REDISHOST', 'localhost');
        $this->port = $this->env->get('REDISPORT', 6379);
        $this->password = $this->env->get('REDISPASSWORD', null);
        $this->database = $this->env->get('REDISDATABASE', 0);
    }

    /**
     * Conecta ao servidor Redis usando as configurações fornecidas.
     * Se nenhuma configuração for fornecida, serão usados os valores padrão.
     *
     * @param string $host
     * @param int $port
     * @param string|null $password
     * @param int $database
     * @throws \Exception Se ocorrer um erro ao conectar ao Redis.
     */
    public function connect()
    {
        try {
            self::$client = new Client([
                'scheme' => 'tcp',
                'host' => $this->hostname,
                'port' => $this->port,
                'password' => $this->password,
                'database' => $this->database,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao conectar ao Redis: ' . $e->getMessage());
        }
    }

    /**
     * Retorna a instância do cliente Redis.
     * Se o cliente não estiver inicializado, uma conexão será estabelecida usando as configurações padrão.
     *
     * @return Client
     * @throws \Exception Se ocorrer um erro ao conectar ao Redis.
     */
    public function getClient()
    {
        if (!self::$client) {
            self::connect();
        }
        return self::$client;
    }

    /**
     * Define um valor para a chave especificada no Redis.
     * Se o tempo de expiração for fornecido, a chave será configurada para expirar após o período especificado.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $expiration
     * @throws \Exception Se ocorrer um erro ao definir o valor no Redis.
     */
    public function set($key, $value, $expiration = null)
    {
        try {
            $client = self::getClient();
            $client->set($key, $value);

            if ($expiration !== null) {
                $client->expire($key, $expiration);
            }
        } catch (\Exception $e) {
            throw new \Exception('Erro ao definir valor no Redis: ' . $e->getMessage());
        }
    }

    /**
     * Retorna o valor armazenado na chave especificada no Redis.
     *
     * @param string $key
     * @return mixed|null
     * @throws \Exception Se ocorrer um erro ao obter o valor do Redis.
     */
    public function get($key)
    {
        try {
            $client = self::getClient();
            return $client->get($key);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao obter valor do Redis: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Remove a chave especificada do Redis.
     *
     * @param string $key
     * @throws \Exception Se ocorrer um erro ao excluir a chave do Redis.
     */
    public function del($key)
    {
        try {
            $client = self::getClient();
            $client->del($key);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao excluir chave do Redis: ' . $e->getMessage());
        }
    }

    /**
     * Incrementa o valor da chave numérica especificada no Redis.
     *
     * @param string $key
     * @return int|null
     * @throws \Exception Se ocorrer um erro ao incrementar o valor no Redis.
     */
    public function incr($key)
    {
        try {
            $client = self::getClient();
            return $client->incr($key);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao incrementar valor no Redis: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Decrementa o valor da chave numérica especificada no Redis.
     *
     * @param string $key
     * @return int|null
     * @throws \Exception Se ocorrer um erro ao decrementar o valor no Redis.
     */
    public function decr($key)
    {
        try {
            $client = self::getClient();
            return $client->decr($key);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao decrementar valor no Redis: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Define um valor para um campo em um hash no Redis.
     *
     * @param string $key
     * @param string $field
     * @param mixed $value
     * @throws \Exception Se ocorrer um erro ao definir o valor do campo no Redis.
     */
    public function hset($key, $field, $value)
    {
        try {
            $client = self::getClient();
            $client->hset($key, $field, $value);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao definir valor do campo no Redis: ' . $e->getMessage());
        }
    }

    public function exists($key)
    {
        try {
            $client = self::getClient();
            return $client->exists($key);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao verificar a existência da chave no Redis: ' . $e->getMessage());
        }
    }

    public function sismember($key, $value)
    {
        try {
            $client = self::getClient();
            return $client->sismember($key, $value);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao verificar a existência da chave no Redis: ' . $e->getMessage());
        }
    }
}
