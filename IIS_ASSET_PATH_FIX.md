# IIS Deployment Fix - Asset Path Issue

## Problem
The application was deployed to IIS at `https://agile.cybaemtech.app:90/` but all JavaScript and CSS assets were returning 404 errors:
- `/Agile/assets/index-*.js` - 404 Not Found
- `/Agile/assets/vendor-*.js` - 404 Not Found
- `/Agile/assets/ui-*.js` - 404 Not Found
- etc.

## Root Cause
The Vite build configuration had `base: '/Agile'` for production builds, which was prepending `/Agile` to all asset paths. However, since the IIS application is already running from the root domain (agile.cybaemtech.app:90), the assets should be at `/assets/` not `/Agile/assets/`.

## Solution Applied

### 1. Updated Vite Configuration
**File:** `vite.config.ts`

Changed from:
```typescript
base: process.env.NODE_ENV === 'production' ? '/Agile' : '/',
```

To:
```typescript
base: '/',
```

This ensures all assets are built with root-relative paths.

### 2. Rebuilt the Frontend
Ran the build command to regenerate the dist folder with correct paths:
```powershell
npm run build
```

### 3. Created web.config for IIS
**File:** `dist/web.config`

Added IIS URL rewrite rules to:
- Route API calls to `../api/` folder
- Serve static assets directly
- Handle React client-side routing (SPA)
- Configure proper MIME types for JavaScript
- Enable CORS headers
- Handle 404 errors by serving index.html

## Verification

After the fix, the `dist/index.html` now contains correct paths:
```html
<script type="module" crossorigin src="/assets/index-4EKo8AAX.js"></script>
<link rel="modulepreload" crossorigin href="/assets/vendor-B_9pl2FA.js">
<link rel="stylesheet" crossorigin href="/assets/index-D8-PLJv8.css">
```

## Testing
1. Navigate to `https://agile.cybaemtech.app:90/`
2. All JavaScript and CSS files should now load successfully
3. The application should render properly
4. Client-side routing should work (e.g., navigating to `/teams` should work)

## Files Modified
1. `vite.config.ts` - Changed base path
2. `dist/` folder - Rebuilt with new configuration
3. `dist/web.config` - Created for IIS configuration

## Date Fixed
February 20, 2026
