<?php
/**
 * CONECTA ERP - CONFIGURACIÓN DE SEGURIDAD
 *
 * Configuración avanzada de seguridad del sistema
 */

return [
    // HASHING DE CONTRASEÑAS
    'password' => [
        'algorithm' => PASSWORD_ARGON2ID, // PASSWORD_BCRYPT o PASSWORD_ARGON2ID
        'options' => [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,
            'threads' => 3,
        ],
    ],

    // TOKENS
    'tokens' => [
        'recovery_expiration' => 900, // 15 minutos
        'session_token_length' => 64,
        'api_token_length' => 64,
    ],

    // RATE LIMITING
    'rate_limit' => [
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 30,
        ],
        'api' => [
            'max_requests' => 60,
            'decay_minutes' => 1,
        ],
        'recovery' => [
            'max_attempts' => 3,
            'decay_minutes' => 60,
        ],
    ],

    // IP WHITELIST (SUPERADMIN)
    'ip_whitelist' => [
        '127.0.0.1',
        '::1',
        // Agregar IPs autorizadas para superadmin
    ],

    // IP BLACKLIST
    'ip_blacklist' => [
        // IPs bloqueadas permanentemente
    ],

    // CAPTCHA
    'captcha' => [
        'enabled' => true,
        'trigger_after_attempts' => 3,
        'provider' => 'recaptcha', // recaptcha, hcaptcha
        'site_key' => '',
        'secret_key' => '',
    ],

    // CSRF
    'csrf' => [
        'enabled' => true,
        'token_lifetime' => 7200, // 2 horas
    ],

    // COOKIES
    'cookies' => [
        'secure' => true, // true en producción (HTTPS)
        'httponly' => true,
        'samesite' => 'Strict',
        'lifetime' => 7200,
    ],

    // AUDITORÍA
    'audit' => [
        'enabled' => true,
        'log_all_actions' => true,
        'log_queries' => false, // true solo para debugging
        'retention_days' => 365, // 1 año
    ],
];
