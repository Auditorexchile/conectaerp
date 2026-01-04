<?php
/**
 * CONECTA ERP - ROUTER CENTRALIZADO
 *
 * TODAS las peticiones al ERP pasan por aquí
 * Protección de sesión + permisos + trial
 */

define('CONECTA_ERP', true);
require_once __DIR__ . '/core/bootstrap.php';

// Verificar autenticación
if (!Session::isAuthenticated()) {
    redirect('/public/login.php');
}

// ==================== SEGURIDAD ANTI-HACKER ====================

// Lista blanca de módulos permitidos
$modulosPermitidos = [
    'dashboard', 'ia', 'fi', 'sd', 'mm', 'hr', 'crm',
    'proyectos', 'calidad', 'mantenimiento', 'bi', 'ecommerce',
    'reloj', 'api', 'config', 'perfil', 'notificaciones', 'ayuda'
];

// Obtener y sanitizar parámetros
$module = $_GET['module'] ?? 'dashboard';
$sub = $_GET['sub'] ?? '';
$view = $_GET['view'] ?? 'index';

// VALIDACIÓN 1: Solo caracteres alfanuméricos y guiones bajos
if (!preg_match('/^[a-z0-9_]+$/i', $module)) {
    error_log("⚠️ Intento de acceso sospechoso - Módulo inválido: " . $module . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    die('Acceso denegado: parámetros inválidos');
}

// VALIDACIÓN 2: Verificar que el módulo esté en la whitelist
if (!in_array($module, $modulosPermitidos)) {
    error_log("⚠️ Intento de acceso no autorizado - Módulo: " . $module . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    setFlash('error', 'Módulo no permitido');
    redirect('/app/router.php?module=dashboard');
}

// VALIDACIÓN 3: Validar submódulo si existe
if (!empty($sub) && !preg_match('/^[a-z0-9_]+$/i', $sub)) {
    error_log("⚠️ Intento de acceso sospechoso - Submódulo inválido: " . $sub . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    die('Acceso denegado: parámetros inválidos');
}

// VALIDACIÓN 4: Prevenir path traversal
if (strpos($module, '..') !== false || strpos($sub, '..') !== false || strpos($view, '..') !== false) {
    error_log("🚨 ALERTA DE SEGURIDAD - Path traversal detectado | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    die('Acceso denegado: ataque detectado');
}

// VALIDACIÓN 5: Validar caracteres de vista
if (!preg_match('/^[a-z0-9_]+$/i', $view)) {
    error_log("⚠️ Intento de acceso sospechoso - Vista inválida: " . $view . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    die('Acceso denegado: parámetros inválidos');
}

// Verificar si está en trial vencido
if (!isInTrial() && !Session::isSuperAdmin()) {
    $suscripcion = db()->selectOne(
        "SELECT * FROM suscripciones
         WHERE empresa_id = :empresa_id
         AND activo = true
         ORDER BY created_at DESC LIMIT 1",
        ['empresa_id' => Session::getEmpresaId()]
    );

    if (!$suscripcion || $suscripcion['plan_id'] == 1) { // Plan starter o sin suscripción
        // Mostrar modal de trial vencido
        if ($module !== 'dashboard') {
            setFlash('warning', 'Tu período de prueba ha finalizado. Actualiza tu plan para acceder a todas las funcionalidades.');
            redirect('/app/router.php?module=dashboard');
        }
    }
}

// Verificar acceso al módulo según plan
if (!Session::isSuperAdmin() && !planHasModule($module)) {
    setFlash('error', 'Tu plan actual no tiene acceso a este módulo.');
    redirect('/app/router.php?module=dashboard');
}

// Determinar ruta del módulo/submódulo de manera segura
if (!empty($sub)) {
    // Si hay submódulo, cargar desde subdirectorio
    // Ej: module=ia&sub=dashboard → /modules/ia/dashboard.php
    $modulePath = __DIR__ . '/modules/' . basename($module) . '/' . basename($sub) . '.php';
} else {
    // Si no hay submódulo, cargar vista principal
    // Ej: module=dashboard → /modules/dashboard/index.php
    $modulePath = __DIR__ . '/modules/' . basename($module) . '/' . basename($view) . '.php';
}

// VALIDACIÓN 6: Verificar que el archivo existe y está dentro del directorio permitido
$realPath = realpath($modulePath);
$allowedBase = realpath(__DIR__ . '/modules/');

if (!$realPath || strpos($realPath, $allowedBase) !== 0) {
    error_log("🚨 ALERTA DE SEGURIDAD - Intento de acceso fuera del directorio permitido | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    setFlash('error', 'Acceso denegado');
    redirect('/app/router.php?module=dashboard');
}

if (!file_exists($modulePath)) {
    error_log("⚠️ Archivo no encontrado: " . $modulePath . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    setFlash('error', 'Módulo no encontrado');
    redirect('/app/router.php?module=dashboard');
}

// Cargar layout
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';

echo '<div class="main-content" id="main-content">';

// Cargar vista del módulo
require_once $modulePath;

echo '</div>';

require_once __DIR__ . '/layout/footer.php';
