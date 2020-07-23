<?php

namespace go1\rest\struct\service\open_api;

use go1\rest\struct\Manifest;

class OpenApiBuilder
{
    private $builder;
    private $config = [];

    public function __construct(Manifest $builder)
    {
        $this->builder = $builder;
        $this->config = ['paths' => [], 'middlewares' => []];
    }

    public function withOpenAPI(string $version)
    {
        $this->config['openapi'] = $version;

        return $this;
    }

    public function withServer(string $url, string $description)
    {
        $this->config['server'][] = [
            'url'         => $url,
            'description' => $description,
        ];

        return $this;
    }

    public function withInfo()
    {
        $this->config['info'] = $this->config['info'] ?? [];
        $infoBuilder = new InfoBuilder($this, $this->config['info']);

        return $infoBuilder;
    }

    public function get(string $path, $controller = null): OpenApiBuilder
    {
        return $this->withPath($path, 'GET', $controller)->end();
    }

    public function post(string $path, $controller = null): OpenApiBuilder
    {
        return $this->withPath($path, 'POST', $controller)->end();
    }

    public function put(string $path, $controller = null): OpenApiBuilder
    {
        return $this->withPath($path, 'PUT', $controller)->end();
    }

    public function delete(string $path, $controller = null): OpenApiBuilder
    {
        return $this->withPath($path, 'DELETE', $controller)->end();
    }

    public function withPath(string $path, string $method, $controller = null): PathBuilder
    {
        $this->config['paths'][$path][$method] = [];
        $pathBuilder = new PathBuilder($this, $this->config['paths'][$path][$method]);

        if ($controller) {
            $pathBuilder->withController($controller);
        }

        return $pathBuilder;
    }

    public function withMiddleware($middleware): self
    {
        $this->config['middlewares'][] = $middleware;

        return $this;
    }

    public function paths()
    {
        return $this->config['paths'];
    }

    public function middlewares()
    {
        return $this->config['middlewares'];
    }

    /**
     * withTag($name, $description)
     *
     * withExternalDocs($url, $description)
     * withSchema()
     * withDefinition()
     * withSecurity()
     */

    /**
     * @return Manifest
     * @deprecated
     */
    public function endSwagger(): Manifest
    {
        return $this->builder;
    }

    public function endOpenAPI(): Manifest
    {
        return $this->builder;
    }

    public function end(): Manifest
    {
        return $this->builder;
    }

    public function build()
    {
        return $this->config;
    }
}
