<?php
/**
 * REGISTER - CONECTA ERP
 * Registro completo multipaís con 5 secciones
 */

require_once 'config/database.php';
require_once 'config/security.php';
require_once 'config/validators.php';

initSecureSession();

// Si ya está logueado, redirigir
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            throw new Exception('Token de seguridad inválido');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        // ========== SECCIÓN 1: DATOS EMPRESA ==========
        $pais_id = intval($_POST['pais_id'] ?? 0);
        $tipo_entidad = sanitize($_POST['tipo_entidad'] ?? '');
        $identificador = sanitize($_POST['identificador_empresa'] ?? '');
        $razon_social = sanitize($_POST['razon_social'] ?? '');
        $nombre_fantasia = sanitize($_POST['nombre_fantasia'] ?? '');
        $giro = sanitize($_POST['giro'] ?? '');
        $actividad_principal = sanitize($_POST['actividad_principal'] ?? '');
        $actividad_secundaria = sanitize($_POST['actividad_secundaria'] ?? '');
        $tipo_contribuyente = sanitize($_POST['tipo_contribuyente'] ?? '');
        $regimen_tributario = sanitize($_POST['regimen_tributario'] ?? '');
        $fecha_inicio_actividades = sanitize($_POST['fecha_inicio_actividades'] ?? '');

        // ========== SECCIÓN 2: DOMICILIO ==========
        $direccion = sanitize($_POST['direccion'] ?? '');
        $numero = sanitize($_POST['numero'] ?? '');
        $oficina = sanitize($_POST['oficina'] ?? '');
        $codigo_postal = sanitize($_POST['codigo_postal'] ?? '');
        $region = sanitize($_POST['region'] ?? '');
        $provincia = sanitize($_POST['provincia'] ?? '');
        $ciudad = sanitize($_POST['ciudad'] ?? '');

        // ========== SECCIÓN 3: REPRESENTANTE LEGAL ==========
        $nombres = sanitize($_POST['nombres'] ?? '');
        $apellido_paterno = sanitize($_POST['apellido_paterno'] ?? '');
        $apellido_materno = sanitize($_POST['apellido_materno'] ?? '');
        $identificador_personal = sanitize($_POST['identificador_personal'] ?? '');
        $fecha_nacimiento = sanitize($_POST['fecha_nacimiento'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $sexo = sanitize($_POST['sexo'] ?? 'otro');
        $estado_civil = sanitize($_POST['estado_civil'] ?? '');

        // ========== SECCIÓN 4: SEGURIDAD ==========
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // ========== SECCIÓN 5: CONFIGURACIÓN ERP ==========
        $idioma_codigo = sanitize($_POST['idioma_codigo'] ?? 'es');
        $plan_id = intval($_POST['plan_id'] ?? 1);

        // ========== VALIDACIONES ==========
        if (!$pais_id || !$razon_social || !$identificador || !$nombres || !$email || !$password) {
            throw new Exception('Complete todos los campos obligatorios');
        }

        // Obtener info del país
        $pais = $db->fetchOne("SELECT * FROM paises WHERE id = ?", [$pais_id]);
        if (!$pais) {
            throw new Exception('País inválido');
        }

        // Validar identificador empresa según país
        $validation = CountryValidator::validate($identificador, $pais['codigo_iso']);
        if (!$validation['valid']) {
            throw new Exception('Identificador empresa inválido: ' . $validation['error']);
        }
        $identificador_formateado = $validation['formatted'];

        // Verificar que empresa no exista
        $existeEmpresa = $db->fetchOne(
            "SELECT id FROM empresas WHERE identificador = ?",
            [$identificador_formateado]
        );
        if ($existeEmpresa) {
            throw new Exception('Esta empresa ya está registrada');
        }

        // Validar email
        if (!validateEmail($email)) {
            throw new Exception('Email inválido');
        }

        // Verificar que email no exista
        $existeEmail = $db->fetchOne("SELECT id FROM usuarios WHERE email = ?", [$email]);
        if ($existeEmail) {
            throw new Exception('Este email ya está registrado');
        }

        // Validar contraseña
        $passwordValidation = validatePasswordStrength($password);
        if (!$passwordValidation['valid']) {
            throw new Exception($passwordValidation['error']);
        }

        if ($password !== $password_confirm) {
            throw new Exception('Las contraseñas no coinciden');
        }

        // ========== CREAR EMPRESA ==========
        $direccion_completa = trim("$direccion $numero $oficina");

        $empresa_id = $db->insert('empresas', [
            'pais_id' => $pais_id,
            'identificador' => $identificador_formateado,
            'razon_social' => $razon_social,
            'nombre_fantasia' => $nombre_fantasia ?: $razon_social,
            'giro' => $giro,
            'actividad_principal' => $actividad_principal,
            'tipo_entidad' => $tipo_entidad,
            'tipo_contribuyente' => $tipo_contribuyente,
            'regimen_tributario' => $regimen_tributario,
            'fecha_inicio_actividades' => $fecha_inicio_actividades ?: null,
            'direccion' => $direccion_completa,
            'ciudad' => $ciudad,
            'region' => $region,
            'codigo_postal' => $codigo_postal,
            'telefono' => $telefono,
            'email' => $email,
            'plan_id' => $plan_id,
            'fecha_trial_inicio' => null, // Se activa en primer login
            'fecha_trial_fin' => null,
            'estado' => 'trial',
            'activo' => 1
        ]);

        // ========== CREAR USUARIO (REPRESENTANTE LEGAL) ==========
        $password_hash = hashPassword($password);

        $usuario_id = $db->insert('usuarios', [
            'empresa_id' => $empresa_id,
            'pais_id' => $pais_id,
            'identificador' => $identificador_personal,
            'nombres' => $nombres,
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'email' => $email,
            'password_hash' => $password_hash,
            'telefono' => $telefono,
            'fecha_nacimiento' => $fecha_nacimiento ?: null,
            'sexo' => $sexo,
            'estado_civil' => $estado_civil,
            'es_representante_legal' => 1,
            'es_superusuario' => 0,
            'idioma_codigo' => $idioma_codigo,
            'zona_horaria' => $pais['zona_horaria'],
            'estado' => 'activo',
            'activo' => 1
        ]);

        // ========== CONFIGURACIÓN EMPRESA ==========
        $db->insert('empresa_configuracion', [
            'empresa_id' => $empresa_id,
            'moneda_base' => $pais['moneda_default'],
            'idioma_default' => $idioma_codigo,
            'zona_horaria' => $pais['zona_horaria'],
            'formato_fecha' => $pais['formato_fecha'],
            'separador_decimal' => $pais['codigo_iso'] === 'US' ? '.' : ',',
            'separador_miles' => $pais['codigo_iso'] === 'US' ? ',' : '.'
        ]);

        $db->commit();

        // Registro exitoso
        $_SESSION['registro_exitoso'] = true;
        header('Location: login.php?registered=1');
        exit;

    } catch (Exception $e) {
        if (isset($db)) {
            $db->rollback();
        }
        $error = $e->getMessage();
    }
}

