-- SQL Queries to Check Admin Accounts
-- Run these in MySQL/phpMyAdmin to see admin accounts

-- 1. View all admin accounts
SELECT 
    id,
    username,
    email,
    status,
    created_at,
    updated_at
FROM admin;

-- 2. View admin accounts with their roles
SELECT 
    a.id,
    a.username,
    a.email,
    a.status,
    ra.role_id
FROM admin a
LEFT JOIN role_admin ra ON a.id = ra.admin_id
ORDER BY a.id;

-- 3. Check if admin account exists
SELECT COUNT(*) as admin_count FROM admin;

-- 4. View password hash (for verification - you can't decrypt it)
SELECT 
    id,
    username,
    email,
    LEFT(password, 20) as password_hash_preview,
    status
FROM admin;

-- 5. Reset password for admin ID 1 (replace 'your_new_password' with actual password)
-- WARNING: This uses MD5 which is NOT secure. Use the PHP script instead!
-- UPDATE admin SET password = MD5('your_new_password') WHERE id = 1;

-- 6. Create new admin account (replace values)
-- INSERT INTO admin (username, email, password, status, created_at, updated_at)
-- VALUES ('admin', 'admin@example.com', MD5('password123'), 'Active', NOW(), NOW());

-- NOTE: Laravel uses bcrypt for password hashing, not MD5!
-- Use the reset_admin_password.php script instead for proper password hashing.




