<?php
/**
 * AJAX - Cambiar idioma del usuario
 */

define('CONECTA_ERP', true);
require_once __DIR__ . '/../core/bootstrap.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!Session::isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos
$data = json_decode(file_get_contents('php://input'), true);
$idioma = $data['idioma'] ?? '';

// Validar idioma
$idiomasDisponibles = array_keys(config('languages'));
if (!in_array($idioma, $idiomasDisponibles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Idioma no válido']);
    exit;
}

// Guardar en sesión
Session::set('locale', $idioma);

// Actualizar en base de datos (opcional)
$userId = Session::getUserId();
if ($userId) {
    try {
        db()->query(
            "UPDATE usuarios_acceso SET idioma_preferido = :idioma WHERE id = :id",
            ['idioma' => $idioma, 'id' => $userId]
        );
    } catch (Exception $e) {
        // Ignorar error si la columna no existe
    }
}

// Auditoría
Audit::log('change_language', "Idioma cambiado a: {$idioma}");

echo json_encode([
    'success' => true,
    'message' => 'Idioma actualizado',
    'idioma' => $idioma
]);
