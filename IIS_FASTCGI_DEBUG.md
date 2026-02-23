# IIS FastCGI Configuration Check

## Current Status
- ✅ PHP CLI working (version 8.4.15)
- ✅ Required modules loaded (mysqli, pdo_mysql, openssl, mbstring)
- ✅ Permissions set correctly
- ✅ IIS handlers configured
- ❌ PHP not executing through IIS (still returning 500 errors)

## Next Steps to Try

### 1. Check FastCGI Configuration
Run in PowerShell as Administrator:
```powershell
C:\Windows\System32\inetsrv\appcmd.exe list config -section:system.webServer/fastCgi
```

### 2. Verify Handler Configuration
```powershell
C:\Windows\System32\inetsrv\appcmd.exe list config -section:system.webServer/handlers
```

### 3. Enable Detailed Error Messages
Add to web.config:
```xml
<httpErrors errorMode="Detailed" />
```

### 4. Check Event Logs
- Open Event Viewer
- Check Windows Logs > Application
- Look for FastCGI or PHP errors

### 5. Alternative: Enable CGI Instead of FastCGI
If FastCGI continues to fail, try regular CGI:
- In IIS Manager > ISAPI and CGI Restrictions
- Add C:\php\php-cgi.exe and set to Allowed

## Manual IIS Manager Steps
If PowerShell commands fail:
1. Open IIS Manager
2. Go to your site > Handler Mappings
3. Add Module Mapping:
   - Request path: *.php
   - Module: FastCgiModule
   - Executable: C:\php\php-cgi.exe
   - Name: PHP-FastCGI
4. In FastCGI Settings:
   - Add application: C:\php\php-cgi.exe
   - Set Environment Variables if needed

The application is working with error handling, but full functionality requires PHP execution to work properly.
