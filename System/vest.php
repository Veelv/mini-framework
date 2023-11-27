<?php

$file_path = __DIR__ . '/../vendor/autoload.php';

if (file_exists($file_path)) {
    require_once $file_path;
} else {
    echo "Vendor não foi encontrado.";
}
require_once __DIR__ . '/../App/Helpers/Common.php';

use Config\Application;
use Config\EnvLoader;

define('APP_PATH', __DIR__ . '/../');
$env = new EnvLoader(APP_PATH . '.env');
$ambient = $env->get('APP_ENV');

if ($ambient === 'development') :
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
else :
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
endif;

$start = new Application();
$start->start();
