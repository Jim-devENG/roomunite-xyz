<?php
/**
 * Direct PHP test - bypasses Laravel entirely
 * This will help identify if the issue is with PHP itself or Laravel
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('max_execution_time', 60);

header('Content-Type: application/json');

$response = [
    'status' => 'ok',
    'php_version' => PHP_VERSION,
    'time' => date('Y-m-d H:i:s'),
    'memory' => memory_get_usage(true),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
];

try {
    // Test if we can require Laravel files
    require __DIR__.'/../vendor/autoload.php';
    $response['autoload'] = 'ok';
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $response['app_bootstrap'] = 'ok';
    
    $response['laravel_version'] = $app->version();
    
} catch (\Throwable $e) {
    $response['error'] = $e->getMessage();
    $response['file'] = $e->getFile();
    $response['line'] = $e->getLine();
    http_response_code(500);
}

echo json_encode($response, JSON_PRETTY_PRINT);




