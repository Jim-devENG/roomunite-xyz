# Quick Fix Commands for XAMPP + Laravel Setup
# Run these commands in PowerShell as Administrator

Write-Host "=== XAMPP Laravel Fix Script ===" -ForegroundColor Cyan

# Step 1: Check PHP versions
Write-Host "`n1. Checking PHP versions..." -ForegroundColor Yellow
$cliVersion = php -v | Select-String "PHP (\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }
Write-Host "CLI PHP Version: $cliVersion"

# Step 2: Create diagnostic file
Write-Host "`n2. Creating diagnostic file..." -ForegroundColor Yellow
$diagnosticContent = @"
<?php
header('Content-Type: text/plain');
echo 'PHP Version: ' . PHP_VERSION . PHP_EOL;
echo 'SAPI: ' . php_sapi_name() . PHP_EOL;
echo 'Document Root: ' . (\$_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . PHP_EOL;
echo 'Error Log: ' . ini_get('error_log') . PHP_EOL;
"@
Set-Content -Path "public\diagnostic.php" -Value $diagnosticContent -Encoding UTF8
Write-Host "Created: public\diagnostic.php"

# Step 3: Clear Laravel caches
Write-Host "`n3. Clearing Laravel caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
Write-Host "Caches cleared"

# Step 4: Ensure storage directories exist
Write-Host "`n4. Ensuring storage directories exist..." -ForegroundColor Yellow
$dirs = @(
    "storage\framework\sessions",
    "storage\framework\views",
    "storage\framework\cache",
    "storage\logs",
    "bootstrap\cache"
)
foreach ($dir in $dirs) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "Created: $dir"
    }
}

# Step 5: Check file permissions
Write-Host "`n5. Checking file permissions..." -ForegroundColor Yellow
Get-ChildItem storage -Recurse -File | ForEach-Object { $_.IsReadOnly = $false }
Get-ChildItem bootstrap\cache -Recurse -File -ErrorAction SilentlyContinue | ForEach-Object { $_.IsReadOnly = $false }
Write-Host "Permissions checked"

# Step 6: Display next steps
Write-Host "`n=== NEXT STEPS ===" -ForegroundColor Green
Write-Host "1. Check Apache PHP version:" -ForegroundColor Cyan
Write-Host "   - Access: http://roomunite.local/diagnostic.php" -ForegroundColor White
Write-Host "   - Or: http://localhost/roomunite/public/diagnostic.php" -ForegroundColor White
Write-Host ""
Write-Host "2. Configure Apache virtual host (see XAMPP_APACHE_FIX.md)" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Configure PHP error logging (see XAMPP_APACHE_FIX.md)" -ForegroundColor Cyan
Write-Host ""
Write-Host "4. Test: Invoke-WebRequest -Uri http://roomunite.local/test-basic" -ForegroundColor Cyan




