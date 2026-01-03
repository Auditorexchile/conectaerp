<?php
/**
 * CONECTA ERP - RECUPERACIÓN DE CONTRASEÑA
 * Sistema de recuperación con tokens temporales (10-15 minutos)
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
$step = $_GET['step'] ?? 'request'; // request | reset
$token = $_GET['token'] ?? '';

// ==========================================
// PASO 1: SOLICITAR RECUPERACIÓN
// ==========================================
if ($step === 'request' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificador = Security::sanitize($_POST['identificador'] ?? '');
    $email = Security::sanitize($_POST['email'] ?? '');

    if (empty($identificador) || empty($email)) {
        $error = 'Todos los campos son obligatorios';
    } else {
        // Buscar empresa por identificador
        $empresa = db()->selectOne(
            "SELECT id, pais_codigo, identificador, razon_social, estado
             FROM empresas
             WHERE identificador = :identificador",
            ['identificador' => $identificador]
        );

        if (!$empresa) {
            // Por seguridad, no revelar que la empresa no existe
            $success = '✅ Si los datos son correctos, recibirás un email con instrucciones para recuperar tu contraseña.';
        } else {
            // Validar identificador según país
            if (!Validators::validarIdentificadorPorPais($empresa['pais_codigo'], $identificador)) {
                $error = 'Identificador tributario inválido';
            } else {
                // Buscar usuario
                $user = db()->selectOne(
                    "SELECT id, empresa_id, email, nombre, estado
                     FROM usuarios_acceso
                     WHERE email = :email AND empresa_id = :empresa_id",
                    ['email' => $email, 'empresa_id' => $empresa['id']]
                );

                if (!$user) {
                    // Por seguridad, no revelar que el usuario no existe
                    $success = '✅ Si los datos son correctos, recibirás un email con instrucciones para recuperar tu contraseña.';
                } elseif ($user['estado'] === 'bloqueado') {
                    $error = '❌ Tu cuenta está bloqueada. Contacta a soporte: contacto@conectaerp.com';
                } else {
                    // Generar token seguro
                    $tokenValue = bin2hex(random_bytes(32)); // 64 caracteres hex
                    $expiraEn = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                    // Invalidar tokens anteriores del usuario
                    db()->query(
                        "UPDATE password_resets SET usado = 1 WHERE usuario_id = :id",
                        ['id' => $user['id']]
                    );

                    // Crear nuevo token
                    db()->insert('password_resets', [
                        'usuario_id' => $user['id'],
                        'token' => hash('sha256', $tokenValue), // Hashear token en BD
                        'email' => $email,
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'expira_en' => $expiraEn,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    // TODO: En producción, enviar email real
                    // Por ahora, mostrar el link en desarrollo
                    $resetLink = "https://conectaerp.com/recover.php?step=reset&token=" . $tokenValue;

                    // Registrar en log
                    error_log("Password reset requested for {$email}. Link: {$resetLink}");

                    $success = '✅ Si los datos son correctos, recibirás un email con instrucciones para recuperar tu contraseña.';

                    // En desarrollo, mostrar el link
                    if (Config::get('app.debug', false)) {
                        $success .= "<br><br><strong>🔧 Modo desarrollo - Link de recuperación:</strong><br>";
                        $success .= "<a href='$resetLink' style='color: #3b82f6; text-decoration: underline;'>$resetLink</a>";
                        $success .= "<br><small>Este link expira en 15 minutos</small>";
                    }
                }
            }
        }
    }
}

// ==========================================
// PASO 2: RESTABLECER CONTRASEÑA
// ==========================================
if ($step === 'reset' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenValue = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (empty($tokenValue) || empty($password) || empty($passwordConfirm)) {
        $error = 'Todos los campos son obligatorios';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Validar fortaleza de contraseña
        $validacion = Validators::validarPassword($password);
        if (!$validacion['valida']) {
            $error = 'Contraseña insegura: ' . implode(', ', $validacion['errores']);
        } else {
            // Buscar token (hasheado)
            $tokenHash = hash('sha256', $tokenValue);
            $reset = db()->selectOne(
                "SELECT pr.*, u.id as user_id, u.email, u.nombre
                 FROM password_resets pr
                 JOIN usuarios_acceso u ON pr.usuario_id = u.id
                 WHERE pr.token = :token
                 AND pr.usado = 0
                 AND pr.expira_en > NOW()
                 LIMIT 1",
                ['token' => $tokenHash]
            );

            if (!$reset) {
                $error = '❌ Token inválido o expirado. Solicita una nueva recuperación.';
            } else {
                // Actualizar contraseña
                $passwordHash = Security::hashPassword($password);

                db()->update('usuarios_acceso', [
                    'password_hash' => $passwordHash,
                    'intentos_fallidos' => 0,
                    'bloqueado' => 0,
                    'bloqueado_hasta' => null,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = :id', ['id' => $reset['user_id']]);

                // Marcar token como usado
                db()->update('password_resets', [
                    'usado' => 1,
                    'usado_en' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = :id', ['id' => $reset['id']]);

                // TODO: Enviar email de confirmación

                // Registrar en log
                error_log("Password reset successful for user ID: {$reset['user_id']}");

                // Redirigir a login con mensaje de éxito
                redirect('/login.php?password_reset=1');
            }
        }
    }
}

// ==========================================
// VERIFICAR TOKEN SI ESTAMOS EN PASO RESET
// ==========================================
$tokenData = null;
if ($step === 'reset' && !empty($token)) {
    $tokenHash = hash('sha256', $token);
    $tokenData = db()->selectOne(
        "SELECT pr.*, u.email, u.nombre
         FROM password_resets pr
         JOIN usuarios_acceso u ON pr.usuario_id = u.id
         WHERE pr.token = :token
         AND pr.usado = 0
         AND pr.expira_en > NOW()
         LIMIT 1",
        ['token' => $tokenHash]
    );

    if (!$tokenData) {
        $error = '❌ Token inválido o expirado. Solicita una nueva recuperación.';
        $step = 'request'; // Volver al paso 1
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

                <?php if ($step === 'request'): ?>
                    <!-- PASO 1: SOLICITAR RECUPERACIÓN -->
                    <div class="auth-card-header">
                        <h1>Recuperar Contraseña</h1>
                        <p>Ingresa tu identificador tributario y email para recuperar tu contraseña</p>
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
                        <input type="hidden" name="step" value="request">

                        <div class="form-group">
                            <label>Identificador Tributario <span class="required">*</span></label>
                            <input type="text" name="identificador"
                                   placeholder="RUT / CUIT / RFC / NIT / etc."
                                   required autofocus>
                            <small class="form-hint">Ej: 12.345.678-9 (Chile), 20-12345678-9 (Argentina), etc.</small>
                        </div>

                        <div class="form-group">
                            <label>Email de acceso <span class="required">*</span></label>
                            <input type="email" name="email"
                                   placeholder="tu@email.com" required>
                            <small class="form-hint">Usa el email con el que te registraste</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            Enviar instrucciones
                        </button>
                    </form>

                <?php elseif ($step === 'reset' && $tokenData): ?>
                    <!-- PASO 2: RESTABLECER CONTRASEÑA -->
                    <div class="auth-card-header">
                        <h1>Nueva Contraseña</h1>
                        <p>Hola <strong><?= e($tokenData['nombre']) ?></strong>, crea tu nueva contraseña</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <span class="alert-icon">⚠️</span>
                            <span class="alert-text"><?= $error ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form" id="resetForm">
                        <input type="hidden" name="step" value="reset">
                        <input type="hidden" name="token" value="<?= e($token) ?>">

                        <div class="form-group">
                            <label>Nueva contraseña <span class="required">*</span></label>
                            <div class="password-input-wrapper">
                                <input type="password" name="password" id="password"
                                       placeholder="••••••••" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <span class="icon-eye">👁️</span>
                                </button>
                                <button type="button" class="password-generate" onclick="generatePassword()">
                                    Generar
                                </button>
                            </div>
                            <small class="form-hint">Mínimo 8 caracteres, incluye mayúsculas, minúsculas y números</small>
                        </div>

                        <!-- Password Strength Meter -->
                        <div id="passwordStrength" class="password-strength" style="display: none;">
                            <div class="strength-bar">
                                <div id="strengthBarFill" class="strength-bar-fill" style="width: 0%;"></div>
                            </div>
                            <div class="strength-text" id="strengthText"></div>
                        </div>

                        <div class="form-group">
                            <label>Confirmar contraseña <span class="required">*</span></label>
                            <div class="password-input-wrapper">
                                <input type="password" name="password_confirm" id="password_confirm"
                                       placeholder="••••••••" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirm')">
                                    <span class="icon-eye">👁️</span>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            Restablecer contraseña
                        </button>
                    </form>
                <?php endif; ?>

                <div class="auth-card-footer">
                    <p>¿Recordaste tu contraseña? <a href="login.php">Volver al login</a></p>
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
        // ===== TOGGLE PASSWORD VISIBILITY =====
        function togglePassword(id) {
            const input = document.getElementById(id);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        // ===== GENERAR CONTRASEÑA SEGURA =====
        function generatePassword() {
            const length = 12;
            const uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
            const lowercase = 'abcdefghjkmnpqrstuvwxyz';
            const numbers = '23456789';
            const symbols = '!@#$%&*';

            let password = '';

            // Garantizar al menos un carácter de cada tipo
            password += uppercase[Math.floor(Math.random() * uppercase.length)];
            password += lowercase[Math.floor(Math.random() * lowercase.length)];
            password += numbers[Math.floor(Math.random() * numbers.length)];
            password += symbols[Math.floor(Math.random() * symbols.length)];

            // Completar el resto con caracteres aleatorios
            const allChars = uppercase + lowercase + numbers + symbols;
            for (let i = 4; i < length; i++) {
                password += allChars[Math.floor(Math.random() * allChars.length)];
            }

            // Mezclar el password
            password = password.split('').sort(() => Math.random() - 0.5).join('');

            // Asignar al input
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirm');

            if (passwordInput) {
                passwordInput.value = password;
                passwordInput.type = 'text';
                calcularFortaleza(password);
            }

            if (passwordConfirmInput) {
                passwordConfirmInput.value = password;
            }

            // Volver a password después de 2 segundos
            setTimeout(() => {
                if (passwordInput) {
                    passwordInput.type = 'password';
                }
            }, 2000);
        }

        // ===== CALCULAR FORTALEZA DE CONTRASEÑA =====
        function calcularFortaleza(password) {
            if (!password) {
                document.getElementById('passwordStrength').style.display = 'none';
                return;
            }

            let score = 0;

            // Longitud
            score += Math.min(password.length * 4, 40);

            // Mayúsculas
            if (/[A-Z]/.test(password)) {
                score += 10;
            }

            // Minúsculas
            if (/[a-z]/.test(password)) {
                score += 10;
            }

            // Números
            if (/[0-9]/.test(password)) {
                score += 10;
            }

            // Caracteres especiales
            if (/[^A-Za-z0-9]/.test(password)) {
                score += 20;
            }

            // Variedad de caracteres
            const uniqueChars = new Set(password.split('')).size;
            score += Math.min(uniqueChars * 2, 20);

            score = Math.min(score, 100);

            // Determinar nivel
            let nivel, color, texto;
            if (score < 40) {
                nivel = 'debil';
                color = '#ef4444';
                texto = 'Débil';
            } else if (score < 70) {
                nivel = 'media';
                color = '#f59e0b';
                texto = 'Media';
            } else {
                nivel = 'fuerte';
                color = '#10b981';
                texto = 'Fuerte';
            }

            // Mostrar
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthBarFill = document.getElementById('strengthBarFill');
            const strengthText = document.getElementById('strengthText');

            if (strengthDiv) {
                strengthDiv.style.display = 'block';
            }

            if (strengthBarFill) {
                strengthBarFill.style.width = score + '%';
                strengthBarFill.style.backgroundColor = color;
            }

            if (strengthText) {
                strengthText.textContent = texto;
                strengthText.style.color = color;
            }
        }

        // ===== EVENT LISTENER PARA PASSWORD INPUT =====
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                calcularFortaleza(this.value);
            });
        }

        // ===== VALIDACIÓN DE FORMULARIO ANTES DE ENVIAR =====
        const resetForm = document.getElementById('resetForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function(event) {
                const passwordInput = document.getElementById('password');
                const confirmInput = document.getElementById('password_confirm');

                if (passwordInput && confirmInput) {
                    if (passwordInput.value !== confirmInput.value) {
                        event.preventDefault();
                        alert('Las contraseñas no coinciden');
                        return false;
                    }
                }
            });
        }
    </script>
</body>
</html>
