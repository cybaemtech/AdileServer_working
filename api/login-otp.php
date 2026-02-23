<?php
// Login with OTP verification API handler
// NOTE: When included via api/index.php, session/CORS/headers are already handled.
// When called directly via api/login-otp/send-otp.php, we handle them here.

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Resolve config directory (works whether called directly or via index.php include)
$_apiConfigDir = __DIR__;
if (!file_exists($_apiConfigDir . '/config/cors.php') && file_exists($_apiConfigDir . '/../config/cors.php')) {
    $_apiConfigDir = realpath($_apiConfigDir . '/..');
}

// Only load CORS/headers if not already loaded (avoid duplicate headers)
if (!function_exists('setCorsHeaders')) {
    require_once $_apiConfigDir . '/config/cors.php';
    setCorsHeaders();
}

// Load database and mailer (safe to require_once multiple times)
require_once $_apiConfigDir . '/config/database.php';
require_once $_apiConfigDir . '/config/mailer.php';

// Ensure Content-Type is set
if (!headers_sent()) {
    header('Content-Type: application/json');
}

$database = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// Handle OPTIONS preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Determine which sub-path to handle
// Priority: AGILE_API_PATH (set by index.php or send-otp.php stub) > REQUEST_URI parse
$path = '';

if (!empty($_SERVER['AGILE_API_PATH']) && $_SERVER['AGILE_API_PATH'] !== '/') {
    $path = $_SERVER['AGILE_API_PATH'];
}

// Parse from REQUEST_URI as fallback
if ($path === '' && isset($_SERVER['REQUEST_URI'])) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    if (preg_match('~/api/login-otp(/[^?]*)?$~i', $uriPath, $m)) {
        $path = $m[1] ?? '/';
    }
}

// Default fallback based on HTTP method
if ($path === '' || $path === '/') {
    $path = ($method === 'POST') ? '/send-otp' : '/';
}

$path = rtrim($path, '/');
if ($path === '') $path = '/';

// Dispatch
switch ($method . ':' . $path) {
    case 'POST:/send-otp':
        sendLoginOTP($conn);
        break;

    case 'POST:/verify-login':
        verifyOTPAndLogin($conn);
        break;

    default:
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint not found', 'path' => $path, 'method' => $method]);
        break;
}

