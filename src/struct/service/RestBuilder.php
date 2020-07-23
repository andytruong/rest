<?php

namespace go1\rest\struct\service;

use go1\rest\controller\ApiController;
use go1\rest\controller\InstallController;
use go1\rest\controller\MessageListenerController;
use go1\rest\RestService;
use go1\rest\Stream;
use go1\rest\struct\Manifest;
use RuntimeException;

class RestBuilder
{
    private $builder;
    private $config = [];
    private $boot;

    public function __construct(Manifest $builder)
    {
        $this->builder = $builder;
        $this->config = ['boot' => $this->onBoot()];
    }

    private function onBoot()
    {
        return function (RestService $rest) {
            $api = $this->builder->openAPI();

            if (!is_null($this->boot)) {
                call_user_func($this->boot, $rest, $this->builder);
            }

            $binding = $this->builder->stream()->build();
            if ($binding) {
                $rest->get('/consume', [MessageListenerController::class, 'get']);
                $rest->post('/consume', [MessageListenerController::class, 'post']);
                $stream = $rest->getContainer()->get(Stream::class);
                foreach ($binding as $_) {
                    $stream->on($_[0], $_[1], $_[2]);
                }
            }

            $api->withPath('/install', 'POST', [InstallController::class, 'post']);

            $paths = $api->paths();
            if (!$paths) {
                return;
            }

            # Provide GET /api
            $rest->server()->get('/api', [ApiController::class, 'get']);

            # Register openAPI routes with slim.
            foreach ($paths as $pattern => &$methods) {
                foreach ($methods as $method => &$_) {
                    $this->addRoute($rest, $method, $pattern, $_['#controller'], $_['#middleware'] ?? [], $_['parameters']);
                }
            }

            foreach ($api->middlewares() as $middleware) {
                $rest->add($this->parseMiddleware($rest, $middleware));
            }
        };
    }

    private function addRoute(RestService $rest, $method, $pattern, $controller, array $middleware, array $parameters)
    {
        $map = $rest->server()->map([$method], $pattern, $controller);

        foreach ($middleware as $m) {
            $map->add($this->parseMiddleware($rest, $m));
        }

        foreach ($parameters as $param) {
            if (isset($param['schema']['default'])) {
                $map->setArgument($param['name'], $param['schema']['default']);
            }
        }
    }

    private function parseMiddleware(RestService $rest, $m)
    {
        if (is_callable($m)) {
            return $m;
        }

        if (is_string($m)) {
            if (!$rest->getContainer()->has($m)) {
                throw new RuntimeException(sprintf('Invalid middleware: service %s not found.', $m));
            }

            return $rest->getContainer()->get($m);
        }

        throw new RuntimeException('Middleware must be a callable or name of service');
    }

    public function has(string $k): bool
    {
        return isset($this->config[$k]);
    }

    public function set($k, $v)
    {
        $this->config[$k] = $v;

        return $this;
    }

    public function withServiceName(string $name)
    {
        putenv('REST_SERVICE_NAME=' . $name);

        $this
            ->builder
            ->dockerCompose()
            ->withEnv('SERVICE_80_NAME', $name);

        $this->config['name'] = $name;

        return $this;
    }

    public function withVersion(string $version)
    {
        putenv('REST_SERVICE_VERSION=' . $version);

        return $this;
    }

    public function withDatabaseSchema(string $dbConnectionClass, string $dbSchemaClass)
    {
        return $this->set('restDbSchema', [$dbConnectionClass, $dbSchemaClass]);
    }

    /**
     * @param string       $name
     * @param string|array $option
     * @return $this
     */
    public function withEsOption(string $name, $option)
    {
        $this->config['esOptions'][$name] = is_array($option) ? $option : ['endpoint' => $option];

        return $this;
    }

    public function withConfigFile(string $path)
    {
        if (file_exists($path)) {
            $_ = require $path;
            foreach ($_ as $k => $v) {
                $this->set($k, $v);
            }
        }

        return $this;
    }

    public function withBootCallback(callable $boot)
    {
        $this->boot = $boot;

        return $this;
    }

    public function endRest(): Manifest
    {
        return $this->builder;
    }

    public function end(): Manifest
    {
        return $this->builder;
    }

    public function build(): array
    {
        return $this->config;
    }

    public function get(): RestService
    {
        return new RestService($this->build());
    }
}
