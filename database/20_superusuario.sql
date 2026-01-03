-- ================================================================
-- CONECTA ERP - INSERCIÓN DE SUPERUSUARIO
-- ================================================================
-- INSTRUCCIONES:
-- 1. Este script crea el superusuario del sistema
-- 2. Ejecutar DESPUÉS de los scripts 17, 18 y 19
-- 3. El superusuario tiene acceso total a todos los módulos
-- ================================================================

USE conectae_conectaerpbd;
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ================================================================
-- SUPERUSUARIO CONECTA ERP
-- RUT: 77.866.873-4
-- Email: auditorexchile@gmail.com
-- Password: Sistemas40&
-- ================================================================

-- Verificar que existen las tablas necesarias
SELECT 'Verificando tablas...' as status;

-- ================================================================
-- 1. INSERTAR EMPRESA SUPERADMIN
-- ================================================================
INSERT INTO empresas (
    pais_codigo,
    identificador,
    razon_social,
    nombre_fantasia,
    giro_id,
    region_id,
    comuna_id,
    direccion,
    email,
    telefono,
    sitio_web,
    logo_url,
    estado,
    bloqueada,
    bloqueada_por_pago,
    plan_id,
    en_trial,
    fecha_inicio_trial,
    fecha_fin_trial,
    suscripcion_activa,
    fecha_activacion,
    created_at,
    updated_at
) VALUES (
    'CL',                                   -- País: Chile
    '77.866.873-4',                        -- RUT superusuario
    'Conecta ERP SpA',                     -- Razón social
    'Conecta ERP',                         -- Nombre fantasía
    1,                                      -- Giro: Servicios de Software (primer giro disponible)
    13,                                     -- Región Metropolitana (ID 13)
    NULL,                                   -- Comuna (NULL por ahora)
    'Av. Providencia 1208, Oficina 1403',  -- Dirección
    'contacto@conectaerp.com',             -- Email empresa
    '+56985745559',                        -- Teléfono
    'https://conectaerp.com',              -- Sitio web
    NULL,                                   -- Logo (NULL por ahora)
    'activa',                              -- Estado: activa
    0,                                      -- No bloqueada
    0,                                      -- No bloqueada por pago
    (SELECT id FROM planes_suscripcion WHERE codigo = 'corporativo' LIMIT 1), -- Plan corporativo
    0,                                      -- No está en trial (es permanente)
    NULL,                                   -- Sin fecha inicio trial
    NULL,                                   -- Sin fecha fin trial
    1,                                      -- Suscripción activa
    NOW(),                                  -- Fecha activación: ahora
    NOW(),                                  -- Created at
    NOW()                                   -- Updated at
) ON DUPLICATE KEY UPDATE
    razon_social = VALUES(razon_social),
    email = VALUES(email),
    updated_at = NOW();

-- Guardar el ID de la empresa
SET @empresa_id = LAST_INSERT_ID();
SELECT CONCAT('✓ Empresa creada/actualizada - ID: ', @empresa_id) as status;

