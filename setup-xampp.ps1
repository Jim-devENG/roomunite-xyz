# XAMPP Virtual Host Setup Script for RoomUnite
# Run this script as Administrator

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "XAMPP Virtual Host Setup for RoomUnite" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$projectPath = "C:/Users/MIKENZY/Documents/Apps/roomunite/public"
$vhostsFile = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"
$httpdFile = "C:\xampp\apache\conf\httpd.conf"
$hostsFile = "C:\Windows\System32\drivers\etc\hosts"

# Check if running as Administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    exit 1
}

Write-Host "[1/4] Checking XAMPP configuration files..." -ForegroundColor Yellow

# Check if files exist
if (-not (Test-Path $vhostsFile)) {
    Write-Host "ERROR: Cannot find $vhostsFile" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path $httpdFile)) {
    Write-Host "ERROR: Cannot find $httpdFile" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path $hostsFile)) {
    Write-Host "ERROR: Cannot find $hostsFile" -ForegroundColor Red
    exit 1
}

Write-Host "✓ All configuration files found" -ForegroundColor Green
Write-Host ""

# Step 1: Add Virtual Host
Write-Host "[2/4] Adding virtual host configuration..." -ForegroundColor Yellow

$vhostConfig = @"

# RoomUnite Virtual Host - Added automatically
<VirtualHost *:80>
    ServerName roomunite.local
    DocumentRoot "$projectPath"
    
    <Directory "$projectPath">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
"@

# Check if virtual host already exists
$existingVhost = Get-Content $vhostsFile -Raw
if ($existingVhost -match "roomunite\.local") {
    Write-Host "⚠ Virtual host for roomunite.local already exists" -ForegroundColor Yellow
    Write-Host "  Skipping virtual host configuration..." -ForegroundColor Yellow
} else {
    Add-Content -Path $vhostsFile -Value $vhostConfig
    Write-Host "✓ Virtual host added to httpd-vhosts.conf" -ForegroundColor Green
}

Write-Host ""

# Step 2: Enable Virtual Hosts in httpd.conf
Write-Host "[3/4] Enabling virtual hosts in httpd.conf..." -ForegroundColor Yellow

$httpdContent = Get-Content $httpdFile -Raw
if ($httpdContent -match "#\s*Include\s+conf/extra/httpd-vhosts\.conf") {
    # Uncomment the line
    $httpdContent = $httpdContent -replace "#\s*Include\s+conf/extra/httpd-vhosts\.conf", "Include conf/extra/httpd-vhosts.conf"
    Set-Content -Path $httpdFile -Value $httpdContent -NoNewline
    Write-Host "✓ Virtual hosts enabled in httpd.conf" -ForegroundColor Green
} elseif ($httpdContent -match "Include\s+conf/extra/httpd-vhosts\.conf") {
    Write-Host "✓ Virtual hosts already enabled" -ForegroundColor Green
} else {
    Write-Host "⚠ Could not find virtual hosts include line. Please check manually." -ForegroundColor Yellow
}

# Check mod_rewrite
if ($httpdContent -match "#\s*LoadModule\s+rewrite_module") {
    Write-Host "⚠ mod_rewrite is commented out. Enabling..." -ForegroundColor Yellow
    $httpdContent = $httpdContent -replace "#\s*LoadModule\s+rewrite_module\s+modules/mod_rewrite\.so", "LoadModule rewrite_module modules/mod_rewrite.so"
    Set-Content -Path $httpdFile -Value $httpdContent -NoNewline
    Write-Host "✓ mod_rewrite enabled" -ForegroundColor Green
} elseif ($httpdContent -match "LoadModule\s+rewrite_module") {
    Write-Host "✓ mod_rewrite is already enabled" -ForegroundColor Green
}

Write-Host ""

# Step 3: Add to Windows hosts file
Write-Host "[4/4] Adding roomunite.local to Windows hosts file..." -ForegroundColor Yellow

$hostsContent = Get-Content $hostsFile
if ($hostsContent -match "roomunite\.local") {
    Write-Host "✓ roomunite.local already exists in hosts file" -ForegroundColor Green
} else {
    Add-Content -Path $hostsFile -Value "`n127.0.0.1    roomunite.local"
    Write-Host "✓ Added roomunite.local to hosts file" -ForegroundColor Green
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Configuration Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Open XAMPP Control Panel" -ForegroundColor White
Write-Host "2. Stop Apache (if running)" -ForegroundColor White
Write-Host "3. Start Apache" -ForegroundColor White
Write-Host "4. Open your browser and go to: http://roomunite.local" -ForegroundColor White
Write-Host ""
Write-Host "If you see any errors, check:" -ForegroundColor Yellow
Write-Host "- C:\xampp\apache\logs\error.log" -ForegroundColor White
Write-Host ""

