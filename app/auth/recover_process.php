<?php
require_once __DIR__ . '/../../public_html/config/database.php';
require_once __DIR__ . '/../../public_html/config/security.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/recover.php');
}

// Verificar CSRF
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    error_response('CSRF token inválido');
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    error_response('Email es requerido');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_response('Email inválido');
}

$db = getDB();

// Buscar usuario
$stmt = $db->prepare("SELECT id, nombre FROM usuarios WHERE email = ? AND activo = 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Por seguridad, siempre mostrar el mismo mensaje
$message = 'Si el email existe, recibirás instrucciones para recuperar tu contraseña.';

if ($user) {
    // Generar token
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);

    // Guardar token
    $stmt = $db->prepare("
        INSERT INTO password_resets (email, token_hash, expira_en)
        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))
    ");
    $stmt->execute([$email, $tokenHash]);

    // En producción, enviar email
    // Por ahora, solo registrar en log
    error_log("Password reset token para {$email}: {$token}");

    // URL de reset
    $resetUrl = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;

    // Aquí iría el envío de email
    // mail($email, "Recuperar Contraseña - Conecta ERP", "Link: $resetUrl");

    // Registrar en auditoría
    $stmt = $db->prepare("
        INSERT INTO auditoria (usuario_id, accion, ip_address, user_agent)
        VALUES (?, 'password_reset_request', ?, ?)
    ");
    $stmt->execute([
        $user['id'],
        $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

success_response([], $message);
