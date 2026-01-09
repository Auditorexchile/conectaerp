<?php
/**
 * Funciones de Seguridad - Antihacking
 */

// Iniciar sesión segura
function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_only_cookies', 1);
        session_start();
    }
}

// Generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validar token CSRF
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitizar input
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hash de contraseña
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID);
}

// Verificar contraseña
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Protección XSS
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Validar fortaleza de contraseña
function validatePasswordStrength($password) {
    if (strlen($password) < 8) {
        return ['valid' => false, 'error' => 'Contraseña debe tener mínimo 8 caracteres'];
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'error' => 'Debe contener al menos una mayúscula'];
    }
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'error' => 'Debe contener al menos una minúscula'];
    }
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'error' => 'Debe contener al menos un número'];
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return ['valid' => false, 'error' => 'Debe contener al menos un símbolo'];
    }
    return ['valid' => true];
}

// Registrar intento de login
function logLoginAttempt($email, $success, $ip) {
    $db = Database::getInstance();
    $db->query(
        "INSERT INTO login_intentos (email, exitoso, ip_address, fecha) VALUES (?, ?, ?, NOW())",
        [$email, $success ? 1 : 0, $ip]
    );
}

// Verificar intentos fallidos
function checkLoginAttempts($email) {
    $db = Database::getInstance();
    $result = $db->fetchOne(
        "SELECT COUNT(*) as intentos FROM login_intentos
         WHERE email = ? AND exitoso = 0 AND fecha > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
        [$email]
    );
    return $result['intentos'] >= 5;
}

// Obtener IP real del cliente
function getRealIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
