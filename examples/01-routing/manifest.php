<?php

namespace go1\rest\examples;

use go1\rest\struct\Manifest;
use HelloController;

$manifest = Manifest::create();

// @formatter:off
$manifest
    ->rest('example', 'v4.0.0')
        ->endRest()
        ->openAPI()
            ->withPath('/hello/{name}', 'GET', [HelloController::class, 'get'])
            ->end()
        ->end()
;

return $manifest;
