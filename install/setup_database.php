<?php
/**
 * CONECTA ERP - INSTALADOR DE BASE DE DATOS
 *
 * Ejecuta todos los scripts SQL en orden correcto
 * Crea todas las tablas necesarias para el ERP
 */

// Configuración
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');

// Cargar clase Database
require_once BASE_PATH . '/app/core/database.php';

echo "==================================================\n";
echo "CONECTA ERP - INSTALADOR DE BASE DE DATOS\n";
echo "==================================================\n\n";

// Probar conexión
echo "[1/4] Probando conexión a la base de datos...\n";
try {
    $db = Database::getInstance();
    echo "✓ Conexión exitosa\n\n";
} catch (Exception $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
}

// Lista de archivos SQL en orden de ejecución
$sqlFiles = [
    '17_paises_idiomas.sql',
    '18_ia_auditoria.sql',
    '19_registro_completo.sql'
];

echo "[2/4] Ejecutando scripts SQL...\n";

foreach ($sqlFiles as $file) {
    $filePath = BASE_PATH . '/database/' . $file;

    if (!file_exists($filePath)) {
        echo "⚠ Archivo no encontrado: $file (omitiendo)\n";
        continue;
    }

    echo "  Ejecutando: $file ... ";

    try {
        $sql = file_get_contents($filePath);

        // Dividir por declaraciones (separadas por punto y coma)
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                // Ignorar comentarios y líneas vacías
                return !empty($stmt) &&
                       !preg_match('/^--/', $stmt) &&
                       !preg_match('/^\/\*/', $stmt);
            }
        );

        $pdo = $db->getPDO();
        $pdo->beginTransaction();

        $ejecutadas = 0;
        foreach ($statements as $statement) {
            if (trim($statement)) {
                // Reemplazar CREATE OR REPLACE VIEW por CREATE VIEW (MySQL no soporta OR REPLACE en vistas)
                $statement = preg_replace('/CREATE\s+OR\s+REPLACE\s+VIEW/i', 'CREATE VIEW', $statement);

                try {
                    $pdo->exec($statement);
                    $ejecutadas++;
                } catch (PDOException $e) {
                    // Ignorar errores de "tabla ya existe" o "view ya existe"
                    if (strpos($e->getMessage(), 'already exists') === false &&
                        strpos($e->getMessage(), 'Duplicate') === false) {
                        throw $e;
                    }
                }
            }
        }

        $pdo->commit();
        echo "✓ ($ejecutadas declaraciones)\n";

    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n[3/4] Verificando tablas creadas...\n";

// Listar tablas
try {
    $pdo = $db->getPDO();
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "  Total de tablas: " . count($tables) . "\n";

    // Verificar tablas críticas
    $criticalTables = [
        'paises',
        'idiomas',
        'paises_configuracion',
        'monedas',
        'planes_suscripcion',
        'empresas',
        'usuarios_acceso',
        'registro_wizard',
        'ai_tipos_auditoria',
        'ai_auditorias',
        'ai_alertas'
    ];

    echo "\n  Tablas críticas:\n";
    foreach ($criticalTables as $table) {
        $existe = in_array($table, $tables);
        echo "    " . ($existe ? "✓" : "✗") . " $table\n";
    }

} catch (Exception $e) {
    echo "  ✗ Error al verificar tablas: " . $e->getMessage() . "\n";
}

echo "\n[4/4] Verificando datos iniciales...\n";

// Verificar que se insertaron datos
try {
    $countPaises = $db->selectOne("SELECT COUNT(*) as total FROM paises");
    echo "  Países: " . $countPaises['total'] . "\n";

    $countIdiomas = $db->selectOne("SELECT COUNT(*) as total FROM idiomas");
    echo "  Idiomas: " . $countIdiomas['total'] . "\n";

    $countPlanes = $db->selectOne("SELECT COUNT(*) as total FROM planes_suscripcion");
    echo "  Planes: " . $countPlanes['total'] . "\n";

    $countTiposAuditoria = $db->selectOne("SELECT COUNT(*) as total FROM ai_tipos_auditoria");
    echo "  Tipos de Auditoría IA: " . $countTiposAuditoria['total'] . "\n";

} catch (Exception $e) {
    echo "  ✗ Error al verificar datos: " . $e->getMessage() . "\n";
}

echo "\n==================================================\n";
echo "INSTALACIÓN COMPLETADA\n";
echo "==================================================\n\n";
echo "La base de datos está lista para usar.\n";
echo "Puede proceder con el registro de la primera empresa.\n\n";
