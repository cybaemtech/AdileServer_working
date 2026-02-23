# Email Troubleshooting Guide for Forgot Password Feature

## Problem: Forgot Password Emails Not Being Received

### What Was Fixed:

1. **Updated Authentication System to Use SMTP**
   - Modified `api/auth.php` to use the `SimpleMailer` class instead of basic `mail()` function
   - Added fallback to basic mail() if SMTP fails
   - Enhanced error logging for debugging

2. **Updated Email Verification System**
   - Modified `api/email-verification.php` to use SMTP for OTP emails
   - Same fallback mechanism as password reset

3. **Enhanced SMTP Mailer Class**
   - Added `sendPasswordResetEmail()` method to handle password reset emails
   - Created professional HTML email template for password resets
   - Improved error handling and logging

### Current Email Configuration:

**SMTP Settings (from `api/config/mailer.php`):**
- **Host:** smtp.gmail.com
- **Port:** 587 (TLS)
- **Username:** priyankakaranjewar567@gmail.com
- **Password:** tfij nlxa yefq ozfd (App Password)
- **From Email:** noreply@cybaemtech.in
- **From Name:** Agile Project Management

### Testing Steps:

1. **Test Mail Configuration**
   - Navigate to: `http://your-domain.com/Agile/mail-test.php`
   - This will show you PHP mail settings and test basic functionality

2. **Test Forgot Password Feature**
   - Go to your login page
   - Click "Forgot Password"
   - Enter a valid email address from your users table
   - Check both inbox and spam/junk folders

3. **Check Error Logs**
   - Windows IIS: Check `C:\inetpub\logs\LogFiles\`
   - PHP Error Log: Check the location shown in `mail-test.php`
   - Look for SMTP connection errors or authentication failures

### Common Issues and Solutions:

#### 1. Gmail App Password Issues
**Problem:** SMTP authentication fails
**Solution:**
- Ensure 2FA is enabled on the Gmail account
- Generate a new App Password specifically for this application
- Update the password in `api/config/mailer.php`

#### 2. Firewall Blocking SMTP
**Problem:** Cannot connect to smtp.gmail.com:587
**Solution:**
- Check Windows Firewall settings
- Ensure outbound port 587 is allowed
- Contact your hosting provider if on shared hosting

#### 3. PHP mail() Function Not Configured
**Problem:** Basic mail() fallback fails
**Solution:**
- Configure SMTP in `php.ini`:
  ```ini
  [mail function]
  SMTP = smtp.gmail.com
  smtp_port = 587
  sendmail_from = noreply@cybaemtech.in
  ```

#### 4. Email Going to Spam
**Problem:** Emails are sent but go to spam folder
**Solutions:**
- Check SPF, DKIM, and DMARC records for your domain
- Use a dedicated SMTP service like SendGrid or Amazon SES
- Improve email content to avoid spam triggers

#### 5. IIS/Windows Server Issues
**Problem:** PHP mail functions not working on Windows
**Solution:**
- Install a local SMTP service or use IIS SMTP feature
- Configure PHP to use local SMTP server
- Or use the SMTP mailer class (which we've implemented)

### Immediate Actions to Take:

1. **Test the new configuration:**
   ```bash
   # Navigate to your test file
   http://your-domain.com/Agile/mail-test.php
   ```

2. **Check if the Gmail account is working:**
   - Log into priyankakaranjekar567@gmail.com
   - Verify 2FA is enabled
   - Generate a fresh App Password if needed

3. **Update SMTP credentials if needed:**
   - Edit `api/config/mailer.php`
   - Update username/password with working credentials
   - Consider using a business email account instead of personal Gmail

4. **Monitor error logs:**
   - Check PHP error logs after testing
   - Look for SMTP connection errors
   - Verify authentication is working

### Alternative Email Providers:

If Gmail continues to have issues, consider these alternatives:

1. **SendGrid** (Recommended for production)
   - 100 emails/day free
   - Better deliverability
   - Professional email service

2. **Amazon SES**
   - Pay-per-use
   - High deliverability
   - Good for AWS-hosted applications

3. **Mailgun**
   - 10,000 emails/month free for first 3 months
   - Good API and SMTP options

4. **Microsoft 365/Outlook**
   - If you have business Microsoft account
   - SMTP: smtp-mail.outlook.com:587

### Files Modified:

1. `api/auth.php` - Added SMTP mailer support for password reset
2. `api/email-verification.php` - Added SMTP mailer support for OTP
3. `api/config/mailer.php` - Added password reset email functionality
4. `mail-test.php` - Created for testing mail configuration

### Next Steps:

1. Test the mail configuration using the test file
2. Try the forgot password feature
3. Check error logs if issues persist
4. Consider switching to a professional email service if Gmail continues to have problems
5. Remove the `mail-test.php` file once testing is complete (for security)

### Security Note:
The `mail-test.php` file contains sensitive information and should be deleted after testing is complete.
