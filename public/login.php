<?php
/**
 * CONECTA ERP - LOGIN MEJORADO
 * Soporte multi-país con validación dinámica
 */

define('CONECTA_ERP', true);
require_once __DIR__ . '/../app/core/bootstrap.php';
require_once __DIR__ . '/../app/helpers/validators.php';
require_once __DIR__ . '/../app/helpers/countries.php';

// Si ya está autenticado, redirigir al dashboard
if (Session::isAuthenticated()) {
    redirect('/app/router.php?module=dashboard');
}

$error = '';
$success = '';
$email = '';
$identificador = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = Security::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $identificador = Security::sanitize($_POST['identificador'] ?? '');

    // Validaciones básicas
    if (empty($identificador) || empty($email) || empty($password)) {
        $error = 'Todos los campos son obligatorios';
    } else {
        // Buscar empresa por identificador para saber el país
        $empresa = db()->selectOne(
            "SELECT id, pais_codigo, identificador, razon_social, estado, bloqueada, bloqueada_por_pago,
                    en_trial, fecha_fin_trial, suscripcion_activa
             FROM empresas
             WHERE identificador = :identificador",
            ['identificador' => $identificador]
        );

        if (!$empresa) {
            $error = 'Empresa no encontrada. Verifica tu identificador tributario.';
        } else {
            // Validar identificador según país
            if (!Validators::validarIdentificadorPorPais($empresa['pais_codigo'], $identificador)) {
                $tipoId = CountryHelper::getIdentifierType($empresa['pais_codigo']);
                $error = "$tipoId inválido para " . $empresa['pais_codigo'];
            } else {
                // Buscar usuario
                $user = db()->selectOne(
                    "SELECT u.*, e.razon_social, e.pais_codigo, e.estado as empresa_estado,
                            e.bloqueada as empresa_bloqueada, e.bloqueada_por_pago,
                            e.en_trial, e.fecha_fin_trial, e.suscripcion_activa
                     FROM usuarios_acceso u
                     JOIN empresas e ON u.empresa_id = e.id
                     WHERE u.email = :email
                     AND u.empresa_id = :empresa_id",
                    ['email' => $email, 'empresa_id' => $empresa['id']]
                );

                if (!$user) {
                    $error = 'Credenciales incorrectas';

                    // Registrar intento fallido
                    db()->insert('intentos_login', [
                        'email' => $email,
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'exitoso' => 0,
                        'motivo_fallo' => 'Usuario no encontrado',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                } elseif ($user['estado'] === 'bloqueado') {
                    $error = '❌ Tu cuenta ha sido bloqueada. Contacta a soporte: contacto@conectaerp.com';

                } elseif ($user['estado'] === 'pendiente') {
                    $error = '⏳ Tu cuenta está pendiente de activación. Revisa tu email.';

                } elseif ($user['empresa_bloqueada'] == 1) {
                    $error = '🚫 La empresa ha sido bloqueada. Contacta a soporte: contacto@conectaerp.com';

                } elseif ($user['bloqueada_por_pago'] == 1) {
                    $error = '💳 Cuenta suspendida por falta de pago. <a href="/pagar.php">Pagar ahora</a>';

                } elseif ($user['empresa_estado'] === 'suspendida') {
                    $error = '⚠️ Empresa suspendida. Verifica el estado de tu suscripción.';

                } elseif (!Security::verifyPassword($password, $user['password_hash'])) {
                    $error = 'Credenciales incorrectas';

                    // Incrementar intentos fallidos
                    db()->query(
                        "UPDATE usuarios_acceso SET intentos_fallidos = intentos_fallidos + 1,
                         ultimo_intento_fallido = NOW() WHERE id = :id",
                        ['id' => $user['id']]
                    );

                    // Bloquear si supera 5 intentos
                    if (($user['intentos_fallidos'] + 1) >= 5) {
                        db()->update('usuarios_acceso', [
                            'bloqueado' => 1,
                            'bloqueado_hasta' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
                            'motivo_bloqueo' => 'Múltiples intentos fallidos'
                        ], 'id = :id', ['id' => $user['id']]);

                        $error = '🔒 Cuenta bloqueada por múltiples intentos fallidos. Espera 30 minutos.';
                    }

                    // Registrar intento fallido
                    db()->insert('intentos_login', [
                        'email' => $email,
                        'usuario_id' => $user['id'],
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'exitoso' => 0,
                        'motivo_fallo' => 'Contraseña incorrecta',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                } else {
                    // ✅ LOGIN EXITOSO

                    // Resetear intentos fallidos
                    db()->update('usuarios_acceso', [
                        'intentos_fallidos' => 0,
                        'ultimo_acceso' => date('Y-m-d H:i:s'),
                        'ip_ultimo_acceso' => $_SERVER['REMOTE_ADDR'] ?? ''
                    ], 'id = :id', ['id' => $user['id']]);

                    // Registrar login exitoso
                    db()->insert('intentos_login', [
                        'email' => $email,
                        'usuario_id' => $user['id'],
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'exitoso' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    // Iniciar sesión
                    Session::set('user_id', $user['id']);
                    Session::set('empresa_id', $user['empresa_id']);
                    Session::set('authenticated', true);
                    Session::set('user_name', $user['nombre']);
                    Session::set('user_email', $user['email']);
                    Session::set('empresa_nombre', $user['razon_social']);
                    Session::set('pais_codigo', $user['pais_codigo']);
                    Session::set('idioma_codigo', $user['idioma_codigo'] ?? 'es');

                    // Verificar/crear trial
                    $trial = db()->selectOne(
                        "SELECT * FROM trial_log WHERE empresa_id = :id ORDER BY id DESC LIMIT 1",
                        ['id' => $user['empresa_id']]
                    );

                    if (!$trial && $user['en_trial'] == 1) {
                        // Crear trial
                        db()->insert('trial_log', [
                            'empresa_id' => $user['empresa_id'],
                            'fecha_inicio' => date('Y-m-d'),
                            'fecha_fin' => date('Y-m-d', strtotime('+14 days')),
                            'dias_totales' => 14,
                            'activo' => 1,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    redirect('/app/router.php?module=dashboard');
                }
            }
        }
    }
}

// Mensajes de sesión
if (isset($_GET['registered'])) {
    $success = '✅ Cuenta creada exitosamente. Ya puedes ingresar.';
}

if (isset($_GET['password_reset'])) {
    $success = '✅ Contraseña restablecida exitosamente. Ya puedes ingresar.';
}

if (isset($_GET['timeout'])) {
    $error = '⏱️ Tu sesión ha expirado. Por favor ingresa nuevamente.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Conecta ERP</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">

    <!-- Header -->
    <header class="auth-header">
        <div class="container">
            <a href="index.php" class="logo">
                <span class="logo-icon">📊</span>
                <span class="logo-text">Conecta ERP</span>
            </a>
        </div>
    </header>

    <!-- Main -->
    <main class="auth-main">
        <div class="container">
            <div class="auth-card">

                <div class="auth-card-header">
                    <h1>Iniciar Sesión</h1>
                    <p>Accede a tu cuenta de Conecta ERP</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">⚠️</span>
                        <span class="alert-text"><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <span class="alert-icon">✓</span>
                        <span class="alert-text"><?= $success ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">

                    <div class="form-group">
                        <label>Identificador Tributario <span class="required">*</span></label>
                        <input type="text" name="identificador" id="identificador"
                               value="<?= e($identificador) ?>"
                               placeholder="RUT / CUIT / RFC / NIT / etc."
                               required autofocus>
                        <small class="form-hint">Ej: 12.345.678-9 (Chile), 20-12345678-9 (Argentina), etc.</small>
                    </div>

                    <div class="form-group">
                        <label>Email de acceso <span class="required">*</span></label>
                        <input type="email" name="email" value="<?= e($email) ?>"
                               placeholder="tu@email.com" required>
                    </div>

                    <div class="form-group">
                        <label>Contraseña <span class="required">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" name="password" id="password"
                                   placeholder="••••••••" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <span class="icon-eye">👁️</span>
                            </button>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="recover.php" class="link">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Ingresar
                    </button>

                </form>

                <div class="auth-card-footer">
                    <p>¿No tienes cuenta? <a href="register.php">Crear cuenta gratis</a></p>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="auth-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Conecta ERP - Todos los derechos reservados</p>
            <p>
                <a href="mailto:contacto@conectaerp.com">contacto@conectaerp.com</a> |
                <a href="tel:+56985745559">+56 9 8574 5559</a>
            </p>
        </div>
    </footer>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>
</body>
</html>
