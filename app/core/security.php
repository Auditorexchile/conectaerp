<?php
/**
 * CONECTA ERP - SISTEMA DE SEGURIDAD
 *
 * Manejo de autenticación, hashing, validaciones y protección
 */

class Security {
    private static $config;

    public static function initialize() {
        self::$config = require CONFIG_PATH . '/security.php';
    }

    /**
     * Hash de contraseña usando Argon2id o Bcrypt
     */
    public static function hashPassword($password) {
        $algo = self::$config['password']['algorithm'];
        $options = self::$config['password']['options'];

        return password_hash($password, $algo, $options);
    }

    /**
     * Verificar contraseña
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Validar fortaleza de contraseña
     */
    public static function validatePasswordStrength($password) {
        $appConfig = require CONFIG_PATH . '/app.php';
        $rules = $appConfig['security'];

        $errors = [];

        if (strlen($password) < $rules['password_min_length']) {
            $errors[] = "La contraseña debe tener al menos {$rules['password_min_length']} caracteres";
        }

        if ($rules['password_require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "La contraseña debe contener al menos una mayúscula";
        }

        if ($rules['password_require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = "La contraseña debe contener al menos una minúscula";
        }

        if ($rules['password_require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = "La contraseña debe contener al menos un número";
        }

        if ($rules['password_require_symbols'] && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "La contraseña debe contener al menos un símbolo";
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Generar contraseña segura
     */
    public static function generateSecurePassword($length = 16) {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Sin I, O
        $lowercase = 'abcdefghjkmnpqrstuvwxyz'; // Sin i, l, o
        $numbers = '23456789'; // Sin 0, 1
        $symbols = '!@#$%^&*-_+=';

        $all = $uppercase . $lowercase . $numbers . $symbols;

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Generar token seguro
     */
    public static function generateToken($length = 64) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Validar RUT chileno
     */
    public static function validateRut($rut) {
        // Limpiar RUT
        $rut = preg_replace('/[^0-9kK]/', '', strtoupper($rut));

        if (strlen($rut) < 2) {
            return false;
        }

        $rutNum = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        // Calcular DV
        $sum = 0;
        $multiplier = 2;

        for ($i = strlen($rutNum) - 1; $i >= 0; $i--) {
            $sum += intval($rutNum[$i]) * $multiplier;
            $multiplier = $multiplier === 7 ? 2 : $multiplier + 1;
        }

        $remainder = $sum % 11;
        $calculatedDv = 11 - $remainder;

        if ($calculatedDv === 11) {
            $calculatedDv = '0';
        } elseif ($calculatedDv === 10) {
            $calculatedDv = 'K';
        } else {
            $calculatedDv = strval($calculatedDv);
        }

        return $dv === $calculatedDv;
    }

    /**
     * Formatear RUT chileno
     */
    public static function formatRut($rut) {
        $rut = preg_replace('/[^0-9kK]/', '', strtoupper($rut));

        if (strlen($rut) < 2) {
            return $rut;
        }

        $rutNum = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        $formatted = number_format($rutNum, 0, '', '.');

        return $formatted . '-' . $dv;
    }

    /**
     * Sanitizar entrada
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }

        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generar token CSRF
     */
    public static function generateCsrfToken() {
        $token = self::generateToken(32);
        Session::set('csrf_token', $token);
        Session::set('csrf_token_time', time());
        return $token;
    }

    /**
     * Verificar token CSRF
     */
    public static function verifyCsrfToken($token) {
        $sessionToken = Session::get('csrf_token');
        $tokenTime = Session::get('csrf_token_time');

        if (!$sessionToken || !$tokenTime) {
            return false;
        }

        // Token expirado (2 horas)
        if (time() - $tokenTime > 7200) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Verificar si IP está en whitelist (para superadmin)
     */
    public static function isIpWhitelisted($ip) {
        return in_array($ip, self::$config['ip_whitelist']);
    }

    /**
     * Verificar si IP está en blacklist
     */
    public static function isIpBlacklisted($ip) {
        // Verificar blacklist de configuración
        if (in_array($ip, self::$config['ip_blacklist'])) {
            return true;
        }

        // Verificar blacklist de base de datos
        $result = db()->selectOne(
            "SELECT COUNT(*) as count FROM ip_blacklist WHERE ip_address = :ip AND activo = true",
            ['ip' => $ip]
        );

        return $result && $result['count'] > 0;
    }

    /**
     * Registrar intento de login fallido
     */
    public static function recordFailedLogin($email, $ip) {
        db()->insert('intentos_login', [
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'exitoso' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Verificar si debe bloquearse
        $attempts = db()->selectOne(
            "SELECT COUNT(*) as count FROM intentos_login
             WHERE email = :email
             AND exitoso = false
             AND created_at > NOW() - INTERVAL '30 minutes'",
            ['email' => $email]
        );

        if ($attempts && $attempts['count'] >= 5) {
            self::blockUser($email);
        }
    }

    /**
     * Bloquear usuario
     */
    private static function blockUser($email) {
        db()->query(
            "UPDATE usuarios_acceso SET bloqueado = true, bloqueado_hasta = NOW() + INTERVAL '30 minutes' WHERE email = :email",
            ['email' => $email]
        );

        Audit::log('user_blocked', "Usuario bloqueado por intentos fallidos: {$email}");
    }

    /**
     * Obtener IP del cliente
     */
    public static function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}

/**
 * CLASE DE AUDITORÍA
 */
class Audit {
    public static function log($action, $description = '', $metadata = []) {
        $userId = Session::get('user_id');
        $empresaId = Session::get('empresa_id');

        db()->insert('auditoria_eventos', [
            'usuario_id' => $userId,
            'empresa_id' => $empresaId,
            'accion' => $action,
            'descripcion' => $description,
            'ip_address' => Security::getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'metadata' => json_encode($metadata),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function logPageView() {
        $userId = Session::get('user_id');
        if (!$userId) return;

        $page = $_SERVER['REQUEST_URI'] ?? '';
        self::log('page_view', $page);
    }
}
