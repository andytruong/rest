<?php

use DDTrace\Bootstrap;
use DDTrace\Integrations\IntegrationsLoader;
use go1\rest\RestService;
use go1\rest\struct\Manifest;

return call_user_func(
    function (): ?RestService {
        if (!defined('REST_ROOT')) {
            define('REST_ROOT', dirname(__DIR__));
        }

        if (!class_exists(RestService::class)) {
            require_once REST_ROOT . '/vendor/autoload.php';
        }

        /** @var Manifest $manifest */
        $path = getenv('REST_MANIFEST');
        $path = $path ?: (defined('REST_MANIFEST') ? REST_MANIFEST : (__DIR__ . '/../manifest.php'));
        $path = realpath($path);
        $manifest = require $path;
        $service = $manifest->rest()->get();
        $service->getContainer()->set('REST_MANIFEST', $path);

        if (!empty(getenv('DD_AGENT_HOST')) && extension_loaded('ddtrace')) {
            Bootstrap::tracerOnce();
            IntegrationsLoader::load();
        }

        if ('cli' === php_sapi_name()) {
            return $service;
        }

        $service->server()->run();

        return null;
    }
);
