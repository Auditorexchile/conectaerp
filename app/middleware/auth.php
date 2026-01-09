<?php
/**
 * Auth Middleware - Verificación de autenticación
 */

class AuthMiddleware {

    public static function handle() {
        if (!SessionManager::isAuthenticated()) {
            redirect('/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        }

        // Regenerar sesión periódicamente
        SessionManager::regenerate();

        // Actualizar última actividad
        SessionManager::updateActivity();
    }

    public static function guest() {
        if (SessionManager::isAuthenticated()) {
            redirect('/app/dashboard/dashboard.php');
        }
    }
}
