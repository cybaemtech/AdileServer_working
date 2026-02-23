<?php
// SMTP Email sender for OTP using socket connection
class SimpleMailer {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        // Configure SMTP settings - Gmail App Password for Ganesh.Kale@cybaemtech.com
        $this->smtp_host = 'smtp.gmail.com';
        $this->smtp_port = 587; // TLS port
        $this->smtp_username = 'priyankakaranjewar567@gmail.com'; // Gmail account for SMTP auth
        $this->smtp_password = 'tfij nlxa yefq ozfd'; // Gmail App Password: Agilemail
        $this->from_email = 'noreply@cybaemtech.in'; // Display email address
        $this->from_name = 'Agile Project Management';
    }
    
    public function sendOTPEmail($to_email, $to_name, $otp) {
        // Try SMTP first, fallback to mail() if needed
        $smtp_success = $this->sendViaSMTP($to_email, $to_name, $otp);
        
        if ($smtp_success) {
            error_log("Email sent successfully via SMTP to {$to_email}");
            return true;
        }
        
        // Fallback to mail() function
        error_log("SMTP failed, trying mail() function");
        return $this->sendViaMailFunction($to_email, $to_name, $otp);
    }
    
    public function sendPasswordResetEmail($to_email, $to_name, $new_password) {
        // Try SMTP first, fallback to mail() if needed
        $smtp_success = $this->sendPasswordResetViaSMTP($to_email, $to_name, $new_password);
        
        if ($smtp_success) {
            error_log("Password reset email sent successfully via SMTP to {$to_email}");
            return true;
        }
        
        // Fallback to mail() function
        error_log("SMTP failed for password reset, trying mail() function");
        return $this->sendPasswordResetViaMailFunction($to_email, $to_name, $new_password);
    }
    
    private function sendViaSMTP($to_email, $to_name, $otp) {
        try {
            // Create socket connection
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            $socket = stream_socket_client("tcp://{$this->smtp_host}:{$this->smtp_port}", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            
            if (!$socket) {
                error_log("SMTP Connection failed: {$errno} - {$errstr}");
                return false;
            }
            
            // Read initial response
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '220') {
                error_log("SMTP Initial response failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // EHLO
            fputs($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n");
            $this->readResponse($socket); // Read all EHLO responses
            
            // STARTTLS
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '220') {
                error_log("SMTP STARTTLS failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Enable crypto
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                error_log("SMTP TLS encryption failed");
                fclose($socket);
                return false;
            }
            
            // EHLO again after TLS
            fputs($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n");
            $this->readResponse($socket); // Read all EHLO responses
            
            // AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '334') {
                error_log("SMTP AUTH LOGIN failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Username
            fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '334') {
                error_log("SMTP Username failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Password
            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '235') {
                error_log("SMTP Password failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // MAIL FROM - Use the authenticated Gmail account
            fputs($socket, "MAIL FROM: <{$this->smtp_username}>\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '250') {
                error_log("SMTP MAIL FROM failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // RCPT TO
            fputs($socket, "RCPT TO: <{$to_email}>\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '250') {
                error_log("SMTP RCPT TO failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // DATA
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '354') {
                error_log("SMTP DATA failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Email content
            $subject = "Login Verification - Agile Project Management";
            $html_message = $this->createOTPEmailTemplate($to_name, $otp);
            
            $email_content = "From: {$this->from_name} <{$this->from_email}>\r\n";
            $email_content .= "To: {$to_name} <{$to_email}>\r\n";
            $email_content .= "Subject: {$subject}\r\n";
            $email_content .= "MIME-Version: 1.0\r\n";
            $email_content .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_content .= "\r\n";
            $email_content .= $html_message . "\r\n";
            $email_content .= ".\r\n";
            
            fputs($socket, $email_content);
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '250') {
                error_log("SMTP Email content failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            return true;
            
        } catch (Exception $e) {
            error_log("SMTP Exception: " . $e->getMessage());
            return false;
        }
    }
    
    private function readResponse($socket) {
        $response = '';
        while (true) {
            $line = fgets($socket, 512);
            $response .= $line;
            if (substr($line, 3, 1) === ' ') break; // Last line of multi-line response
        }
        return $response;
    }
    
    private function sendViaMailFunction($to_email, $to_name, $otp) {
        $subject = "Login Verification - Agile Project Management";
        $html_message = $this->createOTPEmailTemplate($to_name, $otp);
        
        // Create proper headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "X-Priority: 1\r\n";
        
        $success = mail($to_email, $subject, $html_message, $headers);
        error_log("Mail function result for {$to_email}: " . ($success ? 'SUCCESS' : 'FAILED'));
        
        return $success;
    }
    
    private function sendPasswordResetViaSMTP($to_email, $to_name, $new_password) {
        try {
            // Create socket connection
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            $socket = stream_socket_client("tcp://{$this->smtp_host}:{$this->smtp_port}", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            
            if (!$socket) {
                error_log("SMTP Connection failed: {$errno} - {$errstr}");
                return false;
            }
            
            // Read initial response
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '220') {
                error_log("SMTP Initial response failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // EHLO
            fputs($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n");
            $this->readResponse($socket); // Read all EHLO responses
            
            // STARTTLS
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '220') {
                error_log("SMTP STARTTLS failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Enable crypto
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                error_log("SMTP TLS encryption failed");
                fclose($socket);
                return false;
            }
            
            // EHLO again after TLS
            fputs($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n");
            $this->readResponse($socket); // Read all EHLO responses
            
            // AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '334') {
                error_log("SMTP AUTH LOGIN failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Username
            fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '334') {
                error_log("SMTP Username failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Password
            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '235') {
                error_log("SMTP Password failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // MAIL FROM - Use the authenticated Gmail account
            fputs($socket, "MAIL FROM: <{$this->smtp_username}>\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '250') {
                error_log("SMTP MAIL FROM failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // RCPT TO
            fputs($socket, "RCPT TO: <{$to_email}>\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '250') {
                error_log("SMTP RCPT TO failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // DATA
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '354') {
                error_log("SMTP DATA failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Email content
            $subject = "Password Reset - Agile Project Management";
            $html_message = $this->createPasswordResetEmailTemplate($to_name, $new_password);
            
            $email_content = "From: {$this->from_name} <{$this->from_email}>\r\n";
            $email_content .= "To: {$to_name} <{$to_email}>\r\n";
            $email_content .= "Subject: {$subject}\r\n";
            $email_content .= "MIME-Version: 1.0\r\n";
            $email_content .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_content .= "\r\n";
            $email_content .= $html_message . "\r\n";
            $email_content .= ".\r\n";
            
            fputs($socket, $email_content);
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '250') {
                error_log("SMTP Email content failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            return true;
            
        } catch (Exception $e) {
            error_log("SMTP Exception: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendPasswordResetViaMailFunction($to_email, $to_name, $new_password) {
        $subject = "Password Reset - Agile Project Management";
        $html_message = $this->createPasswordResetEmailTemplate($to_name, $new_password);
        
        // Create proper headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "X-Priority: 1\r\n";
        
        $success = mail($to_email, $subject, $html_message, $headers);
        error_log("Mail function result for password reset to {$to_email}: " . ($success ? 'SUCCESS' : 'FAILED'));
        
        return $success;
    }
    
    private function createOTPEmailTemplate($userName, $otp) {
        return "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Login Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .otp-box { background: #f8f9ff; border: 2px dashed #667eea; border-radius: 10px; padding: 25px; text-align: center; margin: 20px 0; }
        .otp { font-size: 36px; font-weight: bold; color: #667eea; letter-spacing: 8px; margin: 10px 0; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔐 Login Verification</h1>
            <p>Agile Project Management</p>
        </div>
        <div class='content'>
            <h2>Hello " . htmlspecialchars($userName) . ",</h2>
            <p>A sign-in attempt requires verification. Please use the code below to complete your login:</p>
            
            <div class='otp-box'>
                <p style='margin: 0; font-size: 16px; color: #666; margin-bottom: 10px;'>Your verification code is:</p>
                <div class='otp'>" . htmlspecialchars($otp) . "</div>
                <p style='margin: 10px 0 0 0; font-size: 14px; color: #888;'>Valid for 10 minutes</p>
            </div>
            
            <div class='warning'>
                <strong>🛡️ Security Notice:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>This code is valid for <strong>10 minutes only</strong></li>
                    <li>Do not share this code with anyone</li>
                    <li>If you didn't attempt to sign in, please ignore this email</li>
                </ul>
            </div>
            
            <div class='footer'>
                <p>This is an automated security message.<br>Please do not reply to this email.</p>
                <p style='margin-top: 15px;'><strong>Cybaem Technology</strong></p>
            </div>
        </div>
    </div>
</body>
</html>";
    }
    
    private function createPasswordResetEmailTemplate($userName, $newPassword) {
        return "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Password Reset</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .password-box { background: #f8f9ff; border: 2px dashed #667eea; border-radius: 10px; padding: 25px; text-align: center; margin: 20px 0; }
        .password { font-size: 24px; font-weight: bold; color: #667eea; letter-spacing: 2px; margin: 10px 0; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔐 Password Reset</h1>
            <p>Agile Project Management</p>
        </div>
        <div class='content'>
            <h2>Hello " . htmlspecialchars($userName) . ",</h2>
            <p>Your password has been reset as requested. Here is your new temporary password:</p>
            
            <div class='password-box'>
                <p style='margin: 0; font-size: 16px; color: #666; margin-bottom: 10px;'>Your new password is:</p>
                <div class='password'>" . htmlspecialchars($newPassword) . "</div>
            </div>
            
            <div class='warning'>
                <strong>🛡️ Important:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Please log in with this new password immediately</li>
                    <li>Change this password to something memorable after logging in</li>
                    <li>Go to your profile settings and click 'Change Password'</li>
                    <li>If you didn't request this reset, contact support immediately</li>
                </ul>
            </div>
            
            <div class='footer'>
                <p>This is an automated security message.<br>Please do not reply to this email.</p>
                <p style='margin-top: 15px;'><strong>Cybaem Technology</strong></p>
            </div>
        </div>
    </div>
</body>
</html>";
    }
}
?>
