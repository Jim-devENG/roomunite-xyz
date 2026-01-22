<?php
/**
 * Test Bootstrap Script
 * This will help identify what's causing the 500 error
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "Starting bootstrap test...\n";

try {
    require __DIR__.'/vendor/autoload.php';
    echo "✓ Autoload successful\n";
} catch (Exception $e) {
    echo "✗ Autoload failed: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    require_once __DIR__.'/bootstrap/php82-compat.php';
    echo "✓ PHP82 compat loaded\n";
} catch (Exception $e) {
    echo "✗ PHP82 compat failed: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    echo "✓ App instance created\n";
} catch (Exception $e) {
    echo "✗ App creation failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✓ HTTP Kernel created\n";
} catch (Exception $e) {
    echo "✗ Kernel creation failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

try {
    $request = Illuminate\Http\Request::create('/');
    echo "✓ Request created\n";
} catch (Exception $e) {
    echo "✗ Request creation failed: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    echo "Handling request...\n";
    $response = $kernel->handle($request);
    echo "✓ Request handled successfully\n";
    echo "Response Status: " . $response->getStatusCode() . "\n";
} catch (Exception $e) {
    echo "✗ Request handling failed: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} catch (Throwable $e) {
    echo "✗ Fatal error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✓ All tests passed!\n";




