<?php

require __DIR__ . '/../../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app = \DI\Bridge\Slim\Bridge::create();
$app->get('/hello/{name}', function (string $name, ServerRequestInterface $request, ResponseInterface $response) {
    $response->getBody()->write("Hello {$name}!");

    return $response;
});

$app->run();
