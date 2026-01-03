<?php
/**
 * CONECTA ERP - FUNCIONES AYUDANTES
 *
 * Funciones globales útiles para todo el sistema
 */

/**
 * Escapar HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener configuración
 */
function config($key, $default = null) {
    static $config = null;

    if ($config === null) {
        $config = require CONFIG_PATH . '/app.php';
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value;
}

/**
 * Redirigir
 */
function redirect($url) {
    header("Location: {$url}");
    exit;
}

/**
 * Obtener URL base
 */
function url($path = '') {
    return config('app_url') . '/' . ltrim($path, '/');
}

/**
 * Obtener asset URL
 */
function asset($path) {
    return url('public/assets/' . ltrim($path, '/'));
}

/**
 * Traducir texto
 */
function __($key, $replacements = []) {
    $locale = Session::get('locale', config('app_locale'));

    // Cargar traducciones
    static $translations = [];

    if (!isset($translations[$locale])) {
        $file = APP_PATH . '/lang/' . $locale . '.php';
        $translations[$locale] = file_exists($file) ? require $file : [];
    }

    $text = $translations[$locale][$key] ?? $key;

    // Reemplazos
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace(":{$placeholder}", $value, $text);
    }

    return $text;
}

/**
 * Formato de fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';

    if (is_string($date)) {
        $date = new DateTime($date);
    }

    return $date->format($format);
}

/**
 * Formato de fecha y hora
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    return formatDate($datetime, $format);
}

/**
 * Formato de moneda
 */
function formatCurrency($amount, $currency = 'CLP') {
    if ($currency === 'CLP') {
        return '$' . number_format($amount, 0, ',', '.');
    } elseif ($currency === 'UF') {
        return 'UF ' . number_format($amount, 2, ',', '.');
    } else {
        return $currency . ' ' . number_format($amount, 2, ',', '.');
    }
}

/**
 * Formato de número
 */
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals, ',', '.');
}

/**
 * Verificar si está en trial
 */
function isInTrial() {
    $empresaId = Session::getEmpresaId();
    if (!$empresaId) return false;

    $empresa = db()->selectOne(
        "SELECT e.*, t.fecha_inicio, t.fecha_fin, t.activo as trial_activo
         FROM empresas e
         LEFT JOIN trial_configuracion t ON e.id = t.empresa_id
         WHERE e.id = :id",
        ['id' => $empresaId]
    );

    if (!$empresa || !$empresa['trial_activo']) {
        return false;
    }

    $fechaFin = new DateTime($empresa['fecha_fin']);
    $hoy = new DateTime();

    return $hoy <= $fechaFin;
}

/**
 * Obtener días restantes de trial
 */
function getDaysRemainingTrial() {
    $empresaId = Session::getEmpresaId();
    if (!$empresaId) return 0;

    $trial = db()->selectOne(
        "SELECT fecha_fin FROM trial_configuracion WHERE empresa_id = :id AND activo = true",
        ['id' => $empresaId]
    );

    if (!$trial) return 0;

    $fechaFin = new DateTime($trial['fecha_fin']);
    $hoy = new DateTime();

    if ($hoy > $fechaFin) return 0;

    $diff = $hoy->diff($fechaFin);
    return $diff->days;
}

/**
 * Verificar si tiene permiso
 */
function hasPermission($permission) {
    // Superadmin tiene todos los permisos
    if (Session::isSuperAdmin()) {
        return true;
    }

    $userId = Session::getUserId();
    if (!$userId) return false;

    $result = db()->selectOne(
        "SELECT COUNT(*) as count
         FROM usuario_permisos up
         JOIN permisos p ON up.permiso_id = p.id
         WHERE up.usuario_id = :user_id
         AND p.codigo = :permission
         AND up.activo = true",
        ['user_id' => $userId, 'permission' => $permission]
    );

    return $result && $result['count'] > 0;
}

/**
 * Verificar si plan tiene acceso a módulo
 */
function planHasModule($module) {
    $empresaId = Session::getEmpresaId();
    if (!$empresaId) return false;

    // Obtener plan actual
    $suscripcion = db()->selectOne(
        "SELECT p.codigo
         FROM suscripciones s
         JOIN planes p ON s.plan_id = p.id
         WHERE s.empresa_id = :empresa_id
         AND s.activo = true
         ORDER BY s.created_at DESC
         LIMIT 1",
        ['empresa_id' => $empresaId]
    );

    if (!$suscripcion) {
        // Si no tiene suscripción, usar plan starter
        $planCodigo = 'starter';
    } else {
        $planCodigo = $suscripcion['codigo'];
    }

    // Verificar acceso
    $modulosConfig = config('modules');

    // Lógica de acceso por plan (simplificada)
    $planModules = [
        'starter' => ['dashboard', 'ventas', 'inventario'],
        'profesional' => ['dashboard', 'contabilidad', 'ventas', 'compras', 'inventario'],
        'empresa' => array_keys($modulosConfig), // Todos
        'corporativo' => array_keys($modulosConfig), // Todos
    ];

    return in_array($module, $planModules[$planCodigo] ?? []);
}

/**
 * Debug (solo en desarrollo)
 */
function dd(...$vars) {
    if (config('app_env') === 'development') {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

/**
 * Generar mensaje flash
 */
function setFlash($type, $message) {
    Session::flash($type, $message);
}

/**
 * Obtener mensaje flash
 */
function getFlash($type) {
    return Session::flash($type);
}
