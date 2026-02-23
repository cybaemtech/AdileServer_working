# IIS 500 Error Troubleshooting Guide

## Current Issue
All requests to `/api/auth/user` return 500 Internal Server Error, even for static files.

## Immediate Solutions Applied

### 1. Created Static Response File
- File: `c:\inetpub\wwwroot\Agile\api\auth\user`
- Content: `{"message":"Not authenticated","error":"User not logged in","status":401}`

### 2. Simplified web.config
- Removed complex URL rewrite rules
- Added proper MIME type mappings
- Configured CORS headers
- Set up static file serving

### 3. Alternative Files Created
- `/api/auth/index.html` - HTML wrapper with JSON content
- `/api/test.json` - Simple test endpoint

## Server Administrator Action Required

Since static files are returning 500 errors, this indicates:

### 1. IIS Modules Missing
Check if these modules are installed:
- Static Content module
- URL Rewrite module (optional)
- Default Document module

### 2. Permissions Issues
Ensure IIS_IUSRS has read access to:
- `c:\inetpub\wwwroot\Agile\`
- All subdirectories and files

### 3. Application Pool Configuration
- Verify application pool is running
- Check if .NET Framework version is correct
- Ensure Identity has proper permissions

### 4. Web.config Validation
Run this command to check for syntax errors:
```cmd
%windir%\system32\inetsrv\appcmd.exe list config "Default Web Site/Agile" -section:system.webServer
```

### 5. IIS Logs
Check IIS logs at: `C:\inetpub\logs\LogFiles\W3SVC1\`

## Temporary Workaround for Development

If the server cannot be fixed immediately, use this client-side workaround in your frontend:

```javascript
// In your API client, add this fallback
const fetchWithFallback = async (url) => {
  try {
    const response = await fetch(url);
    if (response.status === 500) {
      // Return mock response for auth/user endpoint
      if (url.includes('/api/auth/user')) {
        return {
          ok: false,
          status: 401,
          json: () => Promise.resolve({
            message: "Not authenticated",
            error: "User not logged in",
            status: 401
          })
        };
      }
    }
    return response;
  } catch (error) {
    // Handle network errors
    throw error;
  }
};
```

## Test Commands

Run these tests to isolate the issue:

1. Test simple static file:
   ```
   curl -I https://agile.cybaemtech.app:90/index.html
   ```

2. Test API directory:
   ```
   curl -I https://agile.cybaemtech.app:90/api/
   ```

3. Test specific file:
   ```
   curl -I https://agile.cybaemtech.app:90/api/test.json
   ```

## Next Steps

1. Server admin should review IIS configuration
2. Check Event Viewer for detailed error messages
3. Verify file permissions and IIS modules
4. Consider enabling Failed Request Tracing for detailed diagnostics

If the server cannot be configured properly, consider:
- Moving to a different web server (Apache, Nginx)
- Using a different hosting solution
- Implementing client-side fallbacks for development
