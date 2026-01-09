<?php
require_once __DIR__ . '/../../public_html/config/database.php';
require_once __DIR__ . '/../../public_html/config/security.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/login.php');
}

// Verificar CSRF
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    die('CSRF token inválido');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validar campos
if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Email y contraseña son requeridos';
    redirect('/login.php');
}

// Verificar rate limiting
if (!checkRateLimit($email)) {
    $_SESSION['error'] = 'Demasiados intentos fallidos. Intenta nuevamente en 15 minutos.';
    logLoginAttempt($email, false);
    redirect('/login.php');
}

$db = getDB();

// Buscar usuario
$stmt = $db->prepare("
    SELECT u.*, e.nombre as empresa_nombre, e.estado as empresa_estado,
           e.trial_hasta, e.pais_id, e.moneda_id, p.nombre as plan_nombre,
           i.codigo as idioma_codigo
    FROM usuarios u
    INNER JOIN empresas e ON u.empresa_id = e.id
    INNER JOIN planes p ON e.plan_id = p.id
    LEFT JOIN idiomas i ON u.idioma_id = i.id
    WHERE u.email = ? AND u.activo = 1
");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    logLoginAttempt($email, false);
    $_SESSION['error'] = 'Credenciales inválidas';
    redirect('/login.php');
}

// Login exitoso
logLoginAttempt($email, true);

// Crear sesión
$_SESSION['user_id'] = $user['id'];
$_SESSION['empresa_id'] = $user['empresa_id'];
$_SESSION['empresa_nombre'] = $user['empresa_nombre'];
$_SESSION['empresa_estado'] = $user['empresa_estado'];
$_SESSION['trial_hasta'] = $user['trial_hasta'];
$_SESSION['plan'] = $user['plan_nombre'];
$_SESSION['usuario_nombre'] = $user['nombre'];
$_SESSION['usuario_email'] = $user['email'];
$_SESSION['pais_id'] = $user['pais_id'];
$_SESSION['moneda_id'] = $user['moneda_id'];
$_SESSION['idioma_codigo'] = $user['idioma_codigo'] ?? 'es';
$_SESSION['is_superuser'] = ($user['es_superusuario'] == 1);
$_SESSION['authenticated'] = true;
$_SESSION['last_activity'] = time();

// Guardar sesión en DB
$sessionToken = bin2hex(random_bytes(32));
$sessionHash = hash('sha256', $sessionToken);

$stmt = $db->prepare("
    INSERT INTO sesiones (usuario_id, empresa_id, token_hash, ip_address, user_agent, expira_en)
    VALUES (?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
");
$stmt->execute([
    $user['id'],
    $user['empresa_id'],
    $sessionHash,
    $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
    $_SERVER['HTTP_USER_AGENT'] ?? ''
]);

if ($remember) {
    setcookie('remember_token', $sessionToken, time() + (86400 * 30), '/', '', true, true);
}

// Registrar en auditoría
$stmt = $db->prepare("
    INSERT INTO auditoria (usuario_id, empresa_id, accion, ip_address, user_agent)
    VALUES (?, ?, 'login', ?, ?)
");
$stmt->execute([
    $user['id'],
    $user['empresa_id'],
    $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
    $_SERVER['HTTP_USER_AGENT'] ?? ''
]);

// Regenerar ID de sesión
session_regenerate_id(true);

// Redireccionar
$redirect = $_GET['redirect'] ?? '/app/dashboard/dashboard.php';
redirect($redirect);
