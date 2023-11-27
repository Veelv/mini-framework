<?php

namespace Config\Redis;

use Config\Redis\RedisDatabse;

class RedisModel
{
    private $redis;
    private $defaultExpiration;

    public function __construct()
    {
        $this->redis = new RedisDatabse();
        $this->defaultExpiration = 2592000;
    }

    public function set($key, $value, $expiration = null)
    {
        if ($expiration === null) {
            $expiration = $this->defaultExpiration;
        }

        $this->redis->set($key, $value, $expiration);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function del($key)
    {
        $this->redis->del($key);
    }

    public function incr($key)
    {
        return $this->redis->incr($key);
    }

    public function decr($key)
    {
        return $this->redis->decr($key);
    }

    public function hset($key, $field, $value)
    {
        $this->redis->hset($key, $field, $value);
    }

    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    public function sismember($key, $value)
    {
        return $this->redis->sismember($key, $value);
    }
}
