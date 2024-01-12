<?php

namespace App\Middlewares;

class TesteMiddleware {
    function handle($params, $next) {
        echo 'Olá';
        $next();
    }
}