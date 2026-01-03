<?php
define('CONECTA_ERP', true);
require_once __DIR__ . '/../app/core/bootstrap.php';

if (Session::isAuthenticated()) {
    redirect('/app/router.php?module=dashboard');
}

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        // Validar datos empresa
        Session::set('register_empresa', [
            'rut' => Security::sanitize($_POST['rut_empresa'] ?? ''),
            'razon_social' => Security::sanitize($_POST['razon_social'] ?? ''),
            'nombre_fantasia' => Security::sanitize($_POST['nombre_fantasia'] ?? ''),
            'giro' => Security::sanitize($_POST['giro'] ?? ''),
            'direccion' => Security::sanitize($_POST['direccion'] ?? ''),
        ]);

        if (!Security::validateRut(Session::get('register_empresa')['rut'])) {
            $error = 'RUT inválido';
        } else {
            $exists = db()->selectOne("SELECT COUNT(*) as c FROM empresas WHERE rut = :rut",
                ['rut' => Session::get('register_empresa')['rut']]);
            if ($exists && $exists['c'] > 0) {
                $error = 'Este RUT ya está registrado';
            } else {
                redirect('register.php?step=2');
            }
        }
    } elseif ($step === 2) {
        Session::set('register_representante', [
            'nombre' => Security::sanitize($_POST['nombre'] ?? ''),
            'rut' => Security::sanitize($_POST['rut_rep'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'telefono' => Security::sanitize($_POST['telefono'] ?? ''),
        ]);
        redirect('register.php?step=3');
    } elseif ($step === 3) {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($password !== $confirm) {
            $error = 'Las contraseñas no coinciden';
        } else {
            $validation = Security::validatePasswordStrength($password);
            if ($validation !== true) {
                $error = implode(', ', $validation);
            } else {
                Session::set('register_password', $password);
                redirect('register.php?step=4');
            }
        }
    } elseif ($step === 4) {
        Session::set('register_plan', $_POST['plan'] ?? 'starter');
        redirect('register.php?step=5');
    } elseif ($step === 5) {
        if (!isset($_POST['acepta_terminos']) || !isset($_POST['acepta_privacidad'])) {
            $error = 'Debes aceptar los términos y condiciones';
        } else {
            // Crear empresa y usuario
            try {
                db()->beginTransaction();

                $empresa = Session::get('register_empresa');
                $representante = Session::get('register_representante');
                $password = Session::get('register_password');
                $plan = Session::get('register_plan');

                // Crear empresa
                $estadoActivo = db()->selectOne("SELECT id FROM empresa_estado WHERE codigo = 'activa'");
                $empresaId = db()->insert('empresas', [
                    'rut' => $empresa['rut'],
                    'razon_social' => $empresa['razon_social'],
                    'nombre_fantasia' => $empresa['nombre_fantasia'],
                    'giro' => $empresa['giro'],
                    'estado_id' => $estadoActivo['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // Crear dirección
                db()->insert('empresa_direcciones', [
                    'empresa_id' => $empresaId,
                    'tipo' => 'comercial',
                    'direccion' => $empresa['direccion'],
                    'principal' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // Crear representante legal
                db()->insert('empresa_representante_legal', [
                    'empresa_id' => $empresaId,
                    'rut' => $representante['rut'],
                    'nombre_completo' => $representante['nombre'],
                    'email' => $representante['email'],
                    'telefono' => $representante['telefono'],
                    'fecha_nombramiento' => date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // Crear usuario
                $tipoUsuario = db()->selectOne("SELECT id FROM usuario_tipo WHERE codigo = 'admin'");
                $estadoUsuario = db()->selectOne("SELECT id FROM usuario_estado WHERE codigo = 'activo'");

                $usuarioId = db()->insert('usuarios_acceso', [
                    'empresa_id' => $empresaId,
                    'email' => $representante['email'],
                    'password_hash' => Security::hashPassword($password),
                    'nombre' => $representante['nombre'],
                    'tipo_usuario_id' => $tipoUsuario['id'],
                    'estado_id' => $estadoUsuario['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // Crear suscripción al plan
                $planData = db()->selectOne("SELECT * FROM planes WHERE codigo = :codigo", ['codigo' => $plan]);
                if ($planData) {
                    db()->insert('suscripciones', [
                        'empresa_id' => $empresaId,
                        'plan_id' => $planData['id'],
                        'fecha_inicio' => date('Y-m-d'),
                        'precio' => $planData['precio'],
                        'activo' => true,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                db()->commit();

                // Limpiar sesión de registro
                Session::remove('register_empresa');
                Session::remove('register_representante');
                Session::remove('register_password');
                Session::remove('register_plan');

                Session::flash('success', 'Cuenta creada exitosamente. Por favor inicia sesión.');
                redirect('login.php');

            } catch (Exception $e) {
                db()->rollback();
                $error = 'Error al crear la cuenta. Intenta nuevamente.';
                error_log($e->getMessage());
            }
        }
    }
}

$empresa = Session::get('register_empresa', []);
$representante = Session::get('register_representante', []);
$plan = Session::get('register_plan', 'starter');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Conecta ERP</title>
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
        <div class="auth-card register-card">
            <div class="auth-card-header">
                <h1>Crear cuenta</h1>
                <p>Completa el formulario para comenzar</p>
            </div>

            <!-- Progress bar -->
            <div class="progress-steps">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="step <?php echo $i <= $step ? 'active' : ''; ?> <?php echo $i < $step ? 'completed' : ''; ?>">
                        <?php echo $i < $step ? '✓' : $i; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <?php if ($step === 1): ?>
                    <h3>Datos de la Empresa</h3>
                    <div class="form-group">
                        <label>RUT Empresa *</label>
                        <input type="text" name="rut_empresa" value="<?php echo e($empresa['rut'] ?? ''); ?>"
                               placeholder="12.345.678-9" required>
                    </div>
                    <div class="form-group">
                        <label>Razón Social *</label>
                        <input type="text" name="razon_social" value="<?php echo e($empresa['razon_social'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre Fantasía *</label>
                        <input type="text" name="nombre_fantasia" value="<?php echo e($empresa['nombre_fantasia'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Giro *</label>
                        <input type="text" name="giro" value="<?php echo e($empresa['giro'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Dirección *</label>
                        <input type="text" name="direccion" value="<?php echo e($empresa['direccion'] ?? ''); ?>" required>
                    </div>

                <?php elseif ($step === 2): ?>
                    <h3>Representante Legal</h3>
                    <div class="form-group">
                        <label>Nombre Completo *</label>
                        <input type="text" name="nombre" value="<?php echo e($representante['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>RUT *</label>
                        <input type="text" name="rut_rep" value="<?php echo e($representante['rut'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?php echo e($representante['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono *</label>
                        <input type="text" name="telefono" value="<?php echo e($representante['telefono'] ?? ''); ?>" required>
                    </div>

                <?php elseif ($step === 3): ?>
                    <h3>Seguridad</h3>
                    <div class="form-group">
                        <label>Contraseña *</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" required>
                            <button type="button" onclick="togglePassword('password')">👁️</button>
                        </div>
                        <small>Mínimo 12 caracteres, con mayúsculas, minúsculas, números y símbolos</small>
                    </div>
                    <div class="form-group">
                        <label>Confirmar Contraseña *</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                <?php elseif ($step === 4): ?>
                    <h3>Selecciona tu Plan</h3>
                    <div class="plan-selector">
                        <label class="plan-option">
                            <input type="radio" name="plan" value="starter" <?php echo $plan === 'starter' ? 'checked' : ''; ?>>
                            <div class="plan-details">
                                <strong>Starter</strong> - Gratis
                                <p>1 empresa, 1 usuario, facturación básica</p>
                            </div>
                        </label>
                        <label class="plan-option">
                            <input type="radio" name="plan" value="profesional" <?php echo $plan === 'profesional' ? 'checked' : ''; ?>>
                            <div class="plan-details">
                                <strong>Profesional</strong> - $49.990/mes
                                <p>5 usuarios, contabilidad completa</p>
                            </div>
                        </label>
                        <label class="plan-option">
                            <input type="radio" name="plan" value="empresa" <?php echo $plan === 'empresa' ? 'checked' : ''; ?>>
                            <div class="plan-details">
                                <strong>Empresa</strong> - $99.990/mes
                                <p>Usuarios ilimitados, producción</p>
                            </div>
                        </label>
                    </div>

                <?php elseif ($step === 5): ?>
                    <h3>Confirmación</h3>
                    <div class="summary">
                        <p><strong>Empresa:</strong> <?php echo e($empresa['razon_social']); ?></p>
                        <p><strong>RUT:</strong> <?php echo e($empresa['rut']); ?></p>
                        <p><strong>Email:</strong> <?php echo e($representante['email']); ?></p>
                        <p><strong>Plan:</strong> <?php echo ucfirst($plan); ?></p>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="acepta_terminos" required>
                            Acepto los términos y condiciones
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="acepta_privacidad" required>
                            Acepto la política de privacidad
                        </label>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <?php if ($step > 1): ?>
                        <a href="register.php?step=<?php echo $step - 1; ?>" class="btn-secondary">Anterior</a>
                    <?php endif; ?>
                    <button type="submit" class="btn-primary">
                        <?php echo $step === 5 ? 'Crear cuenta' : 'Siguiente'; ?>
                    </button>
                </div>
            </form>

            <div class="auth-card-footer">
                <p>¿Ya tienes cuenta? <a href="login.php">Ingresar</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
