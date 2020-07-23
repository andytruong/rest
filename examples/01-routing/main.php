<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HelloController
{
    public function get(string $name, ServerRequestInterface $request, ResponseInterface $response)
    {
        $response->getBody()->write("Hello, $name!");

        return $response;
    }
}

call_user_func(
    function () {
        define('REST_ROOT', __DIR__);
        define('REST_MANIFEST', __DIR__ . '/manifest.php');

        require __DIR__ . '/../../vendor/autoload.php';
        require __DIR__ . '/../../public/index.php';
    }
);
