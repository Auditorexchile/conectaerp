<?php
/**
 * Configuración General de la Aplicación
 */

return [
    'name' => 'Conecta ERP',
    'version' => '1.0.0',
    'env' => 'production', // production | development
    'debug' => false,
    'url' => 'https://conectaerp.com',
    'timezone' => 'America/Santiago',
    'locale' => 'es',
    'charset' => 'UTF-8',

    // Trial
    'trial_days' => 14,
    'trial_warning_days' => 3,

    // Sesión
    'session_lifetime' => 120, // minutos
    'session_secure' => true,
    'session_httponly' => true,

    // Seguridad
    'password_min_length' => 8,
    'max_login_attempts' => 5,
    'login_lockout_minutes' => 15,

    // Archivos
    'max_upload_size' => 10485760, // 10MB
    'allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'xlsx', 'docx'],

    // Contacto
    'contact_email' => 'contacto@conectaerp.com',
    'support_phone' => '+56 9 8574 5559',
    'support_hours' => 'Lun-Vie 9:00-18:00',
];
