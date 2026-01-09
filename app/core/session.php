<?php
/**
 * Session Manager - Manejo seguro de sesiones
 */

class SessionManager {

    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');

            session_name('CONECTA_ERP_SESSION');
            session_start();

            // Regenerar ID cada 30 minutos
            if (!isset($_SESSION['LAST_REGENERATION'])) {
                self::regenerate();
            } else if (time() - $_SESSION['LAST_REGENERATION'] > 1800) {
                self::regenerate();
            }
        }
    }

    public static function regenerate() {
        session_regenerate_id(true);
        $_SESSION['LAST_REGENERATION'] = time();
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        unset($_SESSION[$key]);
    }

    public static function destroy() {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }

    public static function isLoggedIn() {
        return self::has('usuario_id') && self::has('empresa_id');
    }

    public static function getUserId() {
        return self::get('usuario_id');
    }

    public static function getEmpresaId() {
        return self::get('empresa_id');
    }

    public static function isSuperUser() {
        return self::get('es_superusuario', 0) == 1;
    }

    public static function getPlan() {
        return self::get('plan_codigo');
    }

    public static function getEmpresaEstado() {
        return self::get('empresa_estado');
    }

    public static function isTrialExpired() {
        $estado = self::getEmpresaEstado();
        return $estado === 'trial' && self::has('trial_fin') && strtotime(self::get('trial_fin')) < time();
    }

    public static function getTrialDaysRemaining() {
        if (!self::has('trial_fin')) return 0;

        $diff = strtotime(self::get('trial_fin')) - time();
        return max(0, ceil($diff / 86400));
    }
}
