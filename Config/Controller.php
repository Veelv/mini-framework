<?php

namespace Config;

class Controller
{
  public function __construct()
  {
    $this->enableCors();
  }

  protected function enableCors()
  {
    // Verifica se é uma requisição OPTIONS (pré-voo)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      $this->sendOptionsHeaders();
    } else {
      $this->sendDefaultHeaders();
    }
  }

  protected function sendOptionsHeaders()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400"); // Cache preflight requests for 24 hours
    exit();
  }

  protected function sendDefaultHeaders()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
  }
}
