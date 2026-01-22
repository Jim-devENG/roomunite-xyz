<?php
/**
 * Direct test without Laravel - to verify PHP is working
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 30);

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "Memory: " . memory_get_usage(true) . "\n";

try {
    require __DIR__.'/vendor/autoload.php';
    echo "Autoload: OK\n";
    
    $app = require_once __DIR__.'/bootstrap/app.php';
    echo "App bootstrap: OK\n";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "Kernel: OK\n";
    
    $request = Illuminate\Http\Request::create('/test-minimal');
    echo "Request created: OK\n";
    
    echo "Handling request...\n";
    $response = $kernel->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . substr($response->getContent(), 0, 200) . "\n";
    
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . substr($e->getTraceAsString(), 0, 500) . "\n";
}




