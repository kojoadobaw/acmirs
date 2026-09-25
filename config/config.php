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

$local = file_exists(__DIR__ . '/local.php') ? require __DIR__ . '/local.php' : [];
$configValue = static function (string $key, string $fallback) use ($local): string {
    $environment = getenv($key);
    if ($environment !== false) {
        return (string) $environment;
    }
    return isset($local[$key]) ? (string) $local[$key] : $fallback;
};

date_default_timezone_set($configValue('APP_TIMEZONE', 'Africa/Accra'));

define('DB_HOST', $configValue('DB_HOST', '198.12.254.83'));
define('DB_PORT', $configValue('DB_PORT', '3306'));
define('DB_NAME', $configValue('DB_NAME', 'acmirs_cms'));
define('DB_USER', $configValue('DB_USER', 'acmirs'));
define('DB_PASS', $configValue('DB_PASS', '1@mha9Py79'));

define('PROJECT_ROOT', dirname(__DIR__));
