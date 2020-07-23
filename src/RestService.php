<?php

declare(strict_types=1);

namespace go1\rest;

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use go1\rest\struct\request\Creator;
use Psr\Container\ContainerInterface;
use Slim\App as Server;

class RestService
{
    private ContainerInterface $container;
    private Server             $server;

    public function __construct(array $config = [])
    {
        $builder = new ContainerBuilder();

        if ($config) {
            $builder->addDefinitions($config);
        }

        $this->container = $builder->build();
        $this->server = Bridge::create($this->container);

        if ($config) {
            if (!empty($config['boot'])) {
                call_user_func($config['boot'], $this);
                unset($config['boot']);
            }
        }

        // Psr17FactoryProvider::setFactories([ServerRequestCreatorFactory::class]);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function server(): Server
    {
        return $this->server;
    }
}
