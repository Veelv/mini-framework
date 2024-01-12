<?php

namespace Config\Router;

class MiddlewareHandler
{
    public function applyMiddlewares($middlewares, $action, $params, $validations)
    {
        $validatorHandler = new ValidatorHandler();
        $response = new Response();

        $next = function () use ($action, $params) {
            $this->callAction($action, $params);
        };

        $middlewares = array_reverse($middlewares);

        foreach ($middlewares as $middleware) {
            $nextMiddleware = $next;
            $next = function () use ($middleware, $nextMiddleware, $params) {
                $middlewareObj = new $middleware();
                $middlewareObj->handle($params, $nextMiddleware);
            };
        }

        $errors = $validatorHandler->applyValidations($validations);

        if (count($errors) > 0 && count($errors['fields'])) {
            $response->badRequest($errors);
            return;
        }

        $next();
    }

    public function callAction($action, $params)
    {
        list($controller, $method) = explode('@', $action);

        $controllerObj = new $controller();
        call_user_func_array([$controllerObj, $method], [$params]);
    }
}
