<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Models\User;

echo "Testing Laravel App...\n\n";

try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';

    echo "✓ App bootstrapped successfully\n";

    // Test database connection
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    echo "✓ Kernel bootstrapped\n";

    // Test a simple query
    $count = User::count();

    echo "✓ Database connection works\n";
    echo "  Total users: $count\n\n";

    echo "✓ Everything looks good!\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
