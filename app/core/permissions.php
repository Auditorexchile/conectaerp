<?php
/**
 * Permissions - Control de permisos por plan
 */

class Permissions {

    private static $planPermissions = [
        'STARTER' => [
            'max_usuarios' => 1,
            'max_empresas' => 1,
            'modulos' => ['ventas_basico', 'inventario_basico'],
            'funcionalidades' => ['productos', 'facturas_basicas']
        ],
        'PRO' => [
            'max_usuarios' => 5,
            'max_empresas' => 1,
            'modulos' => ['contabilidad', 'ventas', 'compras', 'inventario'],
            'funcionalidades' => ['reportes', 'graficos', 'exportar']
        ],
        'EMPRESA' => [
            'max_usuarios' => 999,
            'max_empresas' => 1,
            'modulos' => ['todos'],
            'funcionalidades' => ['produccion', 'proyectos', 'sii', 'multimoneda']
        ],
        'CORP' => [
            'max_usuarios' => 9999,
            'max_empresas' => 999,
            'modulos' => ['todos'],
            'funcionalidades' => ['multiempresa', 'bi_avanzado', 'api', 'integraciones']
        ]
    ];

    public static function can($permission) {
        $plan = SessionManager::getPlan();

        if (SessionManager::isSuperUser()) {
            return true;
        }

        if (!isset(self::$planPermissions[$plan])) {
            return false;
        }

        $permissions = self::$planPermissions[$plan];

        // Verificar módulos
        if (isset($permissions['modulos']) && in_array('todos', $permissions['modulos'])) {
            return true;
        }

        // Verificar funcionalidades
        if (isset($permissions['funcionalidades']) && in_array($permission, $permissions['funcionalidades'])) {
            return true;
        }

        // Verificar módulos específicos
        if (isset($permissions['modulos']) && in_array($permission, $permissions['modulos'])) {
            return true;
        }

        return false;
    }

    public static function checkMaxUsers($currentUsers) {
        $plan = SessionManager::getPlan();
        $maxUsers = self::$planPermissions[$plan]['max_usuarios'] ?? 1;

        return $currentUsers < $maxUsers;
    }

    public static function checkTrialAccess() {
        if (SessionManager::isSuperUser()) {
            return true;
        }

        $estado = SessionManager::getEmpresaEstado();

        if ($estado === 'activo') {
            return true;
        }

        if ($estado === 'trial') {
            if (!SessionManager::isTrialExpired()) {
                return true;
            }
        }

        return false;
    }
}
