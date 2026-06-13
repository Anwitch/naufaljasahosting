<?php
/**
 * Application path helpers.
 *
 * Set APP_BASE_PATH to the subdirectory that serves webgis_app.
 * In this repository's Docker image the final app runs at /webgis_app.
 */
require_once __DIR__ . '/env_loader.php';
function app_base_path(): string {
    $basePath = getenv('APP_BASE_PATH');
    if ($basePath === false) {
        $basePath = '/webgis_app';
    }

    $basePath = trim($basePath);
    if ($basePath === '' || $basePath === '/') {
        return '';
    }

    return '/' . trim($basePath, '/');
}

function app_url(string $path = ''): string {
    if ($path === '') {
        return app_base_path();
    }
    $path = '/' . ltrim($path, '/');
    return app_base_path() . $path;
}

function root_url(string $path = ''): string {
    if ($path === '') {
        return '/';
    }
    return '/' . ltrim($path, '/');
}
