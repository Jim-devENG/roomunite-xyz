<?php
/**
 * Quick Admin Password Reset
 * Usage: php quick_reset_admin.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

// Found admin account
$admin = Admin::find(1);

if (!$admin) {
    echo "Admin account not found!\n";
    exit(1);
}

echo "\n=== Admin Account Found ===\n";
echo "ID: {$admin->id}\n";
echo "Username: {$admin->username}\n";
echo "Email: {$admin->email}\n";
echo "Status: {$admin->status}\n\n";

// Reset password to default
$newPassword = 'admin123'; // Change this to your desired password
$admin->password = Hash::make($newPassword);
$admin->save();

echo "✅ Password reset successfully!\n\n";
echo "Login Credentials:\n";
echo "  Email: {$admin->email}\n";
echo "  Username: {$admin->username}\n";
echo "  Password: $newPassword\n\n";
echo "Login URL: http://roomunite.local/admin/login\n\n";




