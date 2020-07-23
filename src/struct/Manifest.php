<?php

namespace go1\rest\struct;

use go1\rest\struct\service\ComposerBuilder;
use go1\rest\struct\service\DockerComposeBuilder;
use go1\rest\struct\service\open_api\OpenApiBuilder;
use go1\rest\struct\service\PHPUnitConfigBuilder;
use go1\rest\struct\service\RestBuilder;
use go1\rest\struct\service\StreamBuilder;

class Manifest
{
    protected string             $serviceRoot;
    private RestBuilder          $rest;
    private StreamBuilder        $stream;
    private DockerComposeBuilder $dockerCompose;
    private ComposerBuilder      $composer;
    private PHPUnitConfigBuilder $phpunit;
    private OpenApiBuilder       $openAPI;

    private function __construct(string $serviceRoot)
    {
        $this->serviceRoot = $serviceRoot;
        $this->rest = new RestBuilder($this);
        $this->stream = new StreamBuilder($this);
        $this->dockerCompose = new DockerComposeBuilder($this);
        $this->composer = new ComposerBuilder($this);
        $this->phpunit = new PHPUnitConfigBuilder($this);
        $this->openAPI = new OpenApiBuilder($this);
    }

    public static function create(string $serviceRoot = ''): Manifest
    {
        return new Manifest(rtrim($serviceRoot, '/'));
    }

    public function composer(): ComposerBuilder
    {
        return $this->composer;
    }

    public function rest(string $serviceName = '', string $version = '')
    {
        if ($serviceName) {
            $this->rest->withServiceName($serviceName);
        }

        if ($version) {
            $this->rest->withVersion($version);
        }

        return $this->rest;
    }

    public function stream()
    {
        return $this->stream;
    }

    public function dockerCompose()
    {
        return $this->dockerCompose;
    }

    public function phpunit()
    {
        return $this->phpunit;
    }

    public function openAPI(string $version = '3.0.0'): OpenApiBuilder
    {
        return $this->swagger()->withOpenAPI($version);
    }

    /**
     * @param string $version
     * @return OpenApiBuilder
     * @deprecated
     */
    public function swagger(string $version = '')
    {
        if ($version) {
            $this->openAPI->withOpenAPI($version);
        }

        return $this->openAPI;
    }

    public function build()
    {
        return [
            'rest'           => $this->rest->build(),
            'docker-compose' => $this->dockerCompose->build(),
            'composer'       => $this->composer->build(),
            'phpunit'        => $this->phpunit->build(),
            'swagger'        => $this->openAPI->build(),
        ];
    }
}
