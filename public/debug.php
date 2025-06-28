<?php
echo "<h2>Laravel Debug Check</h2>";

// Check PHP version
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";

// Check if Laravel files exist
$files = [
    '../bootstrap/app.php' => 'Bootstrap App',
    '../routes/web.php' => 'Web Routes',
    '../routes/api.php' => 'API Routes',
    '../vendor/autoload.php' => 'Composer Autoload',
    '../.env' => 'Environment File'
];

echo "<h3>File Check:</h3>";
foreach ($files as $file => $name) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $name exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $name MISSING</p>";
    }
}

// Check .env APP_KEY
if (file_exists('../.env')) {
    $env = file_get_contents('../.env');
    if (preg_match('/APP_KEY=(.+)/', $env, $matches)) {
        $key = trim($matches[1]);
        if (strlen($key) > 10) {
            echo "<p style='color: green;'>✓ APP_KEY is set</p>";
        } else {
            echo "<p style='color: red;'>✗ APP_KEY is empty</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ APP_KEY not found</p>";
    }
}

// Check storage permissions
$dirs = ['../storage/logs', '../storage/framework/cache', '../storage/framework/sessions'];
echo "<h3>Storage Permissions:</h3>";
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p style='color: green;'>✓ $dir is writable</p>";
        } else {
            echo "<p style='color: red;'>✗ $dir is not writable</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ $dir does not exist</p>";
    }
}

// Try to load Laravel
echo "<h3>Laravel Load Test:</h3>";
try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    echo "<p style='color: green;'>✓ Laravel loaded successfully</p>";
    
    // Test basic Laravel functionality
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<p style='color: green;'>✓ HTTP Kernel created</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Laravel load failed: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='/'>Back to Home</a> | <a href='/test'>Test Route</a></p>";
?>
