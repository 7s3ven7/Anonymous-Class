<?php

namespace Src;

class Server
{

    private string $method;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function response(int $code, array|string|object $body): void
    {

        http_response_code($code);

        Header("Content-type: application/json");

        if (is_array($body)) {
            $body = json_encode($body);
        }
        if (is_object($body)) {
            print_r($body);
            return;
        }

        echo $body;

    }

}