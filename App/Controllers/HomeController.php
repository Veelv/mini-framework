<?php

namespace App\Controllers;

use Config\Controller;
use Config\Language;
use Config\Router\Response;

class HomeController extends Controller
{
    private $response;
    private $language;
    public function __construct()
    {
        $this->enableCors();
        $this->response = new Response();
        $this->language = new Language();
    }
    public function index()
    {
        $data = [
            'message' => 'Dados obtidos com sucesso!',
            'data' => [
                'id' => 1,
                'nome' => 'Usuário Teste'
            ]
        ];
        $this->response->success([
            "data" => $data,
            "message" => $this->language->translation("Welcome_project_vest")
        ]);
    }
}