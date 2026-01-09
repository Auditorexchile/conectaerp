<?php
/**
 * Bootstrap - Inicialización del sistema
 */

// Definir constantes base
define('APP_PATH', dirname(__DIR__));
define('PUBLIC_PATH', dirname(APP_PATH) . '/public_html');
define('CONFIG_PATH', APP_PATH . '/config');
define('CORE_PATH', APP_PATH . '/core');

// Cargar configuración
$config = require CONFIG_PATH . '/app.php';
define('APP_ENV', $config['env']);
define('APP_DEBUG', $config['debug']);

// Configurar zona horaria
date_default_timezone_set($config['timezone']);

// Configurar manejo de errores
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', APP_PATH . '/logs/error.log');
}

// Cargar funciones auxiliares
require_once CORE_PATH . '/helpers.php';

// Cargar clases core
require_once PUBLIC_PATH . '/config/database.php';
require_once PUBLIC_PATH . '/config/security.php';
require_once CORE_PATH . '/session.php';
require_once CORE_PATH . '/security.php';

// Iniciar sesión segura
SessionManager::init();

// Registrar autoloader (si usas clases)
spl_autoload_register(function ($class) {
    $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
