<?php
require_once __DIR__ . '/../../public_html/config/database.php';
require_once __DIR__ . '/../../public_html/config/security.php';
require_once __DIR__ . '/../../public_html/config/validators.php';
require_once __DIR__ . '/validators.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/register.php');
}

// Verificar CSRF
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    error_response('CSRF token inválido');
}

$db = getDB();

try {
    $db->beginTransaction();

    // Validar datos de empresa
    $validation = validateEmpresaData($_POST);
    if (!$validation['valid']) {
        error_response($validation['error']);
    }

    // Verificar que el email no exista
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->fetch()) {
        error_response('El email ya está registrado');
    }

    // Verificar que el identificador fiscal no exista
    $stmt = $db->prepare("SELECT id FROM empresas WHERE identificador_fiscal = ?");
    $stmt->execute([$_POST['identificador_fiscal']]);
    if ($stmt->fetch()) {
        error_response('El identificador fiscal ya está registrado');
    }

    // Crear empresa
    $stmt = $db->prepare("
        INSERT INTO empresas (
            nombre, razon_social, identificador_fiscal, pais_id, moneda_id,
            direccion, ciudad, region, codigo_postal, telefono, email,
            plan_id, estado, trial_hasta
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'trial', DATE_ADD(NOW(), INTERVAL 14 DAY))
    ");

    $stmt->execute([
        $_POST['nombre_comercial'],
        $_POST['razon_social'],
        $_POST['identificador_fiscal'],
        $_POST['pais_id'],
        $_POST['moneda_id'],
        $_POST['direccion'],
        $_POST['ciudad'],
        $_POST['region'],
        $_POST['codigo_postal'],
        $_POST['telefono'],
        $_POST['email_empresa']
    ]);

    $empresaId = $db->lastInsertId();

    // Crear usuario administrador
    $passwordHash = password_hash($_POST['password'], PASSWORD_ARGON2ID);

    $stmt = $db->prepare("
        INSERT INTO usuarios (
            empresa_id, nombre, apellido, email, password_hash,
            identificador_personal, telefono, idioma_id, rol, activo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'admin', 1)
    ");

    $stmt->execute([
        $empresaId,
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['email'],
        $passwordHash,
        $_POST['identificador_personal'],
        $_POST['telefono_personal'],
        $_POST['idioma_id']
    ]);

    $usuarioId = $db->lastInsertId();

    // Crear configuración de empresa
    $stmt = $db->prepare("
        INSERT INTO empresa_configuracion (empresa_id, clave, valor)
        VALUES
            (?, 'fecha_inicio_fiscal', ?),
            (?, 'usa_inventario', '1'),
            (?, 'usa_proyectos', '0'),
            (?, 'notificaciones_email', '1')
    ");

    $stmt->execute([
        $empresaId, $_POST['fecha_inicio_fiscal'],
        $empresaId,
        $empresaId,
        $empresaId
    ]);

    // Registrar en auditoría
    $stmt = $db->prepare("
        INSERT INTO auditoria (usuario_id, empresa_id, accion, tabla, registro_id, ip_address, user_agent)
        VALUES (?, ?, 'registro', 'empresas', ?, ?, ?)
    ");
    $stmt->execute([
        $usuarioId,
        $empresaId,
        $empresaId,
        $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    $db->commit();

    // Auto-login
    $_SESSION['user_id'] = $usuarioId;
    $_SESSION['empresa_id'] = $empresaId;
    $_SESSION['authenticated'] = true;

    success_response(['redirect' => '/app/dashboard/dashboard.php'], 'Registro exitoso');

} catch (Exception $e) {
    $db->rollBack();
    error_log('Error en registro: ' . $e->getMessage());
    error_response('Error al procesar el registro. Intenta nuevamente.');
}
