<?php

namespace Config\Router;
use Config\EnvLoader;

class Response
{
    private $statusCode;
    private $contentType;

    public function __construct()
    {
        $this->statusCode = 200;
        $this->contentType = 'application/json; charset=utf-8';
    }

    public function send($content)
    {
        http_response_code($this->statusCode);
        header('Content-Type: ' . $this->contentType);
        echo $content;
    }

    private function encodeData($data, $decode = false)
    {
        if ($decode) {
            $decodedData = json_decode($data, true);

            if ($decodedData !== null) {
                $json = json_encode($decodedData, JSON_UNESCAPED_UNICODE);
            } else {
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
        } else {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        return $json;
    }

    private function validateData($data)
    {
        if (is_array($data) || is_object($data)) {
            return $data;
        } else {
            return ['error' => $data];
        }
    }

    public function continue($data, $decode = false)
    {

        $this->statusCode = 100;
        $json = $this->encodeData($data, $decode);
        $this->send($json);
    }

    public function success($data, $decode = false)
    {
        
        $this->statusCode = 200;
        $json = $this->encodeData($data, $decode);
        $this->send($json);
    }

    public function created($data, $decode = false)
    {

        $this->statusCode = 201;
        $json = $this->encodeData($data, $decode);
        $this->send($json);
    }

    public function accepted($data, $decode = false)
    {

        $this->statusCode = 202;
        $json = $this->encodeData($data, $decode);
        $this->send($json);
    }

    public function noContent()
    {

        $this->statusCode = 204;
        $this->send('');
    }

    public function badRequest($data, $decode = false)
    {

        $this->statusCode = 400;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function unauthorized($data, $decode = false)
    {
        $this->statusCode = 401;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);

        http_response_code($this->statusCode);
        header('Content-Type: ' . $this->contentType);
        echo $json;
    }

    public function forbidden($data, $decode = false)
    {

        $this->statusCode = 403;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function notFound($data, $decode = false)
    {

        $this->statusCode = 404;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function conflict($data, $decode = false)
    {

        $this->statusCode = 409;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function locked($data, $decode = false)
    {

        $this->statusCode = 423;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function tooManyRequests($data, $decode = false)
    {

        $this->statusCode = 429;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function internalServerError($data, $decode = false)
    {

        $this->statusCode = 500;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function notImplemented($data, $decode = false)
    {

        $this->statusCode = 501;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function badGateway($data, $decode = false)
    {

        $this->statusCode = 502;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }

    public function serviceUnavailable($data, $decode = false)
    {

        $this->statusCode = 503;
        $validatedData = $this->validateData($data);
        $json = $this->encodeData($validatedData, $decode);
        $this->send($json);
    }
}
