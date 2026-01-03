<?php
define('CONECTA_ERP', true);
require_once __DIR__ . '/../app/core/bootstrap.php';

if (Session::isAuthenticated()) {
    redirect('/app/router.php?module=dashboard');
}

$error = '';
$success = '';
$step = isset($_GET['step']) ? $_GET['step'] : 'request';
$token = isset($_GET['token']) ? Security::sanitize($_GET['token']) : '';

// STEP 1: Solicitar recuperación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'request') {
    $rutEmpresa = Security::sanitize($_POST['rut_empresa'] ?? '');
    $email = Security::sanitize($_POST['email'] ?? '');

    if (empty($rutEmpresa) || empty($email)) {
        $error = 'Todos los campos son requeridos';
    } elseif (!Security::validateRut($rutEmpresa)) {
        $error = 'RUT de empresa inválido';
    } else {
        // Buscar usuario
        $user = db()->selectOne(
            "SELECT u.id, u.email, e.rut, e.razon_social
             FROM usuarios_acceso u
             JOIN empresas e ON u.empresa_id = e.id
             WHERE u.email = :email AND e.rut = :rut
             AND u.estado_id = (SELECT id FROM usuario_estado WHERE codigo = 'activo')",
            ['email' => $email, 'rut' => $rutEmpresa]
        );

        if (!$user) {
            $error = 'No se encontró una cuenta con estos datos';
        } else {
            // Verificar límite de solicitudes (3 por hora)
            $recentAttempts = db()->selectOne(
                "SELECT COUNT(*) as count FROM password_resets
                 WHERE email = :email
                 AND created_at > NOW() - INTERVAL '1 hour'",
                ['email' => $email]
            );

            if ($recentAttempts && $recentAttempts['count'] >= 3) {
                $error = 'Has excedido el número de intentos. Intenta nuevamente en 1 hora.';
            } else {
                // Generar token único
                $recoveryToken = Security::generateToken(64);
                $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // Guardar token
                db()->insert('password_resets', [
                    'usuario_id' => $user['id'],
                    'email' => $email,
                    'token' => $recoveryToken,
                    'expires_at' => $expiresAt,
                    'ip_solicitud' => Security::getClientIp(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // En producción, enviar email aquí
                $recoveryLink = url("public/recover.php?step=reset&token={$recoveryToken}");

                // Auditoría
                Audit::log('password_recovery_requested', "Recuperación solicitada para: {$email}");

                // Mostrar el link (en producción esto iría por email)
                $success = "Se ha enviado un enlace de recuperación a tu email. El enlace es válido por 15 minutos.";

                // Para desarrollo, mostrar el link
                if (config('app_env') === 'development') {
                    $success .= "<br><br><strong>Link de recuperación (solo desarrollo):</strong><br>";
                    $success .= "<a href='{$recoveryLink}'>Recuperar contraseña</a>";
                }
            }
        }
    }
}

// STEP 2: Resetear contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'reset') {
    $token = Security::sanitize($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirmPassword)) {
        $error = 'Todos los campos son requeridos';
    } elseif ($password !== $confirmPassword) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Validar fortaleza
        $validation = Security::validatePasswordStrength($password);
        if ($validation !== true) {
            $error = implode('<br>', $validation);
        } else {
            // Verificar token
            $reset = db()->selectOne(
                "SELECT * FROM password_resets
                 WHERE token = :token
                 AND usado = false
                 AND expires_at > NOW()",
                ['token' => $token]
            );

            if (!$reset) {
                $error = 'El enlace de recuperación es inválido o ha expirado';
            } else {
                try {
                    db()->beginTransaction();

                    // Actualizar contraseña
                    $passwordHash = Security::hashPassword($password);
                    db()->update(
                        'usuarios_acceso',
                        ['password_hash' => $passwordHash, 'updated_at' => date('Y-m-d H:i:s')],
                        'id = :id',
                        ['id' => $reset['usuario_id']]
                    );

                    // Marcar token como usado
                    db()->update(
                        'password_resets',
                        ['usado' => true, 'usado_at' => date('Y-m-d H:i:s')],
                        'id = :id',
                        ['id' => $reset['id']]
                    );

                    // Cerrar todas las sesiones del usuario
                    db()->query(
                        "DELETE FROM sesiones_usuario WHERE usuario_id = :user_id",
                        ['user_id' => $reset['usuario_id']]
                    );

                    db()->commit();

                    // Auditoría
                    Audit::log('password_reset_completed', "Contraseña cambiada para usuario ID: {$reset['usuario_id']}");

                    Session::flash('success', 'Tu contraseña ha sido actualizada exitosamente. Por favor inicia sesión.');
                    redirect('login.php');

                } catch (Exception $e) {
                    db()->rollback();
                    $error = 'Error al actualizar la contraseña. Intenta nuevamente.';
                    error_log($e->getMessage());
                }
            }
        }
    }
}

