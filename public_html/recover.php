<?php
/**
 * RECOVER.PHP - Recuperación de contraseña
 * Sistema seguro con tokens temporales
 */

require_once 'config/database.php';
require_once 'config/security.php';

initSecureSession();

$error = '';
$success = '';
$step = $_GET['step'] ?? 'request'; // request | reset
$token = $_GET['token'] ?? '';

// Procesar solicitud de recuperación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'request') {
    try {
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            throw new Exception('Token de seguridad inválido');
        }

        $email = sanitize($_POST['email'] ?? '');

        if (!validateEmail($email)) {
            throw new Exception('Email inválido');
        }

        $db = Database::getInstance();

        // Buscar usuario
        $usuario = $db->fetchOne(
            "SELECT id, email, nombres FROM usuarios WHERE email = ? AND activo = 1",
            [$email]
        );

        // Por seguridad, siempre mostramos el mismo mensaje
        // (no revelamos si el email existe o no)
        $success = 'Si el email existe en nuestro sistema, recibirás un enlace de recuperación en los próximos minutos.';

        if ($usuario) {
            // Generar token único
            $token = bin2hex(random_bytes(32));
            $expira_en = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Guardar token
            $db->insert('password_resets', [
                'usuario_id' => $usuario['id'],
                'token' => hash('sha256', $token),
                'expira_en' => $expira_en,
                'usado' => 0,
                'ip_address' => getRealIP()
            ]);

            // TODO: Enviar email con enlace
            // $enlace = "https://tudominio.com/recover.php?step=reset&token=$token";
            // send_email($email, "Recuperar contraseña", $enlace);

            // Log para desarrollo (REMOVER en producción)
            error_log("Token recuperación para $email: $token");
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Procesar reset de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'reset') {
    try {
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            throw new Exception('Token de seguridad inválido');
        }

        $token = sanitize($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (!$token) {
            throw new Exception('Token inválido');
        }

        // Validar contraseña
        $passwordValidation = validatePasswordStrength($password);
        if (!$passwordValidation['valid']) {
            throw new Exception($passwordValidation['error']);
        }

        if ($password !== $password_confirm) {
            throw new Exception('Las contraseñas no coinciden');
        }

        $db = Database::getInstance();
        $token_hash = hash('sha256', $token);

        // Verificar token
        $reset = $db->fetchOne(
            "SELECT * FROM password_resets WHERE token = ? AND usado = 0 AND expira_en > NOW()",
            [$token_hash]
        );

        if (!$reset) {
            throw new Exception('Token inválido o expirado. Solicite uno nuevo.');
        }

        // Actualizar contraseña
        $password_hash = hashPassword($password);

        $db->query(
            "UPDATE usuarios SET password_hash = ?, updated_at = NOW() WHERE id = ?",
            [$password_hash, $reset['usuario_id']]
        );

        // Marcar token como usado
        $db->query(
            "UPDATE password_resets SET usado = 1 WHERE id = ?",
            [$reset['id']]
        );

        $_SESSION['password_reset_success'] = true;
        header('Location: login.php?password_reset=1');
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Verificar token si estamos en step=reset
if ($step === 'reset' && $token) {
    $db = Database::getInstance();
    $token_hash = hash('sha256', $token);

    $reset = $db->fetchOne(
        "SELECT * FROM password_resets WHERE token = ? AND usado = 0 AND expira_en > NOW()",
        [$token_hash]
    );

    if (!$reset) {
        $error = 'El enlace de recuperación ha expirado o es inválido.';
        $step = 'expired';
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Conecta ERP</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <a href="login.php" class="back-link">← Volver al login</a>
                <img src="assets/img/logo.png" alt="Conecta ERP" class="login-logo">
                <h1>Recuperar Contraseña</h1>

                <?php if ($step === 'request'): ?>
                    <p>Ingresa tu email y te enviaremos un enlace para recuperar tu contraseña</p>
                <?php elseif ($step === 'reset'): ?>
                    <p>Ingresa tu nueva contraseña</p>
                <?php endif; ?>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?= escape($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <?= escape($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($step === 'request' && !$success): ?>
                <form method="POST" action="" class="login-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="su@email.com"
                            required
                            autofocus
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Enviar enlace de recuperación
                    </button>
                </form>
            <?php elseif ($step === 'reset' && !$error): ?>
                <form method="POST" action="" class="login-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="token" value="<?= escape($token) ?>">

                    <div class="form-group">
                        <label for="password">Nueva Contraseña</label>
                        <div class="password-input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required
                                autofocus
                            >
                            <button type="button" class="toggle-password" id="togglePassword">
                                <svg class="eye-open" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                        <small class="form-text">
                            Mínimo 8 caracteres, incluye mayúsculas, minúsculas, números y símbolos
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirmar Contraseña</label>
                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            class="form-control"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Cambiar contraseña
                    </button>
                </form>
            <?php endif; ?>

            <div class="login-footer">
                <p>¿Recordaste tu contraseña? <a href="login.php">Iniciar sesión</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/login.js"></script>
</body>
</html>
