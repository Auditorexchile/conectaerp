<?php
/**
 * Audit - Sistema de auditoría
 */

class Audit {

    public static function log($action, $table = null, $recordId = null, $oldData = null, $newData = null) {
        $db = Database::getInstance();

        try {
            $db->insert('auditoria', [
                'usuario_id' => SessionManager::getUserId(),
                'empresa_id' => SessionManager::getEmpresaId(),
                'accion' => $action,
                'tabla' => $table,
                'registro_id' => $recordId,
                'datos_anteriores' => $oldData ? json_encode($oldData) : null,
                'datos_nuevos' => $newData ? json_encode($newData) : null,
                'ip_address' => self::getIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Error en auditoría: " . $e->getMessage());
        }
    }

    public static function logLogin($email, $success) {
        $db = Database::getInstance();

        $db->insert('login_intentos', [
            'email' => $email,
            'exitoso' => $success ? 1 : 0,
            'ip_address' => self::getIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    public static function logPasswordReset($usuarioId) {
        self::log('password_reset', 'usuarios', $usuarioId);
    }

    public static function logDataChange($table, $recordId, $oldData, $newData) {
        self::log('update', $table, $recordId, $oldData, $newData);
    }

    public static function logDelete($table, $recordId, $data) {
        self::log('delete', $table, $recordId, $data);
    }

    public static function logCreate($table, $recordId, $data) {
        self::log('create', $table, $recordId, null, $data);
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

    public static function getRecentActivity($limit = 10) {
        $db = Database::getInstance();

        return $db->fetchAll(
            "SELECT * FROM auditoria
             WHERE empresa_id = ?
             ORDER BY created_at DESC
             LIMIT ?",
            [SessionManager::getEmpresaId(), $limit]
        );
    }

    public static function getUserActivity($usuarioId, $limit = 50) {
        $db = Database::getInstance();

        return $db->fetchAll(
            "SELECT * FROM auditoria
             WHERE usuario_id = ?
             ORDER BY created_at DESC
             LIMIT ?",
            [$usuarioId, $limit]
        );
    }
}
