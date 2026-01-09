<?php
/**
 * LOGIN - CONECTA ERP
 * Sistema seguro antihacking con validación multipais
 */

require_once 'config/database.php';
require_once 'config/security.php';

initSecureSession();

// Si ya está logueado, redirigir a dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            throw new Exception('Token de seguridad inválido');
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validaciones básicas
        if (empty($email) || empty($password)) {
            throw new Exception('Complete todos los campos');
        }

        if (!validateEmail($email)) {
            throw new Exception('Email inválido');
        }

        // Verificar intentos fallidos
        if (checkLoginAttempts($email)) {
            throw new Exception('Demasiados intentos fallidos. Intente en 15 minutos.');
        }

        $db = Database::getInstance();

        // Buscar usuario
        $usuario = $db->fetchOne(
            "SELECT u.*, e.estado as empresa_estado, e.fecha_trial_fin, p.codigo as plan_codigo
             FROM usuarios u
             INNER JOIN empresas e ON u.empresa_id = e.id
             INNER JOIN planes p ON e.plan_id = p.id
             WHERE u.email = ? AND u.activo = 1",
            [$email]
        );

        if (!$usuario) {
            logLoginAttempt($email, false, getRealIP());
            throw new Exception('Credenciales incorrectas');
        }

        // Verificar contraseña
        if (!verifyPassword($password, $usuario['password_hash'])) {
            logLoginAttempt($email, false, getRealIP());
            throw new Exception('Credenciales incorrectas');
        }

        // Verificar estado usuario
        if ($usuario['estado'] !== 'activo') {
            throw new Exception('Usuario ' . $usuario['estado']);
        }

        // Verificar estado empresa
        if ($usuario['empresa_estado'] === 'bloqueado') {
            throw new Exception('Empresa bloqueada. Contacte soporte.');
        }

        if ($usuario['empresa_estado'] === 'suspendido') {
            throw new Exception('Empresa suspendida por falta de pago.');
        }

        // Login exitoso
        logLoginAttempt($email, true, getRealIP());

        // Crear sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['empresa_id'] = $usuario['empresa_id'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nombres'] = $usuario['nombres'];
        $_SESSION['es_superusuario'] = $usuario['es_superusuario'];
        $_SESSION['plan_codigo'] = $usuario['plan_codigo'];
        $_SESSION['empresa_estado'] = $usuario['empresa_estado'];
        $_SESSION['trial_fin'] = $usuario['fecha_trial_fin'];

        // Actualizar último acceso
        $db->query(
            "UPDATE usuarios SET ultimo_acceso = NOW(), ip_ultimo_acceso = ? WHERE id = ?",
            [getRealIP(), $usuario['id']]
        );

        // Redirigir a dashboard
        header('Location: dashboard.php');
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Conecta ERP</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="assets/img/logo.png" alt="Conecta ERP" class="login-logo">
                <h1>Iniciar Sesión</h1>
                <p>Ingrese sus credenciales para acceder al sistema</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?= escape($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form" id="loginForm">
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
                        autocomplete="email"
                        value="<?= isset($_POST['email']) ? escape($_POST['email']) : '' ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" id="togglePassword">
                            <svg class="eye-open" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            <svg class="eye-closed" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="display:none;">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Recordarme</span>
                    </label>
                    <a href="recover.php" class="link-recover">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Ingresar
                </button>

                <div class="login-footer">
                    <p>¿No tienes cuenta? <a href="register.php" class="link-register">Crear cuenta gratis</a></p>
                    <a href="index.php" class="link-home">← Volver al inicio</a>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/login.js"></script>
</body>
</html>
