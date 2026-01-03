<?php
/**
 * CONECTA ERP - CREAR SUPERUSUARIO
 * Script para crear el superusuario del sistema
 */

define('CONECTA_ERP', true);

// Cargar bootstrap
require_once __DIR__ . '/../app/core/bootstrap.php';

echo "\n";
echo "========================================\n";
echo "CONECTA ERP - CREAR SUPERUSUARIO\n";
echo "========================================\n";
echo "\n";

try {
    // Leer el archivo SQL
    $sqlFile = __DIR__ . '/../database/20_superusuario.sql';

    if (!file_exists($sqlFile)) {
        throw new Exception("No se encuentra el archivo SQL: $sqlFile");
    }

    echo "📄 Leyendo archivo SQL...\n";
    $sql = file_get_contents($sqlFile);

    // Obtener conexión PDO
    $db = db();
    $pdo = $db->getPDO();

    echo "🔌 Conectado a la base de datos\n";
    echo "\n";

    // Dividir el SQL en statements individuales
    // Eliminar comentarios y líneas vacías
    $lines = explode("\n", $sql);
    $currentStatement = '';
    $statements = [];

    foreach ($lines as $line) {
        $line = trim($line);

        // Ignorar comentarios y líneas vacías
        if (empty($line) || strpos($line, '--') === 0) {
            continue;
        }

        $currentStatement .= $line . ' ';

        // Si termina en punto y coma, es un statement completo
        if (substr($line, -1) === ';') {
            $statements[] = trim($currentStatement);
            $currentStatement = '';
        }
    }

    echo "📋 Ejecutando SQL...\n";
    echo "\n";

    // Ejecutar cada statement
    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        if (empty($statement)) continue;

        try {
            // Ejecutar statement
            $result = $pdo->query($statement);

            // Si es un SELECT, mostrar resultados
            if (stripos($statement, 'SELECT') === 0) {
                if ($result) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        foreach ($row as $key => $value) {
                            if (!empty($value) && $value !== '========================================' && $value !== '----------------------------------------') {
                                echo "  $value\n";
                            } elseif ($value === '========================================' || $value === '----------------------------------------') {
                                echo "$value\n";
                            }
                        }
                    }
                }
            }

            $successCount++;

        } catch (PDOException $e) {
            // Ignorar errores de "duplicate key" (ya existe)
            if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "  ⚠️ Error: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
    }

    echo "\n";
    echo "========================================\n";
    echo "RESULTADO\n";
    echo "========================================\n";
    echo "✓ Statements ejecutados: $successCount\n";
    if ($errorCount > 0) {
        echo "⚠️ Errores: $errorCount\n";
    }
    echo "\n";

    // Verificar que el usuario fue creado
    $user = $db->selectOne(
        "SELECT u.*, e.razon_social, e.identificador
         FROM usuarios_acceso u
         JOIN empresas e ON u.empresa_id = e.id
         WHERE u.email = :email",
        ['email' => 'auditorexchile@gmail.com']
    );

    if ($user) {
        echo "✅ SUPERUSUARIO CREADO EXITOSAMENTE\n";
        echo "\n";
        echo "DATOS DE ACCESO:\n";
        echo "----------------\n";
        echo "RUT Empresa:  {$user['identificador']}\n";
        echo "Razón Social: {$user['razon_social']}\n";
        echo "Nombre:       {$user['nombre']} {$user['apellido']}\n";
        echo "Email:        {$user['email']}\n";
        echo "Password:     Sistemas40&\n";
        echo "Rol:          {$user['rol']}\n";
        echo "Superadmin:   " . ($user['es_superadmin'] ? 'SÍ' : 'NO') . "\n";
        echo "Estado:       {$user['estado']}\n";
        echo "\n";
        echo "🌐 Acceder en: https://conectaerp.com/login.php\n";
        echo "\n";
    } else {
        echo "❌ ERROR: No se pudo verificar la creación del usuario\n";
        echo "\n";
    }

} catch (Exception $e) {
    echo "\n";
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\n";
    exit(1);
}

echo "========================================\n";
echo "\n";
