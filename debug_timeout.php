<?php
/**
 * Debug script to identify what's causing the timeout
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 30);

echo "Starting debug...\n";
$start = microtime(true);

require __DIR__.'/vendor/autoload.php';
echo "Autoload: " . round((microtime(true) - $start) * 1000) . "ms\n";

$app = require_once __DIR__.'/bootstrap/app.php';
echo "App bootstrap: " . round((microtime(true) - $start) * 1000) . "ms\n";

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "Kernel: " . round((microtime(true) - $start) * 1000) . "ms\n";

$request = Illuminate\Http\Request::create('/');
echo "Request: " . round((microtime(true) - $start) * 1000) . "ms\n";

echo "Handling request...\n";
$handleStart = microtime(true);

try {
    $response = $kernel->handle($request);
    echo "Request handled: " . round((microtime(true) - $handleStart) * 1000) . "ms\n";
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content length: " . strlen($response->getContent()) . " bytes\n";
} catch (\Exception $e) {
    echo "ERROR after " . round((microtime(true) - $handleStart) * 1000) . "ms\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTotal time: " . round((microtime(true) - $start) * 1000) . "ms\n";




