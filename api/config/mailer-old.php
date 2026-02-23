<?php
// Simple SMTP Email sender for OTP without external dependencies
class SimpleMailer {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        // Configure SMTP settings - update these with actual SMTP credentials
        $this->smtp_host = 'smtp.gmail.com';  // or your SMTP server
        $this->smtp_port = 587;
       $this->smtp_username = 'priyankakaranjewar567@gmail.com'; // Gmail account for SMTP auth
        $this->smtp_password = 'tfij nlxa yefq ozfd'; // Gmail App Password: Agilemail
        $this->from_email = 'noreply@cybaemtech.in'; // Display email address
        $this->from_name = 'Agile Project Management';
    }
    
    public function sendOTPEmail($to_email, $to_name, $otp) {
        // For now, use Windows mail() function with better headers
        // This requires SMTP to be configured on Windows server
        
        $subject = "Login Verification - Agile Project Management";
        $html_message = $this->createOTPEmailTemplate($to_name, $otp);
        
        // Create proper headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "X-Priority: 1\r\n";
        
        // Attempt to send email
        $success = mail($to_email, $subject, $html_message, $headers);
        
        // Log the attempt
        error_log("Email send attempt to {$to_email}: " . ($success ? 'SUCCESS' : 'FAILED'));
        
        if (!$success) {
            // Check if we can get the error
            $error = error_get_last();
            error_log("Mail error: " . print_r($error, true));
        }
        
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
}
?>
