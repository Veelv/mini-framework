<?php

namespace Config\Router;

class RouteMatcher
{
    public function matchesRoute($route, $method, $path)
    {
        return $route['method'] === $method && preg_match($route['pattern'], $path, $matches);
    }

    public function extractParams($route, $path)
    {
        $params = [];
        $pattern = $route['pattern'];

        preg_match($pattern, $path, $matches);

        if ($route['group']) {
            $params['group'] = $route['group'];
        }

        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}