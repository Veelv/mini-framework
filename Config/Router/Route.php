<?php

namespace Config\Router;

use Config\Router\RouteHandler;
use Config\Router\RouterGroup;

class Route
{
    private $routes;
    private $namespace;
    private $routeGroups;
    private $middlewares;

    public function __construct()
    {
        $this->routes = [];
        $this->namespace = 'App\Controllers\\';
        $this->routeGroups = [];
        $this->middlewares = [];
    }

    public function method($method, $path, $action, $middlewares = [])
    {
        $pattern = '#^' . str_replace('/', '\/', $path) . '$#';
        $pattern = preg_replace('/{(\w+)}/', '(?<$1>[^/]+)', $pattern);

        if (is_array($action)) {
            $controller = $action[0];
            $method = $action[1];
            $action = $controller . '@' . $method;
        } else {
            $action = $this->namespace . $action;
        }

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'action' => $action,
            'group' => end($this->routeGroups),
            'middlewares' => array_merge($this->middlewares, (array) $middlewares),
        ];
    }

    public function get($path, $action, $middlewares = [])
    {
        $this->method('GET', $path, $action, $middlewares);
    }

    public function post($path, $action, $middlewares = [])
    {
        $this->method('POST', $path, $action, $middlewares);
    }

    public function put($path, $action, $middlewares = [])
    {
        $this->method('PUT', $path, $action, $middlewares);
    }

    public function patch($path, $action, $middlewares = [])
    {
        $this->method('PATCH', $path, $action, $middlewares);
    }

    public function delete($path, $action, $middlewares = [])
    {
        $this->method('DELETE', $path, $action, $middlewares);
    }

    public function group($prefix, $callback)
    {
        $routeGroup = new RouterGroup($prefix, $this);
        $this->routeGroups[] = $routeGroup;
        $callback($routeGroup);
        array_pop($this->routeGroups);
    }

    public function middleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function handleRequest()
    {
        $routeHandler = new RouteHandler($this->routes);
        $routeHandler->handleRequest();
    }
}