// ─────────────────────────────────────────────────────────────
// FUNCTION: sendLoginOTP
// ─────────────────────────────────────────────────────────────
function sendLoginOTP($conn) {
    $debugLog = "C:/inetpub/wwwroot/Agile/dist/send-otp-debug.log";

    $inputData = @file_get_contents('php://input');
    $input = null;

    file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "SendLoginOTP - Raw input length: " . strlen($inputData) . "\n", FILE_APPEND);
    file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "SendLoginOTP - Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set') . "\n", FILE_APPEND);
    file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "SendLoginOTP - AGILE_API_PATH: " . ($_SERVER['AGILE_API_PATH'] ?? 'not set') . "\n", FILE_APPEND);

    if (!empty($inputData)) {
        $input = json_decode($inputData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "JSON decode error: " . json_last_error_msg() . "\n", FILE_APPEND);
            http_response_code(400);
            echo json_encode(['message' => 'Invalid JSON data: ' . json_last_error_msg()]);
            exit;
        }
    }

    // Fallback to $_POST if JSON failed
    if (empty($input)) {
        $input = $_POST;
    }

    $email    = isset($input['email'])    ? trim($input['email'])    : '';
    $password = isset($input['password']) ? trim($input['password']) : '';

    file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "Parsed email: $email, password length: " . strlen($password) . "\n", FILE_APPEND);

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            'message' => 'Email and password are required',
            'debug'   => [
                'email_empty'       => empty($email),
                'password_empty'    => empty($password),
                'input_data_length' => strlen($inputData ?? ''),
                'content_type'      => $_SERVER['CONTENT_TYPE'] ?? 'not set',
            ]
        ]);
        exit;
    }

    if (!$conn) {
        file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "Database connection failed\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['message' => 'Database connection failed']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT id, email, full_name, username, password, last_otp_sent, otp_attempts FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "User found: " . ($user ? 'yes' : 'no') . "\n", FILE_APPEND);

        if (!$user) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "Password mismatch\n", FILE_APPEND);
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            exit;
        }

        // Rate limiting check
        if ($user['last_otp_sent']) {
            $lastSent = new DateTime($user['last_otp_sent']);
            $now      = new DateTime();
            $interval = $now->diff($lastSent);

            if ($interval->i < 2 && $interval->h == 0 && $interval->days == 0) {
                http_response_code(429);
                echo json_encode(['message' => 'Please wait 2 minutes before requesting another OTP']);
                exit;
            }

            if ($interval->h >= 1 || $interval->days > 0) {
                $conn->prepare("UPDATE users SET otp_attempts = 0 WHERE id = ?")->execute([$user['id']]);
                $user['otp_attempts'] = 0;
            }
        }

        if ($user['otp_attempts'] >= 3) {
            http_response_code(429);
            echo json_encode(['message' => 'Maximum OTP attempts exceeded. Please try again after 1 hour']);
            exit;
        }

        // Generate OTP
        $otp       = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Store OTP in session
        $_SESSION['login_otp']        = $otp;
        $_SESSION['login_otp_expiry'] = $otpExpiry;
        $_SESSION['login_email']      = $email;
        $_SESSION['login_user_id']    = $user['id'];

        // Save OTP to database
        $conn->prepare("UPDATE users SET otp_code = ?, otp_expires = ?, last_otp_sent = ?, otp_attempts = otp_attempts + 1 WHERE id = ?")
             ->execute([$otp, $otpExpiry, date('Y-m-d H:i:s'), $user['id']]);

        file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "OTP generated: $otp for $email\n", FILE_APPEND);

        // Send OTP email
        $emailSent = false;
        try {
            $mailer    = new SimpleMailer();
            $emailSent = $mailer->sendOTPEmail($email, $user['full_name'] ?: $user['username'], $otp);
        } catch (Exception $mailEx) {
            error_log("Mailer exception: " . $mailEx->getMessage());
        }

        if ($emailSent) {
            echo json_encode([
                'success'          => true,
                'message'          => 'OTP sent successfully to your email address',
                'expires_in_minutes' => 10
            ]);
        } else {
            echo json_encode([
                'success'          => true,
                'message'          => 'OTP generated. Check email (or server log if email not configured).',
                'expires_in_minutes' => 10,
                'debug_otp'        => $otp   // Remove in production
            ]);
        }
        exit;

    } catch (PDOException $e) {
        error_log("Send OTP DB error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        error_log("Send OTP error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

// ─────────────────────────────────────────────────────────────
// FUNCTION: verifyOTPAndLogin
// ─────────────────────────────────────────────────────────────
function verifyOTPAndLogin($conn) {
    $inputData = @file_get_contents('php://input');
    $input     = json_decode($inputData, true);
    $email     = isset($input['email'])    ? trim($input['email'])    : '';
    $password  = isset($input['password']) ? trim($input['password']) : '';
    $otp       = isset($input['otp'])      ? trim($input['otp'])      : '';

    if (empty($email) || empty($password) || empty($otp)) {
        http_response_code(400);
        echo json_encode(['message' => 'Email, password and OTP are required']);
        exit;
    }

    if (!$conn) {
        http_response_code(500);
        echo json_encode(['message' => 'Database connection failed']);
        exit;
    }

    try {
        // Verify OTP from session
        if (!isset($_SESSION['login_otp']) || !isset($_SESSION['login_email']) ||
            $_SESSION['login_email'] !== $email || $_SESSION['login_otp'] !== $otp) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid or expired OTP']);
            exit;
        }

        // Check OTP expiry
        $otpExpiry = new DateTime($_SESSION['login_otp_expiry']);
        $now       = new DateTime();
        if ($now > $otpExpiry) {
            unset($_SESSION['login_otp'], $_SESSION['login_otp_expiry'],
                  $_SESSION['login_email'], $_SESSION['login_user_id']);
            http_response_code(400);
            echo json_encode(['message' => 'OTP has expired. Please request a new OTP']);
            exit;
        }

        // Verify credentials
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            exit;
        }

        // Clear OTP data
        $conn->prepare("UPDATE users SET otp_code = NULL, otp_expires = NULL, otp_attempts = 0 WHERE id = ?")
             ->execute([$user['id']]);
        unset($_SESSION['login_otp'], $_SESSION['login_otp_expiry'],
              $_SESSION['login_email'], $_SESSION['login_user_id']);

        // Update last login
        $conn->prepare("UPDATE users SET last_login = ? WHERE id = ?")
             ->execute([date('Y-m-d H:i:s'), $user['id']]);

        // Set session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['user_role'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user'    => [
                'id'        => $user['id'],
                'username'  => $user['username'],
                'email'     => $user['email'],
                'fullName'  => $user['full_name'],
                'role'      => $user['user_role'],
                'avatarUrl' => $user['avatar_url']
            ]
        ]);
        exit;

    } catch (PDOException $e) {
        error_log("Verify OTP DB error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        error_log("Verify OTP error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

// Flush output buffer if active
if (ob_get_level()) {
    ob_end_flush();
}
?>