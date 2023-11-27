<?php

namespace Config\Router;

use Config\Router\MiddlewareHandler;
use Config\Router\RouteMatcher;

class RouteHandler
{
    private $routes;
    private $routeMatcher;
    private $middlewareHandler;

    public function __construct($routes)
    {
        $this->routes = $routes;
        $this->routeMatcher = new RouteMatcher();
        $this->middlewareHandler = new MiddlewareHandler();
    }

    public function handleRequest()
    {
        try {
            $method = $this->getRequestMethod();
            $path = $this->getRequestPath();

            $matchedRoute = $this->findMatchingRoute($method, $path);

            if ($matchedRoute !== null) {
                $action = $matchedRoute['action'];
                $params = $this->routeMatcher->extractParams($matchedRoute, $path);
                $middlewares = $matchedRoute['middlewares'];

                $this->middlewareHandler->applyMiddlewares($middlewares, $action, $params);
                return;
            }

            $this->sendNotFoundResponse();
        } catch (\Exception $e) {
            $this->sendErrorResponse(500, 'Internal Server Error');
        }
    }

    private function findMatchingRoute($method, $path)
    {
        foreach ($this->routes as $route) {
            if ($this->routeMatcher->matchesRoute($route, $method, $path)) {
                return $route;
            }
        }

        return null;
    }

    private function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function getRequestPath()
    {
        $path = $_SERVER['REQUEST_URI'];
        $path = parse_url($path, PHP_URL_PATH);
        $path = rtrim($path, '/');
        return $path;
    }

    private function sendNotFoundResponse()
    {
        http_response_code(404);
        echo 'Not Found';
    }

    private function sendErrorResponse($statusCode, $message)
    {
        http_response_code($statusCode);
        echo $message;
    }
}