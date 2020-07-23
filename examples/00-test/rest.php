<?php

require __DIR__ . '/../../vendor/autoload.php';

use go1\rest\RestService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app = new RestService();

$app
    ->server()
    ->get('/hello/{name}', function (string $name, ServerRequestInterface $request, ResponseInterface $response) {
        $response->getBody()->write("Hello, $name!");

        return $response;
    });

$app->server()->run();
