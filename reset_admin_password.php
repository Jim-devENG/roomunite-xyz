<?php
/**
 * Admin Password Reset Script
 * 
 * This script allows you to:
 * 1. View all admin accounts in the database
 * 2. Reset an admin password
 * 3. Create a new admin account
 * 
 * Usage: php reset_admin_password.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use App\Models\RoleAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "\n=== Admin Password Reset Tool ===\n\n";

// Show existing admin accounts
echo "1. Checking existing admin accounts...\n";
echo str_repeat("-", 60) . "\n";

try {
    $admins = Admin::all();
    
    if ($admins->count() > 0) {
        echo "Found " . $admins->count() . " admin account(s):\n\n";
        foreach ($admins as $admin) {
            echo "  ID: " . $admin->id . "\n";
            echo "  Username: " . $admin->username . "\n";
            echo "  Email: " . $admin->email . "\n";
            echo "  Status: " . $admin->status . "\n";
            
            // Check roles
            $roles = RoleAdmin::where('admin_id', $admin->id)->get();
            if ($roles->count() > 0) {
                echo "  Roles: ";
                foreach ($roles as $role) {
                    echo $role->role_id . " ";
                }
                echo "\n";
            }
            echo "\n";
        }
    } else {
        echo "No admin accounts found in database.\n\n";
    }
} catch (\Exception $e) {
    echo "Error reading admin accounts: " . $e->getMessage() . "\n";
    exit(1);
}

echo str_repeat("-", 60) . "\n\n";

// Interactive menu
echo "What would you like to do?\n";
echo "  1. Reset password for existing admin\n";
echo "  2. Create new admin account\n";
echo "  3. Exit\n";
echo "\nEnter choice (1-3): ";

$handle = fopen("php://stdin", "r");
$choice = trim(fgets($handle));

if ($choice == "1") {
    // Reset password
    echo "\nEnter admin ID to reset password: ";
    $adminId = trim(fgets($handle));
    
    $admin = Admin::find($adminId);
    if (!$admin) {
        echo "Admin with ID $adminId not found!\n";
        exit(1);
    }
    
    echo "Admin found: {$admin->username} ({$admin->email})\n";
    echo "Enter new password: ";
    $password = trim(fgets($handle));
    
    if (empty($password)) {
        echo "Password cannot be empty!\n";
        exit(1);
    }
    
    $admin->password = Hash::make($password);
    $admin->save();
    
    echo "\n✅ Password reset successfully!\n";
    echo "Username: {$admin->username}\n";
    echo "Email: {$admin->email}\n";
    echo "New Password: $password\n";
    
} elseif ($choice == "2") {
    // Create new admin
    echo "\nCreating new admin account...\n";
    echo "Enter username: ";
    $username = trim(fgets($handle));
    
    echo "Enter email: ";
    $email = trim(fgets($handle));
    
    echo "Enter password: ";
    $password = trim(fgets($handle));
    
    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required!\n";
        exit(1);
    }
    
    // Check if username or email already exists
    if (Admin::where('username', $username)->exists()) {
        echo "Username already exists!\n";
        exit(1);
    }
    
    if (Admin::where('email', $email)->exists()) {
        echo "Email already exists!\n";
        exit(1);
    }
    
    $admin = new Admin();
    $admin->username = $username;
    $admin->email = $email;
    $admin->password = Hash::make($password);
    $admin->status = 'Active';
    $admin->save();
    
    // Assign default role (role_id = 1)
    RoleAdmin::insert([
        'admin_id' => $admin->id,
        'role_id' => 1
    ]);
    
    echo "\n✅ Admin account created successfully!\n";
    echo "ID: {$admin->id}\n";
    echo "Username: {$admin->username}\n";
    echo "Email: {$admin->email}\n";
    echo "Password: $password\n";
    
} else {
    echo "Exiting...\n";
    exit(0);
}

fclose($handle);

echo "\n=== Done ===\n";