// Obtener datos para formulario
$db = Database::getInstance();
$paises = $db->fetchAll("SELECT * FROM paises WHERE activo = 1 ORDER BY nombre_es");
$idiomas = $db->fetchAll("SELECT * FROM idiomas WHERE activo = 1 ORDER BY nombre_es");
$planes = $db->fetchAll("SELECT * FROM planes WHERE activo = 1 AND codigo != 'CORP' ORDER BY id");

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Conecta ERP</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body class="register-body">
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <a href="index.php" class="back-link">← Volver</a>
                <img src="assets/img/logo.png" alt="Conecta ERP" class="register-logo">
                <h1>Crear Cuenta</h1>
                <p>Complete los siguientes datos para comenzar</p>
            </div>

            <!-- Progress bar -->
            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Empresa</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Domicilio</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Representante</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Seguridad</div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-number">5</div>
                    <div class="step-label">Configuración</div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?= escape($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm" class="register-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- SECCIÓN 1: DATOS EMPRESA -->
                <div class="form-section active" id="section1">
                    <h3 class="section-title">Datos de la Empresa</h3>

                    <div class="form-group">
                        <label for="tipo_entidad">Tipo de Entidad *</label>
                        <select name="tipo_entidad" id="tipo_entidad" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <option value="empresa">Empresa</option>
                            <option value="persona_natural">Persona Natural con Giro</option>
                            <option value="profesional">Profesional Independiente</option>
                            <option value="ong">ONG / Fundación</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="pais_id">País *</label>
                            <select name="pais_id" id="pais_id" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($paises as $p): ?>
                                    <option value="<?= $p['id'] ?>"
                                        data-codigo="<?= $p['codigo_iso'] ?>"
                                        data-formato="<?= escape($p['formato_identificador']) ?>"
                                        data-tipo="<?= escape($p['tipo_identificador']) ?>"
                                        data-moneda="<?= $p['moneda_default'] ?>"
                                        data-zona="<?= $p['zona_horaria'] ?>">
                                        <?= escape($p['nombre_es']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="identificador_empresa"><span id="label_identificador">Identificador</span> *</label>
                            <input
                                type="text"
                                id="identificador_empresa"
                                name="identificador_empresa"
                                class="form-control"
                                placeholder="Seleccione primero el país"
                                required
                            >
                            <small class="form-text" id="formato_ayuda"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="razon_social">Razón Social *</label>
                        <input type="text" id="razon_social" name="razon_social" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre_fantasia">Nombre Fantasía o Comercial</label>
                        <input type="text" id="nombre_fantasia" name="nombre_fantasia" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="giro">Giro o Actividad Principal *</label>
                        <input type="text" id="giro" name="giro" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="actividad_principal">Descripción Actividad Principal</label>
                        <textarea id="actividad_principal" name="actividad_principal" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="tipo_contribuyente">Tipo de Contribuyente</label>
                            <select name="tipo_contribuyente" id="tipo_contribuyente" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="primera_categoria">Primera Categoría</option>
                                <option value="segunda_categoria">Segunda Categoría</option>
                                <option value="exento">Exento</option>
                                <option value="no_afecto">No Afecto</option>
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="regimen_tributario">Régimen Tributario</label>
                            <select name="regimen_tributario" id="regimen_tributario" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="general">General</option>
                                <option value="pro_pyme">Pro Pyme</option>
                                <option value="simplificado">Simplificado</option>
                                <option value="transparente">Transparente</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio_actividades">Fecha Inicio de Actividades</label>
                        <input type="date" id="fecha_inicio_actividades" name="fecha_inicio_actividades" class="form-control">
                    </div>

                    <button type="button" class="btn btn-primary btn-next" onclick="nextSection(2)">
                        Siguiente →
                    </button>
                </div>

                <!-- SECCIÓN 2: DOMICILIO -->
                <div class="form-section" id="section2">
                    <h3 class="section-title">Domicilio Legal</h3>

                    <div class="form-row">
                        <div class="form-group col-8">
                            <label for="direccion">Dirección *</label>
                            <input type="text" id="direccion" name="direccion" class="form-control" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="numero">Número *</label>
                            <input type="text" id="numero" name="numero" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="oficina">Oficina / Depto</label>
                            <input type="text" id="oficina" name="oficina" class="form-control">
                        </div>
                        <div class="form-group col-6">
                            <label for="codigo_postal">Código Postal</label>
                            <input type="text" id="codigo_postal" name="codigo_postal" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="region">Región / Estado *</label>
                        <input type="text" id="region" name="region" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="provincia">Provincia</label>
                        <input type="text" id="provincia" name="provincia" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="ciudad">Comuna / Ciudad *</label>
                        <input type="text" id="ciudad" name="ciudad" class="form-control" required>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="prevSection(1)">
                            ← Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-next" onclick="nextSection(3)">
                            Siguiente →
                        </button>
                    </div>
                </div>

                <!-- SECCIÓN 3: REPRESENTANTE LEGAL -->
                <div class="form-section" id="section3">
                    <h3 class="section-title">Representante Legal</h3>

                    <div class="form-group">
                        <label for="nombres">Nombres *</label>
                        <input type="text" id="nombres" name="nombres" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="apellido_paterno">Apellido Paterno *</label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="apellido_materno">Apellido Materno</label>
                            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="identificador_personal"><span id="label_identificador_personal">Identificación Personal</span> *</label>
                        <input type="text" id="identificador_personal" name="identificador_personal" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control">
                        </div>
                        <div class="form-group col-6">
                            <label for="sexo">Sexo</label>
                            <select name="sexo" id="sexo" class="form-control">
                                <option value="otro">Prefiero no especificar</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Corporativo (será su usuario) *</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="telefono">Teléfono *</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="estado_civil">Estado Civil</label>
                            <select name="estado_civil" id="estado_civil" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="soltero">Soltero(a)</option>
                                <option value="casado">Casado(a)</option>
                                <option value="divorciado">Divorciado(a)</option>
                                <option value="viudo">Viudo(a)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="prevSection(2)">
                            ← Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-next" onclick="nextSection(4)">
                            Siguiente →
                        </button>
                    </div>
                </div>

                <!-- SECCIÓN 4: SEGURIDAD -->
                <div class="form-section" id="section4">
                    <h3 class="section-title">Seguridad de Acceso</h3>

                    <div class="form-group">
                        <label for="password">Contraseña *</label>
                        <div class="password-input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-password" data-target="password">
                                <svg class="eye-open" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength">
                            <div class="strength-bar"></div>
                            <div class="strength-text"></div>
                        </div>
                        <small class="form-text">
                            Mínimo 8 caracteres, debe incluir mayúsculas, minúsculas, números y símbolos
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirmar Contraseña *</label>
                        <div class="password-input-wrapper">
                            <input
                                type="password"
                                id="password_confirm"
                                name="password_confirm"
                                class="form-control"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-password" data-target="password_confirm">
                                <svg class="eye-open" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary btn-sm" onclick="generatePassword()">
                        🔐 Generar contraseña segura
                    </button>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="prevSection(3)">
                            ← Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-next" onclick="nextSection(5)">
                            Siguiente →
                        </button>
                    </div>
                </div>

                <!-- SECCIÓN 5: CONFIGURACIÓN -->
                <div class="form-section" id="section5">
                    <h3 class="section-title">Configuración Inicial</h3>

                    <div class="form-group">
                        <label for="idioma_codigo">Idioma del Sistema *</label>
                        <select name="idioma_codigo" id="idioma_codigo" class="form-control" required>
                            <?php foreach ($idiomas as $i): ?>
                                <option value="<?= $i['codigo'] ?>" <?= $i['codigo'] === 'es' ? 'selected' : '' ?>>
                                    <?= escape($i['nombre_nativo']) ?> (<?= escape($i['nombre_es']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="plan_id">Seleccionar Plan *</label>
                        <?php foreach ($planes as $plan): ?>
                            <div class="plan-option" data-plan="<?= $plan['codigo'] ?>">
                                <input
                                    type="radio"
                                    name="plan_id"
                                    id="plan_<?= $plan['id'] ?>"
                                    value="<?= $plan['id'] ?>"
                                    <?= $plan['codigo'] === 'STARTER' ? 'checked' : '' ?>
                                    required
                                >
                                <label for="plan_<?= $plan['id'] ?>" class="plan-label">
                                    <div class="plan-name"><?= escape($plan['nombre']) ?></div>
                                    <div class="plan-price">
                                        <?php if ($plan['precio_mensual'] > 0): ?>
                                            $<?= number_format($plan['precio_mensual'], 0, ',', '.') ?>/mes
                                        <?php else: ?>
                                            Gratis
                                        <?php endif; ?>
                                    </div>
                                    <div class="plan-description"><?= escape($plan['descripcion']) ?></div>
                                    <div class="plan-trial">✓ <?= $plan['trial_dias'] ?> días de prueba gratis</div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="acepto_terminos" required>
                            <span>
                                Acepto los <a href="#" target="_blank">términos y condiciones</a> y la
                                <a href="#" target="_blank">política de privacidad</a> *
                            </span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="autorizo_validacion">
                            <span>Autorizo la validación de datos tributarios con organismos oficiales</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="prevSection(4)">
                            ← Anterior
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            🚀 Crear Cuenta
                        </button>
                    </div>
                </div>
            </form>

            <div class="register-footer">
                <p>¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/country-config.js"></script>
    <script src="assets/js/rut-validator.js"></script>
    <script src="assets/js/register.js"></script>
</body>
</html>