// Verificar token si es step reset
$validToken = false;
if ($step === 'reset' && !empty($token)) {
    $reset = db()->selectOne(
        "SELECT * FROM password_resets
         WHERE token = :token AND usado = false AND expires_at > NOW()",
        ['token' => $token]
    );
    $validToken = $reset ? true : false;

    if (!$validToken) {
        $error = 'El enlace de recuperación es inválido o ha expirado';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Conecta ERP</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-header">
        <a href="index.php" class="logo">
            <span class="logo-icon">C</span>
            <span class="logo-text">Conecta ERP</span>
        </a>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <?php if ($step === 'request'): ?>
                <div class="auth-card-header">
                    <h1>Recuperar contraseña</h1>
                    <p>Ingresa tus datos para recibir un enlace de recuperación</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php else: ?>
                    <form method="POST" class="auth-form">
                        <input type="hidden" name="step" value="request">

                        <div class="form-group">
                            <label>RUT Empresa</label>
                            <input type="text" name="rut_empresa" placeholder="12.345.678-9"
                                   value="<?php echo e($_POST['rut_empresa'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email registrado</label>
                            <input type="email" name="email" placeholder="tu@email.com"
                                   value="<?php echo e($_POST['email'] ?? ''); ?>" required>
                        </div>

                        <button type="submit" class="btn-primary btn-block">Recuperar contraseña</button>
                    </form>
                <?php endif; ?>

            <?php elseif ($step === 'reset' && $validToken): ?>
                <div class="auth-card-header">
                    <h1>Nueva contraseña</h1>
                    <p>Ingresa tu nueva contraseña segura</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <input type="hidden" name="step" value="reset">
                    <input type="hidden" name="token" value="<?php echo e($token); ?>">

                    <div class="form-group">
                        <label>Nueva Contraseña *</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" required>
                            <button type="button" onclick="togglePassword('password')">👁️</button>
                        </div>
                        <small>Mínimo 12 caracteres, con mayúsculas, minúsculas, números y símbolos</small>
                    </div>

                    <div class="form-group">
                        <label>Confirmar Contraseña *</label>
                        <div class="password-input">
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <button type="button" onclick="togglePassword('confirm_password')">👁️</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary btn-block">Cambiar contraseña</button>
                </form>

            <?php else: ?>
                <div class="auth-card-header">
                    <h1>Enlace inválido</h1>
                </div>
                <div class="alert alert-error">
                    El enlace de recuperación es inválido o ha expirado.
                </div>
                <div class="text-center">
                    <a href="recover.php" class="btn-primary">Solicitar nuevo enlace</a>
                </div>
            <?php endif; ?>

            <div class="auth-card-footer">
                <p><a href="login.php">Volver al login</a></p>
            </div>
        </div>
    </div>

    <div class="auth-footer">
        <p>&copy; <?php echo date('Y'); ?> Conecta ERP - Todos los derechos reservados</p>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
