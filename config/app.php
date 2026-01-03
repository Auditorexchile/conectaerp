<?php
/**
 * CONECTA ERP - CONFIGURACIÓN GENERAL DE LA APLICACIÓN
 *
 * Este archivo contiene toda la configuración principal del sistema
 */

return [
    // INFORMACIÓN DE LA APLICACIÓN
    'app_name' => 'Conecta ERP',
    'app_version' => '1.0.0',
    'app_env' => 'development', // production, development, testing
    'app_url' => 'http://localhost',
    'app_timezone' => 'America/Santiago',
    'app_locale' => 'es',

    // SEGURIDAD
    'security' => [
        'session_lifetime' => 7200, // 2 horas en segundos
        'session_name' => 'CONECTA_ERP_SESSION',
        'csrf_token_name' => 'csrf_token',
        'max_login_attempts' => 5,
        'lockout_duration' => 1800, // 30 minutos en segundos
        'password_min_length' => 12,
        'password_require_uppercase' => true,
        'password_require_lowercase' => true,
        'password_require_numbers' => true,
        'password_require_symbols' => true,
        'enable_2fa' => true,
        'force_2fa_superadmin' => true,
    ],

    // TRIAL Y LICENCIAMIENTO
    'trial' => [
        'enabled' => true,
        'duration_days' => 14,
        'start_on_first_login' => true,
    ],

    // MULTIIDIOMA
    'languages' => [
        'es' => 'Español',
        'en' => 'English',
        'pt' => 'Português',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'zh' => '中文',
        'ja' => '日本語',
        'ko' => '한국어',
        'hi' => 'हिन्दी',
    ],

    // MULTIMONEDA
    'currencies' => [
        'CLP' => 'Peso Chileno',
        'USD' => 'Dólar Estadounidense',
        'EUR' => 'Euro',
        'BRL' => 'Real Brasileño',
        'ARS' => 'Peso Argentino',
        'PEN' => 'Sol Peruano',
        'COP' => 'Peso Colombiano',
        'MXN' => 'Peso Mexicano',
        'UF' => 'Unidad de Fomento',
        'UTM' => 'Unidad Tributaria Mensual',
    ],

    // PAÍSES SOPORTADOS
    'countries' => [
        'CL' => 'Chile',
        'AR' => 'Argentina',
        'PE' => 'Perú',
        'CO' => 'Colombia',
        'BR' => 'Brasil',
        'MX' => 'México',
        'US' => 'Estados Unidos',
        'CA' => 'Canadá',
        'ES' => 'España',
        'DE' => 'Alemania',
        'FR' => 'Francia',
        'IT' => 'Italia',
        'GB' => 'Reino Unido',
        'CN' => 'China',
        'JP' => 'Japón',
        'KR' => 'Corea del Sur',
        'IN' => 'India',
    ],

    // MÓDULOS DEL ERP
    'modules' => [
        'dashboard' => 'Dashboard',
        'administracion' => 'Administración',
        'entidades' => 'Entidades',
        'contabilidad' => 'Contabilidad (FI)',
        'controlling' => 'Controlling (CO)',
        'ventas' => 'Ventas (SD)',
        'materiales' => 'Materiales (MM)',
        'produccion' => 'Producción (PP)',
        'rrhh' => 'RRHH (HCM)',
        'scm' => 'SCM',
        'crm' => 'CRM',
        'fidelizacion' => 'Fidelización',
        'bi' => 'Business Intelligence',
        'configuracion' => 'Configuración',
    ],

    // PLANES COMERCIALES
    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'price' => 0,
            'currency' => 'CLP',
            'period' => 'mes',
            'max_users' => 1,
            'max_companies' => 1,
            'max_branches' => 1,
        ],
        'profesional' => [
            'name' => 'Profesional',
            'price' => 49990,
            'currency' => 'CLP',
            'period' => 'mes',
            'max_users' => 5,
            'max_companies' => 1,
            'max_branches' => 1,
        ],
        'empresa' => [
            'name' => 'Empresa',
            'price' => 99990,
            'currency' => 'CLP',
            'period' => 'mes',
            'max_users' => null, // ilimitado
            'max_companies' => 1,
            'max_branches' => 5,
        ],
        'corporativo' => [
            'name' => 'Corporativo',
            'price' => null, // contactar
            'currency' => 'CLP',
            'period' => 'mes',
            'max_users' => null,
            'max_companies' => null,
            'max_branches' => null,
        ],
    ],

    // INTEGRACIONES
    'integrations' => [
        'sii' => [
            'enabled' => true,
            'environment' => 'certificacion', // certificacion, produccion
        ],
        'previred' => [
            'enabled' => true,
        ],
        'transbank' => [
            'enabled' => false,
        ],
        'email' => [
            'enabled' => true,
            'driver' => 'smtp',
        ],
    ],

    // CONTACTO
    'contact' => [
        'email' => 'contacto@conectaerp.com',
        'phone' => '+56 9 8574 5559',
        'support_hours' => 'Lunes a Viernes 9:00 - 18:00',
    ],
];
