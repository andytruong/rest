<?php

namespace go1\rest\struct\request;

use Slim\Factory\Psr17\Psr17Factory;
use Slim\Interfaces\ServerRequestCreatorInterface;

class ServerRequestCreatorFactory extends Psr17Factory
{
    public static function getServerRequestCreator(): ServerRequestCreatorInterface
    {
        return new ServerRequestCreator;
    }
}
