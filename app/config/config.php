<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_NAME', 'Mini ERP');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development');
define('BASE_URL', 'http://localhost:8080');
define('FRETE_PADRAO', 20.00);
define('FRETE_INTERMEDIARIO', 15.00);
define('FRETE_GRATIS_MIN', 200.00);
define('FRETE_INTERMEDIARIO_MIN', 52.00);
define('FRETE_INTERMEDIARIO_MAX', 166.59);

date_default_timezone_set('America/Sao_Paulo');

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../models/' . $class . '.php',
        __DIR__ . '/../controllers/' . $class . '.php',
        __DIR__ . '/' . $class . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/database.php';

function dd($var) {
    echo '<pre>'; var_dump($var); echo '</pre>'; die();
}

function writeLog($message, $type = 'INFO') {
    $logFile = '/var/www/html/logs/app.log';
    $log = date('Y-m-d H:i:s') . " [{$type}] " . $message . PHP_EOL;
    if (is_writable('/var/www/html/logs/')) {
        @file_put_contents($logFile, $log, FILE_APPEND | LOCK_EX);
    }
}

function clean($input) {
    if (is_array($input)) return array_map('clean', $input);
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatMoney($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    return strlen($cep) === 8;
}
?>
