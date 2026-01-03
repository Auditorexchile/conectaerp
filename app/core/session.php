<?php
/**
 * CONECTA ERP - GESTOR DE SESIONES
 *
 * Manejo seguro de sesiones de usuario
 */

class Session {
    private static $started = false;

    /**
     * Iniciar sesión
     */
    public static function start() {
        if (self::$started) {
            return;
        }

        $config = require CONFIG_PATH . '/app.php';

        session_name($config['security']['session_name']);
        session_start();

        self::$started = true;

        // Regenerar ID de sesión periódicamente
        if (!self::has('last_regeneration')) {
            self::regenerate();
        } elseif (time() - self::get('last_regeneration') > 600) { // Cada 10 minutos
            self::regenerate();
        }

        // Verificar timeout de inactividad
        if (self::isAuthenticated()) {
            $lastActivity = self::get('last_activity');
            if ($lastActivity && (time() - $lastActivity) > $config['security']['session_lifetime']) {
                self::destroy();
                header('Location: /public/login.php?timeout=1');
                exit;
            }
            self::set('last_activity', time());
        }
    }

    /**
     * Regenerar ID de sesión
     */
    public static function regenerate() {
        session_regenerate_id(true);
        self::set('last_regeneration', time());
    }

    /**
     * Establecer valor en sesión
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Obtener valor de sesión
     */
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Verificar si existe una clave
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Eliminar valor de sesión
     */
    public static function remove($key) {
        unset($_SESSION[$key]);
    }

    /**
     * Destruir sesión
     */
    public static function destroy() {
        if (self::$started) {
            session_destroy();
            $_SESSION = [];
            self::$started = false;
        }
    }

    /**
     * Verificar si usuario está autenticado
     */
    public static function isAuthenticated() {
        return self::has('user_id') && self::has('authenticated') && self::get('authenticated') === true;
    }

    /**
     * Iniciar sesión de usuario
     */
    public static function login($user) {
        self::regenerate(); // Regenerar ID al hacer login

        self::set('authenticated', true);
        self::set('user_id', $user['id']);
        self::set('empresa_id', $user['empresa_id']);
        self::set('email', $user['email']);
        self::set('nombre', $user['nombre'] ?? '');
        self::set('tipo_usuario', $user['tipo_usuario'] ?? 'normal');
        self::set('last_activity', time());

        // Registrar login en base de datos
        db()->query(
            "UPDATE usuarios_acceso SET ultimo_acceso = NOW(), ip_ultimo_acceso = :ip WHERE id = :id",
            ['ip' => Security::getClientIp(), 'id' => $user['id']]
        );

        // Auditoría
        Audit::log('login', "Usuario ingresó al sistema: {$user['email']}");
    }

    /**
     * Cerrar sesión
     */
    public static function logout() {
        $email = self::get('email');

        Audit::log('logout', "Usuario cerró sesión: {$email}");

        self::destroy();
    }

    /**
     * Verificar si es superusuario
     */
    public static function isSuperAdmin() {
        return self::isAuthenticated() && self::get('tipo_usuario') === 'superadmin';
    }

    /**
     * Obtener empresa actual
     */
    public static function getEmpresaId() {
        return self::get('empresa_id');
    }

    /**
     * Obtener usuario actual
     */
    public static function getUserId() {
        return self::get('user_id');
    }

    /**
     * Flash messages
     */
    public static function flash($key, $message = null) {
        if ($message === null) {
            $msg = self::get('flash_' . $key);
            self::remove('flash_' . $key);
            return $msg;
        } else {
            self::set('flash_' . $key, $message);
        }
    }
}
