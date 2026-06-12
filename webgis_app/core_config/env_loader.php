<?php
/**
 * Simple .env loader.
 * Reads the .env file from the repository root and sets environment variables
 * via putenv() so getenv() calls throughout the app work correctly.
 * Only loads once per request.
 */
function loadEnv(): void {
    static $loaded = false;
    if ($loaded) return;

    // Walk up from webgis_app/config to the repository root
    $envFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';

    if (!file_exists($envFile)) {
        $loaded = true;
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($lines)) {
        if (substr($lines[0], 0, 3) === "\xEF\xBB\xBF") {
            $lines[0] = substr($lines[0], 3);
        }
    }
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments
        if ($line === '' || $line[0] === '#') continue;

        if (strpos($line, '=') === false) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Remove surrounding quotes if present
        if (strlen($value) >= 2 &&
            (($value[0] === '"' && $value[-1] === '"') ||
             ($value[0] === "'" && $value[-1] === "'"))) {
            $value = substr($value, 1, -1);
        }

        // Only set if not already defined in the environment (server env wins)
        if (getenv($key) === false) {
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }

    $loaded = true;
}

// Auto-load on include
loadEnv();
