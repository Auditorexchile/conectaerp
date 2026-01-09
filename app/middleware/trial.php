<?php
/**
 * Trial Middleware - Verificación de estado trial
 */

class TrialMiddleware {

    public static function handle() {
        if (!Permissions::checkTrialAccess()) {
            self::showExpiredPage();
        }

        // Advertir si quedan pocos días
        $diasRestantes = SessionManager::getTrialDaysRemaining();
        if ($diasRestantes !== null && $diasRestantes <= 3 && $diasRestantes > 0) {
            SessionManager::setFlash('trial_warning',
                "Tu período de prueba expira en {$diasRestantes} días. Actualiza tu plan para seguir usando el sistema."
            );
        }
    }

    private static function showExpiredPage() {
        http_response_code(403);
        require BASE_PATH . '/app/layout/trial_expired.php';
        exit;
    }
}
