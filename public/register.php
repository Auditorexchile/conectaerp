<?php
/**
 * CONECTA ERP - REGISTRO DE EMPRESAS
 * Wizard de 5 pasos profesional
 */

define('CONECTA_ERP', true);
require_once __DIR__ . '/../app/core/bootstrap.php';
require_once __DIR__ . '/../app/helpers/validators.php';
require_once __DIR__ . '/../app/helpers/countries.php';

// Si ya está autenticado, redirigir al dashboard
if (Session::isAuthenticated()) {
    redirect('/app/router.php?module=dashboard');
}

// Paso actual
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$step = max(1, min(5, $step)); // Entre 1 y 5

// Variables de estado
$error = '';
$success = '';

// Cargar datos de países e idiomas
$paises = CountryHelper::getActiveCountries();
$idiomas = LanguageHelper::getActiveLanguages();
$planes = PlanHelper::getActivePlans();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // PASO 1: Datos de Empresa
    if ($step === 1) {
        $paisCodigo = Security::sanitize($_POST['pais'] ?? '');
        $identificador = Security::sanitize($_POST['identificador'] ?? '');
        $razonSocial = Security::sanitize($_POST['razon_social'] ?? '');
        $giroId = intval($_POST['giro_id'] ?? 0);
        $direccion = Security::sanitize($_POST['direccion'] ?? '');
        $regionId = intval($_POST['region_id'] ?? 0);
        $comunaId = intval($_POST['comuna_id'] ?? 0);
        $telefono = Security::sanitize($_POST['telefono'] ?? '');
        $email = Security::sanitize($_POST['email'] ?? '');

        // Validaciones
        if (empty($paisCodigo) || empty($identificador) || empty($razonSocial)) {
            $error = 'Todos los campos obligatorios deben ser completados';
        } elseif (!Validators::validarIdentificadorPorPais($paisCodigo, $identificador)) {
            $tipoId = CountryHelper::getIdentifierType($paisCodigo);
            $error = "$tipoId inválido. Verifique el formato.";
        } elseif (!Validators::validarEmail($email)) {
            $error = 'Email inválido';
        } else {
            // Verificar que no exista
            $existe = db()->selectOne(
                "SELECT COUNT(*) as total FROM empresas WHERE pais_codigo = :pais AND identificador = :id",
                ['pais' => $paisCodigo, 'id' => $identificador]
            );

            if ($existe && $existe['total'] > 0) {
                $error = 'Esta empresa ya está registrada';
            } else {
                // Guardar en sesión
                Session::set('register_paso1', [
                    'pais_codigo' => $paisCodigo,
                    'identificador' => $identificador,
                    'razon_social' => $razonSocial,
                    'giro_id' => $giroId,
                    'direccion' => $direccion,
                    'region_id' => $regionId,
                    'comuna_id' => $comunaId,
                    'telefono' => $telefono,
                    'email' => $email
                ]);
                redirect('register.php?step=2');
            }
        }
    }

    // PASO 2: Representante Legal
    elseif ($step === 2) {
        $paso1 = Session::get('register_paso1');
        if (!$paso1) {
            redirect('register.php?step=1');
        }

        $repNombre = Security::sanitize($_POST['rep_nombre'] ?? '');
        $repIdentificador = Security::sanitize($_POST['rep_identificador'] ?? '');
        $repEmail = Security::sanitize($_POST['rep_email'] ?? '');
        $repTelefono = Security::sanitize($_POST['rep_telefono'] ?? '');
        $repCargo = Security::sanitize($_POST['rep_cargo'] ?? 'Gerente General');

        if (empty($repNombre) || empty($repIdentificador) || empty($repEmail)) {
            $error = 'Todos los campos obligatorios deben ser completados';
        } elseif (!Validators::validarIdentificadorPorPais($paso1['pais_codigo'], $repIdentificador)) {
            $tipoId = CountryHelper::getIdentifierType($paso1['pais_codigo']);
            $error = "$tipoId del representante inválido";
        } elseif (!Validators::validarEmail($repEmail)) {
            $error = 'Email inválido';
        } else {
            Session::set('register_paso2', [
                'rep_nombre' => $repNombre,
                'rep_identificador' => $repIdentificador,
                'rep_email' => $repEmail,
                'rep_telefono' => $repTelefono,
                'rep_cargo' => $repCargo
            ]);
            redirect('register.php?step=3');
        }
    }

    // PASO 3: Seguridad
    elseif ($step === 3) {
        if (!Session::get('register_paso1') || !Session::get('register_paso2')) {
            redirect('register.php?step=1');
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $idiomaCodigo = Security::sanitize($_POST['idioma'] ?? 'es');

        if (empty($password)) {
            $error = 'La contraseña es obligatoria';
        } elseif ($password !== $passwordConfirm) {
            $error = 'Las contraseñas no coinciden';
        } else {
            $validacion = Validators::validarPassword($password);
            if (!$validacion['valido']) {
                $error = $validacion['mensaje'];
            } else {
                Session::set('register_paso3', [
                    'password' => $password,
                    'idioma' => $idiomaCodigo
                ]);
                redirect('register.php?step=4');
            }
        }
    }

    // PASO 4: Selección de Plan
    elseif ($step === 4) {
        if (!Session::get('register_paso1') || !Session::get('register_paso2') || !Session::get('register_paso3')) {
            redirect('register.php?step=1');
        }

        $planCodigo = Security::sanitize($_POST['plan'] ?? 'starter');

        Session::set('register_paso4', [
            'plan_codigo' => $planCodigo
        ]);
        redirect('register.php?step=5');
    }

    // PASO 5: Confirmación y Creación
    elseif ($step === 5) {
        $paso1 = Session::get('register_paso1');
        $paso2 = Session::get('register_paso2');
        $paso3 = Session::get('register_paso3');
        $paso4 = Session::get('register_paso4');

        if (!$paso1 || !$paso2 || !$paso3 || !$paso4) {
            redirect('register.php?step=1');
        }

        $aceptaTerminos = isset($_POST['acepta_terminos']);
        $aceptaPrivacidad = isset($_POST['acepta_privacidad']);

        if (!$aceptaTerminos || !$aceptaPrivacidad) {
            $error = 'Debe aceptar los términos y condiciones y la política de privacidad';
        } else {
            // Crear empresa, usuario y todo
            try {
                db()->beginTransaction();

                // 1. Obtener plan
                $plan = PlanHelper::getPlanInfo($paso4['plan_codigo']);

                // 2. Crear empresa
                $empresaId = db()->insert('empresas', [
                    'pais_codigo' => $paso1['pais_codigo'],
                    'identificador' => $paso1['identificador'],
                    'razon_social' => $paso1['razon_social'],
                    'giro_id' => $paso1['giro_id'] ?: null,
                    'direccion' => $paso1['direccion'],
                    'region_id' => $paso1['region_id'] ?: null,
                    'comuna_id' => $paso1['comuna_id'] ?: null,
                    'telefono' => $paso1['telefono'],
                    'email' => $paso1['email'],
                    'rep_legal_nombre' => $paso2['rep_nombre'],
                    'rep_legal_identificador' => $paso2['rep_identificador'],
                    'rep_legal_email' => $paso2['rep_email'],
                    'rep_legal_telefono' => $paso2['rep_telefono'],
                    'rep_legal_cargo' => $paso2['rep_cargo'],
                    'plan_id' => $plan['id'],
                    'idioma_codigo' => $paso3['idioma'],
                    'moneda_base' => CountryHelper::getCurrencyInfo($paso1['pais_codigo'])['codigo'],
                    'en_trial' => $plan['tiene_trial'] ? 1 : 0,
                    'fecha_inicio_trial' => date('Y-m-d'),
                    'fecha_fin_trial' => date('Y-m-d', strtotime('+' . $plan['dias_trial'] . ' days')),
                    'estado' => 'trial',
                    'suscripcion_activa' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // 3. Crear usuario administrador
                $usuarioId = db()->insert('usuarios_acceso', [
                    'empresa_id' => $empresaId,
                    'email' => $paso2['rep_email'],
                    'password_hash' => Security::hashPassword($paso3['password']),
                    'nombre' => $paso2['rep_nombre'],
                    'tipo_usuario' => 'admin',
                    'estado' => 'activo',
                    'email_verificado' => 0,
                    'idioma_codigo' => $paso3['idioma'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // 4. Crear log de trial
                if ($plan['tiene_trial']) {
                    db()->insert('trial_log', [
                        'empresa_id' => $empresaId,
                        'fecha_inicio' => date('Y-m-d'),
                        'fecha_fin' => date('Y-m-d', strtotime('+' . $plan['dias_trial'] . ' days')),
                        'dias_totales' => $plan['dias_trial'],
                        'activo' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // 5. Registrar aceptación de términos
                $termino = db()->selectOne("SELECT id FROM terminos_condiciones WHERE tipo = 'terminos' AND activo = 1 ORDER BY fecha_vigencia DESC LIMIT 1");
                if ($termino) {
                    db()->insert('terminos_aceptados', [
                        'empresa_id' => $empresaId,
                        'usuario_id' => $usuarioId,
                        'termino_id' => $termino['id'],
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'acepto' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                db()->commit();

                // Limpiar sesiones de registro
                Session::remove('register_paso1');
                Session::remove('register_paso2');
                Session::remove('register_paso3');
                Session::remove('register_paso4');

                // Autenticar automáticamente
                Session::set('user_id', $usuarioId);
                Session::set('empresa_id', $empresaId);
                Session::set('authenticated', true);

                redirect('/app/router.php?module=dashboard');

            } catch (Exception $e) {
                if (db()->getPDO()->inTransaction()) {
                    db()->rollback();
                }
                $error = 'Error al crear la cuenta. Por favor intente nuevamente.';
                error_log('Error registro: ' . $e->getMessage());
            }
        }
    }
}

// Recuperar datos guardados
$paso1 = Session::get('register_paso1', []);
$paso2 = Session::get('register_paso2', []);
$paso3 = Session::get('register_paso3', []);
$paso4 = Session::get('register_paso4', []);

// Cargar datos dinámicos según país seleccionado
$regiones = [];
$comunas = [];
$giros = [];

if (!empty($paso1['pais_codigo'])) {
    $regiones = CountryHelper::getRegions($paso1['pais_codigo']);
    $giros = CountryHelper::getBusinessActivities($paso1['pais_codigo']);

    if (!empty($paso1['region_id'])) {
        $comunas = CountryHelper::getCommunesByRegion($paso1['region_id']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Conecta ERP</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body class="register-page">

    <!-- Header -->
    <header class="register-header">
        <div class="container">
            <a href="index.php" class="logo">
                <span class="logo-icon">📊</span>
                <span class="logo-text">Conecta ERP</span>
            </a>
            <div class="header-actions">
                <a href="login.php" class="btn btn-secondary">Ingresar</a>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="register-main">
        <div class="container">
            <div class="register-wizard">

                <!-- Progress Steps -->
                <div class="wizard-progress">
                    <div class="progress-step <?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">
                        <div class="step-icon"><?= $step > 1 ? '✓' : '1' ?></div>
                        <div class="step-label">Empresa</div>
                    </div>
                    <div class="progress-line <?= $step >= 2 ? 'active' : '' ?>"></div>
                    <div class="progress-step <?= $step >= 2 ? 'active' : '' ?> <?= $step > 2 ? 'completed' : '' ?>">
                        <div class="step-icon"><?= $step > 2 ? '✓' : '2' ?></div>
                        <div class="step-label">Representante</div>
                    </div>
                    <div class="progress-line <?= $step >= 3 ? 'active' : '' ?>"></div>
                    <div class="progress-step <?= $step >= 3 ? 'active' : '' ?> <?= $step > 3 ? 'completed' : '' ?>">
                        <div class="step-icon"><?= $step > 3 ? '✓' : '3' ?></div>
                        <div class="step-label">Seguridad</div>
                    </div>
                    <div class="progress-line <?= $step >= 4 ? 'active' : '' ?>"></div>
                    <div class="progress-step <?= $step >= 4 ? 'active' : '' ?> <?= $step > 4 ? 'completed' : '' ?>">
                        <div class="step-icon"><?= $step > 4 ? '✓' : '4' ?></div>
                        <div class="step-label">Plan</div>
                    </div>
                    <div class="progress-line <?= $step >= 5 ? 'active' : '' ?>"></div>
                    <div class="progress-step <?= $step >= 5 ? 'active' : '' ?>">
                        <div class="step-icon">5</div>
                        <div class="step-label">Confirmación</div>
                    </div>
                </div>

                <!-- Wizard Card -->
                <div class="wizard-card">

                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <span class="alert-icon">⚠️</span>
                            <span class="alert-text"><?= e($error) ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="wizardForm" class="wizard-form">

                        <!-- PASO 1: Datos Empresa -->
                        <?php if ($step === 1): ?>
                            <div class="wizard-step">
                                <h2>Datos de la Empresa</h2>
                                <p class="step-description">Ingrese la información básica de su empresa</p>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>País <span class="required">*</span></label>
                                        <select name="pais" id="paisSelect" required onchange="handlePaisChange(this.value)">
                                            <option value="">Seleccione un país</option>
                                            <?php foreach ($paises as $pais): ?>
                                                <option value="<?= e($pais['codigo']) ?>" <?= ($paso1['pais_codigo'] ?? '') === $pais['codigo'] ? 'selected' : '' ?>>
                                                    <?= e($pais['bandera']) ?> <?= e($pais['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label id="identificadorLabel">Identificador Tributario <span class="required">*</span></label>
                                        <input type="text" name="identificador" id="identificador"
                                               value="<?= e($paso1['identificador'] ?? '') ?>"
                                               placeholder="Ej: 12.345.678-9" required>
                                        <small id="identificadorHint" class="form-hint"></small>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Razón Social <span class="required">*</span></label>
                                        <input type="text" name="razon_social" value="<?= e($paso1['razon_social'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Giro Comercial</label>
                                        <select name="giro_id" id="giroSelect">
                                            <option value="">Seleccione un giro</option>
                                            <?php foreach ($giros as $giro): ?>
                                                <option value="<?= $giro['id'] ?>" <?= ($paso1['giro_id'] ?? 0) == $giro['id'] ? 'selected' : '' ?>>
                                                    <?= e($giro['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Dirección <span class="required">*</span></label>
                                        <input type="text" name="direccion" value="<?= e($paso1['direccion'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="form-row form-row-2">
                                    <div class="form-group">
                                        <label>Región</label>
                                        <select name="region_id" id="regionSelect" onchange="handleRegionChange(this.value)">
                                            <option value="">Seleccione región</option>
                                            <?php foreach ($regiones as $region): ?>
                                                <option value="<?= $region['id'] ?>" <?= ($paso1['region_id'] ?? 0) == $region['id'] ? 'selected' : '' ?>>
                                                    <?= e($region['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Comuna/Ciudad</label>
                                        <select name="comuna_id" id="comunaSelect">
                                            <option value="">Seleccione comuna</option>
                                            <?php foreach ($comunas as $comuna): ?>
                                                <option value="<?= $comuna['id'] ?>" <?= ($paso1['comuna_id'] ?? 0) == $comuna['id'] ? 'selected' : '' ?>>
                                                    <?= e($comuna['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row form-row-2">
                                    <div class="form-group">
                                        <label>Teléfono <span class="required">*</span></label>
                                        <input type="tel" name="telefono" value="<?= e($paso1['telefono'] ?? '') ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email <span class="required">*</span></label>
                                        <input type="email" name="email" value="<?= e($paso1['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>

                        <!-- PASO 2: Representante Legal -->
                        <?php elseif ($step === 2): ?>
                            <div class="wizard-step">
                                <h2>Representante Legal</h2>
                                <p class="step-description">Información del representante legal de la empresa</p>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Nombre Completo <span class="required">*</span></label>
                                        <input type="text" name="rep_nombre" value="<?= e($paso2['rep_nombre'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label id="repIdentificadorLabel">
                                            <?= CountryHelper::getIdentifierType($paso1['pais_codigo']) ?> <span class="required">*</span>
                                        </label>
                                        <input type="text" name="rep_identificador" value="<?= e($paso2['rep_identificador'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="form-row form-row-2">
                                    <div class="form-group">
                                        <label>Email <span class="required">*</span></label>
                                        <input type="email" name="rep_email" value="<?= e($paso2['rep_email'] ?? '') ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Teléfono <span class="required">*</span></label>
                                        <input type="tel" name="rep_telefono" value="<?= e($paso2['rep_telefono'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Cargo</label>
                                        <input type="text" name="rep_cargo" value="<?= e($paso2['rep_cargo'] ?? 'Gerente General') ?>">
                                    </div>
                                </div>
                            </div>

                        <!-- PASO 3: Seguridad -->
                        <?php elseif ($step === 3): ?>
                            <div class="wizard-step">
                                <h2>Seguridad</h2>
                                <p class="step-description">Configure su contraseña e idioma preferido</p>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Idioma del Sistema <span class="required">*</span></label>
                                        <select name="idioma" required>
                                            <?php foreach ($idiomas as $idioma): ?>
                                                <option value="<?= e($idioma['codigo']) ?>" <?= ($paso3['idioma'] ?? 'es') === $idioma['codigo'] ? 'selected' : '' ?>>
                                                    <?= e($idioma['bandera']) ?> <?= e($idioma['nombre_nativo']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Contraseña <span class="required">*</span></label>
                                        <div class="password-input-wrapper">
                                            <input type="password" name="password" id="password" required>
                                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                                <span class="icon-eye">👁️</span>
                                            </button>
                                            <button type="button" class="password-generate" onclick="generatePassword()">
                                                <span>🎲 Generar</span>
                                            </button>
                                        </div>
                                        <div id="passwordStrength" class="password-strength" style="display:none;">
                                            <div class="strength-bar">
                                                <div class="strength-bar-fill" id="strengthBarFill"></div>
                                            </div>
                                            <div class="strength-text" id="strengthText"></div>
                                        </div>
                                        <small class="form-hint">Mínimo 8 caracteres, incluya mayúsculas, minúsculas y números</small>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Confirmar Contraseña <span class="required">*</span></label>
                                        <div class="password-input-wrapper">
                                            <input type="password" name="password_confirm" id="password_confirm" required>
                                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirm')">
                                                <span class="icon-eye">👁️</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- PASO 4: Plan -->
                        <?php elseif ($step === 4): ?>
                            <div class="wizard-step">
                                <h2>Seleccione su Plan</h2>
                                <p class="step-description">Todos los planes incluyen 14 días de prueba gratis</p>

                                <div class="plans-grid">
                                    <?php foreach ($planes as $plan): ?>
                                        <?php
                                        $precio = PlanHelper::getPlanPrice($plan['codigo'], $paso1['pais_codigo']);
                                        $moneda = CountryHelper::getCurrencyInfo($paso1['pais_codigo']);
                                        ?>
                                        <label class="plan-card <?= $plan['destacado'] ? 'plan-featured' : '' ?>">
                                            <input type="radio" name="plan" value="<?= e($plan['codigo']) ?>"
                                                   <?= ($paso4['plan_codigo'] ?? 'starter') === $plan['codigo'] ? 'checked' : '' ?> required>

                                            <?php if ($plan['badge']): ?>
                                                <div class="plan-badge"><?= e($plan['badge']) ?></div>
                                            <?php endif; ?>

                                            <div class="plan-header">
                                                <h3><?= e($plan['nombre']) ?></h3>
                                                <div class="plan-price">
                                                    <?php if ($precio === null || $precio === 0): ?>
                                                        <span class="price-free">Gratis</span>
                                                    <?php elseif ($precio === null): ?>
                                                        <span class="price-contact">Contactar</span>
                                                    <?php else: ?>
                                                        <span class="price-amount"><?= $moneda['simbolo'] ?><?= number_format($precio, 0, ',', '.') ?></span>
                                                        <span class="price-period">/mes</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="plan-features">
                                                <div class="feature">✓ <?= $plan['max_usuarios'] == 999 ? 'Usuarios ilimitados' : $plan['max_usuarios'] . ' usuario(s)' ?></div>
                                                <div class="feature">✓ <?= $plan['max_empresas'] == 999 ? 'Multi-empresa' : $plan['max_empresas'] . ' empresa(s)' ?></div>
                                                <div class="feature">✓ <?= $plan['almacenamiento_gb'] ?> GB almacenamiento</div>
                                                <?php if ($plan['tiene_soporte_chat']): ?>
                                                    <div class="feature">✓ Soporte chat</div>
                                                <?php endif; ?>
                                                <?php if ($plan['tiene_modulo_ia']): ?>
                                                    <div class="feature">✓ Módulo IA</div>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        <!-- PASO 5: Confirmación -->
                        <?php elseif ($step === 5): ?>
                            <div class="wizard-step">
                                <h2>Confirmación</h2>
                                <p class="step-description">Revise sus datos antes de crear la cuenta</p>

                                <div class="summary-box">
                                    <div class="summary-section">
                                        <h3>Empresa</h3>
                                        <p><strong>Razón Social:</strong> <?= e($paso1['razon_social']) ?></p>
                                        <p><strong>Identificador:</strong> <?= e($paso1['identificador']) ?></p>
                                        <p><strong>Email:</strong> <?= e($paso1['email']) ?></p>
                                    </div>

                                    <div class="summary-section">
                                        <h3>Representante Legal</h3>
                                        <p><strong>Nombre:</strong> <?= e($paso2['rep_nombre']) ?></p>
                                        <p><strong>Email:</strong> <?= e($paso2['rep_email']) ?></p>
                                    </div>

                                    <div class="summary-section">
                                        <h3>Plan Seleccionado</h3>
                                        <?php
                                        $planSeleccionado = PlanHelper::getPlanInfo($paso4['plan_codigo']);
                                        $precio = PlanHelper::getPlanPrice($paso4['plan_codigo'], $paso1['pais_codigo']);
                                        $moneda = CountryHelper::getCurrencyInfo($paso1['pais_codigo']);
                                        ?>
                                        <p><strong><?= e($planSeleccionado['nombre']) ?></strong></p>
                                        <?php if ($precio > 0): ?>
                                            <p><?= $moneda['simbolo'] ?><?= number_format($precio, 0, ',', '.') ?>/mes</p>
                                        <?php else: ?>
                                            <p>Gratis</p>
                                        <?php endif; ?>
                                        <?php if ($planSeleccionado['tiene_trial']): ?>
                                            <p class="trial-notice">✓ Incluye <?= $planSeleccionado['dias_trial'] ?> días de prueba gratis</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="terms-box">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="acepta_terminos" required>
                                        <span>Acepto los <a href="#" onclick="openTermsModal(event, 'terminos')">Términos y Condiciones</a></span>
                                    </label>

                                    <label class="checkbox-label">
                                        <input type="checkbox" name="acepta_privacidad" required>
                                        <span>Acepto la <a href="#" onclick="openTermsModal(event, 'privacidad')">Política de Privacidad</a></span>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Wizard Navigation -->
                        <div class="wizard-nav">
                            <?php if ($step > 1): ?>
                                <a href="register.php?step=<?= $step - 1 ?>" class="btn btn-secondary">
                                    ← Anterior
                                </a>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary">
                                <?= $step === 5 ? '✓ Crear Cuenta' : 'Siguiente →' ?>
                            </button>
                        </div>

                    </form>
                </div>

                <div class="wizard-footer">
                    <p>¿Ya tienes cuenta? <a href="login.php">Ingresar</a></p>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Términos y Condiciones -->
    <div id="termsModal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Términos y Condiciones</h2>
                <button class="modal-close" onclick="closeTermsModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <p>Cargando...</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeTermsModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="assets/js/register.js"></script>
</body>
</html>
