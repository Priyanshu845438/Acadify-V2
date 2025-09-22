<?php
/**
 * Simple .env file loader for shared hosting environments
 * This loads environment variables from .env file into PHP's $_ENV superglobal
 */

function loadEnvFile($file = null) {
    // Default to .env file in project root
    if ($file === null) {
        $file = __DIR__ . '/../.env';
    }
    
    // Check if .env file exists
    if (!file_exists($file)) {
        // If no .env file, return silently (environment variables might be set by hosting provider)
        return false;
    }
    
    // Read the .env file
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if ($lines === false) {
        return false;
    }
    
    foreach ($lines as $line) {
        // Skip comments and empty lines
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') || 
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            // Set environment variable
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    
    return true;
}

// Auto-load .env file when this script is included
loadEnvFile();
?>