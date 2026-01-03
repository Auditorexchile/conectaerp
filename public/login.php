<?php
define('CONECTA_ERP', true);
require_once __DIR__ . '/../app/core/bootstrap.php';

// Si ya está autenticado, redirigir al dashboard
if (Session::isAuthenticated()) {
    redirect('/app/router.php?module=dashboard');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = Security::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rutEmpresa = Security::sanitize($_POST['rut_empresa'] ?? '');

    // Validaciones
    if (empty($rutEmpresa) || empty($email) || empty($password)) {
        $error = 'Todos los campos son requeridos';
    } elseif (!Security::validateRut($rutEmpresa)) {
        $error = 'RUT de empresa inválido';
    } else {
        // Buscar usuario
        $user = db()->selectOne(
            "SELECT u.*, e.rut as empresa_rut, e.razon_social, ut.codigo as tipo_usuario
             FROM usuarios_acceso u
             JOIN empresas e ON u.empresa_id = e.id
             JOIN usuario_tipo ut ON u.tipo_usuario_id = ut.id
             WHERE u.email = :email
             AND e.rut = :rut
             AND u.estado_id = (SELECT id FROM usuario_estado WHERE codigo = 'activo')
             AND (u.bloqueado = false OR u.bloqueado_hasta < NOW())",
            ['email' => $email, 'rut' => $rutEmpresa]
        );

        if (!$user) {
            $error = 'Credenciales incorrectas';
            Security::recordFailedLogin($email, Security::getClientIp());
        } elseif (!Security::verifyPassword($password, $user['password_hash'])) {
            $error = 'Credenciales incorrectas';
            Security::recordFailedLogin($email, Security::getClientIp());
        } else {
            // Login exitoso
            db()->insert('intentos_login', [
                'email' => $email,
                'usuario_id' => $user['id'],
                'ip_address' => Security::getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'exitoso' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Iniciar sesión
            Session::login($user);

            // Iniciar trial si es primer login
            $trial = db()->selectOne(
                "SELECT * FROM trial_configuracion WHERE empresa_id = :id",
                ['id' => $user['empresa_id']]
            );

            if (!$trial) {
                db()->insert('trial_configuracion', [
                    'empresa_id' => $user['empresa_id'],
                    'fecha_inicio' => date('Y-m-d'),
                    'fecha_fin' => date('Y-m-d', strtotime('+14 days')),
                    'dias_trial' => 14,
                    'primer_login' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } elseif (!$trial['primer_login']) {
                db()->update(
                    'trial_configuracion',
                    ['primer_login' => date('Y-m-d H:i:s')],
                    'id = :id',
                    ['id' => $trial['id']]
                );
            }

            redirect('/app/router.php?module=dashboard');
        }
    }
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
    <div class="auth-header">
        <a href="index.php" class="logo">
            <span class="logo-icon">C</span>
            <span class="logo-text">Conecta ERP</span>
        </a>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <h1>Iniciar sesión</h1>
                <p>Accede a tu cuenta de Conecta ERP</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['timeout'])): ?>
                <div class="alert alert-warning">Tu sesión ha expirado. Por favor ingresa nuevamente.</div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>RUT Empresa</label>
                    <input type="text" name="rut_empresa" placeholder="12.345.678-9"
                           value="<?php echo e($_POST['rut_empresa'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email de acceso</label>
                    <input type="email" name="email" placeholder="tu@email.com"
                           value="<?php echo e($email); ?>" required>
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <span id="password-icon">👁️</span>
                        </button>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="recover.php" class="link">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn-primary btn-block">Ingresar</button>
            </form>

            <div class="auth-card-footer">
                <p>¿No tienes cuenta? <a href="register.php">Crear cuenta</a></p>
            </div>
        </div>
    </div>

    <div class="auth-footer">
        <p>&copy; <?php echo date('Y'); ?> Conecta ERP - Todos los derechos reservados</p>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = document.getElementById(id + '-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = '👁️‍🗨️';
            } else {
                input.type = 'password';
                icon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