-- ================================================================
-- 2. INSERTAR USUARIO SUPERADMIN
-- ================================================================
INSERT INTO usuarios_acceso (
    empresa_id,
    nombre,
    apellido,
    email,
    password_hash,
    telefono,
    cargo,
    departamento,
    rol,
    permisos_especiales,
    es_superadmin,
    es_admin,
    puede_aprobar,
    puede_eliminar,
    acceso_total_modulos,
    idioma_codigo,
    zona_horaria,
    formato_fecha,
    estado,
    bloqueado,
    bloqueado_hasta,
    motivo_bloqueo,
    intentos_fallidos,
    ultimo_intento_fallido,
    ultimo_acceso,
    ip_ultimo_acceso,
    fecha_activacion,
    activacion_token,
    activado,
    avatar_url,
    preferencias_json,
    notificaciones_email,
    notificaciones_sistema,
    created_at,
    updated_at
) VALUES (
    @empresa_id,                                                                              -- Empresa ID
    'Administrador',                                                                          -- Nombre
    'Sistema',                                                                                -- Apellido
    'auditorexchile@gmail.com',                                                              -- Email
    '$argon2id$v=19$m=65536,t=4,p=1$OEVMNGpiNHlmQS9GejAvWQ$5hUvI3S4EhAfmATgpqv5B/61AL5O9mb/6N9VCznao7o', -- Password: Sistemas40&
    '+56985745559',                                                                          -- Teléfono
    'Superadministrador',                                                                    -- Cargo
    'TI - Sistemas',                                                                         -- Departamento
    'superadmin',                                                                            -- Rol
    JSON_ARRAY('*'),                                                                         -- Permisos especiales: todos
    1,                                                                                       -- Es superadmin: SÍ
    1,                                                                                       -- Es admin: SÍ
    1,                                                                                       -- Puede aprobar: SÍ
    1,                                                                                       -- Puede eliminar: SÍ
    1,                                                                                       -- Acceso total módulos: SÍ
    'es',                                                                                    -- Idioma: Español
    'America/Santiago',                                                                      -- Zona horaria: Chile
    'DD/MM/YYYY',                                                                           -- Formato fecha
    'activo',                                                                                -- Estado: activo
    0,                                                                                       -- No bloqueado
    NULL,                                                                                    -- Sin bloqueo
    NULL,                                                                                    -- Sin motivo bloqueo
    0,                                                                                       -- Sin intentos fallidos
    NULL,                                                                                    -- Sin intentos fallidos
    NULL,                                                                                    -- Sin último acceso aún
    NULL,                                                                                    -- Sin IP último acceso
    NOW(),                                                                                   -- Activado ahora
    NULL,                                                                                    -- Sin token (ya activado)
    1,                                                                                       -- Activado: SÍ
    NULL,                                                                                    -- Sin avatar
    JSON_OBJECT(
        'tema', 'light',
        'idioma', 'es',
        'notificaciones_push', true,
        'dashboard_widgets', JSON_ARRAY('ventas', 'contabilidad', 'rrhh', 'ia')
    ),                                                                                       -- Preferencias
    1,                                                                                       -- Notificaciones email: SÍ
    1,                                                                                       -- Notificaciones sistema: SÍ
    NOW(),                                                                                   -- Created at
    NOW()                                                                                    -- Updated at
) ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    apellido = VALUES(apellido),
    rol = VALUES(rol),
    es_superadmin = VALUES(es_superadmin),
    es_admin = VALUES(es_admin),
    acceso_total_modulos = VALUES(acceso_total_modulos),
    estado = VALUES(estado),
    updated_at = NOW();

-- Guardar el ID del usuario
SET @usuario_id = LAST_INSERT_ID();
SELECT CONCAT('✓ Usuario superadmin creado/actualizado - ID: ', @usuario_id) as status;

-- ================================================================
-- 3. REGISTRAR ACCESO A TODOS LOS MÓDULOS
-- ================================================================
-- El superadmin tiene acceso a TODOS los módulos del sistema

INSERT INTO usuario_modulos (usuario_id, modulo_id, puede_leer, puede_crear, puede_editar, puede_eliminar, puede_aprobar)
SELECT
    @usuario_id,
    m.id,
    1, -- puede_leer
    1, -- puede_crear
    1, -- puede_editar
    1, -- puede_eliminar
    1  -- puede_aprobar
FROM modulos m
ON DUPLICATE KEY UPDATE
    puede_leer = VALUES(puede_leer),
    puede_crear = VALUES(puede_crear),
    puede_editar = VALUES(puede_editar),
    puede_eliminar = VALUES(puede_eliminar),
    puede_aprobar = VALUES(puede_aprobar);

SELECT CONCAT('✓ Permisos de módulos asignados al superadmin') as status;

-- ================================================================
-- 4. CONFIGURACIÓN ADICIONAL
-- ================================================================

-- Marcar que la empresa no está en trial (es permanente)
UPDATE empresas
SET
    en_trial = 0,
    fecha_inicio_trial = NULL,
    fecha_fin_trial = NULL,
    suscripcion_activa = 1,
    plan_id = (SELECT id FROM planes_suscripcion WHERE codigo = 'corporativo' LIMIT 1)
WHERE id = @empresa_id;

SELECT CONCAT('✓ Empresa configurada con plan Corporativo permanente') as status;

-- ================================================================
-- 5. RESUMEN FINAL
-- ================================================================
SELECT '========================================' as '';
SELECT 'SUPERUSUARIO CREADO EXITOSAMENTE' as status;
SELECT '========================================' as '';
SELECT '' as '';
SELECT 'DATOS DE ACCESO:' as info;
SELECT '----------------------------------------' as '';
SELECT CONCAT('RUT Empresa: ', '77.866.873-4') as dato;
SELECT CONCAT('Razón Social: ', 'Conecta ERP SpA') as dato;
SELECT CONCAT('Email: ', 'auditorexchile@gmail.com') as dato;
SELECT CONCAT('Password: ', 'Sistemas40&') as dato;
SELECT CONCAT('Plan: ', 'Corporativo (Permanente)') as dato;
SELECT CONCAT('Rol: ', 'Superadministrador') as dato;
SELECT CONCAT('Acceso: ', 'TOTAL - Todos los módulos') as dato;
SELECT '' as '';
SELECT '----------------------------------------' as '';
SELECT 'Puedes acceder en: https://conectaerp.com/login.php' as url;
SELECT '========================================' as '';

-- ================================================================
-- FIN DEL SCRIPT
-- ================================================================
