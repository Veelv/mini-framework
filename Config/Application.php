<?php

namespace Config;
use Config\Language;
use Config\Mongo\MongoDatabase;
use Config\Mysql\MysqlDatabase;
use Config\Redis\RedisDatabse;

define('API', '/../global/api.php');

class Application
{
    private $debug = false;
    private $mysql;
    private $mongo;
    private $language;
    private $redis;
    
    public function __construct() {
        $this->septup();
        $this->mysql = new MysqlDatabase();
        $this->mongo =  new MongoDatabase();
        $this->language = new Language();
        $this->redis = new RedisDatabse();
    }

    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    public function load(){
        require_once __DIR__ . API;
        $router->handleRequest();      
    }

    public function start(){
        try {
        //    $this->mysql->connect();
        //    $this->mongo->connect();
        //    $this->redis->connect();
           self::load();
           $this->language->getLanguage();         
        } catch (\Exception $err) {
            if ($this->debug) {
                echo $err->getMessage();             
            }
            
        }
    }

    public function septup()
    {
        $this->setDebug(true);
        
    }
}