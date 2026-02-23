<?php
// Direct Password Reset Test
header('Content-Type: text/html; charset=UTF-8');

echo "<h1>Password Reset Test</h1>";

// Replace with a test email address
$test_email = "test@example.com"; // CHANGE THIS TO YOUR EMAIL FOR TESTING

// Include necessary files
require_once 'api/config/database.php';
require_once 'api/config/mailer.php';

try {
    // Connect to database
    $database = new Database();
    $conn = $database->getConnection();
    
    // Check if test email exists in users table
    $stmt = $conn->prepare("SELECT id, email, username, full_name FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$test_email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "<p>❌ Test email '{$test_email}' not found in users table.</p>";
        echo "<p>Please change the \$test_email variable to a valid email from your users table.</p>";
        
        // Show available users for testing
        $allUsers = $conn->prepare("SELECT email, username, full_name FROM users WHERE is_active = 1 LIMIT 5");
        $allUsers->execute();
        $users = $allUsers->fetchAll();
        
        if ($users) {
            echo "<h3>Available users for testing:</h3><ul>";
            foreach ($users as $u) {
                echo "<li>" . htmlspecialchars($u['email']) . " - " . htmlspecialchars($u['full_name'] ?: $u['username']) . "</li>";
            }
            echo "</ul>";
        }
        exit;
    }
    
    echo "<p>✅ User found: " . htmlspecialchars($user['email']) . " - " . htmlspecialchars($user['full_name'] ?: $user['username']) . "</p>";
    
    // Generate new password
    function generateRandomPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }
    
    $newPassword = generateRandomPassword();
    echo "<p>Generated new password: <strong>" . htmlspecialchars($newPassword) . "</strong></p>";
    
    // Update password in database
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $passwordStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $passwordStmt->execute([$hashedPassword, $user['id']]);
    echo "<p>✅ Password updated in database</p>";
    
    // Try sending email with SMTP
    echo "<h2>Testing SMTP Email Sending:</h2>";
    
    $mailer = new SimpleMailer();
    $smtp_result = $mailer->sendPasswordResetEmail($test_email, $user['full_name'] ?: $user['username'], $newPassword);
    
    if ($smtp_result) {
        echo "<p>✅ <strong>SUCCESS!</strong> Password reset email sent via SMTP to {$test_email}</p>";
        echo "<p>Check your email inbox and spam/junk folder.</p>";
    } else {
        echo "<p>❌ SMTP sending failed. Trying fallback mail() function...</p>";
        
        // Try basic mail() function
        $subject = "Password Reset Test - Agile Project Management";
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>Password Reset Test</h2>
            <p>Hello " . htmlspecialchars($user['full_name'] ?: $user['username']) . ",</p>
            <p>Your new temporary password is: <strong>" . htmlspecialchars($newPassword) . "</strong></p>
            <p>Please login with this password and change it immediately.</p>
        </body>
        </html>";
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Agile Project Management <noreply@cybaemtech.in>\r\n";
        
        $mail_result = mail($test_email, $subject, $message, $headers);
        
        if ($mail_result) {
            echo "<p>✅ Email sent successfully using mail() function!</p>";
        } else {
            echo "<p>❌ Both SMTP and mail() function failed.</p>";
            echo "<p>Please check your mail server configuration.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>Important:</strong> Delete this file after testing for security reasons.</p>";
echo "<p><a href='mail-test.php'>Run Mail Configuration Test</a></p>";
?>
