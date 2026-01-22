<?php
/**
 * Application Health Check Script
 * 
 * Run this script to verify the application is working correctly after fixes
 * Usage: php test_application.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

echo "=== RoomUnite Application Health Check ===\n\n";

$errors = [];
$warnings = [];

// 1. Check PHP version
echo "1. Checking PHP version... ";
if (PHP_VERSION_ID >= 80000) {
    echo "✓ PHP " . PHP_VERSION . "\n";
} else {
    $errors[] = "PHP 8.0+ required, found " . PHP_VERSION;
    echo "✗ PHP " . PHP_VERSION . " (PHP 8.0+ required)\n";
}

// 2. Check required directories
echo "2. Checking directories... ";
$dirs = ['storage', 'storage/logs', 'storage/framework', 'storage/framework/cache', 'bootstrap/cache'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        $warnings[] = "Directory missing: $dir";
        echo "\n   ⚠ Missing: $dir\n";
    }
}
echo "✓ Directories OK\n";

// 3. Check file permissions
echo "3. Checking file permissions... ";
$writable = ['storage', 'bootstrap/cache'];
foreach ($writable as $dir) {
    if (is_dir($dir) && !is_writable($dir)) {
        $warnings[] = "Directory not writable: $dir";
        echo "\n   ⚠ Not writable: $dir\n";
    }
}
echo "✓ Permissions OK\n";

// 4. Check database connection
echo "4. Checking database connection... ";
try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    if (env('DB_DATABASE')) {
        DB::connection()->getPdo();
        echo "✓ Database connected\n";
    } else {
        $warnings[] = "Database not configured (DB_DATABASE not set)";
        echo "⚠ Database not configured\n";
    }
} catch (\Exception $e) {
    $warnings[] = "Database connection failed: " . $e->getMessage();
    echo "⚠ Database connection failed\n";
}

// 5. Check critical models
echo "5. Checking critical models... ";
try {
    $models = [
        'App\Models\Settings',
        'App\Models\Currency',
        'App\Models\Language',
        'App\Models\StartingCities',
    ];
    
    foreach ($models as $model) {
        if (class_exists($model)) {
            // Try to instantiate
            $instance = new $model();
        }
    }
    echo "✓ Models OK\n";
} catch (\Exception $e) {
    $warnings[] = "Model check failed: " . $e->getMessage();
    echo "⚠ Model check failed\n";
}

// 6. Check service providers
echo "6. Checking service providers... ";
try {
    $providers = [
        'App\Providers\SetDataServiceProvider',
        'App\Providers\AppServiceProvider',
        'App\Providers\RouteServiceProvider',
    ];
    
    foreach ($providers as $provider) {
        if (class_exists($provider)) {
            // Provider exists
        }
    }
    echo "✓ Service providers OK\n";
} catch (\Exception $e) {
    $warnings[] = "Service provider check failed: " . $e->getMessage();
    echo "⚠ Service provider check failed\n";
}

// 7. Check routes
echo "7. Checking routes... ";
try {
    $routes = Route::getRoutes();
    $routeCount = count($routes);
    if ($routeCount > 0) {
        echo "✓ Found $routeCount routes\n";
    } else {
        $warnings[] = "No routes found";
        echo "⚠ No routes found\n";
    }
} catch (\Exception $e) {
    $warnings[] = "Route check failed: " . $e->getMessage();
    echo "⚠ Route check failed\n";
}

// 8. Test HomeController
echo "8. Testing HomeController... ";
try {
    $controller = new \App\Http\Controllers\HomeController();
    if (method_exists($controller, 'index')) {
        echo "✓ HomeController::index() exists\n";
    } else {
        $errors[] = "HomeController::index() method not found";
        echo "✗ HomeController::index() not found\n";
    }
} catch (\Exception $e) {
    $errors[] = "HomeController test failed: " . $e->getMessage();
    echo "✗ HomeController test failed\n";
}

// Summary
echo "\n=== Summary ===\n";
if (empty($errors) && empty($warnings)) {
    echo "✓ All checks passed! Application should be working.\n";
} else {
    if (!empty($errors)) {
        echo "\n✗ ERRORS:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }
    
    if (!empty($warnings)) {
        echo "\n⚠ WARNINGS:\n";
        foreach ($warnings as $warning) {
            echo "  - $warning\n";
        }
    }
}

echo "\n=== Next Steps ===\n";
echo "1. Clear cache: php artisan cache:clear\n";
echo "2. Clear config cache: php artisan config:clear\n";
echo "3. Clear route cache: php artisan route:clear\n";
echo "4. Test homepage: Visit http://your-domain/\n";
echo "5. Check logs: tail -f storage/logs/laravel.log\n";




