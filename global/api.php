<?php

use Config\Router\Route;

$router = new Route();

// $router->method('GET', '', 'HomeController@index');
$router->post('', 'HomeController@index', [], [
    // 'cnpj' => [
    //     [
    //         'method' => 'isCNPJ',
    //         'params' => [
    //             'message' => 'O campo não é um CNPJ.',
    //         ]
    //     ],
    // ],
    'message' => [
        'method' => 'isEmpty',
        'params' => [
            'message' => 'o campo mensagem não pode ser vazio',
            'field' => 'message',
        ]
    ]
]);