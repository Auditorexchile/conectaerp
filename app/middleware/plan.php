<?php
/**
 * Plan Middleware - Verificación de permisos por plan
 */

class PlanMiddleware {

    public static function requirePermission($permission) {
        if (!Permissions::can($permission)) {
            self::showUpgradePage($permission);
        }
    }

    public static function requireModule($module) {
        if (!Permissions::can($module)) {
            self::showUpgradePage($module);
        }
    }

    private static function showUpgradePage($feature) {
        $plan = SessionManager::getPlan();

        http_response_code(403);
        require BASE_PATH . '/app/layout/upgrade_required.php';
        exit;
    }

    public static function checkMaxUsers() {
        $db = Database::getInstance();
        $empresaId = SessionManager::getEmpresaId();

        $result = $db->fetchOne(
            "SELECT COUNT(*) as total FROM usuarios WHERE empresa_id = ? AND activo = 1",
            [$empresaId]
        );

        if (!Permissions::checkMaxUsers($result['total'])) {
            SessionManager::setFlash('error',
                'Has alcanzado el límite de usuarios para tu plan. Actualiza para agregar más usuarios.'
            );
            redirect('/app/dashboard/dashboard.php');
        }
    }
}
