<?php
/**
 * CONECTA ERP - BOOTSTRAP DEL SISTEMA
 *
 * Este archivo inicializa todo el sistema
 * DEBE ser incluido en TODAS las páginas del ERP
 */

// Prevenir acceso directo
if (!defined('CONECTA_ERP')) {
    define('CONECTA_ERP', true);
}

// Definir rutas del sistema
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOGS_PATH', ROOT_PATH . '/logs');

// Cargar configuración
$config = require CONFIG_PATH . '/app.php';
$dbConfig = file_exists(CONFIG_PATH . '/database.php')
    ? require CONFIG_PATH . '/database.php'
    : require CONFIG_PATH . '/database.example.php';
$securityConfig = require CONFIG_PATH . '/security.php';

// Configuración global
define('APP_NAME', $config['app_name']);
define('APP_VERSION', $config['app_version']);
define('APP_ENV', $config['app_env']);
define('APP_URL', $config['app_url']);

// Configuración PHP
error_reporting(APP_ENV === 'production' ? 0 : E_ALL);
ini_set('display_errors', APP_ENV === 'production' ? '0' : '1');
ini_set('log_errors', '1');
ini_set('error_log', LOGS_PATH . '/php_errors.log');

date_default_timezone_set($config['app_timezone']);
mb_internal_encoding('UTF-8');

// Configuración de sesión segura
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', $securityConfig['cookies']['secure'] ? '1' : '0');
ini_set('session.cookie_samesite', $securityConfig['cookies']['samesite']);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_lifetime', $config['security']['session_lifetime']);

// Autoloader simple
spl_autoload_register(function ($class) {
    $file = APP_PATH . '/core/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Cargar componentes core
require_once APP_PATH . '/core/database.php';
require_once APP_PATH . '/core/security.php';
require_once APP_PATH . '/core/session.php';
require_once APP_PATH . '/core/helpers.php';

// Iniciar sistema
Security::initialize();
Session::start();

// Registrar función de auditoría en shutdown
register_shutdown_function(function() {
    if (Session::isAuthenticated()) {
        Audit::logPageView();
    }
});
