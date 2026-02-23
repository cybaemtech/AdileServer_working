# PHP Configuration Fix for IIS

## Issue
PHP scripts are returning 500 errors on IIS server. This indicates PHP is not properly configured.

## Solution Steps

### 1. Install PHP on IIS (Server Admin Required)

Run these PowerShell commands as Administrator:

```powershell
# Install PHP via Chocolatey (if available)
choco install php

# OR download PHP manually
# 1. Download PHP for Windows from https://windows.php.net/download/
# 2. Extract to C:\php
# 3. Copy php.ini-production to php.ini
# 4. Edit php.ini:
#    - extension_dir = "C:\php\ext"
#    - Enable required extensions (uncomment lines):
#      extension=curl
#      extension=mbstring
#      extension=openssl
#      extension=pdo_mysql
```

### 2. Configure IIS Handler Mappings

```powershell
# Add PHP FastCGI handler
Import-Module WebAdministration

# Remove existing PHP handlers
Remove-WebHandler -Name "PHP*" -PSPath "IIS:\Sites\Default Web Site"

# Add new PHP handler
New-WebHandler -Name "PHP-FastCGI" -Path "*.php" -Verb "*" -Modules "FastCgiModule" -ScriptProcessor "C:\php\php-cgi.exe" -ResourceType "File" -PSPath "IIS:\Sites\Default Web Site"
```

### 3. Configure FastCGI Settings

```powershell
# Configure FastCGI application
New-WebFastCGIApplication -FilePath "C:\php\php-cgi.exe"

# Set environment variables
Set-WebConfigurationProperty -Filter "system.webServer/fastCgi/application[@fullPath='C:\php\php-cgi.exe']/environmentVariables" -Name "." -Value @{name="PHP_FCGI_MAX_REQUESTS";value="10000"}
```

### 4. Set Permissions

```powershell
# Give IIS user permission to PHP folder
icacls "C:\php" /grant "IIS_IUSRS:(OI)(CI)RX" /T
icacls "C:\inetpub\wwwroot" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### 5. Test PHP Installation

Create test file: C:\inetpub\wwwroot\test.php
```php
<?php phpinfo(); ?>
```

Browse to: http://localhost/test.php

## Alternative: Use Different Server

If PHP cannot be configured on this IIS server, consider:

1. **Apache/Nginx**: Move to a different web server
2. **Docker**: Use PHP in a container
3. **Cloud hosting**: Use a managed PHP hosting service
4. **Node.js**: Convert backend to Node.js/Express

## Current Status

- ✅ Frontend authentication error handling implemented
- ✅ Application loads and shows login screen
- ❌ PHP backend not executing on IIS
- ⚠️  Using client-side fallbacks for API responses

The application now works with graceful error handling, but full functionality requires proper PHP configuration.
