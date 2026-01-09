<?php
/**
 * Security - Funciones de seguridad del core
 */

class Security {

    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    public static function hashToken($token) {
        return hash('sha256', $token);
    }

    public static function verifyToken($token, $hash) {
        return hash_equals($hash, hash('sha256', $token));
    }

    public static function encryptData($data, $key = null) {
        $key = $key ?? getenv('APP_KEY');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function decryptData($data, $key = null) {
        $key = $key ?? getenv('APP_KEY');
        list($encrypted, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }

    public static function preventXSS($data) {
        if (is_array($data)) {
            return array_map([self::class, 'preventXSS'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public static function checkRateLimit($identifier, $maxAttempts = 5, $decayMinutes = 15) {
        $db = Database::getInstance();

        $count = $db->fetchOne(
            "SELECT COUNT(*) as attempts FROM login_intentos
             WHERE email = ? AND exitoso = 0 AND fecha > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$identifier, $decayMinutes]
        );

        return $count['attempts'] < $maxAttempts;
    }

    public static function sanitizeFileName($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return substr($filename, 0, 255);
    }

    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 10485760) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['valid' => false, 'error' => 'Archivo inválido'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Error al subir archivo'];
        }

        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'Archivo muy grande'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!empty($allowedTypes) && !in_array($ext, $allowedTypes)) {
            return ['valid' => false, 'error' => 'Tipo de archivo no permitido'];
        }

        return ['valid' => true];
    }

    public static function isSecurePassword($password) {
        if (strlen($password) < 8) return false;
        if (!preg_match('/[A-Z]/', $password)) return false;
        if (!preg_match('/[a-z]/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false;
        if (!preg_match('/[^A-Za-z0-9]/', $password)) return false;
        return true;
    }
}
