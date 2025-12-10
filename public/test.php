<?php
/**
 * Test file untuk troubleshooting LiteSpeed
 * Akses via: https://hris.akti.ac.id/test.php
 * HAPUS file ini setelah troubleshooting selesai!
 */

echo "<h1>PHP Test - LiteSpeed Configuration</h1>";
echo "<hr>";

echo "<h2>1. PHP Information</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Script Filename:</strong> " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown') . "</p>";
echo "<p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";

echo "<hr>";

echo "<h2>2. File System Check</h2>";
$publicPath = __DIR__;
$rootPath = dirname($publicPath);
echo "<p><strong>Public Folder:</strong> " . $publicPath . "</p>";
echo "<p><strong>Root Folder:</strong> " . $rootPath . "</p>";
echo "<p><strong>index.php exists:</strong> " . (file_exists($publicPath . '/index.php') ? 'YES ✓' : 'NO ✗') . "</p>";
echo "<p><strong>.htaccess exists:</strong> " . (file_exists($publicPath . '/.htaccess') ? 'YES ✓' : 'NO ✗') . "</p>";
echo "<p><strong>vendor/autoload.php exists:</strong> " . (file_exists($rootPath . '/vendor/autoload.php') ? 'YES ✓' : 'NO ✗') . "</p>";
echo "<p><strong>bootstrap/app.php exists:</strong> " . (file_exists($rootPath . '/bootstrap/app.php') ? 'YES ✓' : 'NO ✗') . "</p>";

echo "<hr>";

echo "<h2>3. Permissions Check</h2>";
echo "<p><strong>Public folder readable:</strong> " . (is_readable($publicPath) ? 'YES ✓' : 'NO ✗') . "</p>";
echo "<p><strong>Public folder writable:</strong> " . (is_writable($publicPath) ? 'YES ✓' : 'NO ✗') . "</p>";
echo "<p><strong>Storage folder exists:</strong> " . (is_dir($rootPath . '/storage') ? 'YES ✓' : 'NO ✗') . "</p>";
echo "<p><strong>Storage folder writable:</strong> " . (is_writable($rootPath . '/storage') ? 'YES ✓' : 'NO ✗') . "</p>";

echo "<hr>";

echo "<h2>4. Environment Check</h2>";
$envPath = $rootPath . '/.env';
echo "<p><strong>.env file exists:</strong> " . (file_exists($envPath) ? 'YES ✓' : 'NO ✗') . "</p>";
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    echo "<p><strong>APP_URL in .env:</strong> " . (preg_match('/APP_URL=(.+)/', $envContent, $matches) ? htmlspecialchars($matches[1]) : 'Not found') . "</p>";
    echo "<p><strong>DB_DATABASE in .env:</strong> " . (preg_match('/DB_DATABASE=(.+)/', $envContent, $matches) ? htmlspecialchars($matches[1]) : 'Not found') . "</p>";
}

echo "<hr>";

echo "<h2>5. Mod_Rewrite Test</h2>";
echo "<p>Jika Anda melihat halaman ini, berarti PHP berfungsi dengan baik.</p>";
echo "<p>Coba akses: <a href='/'>https://hris.akti.ac.id/</a> (tanpa test.php)</p>";
echo "<p>Jika masih 404, kemungkinan masalahnya di:</p>";
echo "<ul>";
echo "<li>Document Root tidak mengarah ke folder <code>public</code></li>";
echo "<li>Mod_rewrite tidak enabled di LiteSpeed</li>";
echo "<li>File .htaccess tidak terbaca</li>";
echo "</ul>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ PENTING: Hapus file test.php ini setelah troubleshooting selesai untuk keamanan!</strong></p>";
?>

