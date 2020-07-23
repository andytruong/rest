<?php

namespace go1\rest\struct\service;

use go1\rest\struct\Manifest;

class PHPUnitConfigBuilder
{
    private $builder;
    private $config = [
        '@color'         => 'true',
        '@stopOnFailure' => 'true',
    ];

    public function __construct(Manifest $builder)
    {
        $this->builder = $builder;
        $this->config = [];
    }

    public function withBootstrapFile(string $path)
    {
        $this->config['@bootstrap'] = $path;

        return $this;
    }

    public function withTestSuite(string $name, array $directories)
    {
        $this->config['testsuites'][] = [
            '@name'     => $name,
            'directory' => $directories,
        ];

        foreach ($directories as $directory) {
            $this->withoutWhitelistDirectory($directory);
        }

        return $this;
    }

    public function withWhitelistDirectory(string $dir)
    {
        $this->config['filter']['whitelist']['directory'][] = $dir;

        return $this;
    }

    public function withoutWhitelistDirectory(string $dir)
    {
        $this->config['filter']['whitelist']['exclude']['directory'][] = $dir;

        return $this;
    }

    public function endPHPUnit(): Manifest
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
