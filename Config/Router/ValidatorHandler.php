<?php

namespace Config\Router;

use Config\Router\Request;
use Config\Router\Response;
use Config\Validator;
use Exception;

class ValidatorHandler {
    function applyValidations($validations) {
        $request = new Request();

        $validator = new Validator();

        $body = $request->getParsedBody();

        $fields = array_keys($validations);
        $methods = [];

        foreach ($validations as $key => $value) {
            $methodsObj = [];

            if (is_array(array_values($value)[0])) {
                foreach ($value as $value) {
                    array_push($methodsObj, $value['method']);
                }
            }

            if (count($methodsObj) > 0) {
                array_push($methods, ...$methodsObj);
                continue;
            }

            array_push($methods, $value['method']);
        }

        foreach ($methods as $method) {
            if (!method_exists($validator, $method)) {
                throw new Exception('O metodo de validação não existe');
            }
        }

        foreach ($fields as $field) {
            if (array_key_exists($field, $body)) {
                $validatorObj = [];

                if (is_array(array_values($validations[$field])[0])) {
                    foreach ($validations[$field] as $value) {
                        array_push($validatorObj, $value);
                    }
                }
                
                
                if (count($validatorObj) > 0) {
                    foreach ($validatorObj as $validatorItem) {
                        $params = [$body[$field], ...array_values($validatorItem['params'])];
                        call_user_func_array([$validator, $validatorItem['method']], $params);
                    }
                    continue;
                }

                $params = [$body[$field], ...array_values($validations[$field]['params'])];

                $method = $validations[$field]['method'];
                call_user_func_array([$validator, $method], $params);
            }
        }

        $errors = $validator->errors();

        return $errors;
    }
}