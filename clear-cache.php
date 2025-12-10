<?php
/**
 * Script untuk clear cache Laravel di server production
 * Akses via: https://hris.akti.ac.id/clear-cache.php
 * ⚠️ PENTING: HAPUS file ini setelah selesai untuk keamanan!
 */

// Set execution time limit
set_time_limit(300);

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h1>Laravel Cache Clear Script</h1>";
echo "<pre>";

echo "1. Removing old cache files...\n";
// Hapus file cache yang mungkin menyimpan path lama
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/services.php',
];

foreach ($cacheFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        unlink($fullPath);
        echo "   Deleted: $file\n";
    }
}

// Hapus cache data
$cacheDataPath = __DIR__ . '/storage/framework/cache/data';
if (is_dir($cacheDataPath)) {
    $files = glob($cacheDataPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "   Cleared cache data directory\n";
}

echo "\n2. Clearing Laravel cache via Artisan...\n";
try {
    $kernel->call('config:clear');
    echo "   ✓ Config cache cleared\n";
    
    $kernel->call('cache:clear');
    echo "   ✓ Application cache cleared\n";
    
    $kernel->call('route:clear');
    echo "   ✓ Route cache cleared\n";
    
    $kernel->call('view:clear');
    echo "   ✓ View cache cleared\n";
    
    $kernel->call('optimize:clear');
    echo "   ✓ All optimized cache cleared\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n3. Creating storage directories...\n";
$dirs = [
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache/data',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($dirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0775, true);
        echo "   Created: $dir\n";
    } else {
        echo "   Exists: $dir\n";
    }
}

echo "\n4. Setting permissions...\n";
$storagePath = __DIR__ . '/storage';
$bootstrapCachePath = __DIR__ . '/bootstrap/cache';

if (is_dir($storagePath)) {
    chmod($storagePath, 0775);
    echo "   ✓ Storage permissions set\n";
}

if (is_dir($bootstrapCachePath)) {
    chmod($bootstrapCachePath, 0775);
    echo "   ✓ Bootstrap cache permissions set\n";
}

echo "\n5. Rebuilding cache with correct paths...\n";
try {
    $kernel->call('config:cache');
    echo "   ✓ Config cache rebuilt\n";
    
    $kernel->call('route:cache');
    echo "   ✓ Route cache rebuilt\n";
    
    $kernel->call('view:cache');
    echo "   ✓ View cache rebuilt\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n6. Verifying paths...\n";
echo "   Base Path: " . base_path() . "\n";
echo "   Storage Path: " . storage_path() . "\n";
echo "   Public Path: " . public_path() . "\n";

echo "\n✅ Done! Please test the website now.\n";
echo "\n⚠️ PENTING: Hapus file clear-cache.php ini setelah selesai untuk keamanan!\n";
echo "</pre>";
?>

