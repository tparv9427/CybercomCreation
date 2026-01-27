<?php
/**
 * Manual PSR-4 Autoloader
 * 
 * This autoloader is used when Composer is not available.
 * It follows PSR-4 standard for the EasyCart namespace.
 */

spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'EasyCart\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/../app/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
