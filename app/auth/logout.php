<?php
session_start();

require_once __DIR__ . '/../../public_html/config/database.php';

// Registrar logout en auditoría si hay sesión activa
if (isset($_SESSION['user_id'])) {
    $db = getDB();

    $stmt = $db->prepare("
        INSERT INTO auditoria (usuario_id, empresa_id, accion, ip_address, user_agent)
        VALUES (?, ?, 'logout', ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $_SESSION['empresa_id'] ?? null,
        $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    // Invalidar sesión en DB
    if (isset($_COOKIE['remember_token'])) {
        $tokenHash = hash('sha256', $_COOKIE['remember_token']);
        $stmt = $db->prepare("UPDATE sesiones SET activa = 0 WHERE token_hash = ?");
        $stmt->execute([$tokenHash]);

        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
}

// Destruir sesión
$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();

header('Location: /login.php');
exit;
