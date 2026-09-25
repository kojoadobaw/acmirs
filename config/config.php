<?php
declare(strict_types=1);

const APP_NAME = 'ACMIRS CMS';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim(trim($value), "\"'");
        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
        }
    }
}

$local = file_exists(__DIR__ . '/local.php') ? require __DIR__ . '/local.php' : [];
$configValue = static function (string $key, ?string $fallback = null) use ($local): string {
    $environment = getenv($key);
    if ($environment !== false && $environment !== '') {
        return (string) $environment;
    }
    if (isset($local[$key]) && $local[$key] !== '') {
        return (string) $local[$key];
    }
    if ($fallback !== null) {
        return $fallback;
    }
    throw new RuntimeException(
        "Missing required configuration value \"{$key}\". Set it in .env, config/local.php, or as a server environment variable."
    );
};

date_default_timezone_set($configValue('APP_TIMEZONE', 'Africa/Accra'));

define('DB_HOST', $configValue('DB_HOST'));
define('DB_PORT', $configValue('DB_PORT', '3306'));
define('DB_NAME', $configValue('DB_NAME'));
define('DB_USER', $configValue('DB_USER'));
define('DB_PASS', $configValue('DB_PASS', ''));

define('PROJECT_ROOT', dirname(__DIR__));
