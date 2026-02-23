<?php
// Reset OTP attempts for testing
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

$email = 'priyanka.k@cybaemtech.com';

$stmt = $conn->prepare("UPDATE users SET otp_attempts = 0, last_otp_sent = NULL WHERE email = ?");
$stmt->execute([$email]);

echo "OTP attempts reset for $email\n";
echo "You can now request OTP again.\n";
?>
