<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

// PHP 8.2 compatibility: Suppress deprecation warnings for Laravel 8
if (PHP_VERSION_ID >= 80200) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    error_reporting(E_ALL);
}

// Increase execution time for view rendering - set early to prevent timeouts
ini_set('max_execution_time', 60);
set_time_limit(60); // 60 seconds should be enough for view rendering

// Enable error logging and display (temporarily for debugging)
ini_set('log_errors', 1);
ini_set('display_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/php-errors.log');

// Log startup
file_put_contents(__DIR__ . '/../storage/logs/php-errors.log', date('Y-m-d H:i:s') . " - Index.php started\n", FILE_APPEND);

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

// PHP 8.2 compatibility fix
require_once __DIR__.'/../bootstrap/php82-compat.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    // Log fatal errors that occur before Laravel can handle them
    $errorLog = __DIR__ . '/../storage/logs/fatal-errors.log';
    $errorMsg = date('Y-m-d H:i:s') . " - FATAL ERROR\n";
    $errorMsg .= "Message: " . $e->getMessage() . "\n";
    $errorMsg .= "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    $errorMsg .= "Trace:\n" . $e->getTraceAsString() . "\n\n";
    file_put_contents($errorLog, $errorMsg, FILE_APPEND);
    
    // Try to send a response
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => config('app.debug') ? $e->getMessage() : 'An error occurred',
            'file' => config('app.debug') ? $e->getFile() : null,
            'line' => config('app.debug') ? $e->getLine() : null
        ]);
    }
}
