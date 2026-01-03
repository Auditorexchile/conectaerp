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

// Obtener módulo solicitado
$module = $_GET['module'] ?? 'dashboard';
$view = $_GET['view'] ?? 'index';

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

// Cargar vista del módulo
$modulePath = __DIR__ . '/modules/' . $module . '/' . $view . '.php';

if (!file_exists($modulePath)) {
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
