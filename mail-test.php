<?php
// Mail Configuration Test
header('Content-Type: text/html; charset=UTF-8');

echo "<h1>Mail Configuration Test</h1>";

// Check if mail function is available
echo "<h2>1. PHP Mail Function Status:</h2>";
if (function_exists('mail')) {
    echo "✅ mail() function is available<br>";
} else {
    echo "❌ mail() function is NOT available<br>";
}

// Check PHP mail configuration
echo "<h2>2. PHP Mail Configuration:</h2>";
echo "SMTP: " . (ini_get('SMTP') ?: 'Not set') . "<br>";
echo "smtp_port: " . (ini_get('smtp_port') ?: 'Not set') . "<br>";
echo "sendmail_from: " . (ini_get('sendmail_from') ?: 'Not set') . "<br>";
echo "sendmail_path: " . (ini_get('sendmail_path') ?: 'Not set') . "<br>";

// Test basic mail sending
echo "<h2>3. Test Email Sending:</h2>";

$test_email = "test@example.com"; // Replace with your email for testing
$subject = "Mail Test from Agile Project";
$message = "This is a test email to check if mail configuration is working.";
$headers = "From: noreply@cybaemtech.in\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

echo "Attempting to send test email...<br>";
$result = mail($test_email, $subject, $message, $headers);

if ($result) {
    echo "✅ mail() function executed successfully (but check if email was actually received)<br>";
} else {
    echo "❌ mail() function failed to execute<br>";
}

// Check if we can include the SMTP mailer
echo "<h2>4. SMTP Mailer Test:</h2>";
if (file_exists('api/config/mailer.php')) {
    echo "✅ SMTP Mailer file exists<br>";
    require_once 'api/config/mailer.php';
    
    try {
        $mailer = new SimpleMailer();
        echo "✅ SimpleMailer class instantiated successfully<br>";
        
        // Test SMTP connection (you can uncomment this to test actual sending)
        /*
        $smtp_result = $mailer->sendOTPEmail($test_email, "Test User", "123456");
        if ($smtp_result) {
            echo "✅ SMTP email sent successfully<br>";
        } else {
            echo "❌ SMTP email failed<br>";
        }
        */
        echo "⏸️ SMTP sending test is commented out. Uncomment the lines in this file to test actual sending.<br>";
        
    } catch (Exception $e) {
        echo "❌ Error with SimpleMailer: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ SMTP Mailer file not found<br>";
}

echo "<h2>5. Error Log Check:</h2>";
echo "Check your PHP error log for any mail-related errors.<br>";
echo "Error log location: " . (ini_get('error_log') ?: 'System default') . "<br>";

?>
