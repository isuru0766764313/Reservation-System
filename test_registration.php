<?php
// Simple test script to identify registration issues
require_once 'vendor/autoload.php';

echo "=== CUSTOMER REGISTRATION OTP TEST ===\n";

// Test 1: Check if routes are accessible
echo "1. Testing route accessibility...\n";
$routes = [
    '/customer/register',
    '/customer/verify-email',
    '/customer/login'
];

foreach ($routes as $route) {
    $url = "http://127.0.0.1:8000" . $route;
    echo "   Route: $route - ";
    
    $headers = @get_headers($url);
    if ($headers) {
        echo "✅ Accessible\n";
    } else {
        echo "❌ Not accessible\n";
    }
}

// Test 2: Check environment variables
echo "\n2. Checking environment configuration...\n";
$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    
    echo "   APP_KEY: " . (strpos($env_content, 'APP_KEY=base64:') !== false ? "✅ Set" : "❌ Missing") . "\n";
    echo "   DB_DATABASE: " . (strpos($env_content, 'DB_DATABASE=reservation_system') !== false ? "✅ Set" : "❌ Wrong") . "\n";
    echo "   SMS_USERNAME: " . (strpos($env_content, 'SMS_USERNAME=your-mobitel-username') !== false ? "⚠️  Needs real credentials" : "✅ Configured") . "\n";
    echo "   MAIL_MAILER: " . (strpos($env_content, 'MAIL_MAILER=log') !== false ? "✅ Set to log (testing)" : "❌ Check config") . "\n";
} else {
    echo "   ❌ .env file not found!\n";
}

// Test 3: Check if tables exist
echo "\n3. Checking database tables...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=reservation_system', 'root', '');
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $required_tables = ['customers_table', 'admins_table'];
    foreach ($required_tables as $table) {
        echo "   Table $table: " . (in_array($table, $tables) ? "✅ Exists" : "❌ Missing") . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "If all items show ✅, the registration should work.\n";
echo "⚠️  items need your attention.\n";
echo "❌ items are blocking registration.\n";
?>