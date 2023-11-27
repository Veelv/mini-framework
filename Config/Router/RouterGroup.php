<?php

namespace Config\Router;

class RouterGroup
{
    private $prefix;
    private $router;
    private $middlewares;

    public function __construct($prefix, $router)
    {
        $this->prefix = $prefix;
        $this->router = $router;
        $this->middlewares = [];
    }

    public function method($method, $path, $action, $middlewares = [])
    {
        $prefixedPath = $this->prefix . $path;
        $middlewares = array_merge($this->middlewares, $middlewares);
        $this->router->method($method, $prefixedPath, $action, $middlewares);
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

    public function middleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function group($prefix, $callback)
    {
        $routeGroup = new self($this->prefix . $prefix, $this->router);
        $routeGroup->middlewares = array_merge($this->middlewares, $routeGroup->middlewares);
        $callback($routeGroup);
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}