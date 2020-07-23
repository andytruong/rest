<?php

namespace go1\rest\struct\request;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\Psr17\NyholmPsr17Factory;
use Slim\Interfaces\ServerRequestCreatorInterface;

class ServerRequestCreator implements ServerRequestCreatorInterface
{
    public function createServerRequestFromGlobals(): ServerRequestInterface
    {

        return NyholmPsr17Factory::getServerRequestCreator();
    }
}
