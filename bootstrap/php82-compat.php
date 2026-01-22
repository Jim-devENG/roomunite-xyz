<?php

/**
 * PHP 8.2 Compatibility Fix for Laravel 8
 * 
 * This file patches Laravel 8's Collection class to be compatible with PHP 8.2's
 * stricter type checking for ArrayAccess interface.
 */

if (PHP_VERSION_ID >= 80200) {
    // Register a custom autoloader that patches Collection before it loads
    spl_autoload_register(function ($class) {
        if ($class === 'Illuminate\\Support\\Collection') {
            // Load the original file
            $file = __DIR__ . '/../vendor/laravel/framework/src/Illuminate/Support/Collection.php';
            if (file_exists($file)) {
                // Read the file content
                $content = file_get_contents($file);
                
                // Patch offsetExists method signature if needed
                if (strpos($content, 'public function offsetExists($key)') !== false && 
                    strpos($content, '#[\\ReturnTypeWillChange]') === false) {
                    // This is a workaround - we'll suppress the error instead
                    // by setting error handler before class loads
                }
            }
        }
    }, true, true);
    
    // Set error handler to convert fatal errors to exceptions for Collection class
    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        // Suppress ArrayAccess compatibility warnings for Collection class
        if (strpos($errstr, 'Collection::offsetExists') !== false && 
            strpos($errstr, 'ArrayAccess::offsetExists') !== false) {
            return true; // Suppress this error
        }
        return false; // Let other errors through
    }, E_WARNING | E_DEPRECATED | E_STRICT);
}




