<?php
/**
 * CSRF Middleware - Verificación de tokens CSRF
 */

class CsrfMiddleware {

    public static function verify() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';

            if (!SessionManager::validateCsrfToken($token)) {
                error_log('CSRF token validation failed from IP: ' . self::getIP());
                http_response_code(419);
                die('CSRF token mismatch. Por favor, recarga la página e intenta nuevamente.');
            }

            // Regenerar token después de uso
            SessionManager::regenerateCsrfToken();
        }
    }

    private static function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
