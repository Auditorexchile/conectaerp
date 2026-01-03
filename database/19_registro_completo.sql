-- ================================================================
-- MÓDULO: REGISTRO COMPLETO Y PLANES DE SUSCRIPCIÓN
-- ================================================================
-- Tablas para wizard de registro de 5 pasos
-- Sistema de planes, trial, términos y condiciones
-- ================================================================

-- ================================================================
-- PLANES DE SUSCRIPCIÓN
-- ================================================================

CREATE TABLE IF NOT EXISTS planes_suscripcion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,

    -- TIPO Y CATEGORÍA
    tipo ENUM('gratuito', 'pago', 'empresarial', 'corporativo') DEFAULT 'pago',
    destacado BOOLEAN DEFAULT FALSE,

    -- PRECIOS POR PAÍS (JSON con estructura: {CL: 49990, US: 99, etc.})
    precios_por_pais JSON COMMENT 'Precios en moneda local de cada país',
    precio_base_usd DECIMAL(10,2) DEFAULT 0.00,

    -- PERIODICIDAD
    periodicidad ENUM('mensual', 'anual', 'bienal') DEFAULT 'mensual',
    descuento_anual INT DEFAULT 0 COMMENT 'Porcentaje de descuento si paga anual',

    -- LÍMITES
    max_empresas INT DEFAULT 1,
    max_usuarios INT DEFAULT 1,
    max_sucursales INT DEFAULT 1,
    max_documentos_mes INT DEFAULT 100,
    almacenamiento_gb INT DEFAULT 5,

    -- MÓDULOS INCLUIDOS (JSON array: ['FI', 'SD', 'MM', ...])
    modulos_incluidos JSON,
    modulos_bloqueados JSON,

    -- CARACTERÍSTICAS
    tiene_trial BOOLEAN DEFAULT TRUE,
    dias_trial INT DEFAULT 14,
    tiene_soporte_email BOOLEAN DEFAULT TRUE,
    tiene_soporte_chat BOOLEAN DEFAULT FALSE,
    tiene_soporte_telefono BOOLEAN DEFAULT FALSE,
    tiene_soporte_prioritario BOOLEAN DEFAULT FALSE,
    tiene_api_acceso BOOLEAN DEFAULT FALSE,
    tiene_integraciones BOOLEAN DEFAULT FALSE,
    tiene_modulo_ia BOOLEAN DEFAULT FALSE,
    tiene_multimoneda BOOLEAN DEFAULT FALSE,
    tiene_multiempresa BOOLEAN DEFAULT FALSE,

    -- ESTADO
    activo BOOLEAN DEFAULT TRUE,
    visible_publico BOOLEAN DEFAULT TRUE,
    orden INT DEFAULT 0,

    -- ESTILO
    color VARCHAR(20) DEFAULT '#3b82f6',
    badge VARCHAR(100) COMMENT 'Ej: "Más Popular", "Recomendado"',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_planes_codigo (codigo),
    INDEX idx_planes_activo (activo),
    INDEX idx_planes_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar planes básicos
INSERT INTO planes_suscripcion (
    codigo, nombre, descripcion, tipo, destacado,
    precios_por_pais, precio_base_usd,
    max_empresas, max_usuarios, max_documentos_mes, almacenamiento_gb,
    modulos_incluidos, tiene_modulo_ia, tiene_multimoneda, tiene_multiempresa,
    tiene_soporte_chat, tiene_soporte_telefono, tiene_api_acceso, tiene_integraciones,
    color, badge, orden, activo
) VALUES
(
    'starter', 'Starter', 'Plan gratuito para emprendedores', 'gratuito', FALSE,
    '{"CL": 0, "AR": 0, "PE": 0, "CO": 0, "MX": 0, "BR": 0, "US": 0, "ES": 0, "FR": 0, "DE": 0, "IT": 0, "GB": 0}',
    0.00, 1, 1, 50, 1,
    '["FI"]', FALSE, FALSE, FALSE,
    FALSE, FALSE, FALSE, FALSE,
    '#10b981', NULL, 1, TRUE
),
(
    'profesional', 'Profesional', 'Ideal para pequeñas empresas', 'pago', TRUE,
    '{"CL": 49990, "AR": 29990, "PE": 199, "CO": 199900, "MX": 1299, "BR": 299, "US": 99, "ES": 79, "FR": 79, "DE": 79, "IT": 79, "GB": 69}',
    99.00, 1, 5, 500, 10,
    '["FI", "SD", "MM", "HR"]', FALSE, TRUE, FALSE,
    TRUE, FALSE, FALSE, TRUE,
    '#3b82f6', 'Más Popular', 2, TRUE
),
(
    'empresa', 'Empresa', 'Para empresas en crecimiento', 'empresarial', FALSE,
    '{"CL": 99990, "AR": 59990, "PE": 399, "CO": 399900, "MX": 2599, "BR": 599, "US": 199, "ES": 159, "FR": 159, "DE": 159, "IT": 159, "GB": 139}',
    199.00, 3, 999, 5000, 50,
    '["FI", "SD", "MM", "HR", "CO", "PP", "CRM", "BI"]', TRUE, TRUE, TRUE,
    TRUE, TRUE, TRUE, TRUE,
    '#8b5cf6', NULL, 3, TRUE
),
(
    'corporativo', 'Corporativo', 'Solución enterprise completa', 'corporativo', FALSE,
    '{"CL": null, "AR": null, "PE": null, "CO": null, "MX": null, "BR": null, "US": null, "ES": null, "FR": null, "DE": null, "IT": null, "GB": null}',
    NULL, 999, 999, 999999, 999,
    '["FI", "SD", "MM", "HR", "CO", "PP", "CRM", "BI", "SCM", "API", "ECOM"]', TRUE, TRUE, TRUE,
    TRUE, TRUE, TRUE, TRUE,
    '#f59e0b', 'Enterprise', 4, TRUE
);

-- ================================================================
-- GIROS COMERCIALES (CHILE)
-- ================================================================

CREATE TABLE IF NOT EXISTS giros_comerciales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pais_codigo VARCHAR(2) DEFAULT 'CL',
    codigo VARCHAR(50),
    nombre VARCHAR(500) NOT NULL,
    categoria VARCHAR(200),
    activo BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (pais_codigo) REFERENCES paises(codigo) ON DELETE CASCADE,
    INDEX idx_giros_pais (pais_codigo),
    INDEX idx_giros_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Giros comerciales comunes de Chile
INSERT INTO giros_comerciales (pais_codigo, codigo, nombre, categoria) VALUES
('CL', '620100', 'Desarrollo y edición de software', 'Tecnología'),
('CL', '465911', 'Comercio al por mayor de computadores y equipos periféricos', 'Tecnología'),
('CL', '471911', 'Comercio al por menor en tiendas no especializadas con surtido compuesto principalmente de alimentos, bebidas y tabaco', 'Retail'),
('CL', '561011', 'Actividades de restaurantes', 'Gastronomía'),
('CL', '561012', 'Actividades de cafés y bares', 'Gastronomía'),
('CL', '692010', 'Actividades de contabilidad, teneduría de libros y auditoría; consultoría fiscal', 'Servicios Profesionales'),
('CL', '691010', 'Actividades jurídicas', 'Servicios Profesionales'),
('CL', '711020', 'Actividades de arquitectura', 'Servicios Profesionales'),
('CL', '711010', 'Actividades de ingeniería y actividades conexas de consultoría técnica', 'Servicios Profesionales'),
('CL', '702000', 'Actividades de consultoría de gestión', 'Consultoría'),
('CL', '431100', 'Demolición', 'Construcción'),
('CL', '432100', 'Instalaciones eléctricas', 'Construcción'),
('CL', '432200', 'Instalaciones de gasfitería, calefacción y aire acondicionado', 'Construcción'),
('CL', '410010', 'Construcción de edificios', 'Construcción'),
('CL', '466110', 'Venta al por mayor de combustibles sólidos, líquidos y gaseosos y productos conexos', 'Comercio'),
('CL', 'OTRO', 'Otro giro comercial', 'Otros');

-- ================================================================
-- REGIONES Y COMUNAS (CHILE)
-- ================================================================

CREATE TABLE IF NOT EXISTS regiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pais_codigo VARCHAR(2) NOT NULL,
    codigo VARCHAR(10) NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    numero INT,
    activo BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_region_pais (pais_codigo, codigo),
    FOREIGN KEY (pais_codigo) REFERENCES paises(codigo) ON DELETE CASCADE,
    INDEX idx_regiones_pais (pais_codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comunas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    codigo VARCHAR(10),
    nombre VARCHAR(200) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (region_id) REFERENCES regiones(id) ON DELETE CASCADE,
    INDEX idx_comunas_region (region_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Regiones de Chile
INSERT INTO regiones (pais_codigo, codigo, nombre, numero) VALUES
('CL', 'XV', 'Arica y Parinacota', 15),
('CL', 'I', 'Tarapacá', 1),
('CL', 'II', 'Antofagasta', 2),
('CL', 'III', 'Atacama', 3),
('CL', 'IV', 'Coquimbo', 4),
('CL', 'V', 'Valparaíso', 5),
('CL', 'RM', 'Metropolitana de Santiago', 13),
('CL', 'VI', 'Libertador General Bernardo O\'Higgins', 6),
('CL', 'VII', 'Maule', 7),
('CL', 'XVI', 'Ñuble', 16),
('CL', 'VIII', 'Biobío', 8),
('CL', 'IX', 'La Araucanía', 9),
('CL', 'XIV', 'Los Ríos', 14),
('CL', 'X', 'Los Lagos', 10),
('CL', 'XI', 'Aysén del General Carlos Ibáñez del Campo', 11),
('CL', 'XII', 'Magallanes y de la Antártica Chilena', 12);

-- Comunas principales de la Región Metropolitana (ejemplo)
INSERT INTO comunas (region_id, nombre) VALUES
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Santiago'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Providencia'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Las Condes'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Vitacura'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'La Reina'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Ñuñoa'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Maipú'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'La Florida'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Puente Alto'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'San Bernardo'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Peñalolén'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Macul'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Independencia'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Recoleta'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Quilicura'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Huechuraba'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Estación Central'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Cerrillos'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Quinta Normal'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Pudahuel'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Lo Barnechea'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Colina'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Lampa'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Til Til'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Pirque'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'San José de Maipo'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Buin'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Paine'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Melipilla'),
((SELECT id FROM regiones WHERE codigo = 'RM' LIMIT 1), 'Talagante');

-- ================================================================
-- TABLA EMPRESAS EXTENDIDA
-- ================================================================

CREATE TABLE IF NOT EXISTS empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- DATOS BÁSICOS (PASO 1 DEL REGISTER)
    pais_codigo VARCHAR(2) NOT NULL,
    identificador VARCHAR(50) NOT NULL COMMENT 'RUT, CUIT, RFC, etc.',
    razon_social VARCHAR(255) NOT NULL,
    nombre_fantasia VARCHAR(255),
    giro_id INT,
    sitio_web VARCHAR(255),

    -- DIRECCIÓN (PASO 1)
    direccion VARCHAR(500),
    comuna_id INT,
    region_id INT,
    codigo_postal VARCHAR(20),

    -- CONTACTO (PASO 1)
    email VARCHAR(255),
    telefono VARCHAR(50),
    telefono_movil VARCHAR(50),

    -- REPRESENTANTE LEGAL (PASO 2)
    rep_legal_nombre VARCHAR(255),
    rep_legal_identificador VARCHAR(50),
    rep_legal_email VARCHAR(255),
    rep_legal_telefono VARCHAR(50),
    rep_legal_cargo VARCHAR(100),

    -- CONFIGURACIÓN
    plan_id INT,
    idioma_codigo VARCHAR(10) DEFAULT 'es',
    moneda_base VARCHAR(3),
    zona_horaria VARCHAR(100),

    -- TRIAL Y SUSCRIPCIÓN
    en_trial BOOLEAN DEFAULT TRUE,
    fecha_inicio_trial DATE,
    fecha_fin_trial DATE,
    trial_dias_restantes INT,
    suscripcion_activa BOOLEAN DEFAULT TRUE,
    fecha_inicio_suscripcion DATE,
    fecha_fin_suscripcion DATE,

    -- ESTADO
    estado ENUM('activa', 'trial', 'suspendida', 'bloqueada', 'cancelada') DEFAULT 'trial',
    motivo_suspension TEXT,
    bloqueada BOOLEAN DEFAULT FALSE,
    bloqueada_por_pago BOOLEAN DEFAULT FALSE,

    -- DATOS TÉCNICOS
    logo_url VARCHAR(500),
    configuracion_json JSON,

    -- AUDITORÍA
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,

    UNIQUE KEY unique_empresa_identificador (pais_codigo, identificador),
    FOREIGN KEY (pais_codigo) REFERENCES paises(codigo) ON DELETE RESTRICT,
    FOREIGN KEY (plan_id) REFERENCES planes_suscripcion(id) ON DELETE SET NULL,
    FOREIGN KEY (idioma_codigo) REFERENCES idiomas(codigo) ON DELETE SET NULL,
    FOREIGN KEY (giro_id) REFERENCES giros_comerciales(id) ON DELETE SET NULL,
    FOREIGN KEY (comuna_id) REFERENCES comunas(id) ON DELETE SET NULL,
    FOREIGN KEY (region_id) REFERENCES regiones(id) ON DELETE SET NULL,

    INDEX idx_empresas_pais (pais_codigo),
    INDEX idx_empresas_identificador (identificador),
    INDEX idx_empresas_estado (estado),
    INDEX idx_empresas_plan (plan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- PROGRESO DEL WIZARD DE REGISTRO
-- ================================================================

CREATE TABLE IF NOT EXISTS registro_wizard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id VARCHAR(100) UNIQUE NOT NULL COMMENT 'ID de sesión temporal',
    email VARCHAR(255),

    -- PASO ACTUAL
    paso_actual INT DEFAULT 1 COMMENT '1-5',
    paso_completado INT DEFAULT 0,

    -- DATOS PASO 1: Empresa
    paso1_pais_codigo VARCHAR(2),
    paso1_identificador VARCHAR(50),
    paso1_razon_social VARCHAR(255),
    paso1_giro_id INT,
    paso1_direccion VARCHAR(500),
    paso1_region_id INT,
    paso1_comuna_id INT,
    paso1_telefono VARCHAR(50),
    paso1_email VARCHAR(255),
    paso1_json JSON,
    paso1_completado BOOLEAN DEFAULT FALSE,

    -- DATOS PASO 2: Representante Legal
    paso2_rep_nombre VARCHAR(255),
    paso2_rep_identificador VARCHAR(50),
    paso2_rep_email VARCHAR(255),
    paso2_rep_telefono VARCHAR(50),
    paso2_rep_cargo VARCHAR(100),
    paso2_json JSON,
    paso2_completado BOOLEAN DEFAULT FALSE,

    -- DATOS PASO 3: Seguridad
    paso3_password_hash VARCHAR(255),
    paso3_json JSON,
    paso3_completado BOOLEAN DEFAULT FALSE,

    -- DATOS PASO 4: Plan
    paso4_plan_codigo VARCHAR(50),
    paso4_periodicidad VARCHAR(50),
    paso4_json JSON,
    paso4_completado BOOLEAN DEFAULT FALSE,

    -- DATOS PASO 5: Confirmación
    paso5_acepta_terminos BOOLEAN DEFAULT FALSE,
    paso5_acepta_privacidad BOOLEAN DEFAULT FALSE,
    paso5_acepta_comerciales BOOLEAN DEFAULT FALSE,
    paso5_json JSON,
    paso5_completado BOOLEAN DEFAULT FALSE,

    -- METADATA
    ip_address VARCHAR(50),
    user_agent TEXT,
    idioma_seleccionado VARCHAR(10) DEFAULT 'es',

    -- CONTROL
    registro_completado BOOLEAN DEFAULT FALSE,
    empresa_id INT COMMENT 'ID de empresa creada al finalizar',
    usuario_id INT COMMENT 'ID de usuario creado al finalizar',

    expires_at TIMESTAMP COMMENT 'Sesión expira en 2 horas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_wizard_sesion (sesion_id),
    INDEX idx_wizard_email (email),
    INDEX idx_wizard_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TÉRMINOS Y CONDICIONES
-- ================================================================

CREATE TABLE IF NOT EXISTS terminos_condiciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    version VARCHAR(20) NOT NULL,
    idioma_codigo VARCHAR(10) NOT NULL,

    -- CONTENIDO
    titulo VARCHAR(500),
    contenido LONGTEXT NOT NULL,
    resumen TEXT,

    -- TIPO
    tipo ENUM('terminos', 'privacidad', 'uso_aceptable', 'sla') DEFAULT 'terminos',

    -- VIGENCIA
    fecha_vigencia DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (idioma_codigo) REFERENCES idiomas(codigo) ON DELETE CASCADE,
    INDEX idx_terminos_version (version),
    INDEX idx_terminos_idioma (idioma_codigo),
    INDEX idx_terminos_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- ACEPTACIÓN DE TÉRMINOS POR USUARIO
-- ================================================================

CREATE TABLE IF NOT EXISTS terminos_aceptados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    usuario_id INT,
    termino_id INT NOT NULL,

    -- DATOS ACEPTACIÓN
    ip_address VARCHAR(50),
    user_agent TEXT,
    acepto BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (termino_id) REFERENCES terminos_condiciones(id) ON DELETE CASCADE,
    INDEX idx_terminos_acepta_empresa (empresa_id),
    INDEX idx_terminos_acepta_usuario (usuario_id),
    INDEX idx_terminos_acepta_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- LOG DE TRIAL (14 días)
-- ================================================================

CREATE TABLE IF NOT EXISTS trial_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,

    -- FECHAS
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    dias_totales INT DEFAULT 14,

    -- ESTADO
    activo BOOLEAN DEFAULT TRUE,
    expirado BOOLEAN DEFAULT FALSE,
    convertido_a_pago BOOLEAN DEFAULT FALSE,
    fecha_conversion DATE,

    -- NOTIFICACIONES ENVIADAS
    notif_7_dias BOOLEAN DEFAULT FALSE,
    notif_3_dias BOOLEAN DEFAULT FALSE,
    notif_1_dia BOOLEAN DEFAULT FALSE,
    notif_expiracion BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_trial_empresa (empresa_id),
    INDEX idx_trial_activo (activo),
    INDEX idx_trial_expirado (expirado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- USUARIOS DE ACCESO (Actualización de tabla existente)
-- ================================================================

CREATE TABLE IF NOT EXISTS usuarios_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,

    -- CREDENCIALES
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,

    -- DATOS PERSONALES
    nombre VARCHAR(255),
    apellido VARCHAR(255),
    telefono VARCHAR(50),

    -- PERFIL
    tipo_usuario ENUM('superadmin', 'admin', 'usuario', 'limitado') DEFAULT 'usuario',
    rol VARCHAR(100),

    -- ESTADO
    estado ENUM('activo', 'inactivo', 'bloqueado', 'pendiente') DEFAULT 'pendiente',
    email_verificado BOOLEAN DEFAULT FALSE,
    email_verificado_at TIMESTAMP NULL,

    -- SEGURIDAD 2FA
    mfa_enabled BOOLEAN DEFAULT FALSE,
    mfa_secret VARCHAR(255),

    -- BLOQUEO
    bloqueado BOOLEAN DEFAULT FALSE,
    bloqueado_hasta TIMESTAMP NULL,
    motivo_bloqueo TEXT,

    -- SESIÓN
    ultimo_acceso TIMESTAMP NULL,
    ip_ultimo_acceso VARCHAR(50),
    token_sesion TEXT,
    token_expiracion TIMESTAMP NULL,

    -- INTENTOS FALLIDOS
    intentos_fallidos INT DEFAULT 0,
    ultimo_intento_fallido TIMESTAMP NULL,

    -- PREFERENCIAS
    idioma_codigo VARCHAR(10) DEFAULT 'es',
    zona_horaria VARCHAR(100),

    -- METADATA
    avatar_url VARCHAR(500),
    configuracion_json JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,

    FOREIGN KEY (idioma_codigo) REFERENCES idiomas(codigo) ON DELETE SET NULL,
    INDEX idx_usuarios_empresa (empresa_id),
    INDEX idx_usuarios_email (email),
    INDEX idx_usuarios_estado (estado),
    INDEX idx_usuarios_tipo (tipo_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TOKENS DE RECUPERACIÓN DE CONTRASEÑA
-- ================================================================

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,

    -- VALIDEZ
    expires_at TIMESTAMP NOT NULL COMMENT 'Token válido 10-15 minutos',

    -- ESTADO
    usado BOOLEAN DEFAULT FALSE,
    usado_at TIMESTAMP NULL,
    ip_solicitud VARCHAR(50),
    ip_uso VARCHAR(50),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_password_resets_token (token),
    INDEX idx_password_resets_email (email),
    INDEX idx_password_resets_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- INSERTAR TÉRMINOS Y CONDICIONES BÁSICOS
-- ================================================================

INSERT INTO terminos_condiciones (version, idioma_codigo, titulo, contenido, tipo, fecha_vigencia, activo)
VALUES
('1.0', 'es', 'Términos y Condiciones de Uso - Conecta ERP',
'TÉRMINOS Y CONDICIONES DE USO

1. ACEPTACIÓN DE LOS TÉRMINOS
Al registrarse en Conecta ERP, usted acepta estos términos y condiciones en su totalidad.

2. DESCRIPCIÓN DEL SERVICIO
Conecta ERP es un sistema de gestión empresarial (ERP) en la nube que proporciona módulos de contabilidad, ventas, inventario, RRHH y más.

3. PERÍODO DE PRUEBA (TRIAL)
- Todos los planes incluyen 14 días de prueba gratuita
- Durante el trial tiene acceso completo a las funcionalidades del plan seleccionado
- No se requiere tarjeta de crédito para iniciar el trial
- Al finalizar el trial, debe contratar un plan para continuar usando el sistema

4. PLANES Y PAGOS
- Los precios se muestran en la moneda local de su país
- Los pagos son mensuales o anuales según su elección
- Las renovaciones son automáticas
- Puede cancelar en cualquier momento

5. USO ACEPTABLE
- No usar el sistema para actividades ilegales
- No intentar acceder a datos de otras empresas
- Mantener la confidencialidad de sus credenciales
- No revender o sublicenciar el servicio

6. PRIVACIDAD Y SEGURIDAD
- Sus datos están protegidos con encriptación
- No compartimos su información con terceros sin su consentimiento
- Cumplimos con las leyes de protección de datos aplicables

7. PROPIEDAD INTELECTUAL
- Conecta ERP es propiedad de sus desarrolladores
- Los datos que usted ingresa son de su propiedad
- Puede exportar sus datos en cualquier momento

8. LIMITACIÓN DE RESPONSABILIDAD
- El servicio se proporciona "tal cual"
- No garantizamos funcionamiento ininterrumpido
- No somos responsables por pérdida de datos debido a causas fuera de nuestro control

9. MODIFICACIONES
- Podemos modificar estos términos con previo aviso
- El uso continuado implica aceptación de los nuevos términos

10. TERMINACIÓN
- Puede cancelar su cuenta en cualquier momento
- Podemos suspender cuentas que violen estos términos
- Tras la cancelación, sus datos se conservan por 30 días

11. LEY APLICABLE
- Estos términos se rigen por las leyes del país donde opera su empresa

12. CONTACTO
Para consultas sobre estos términos: contacto@conectaerp.com

Última actualización: ' || CURRENT_DATE,
'terminos', CURRENT_DATE, TRUE);

-- ================================================================
-- NOTAS FINALES
-- ================================================================
--
-- FLUJO DE REGISTRO (5 PASOS):
--
-- PASO 1: Datos de Empresa
--   - Selección de país (activa validaciones específicas)
--   - RUT/CUIT/RFC (validación dinámica según país)
--   - Razón social, giro, dirección, región, comuna
--
-- PASO 2: Representante Legal
--   - Nombre, RUT, email, teléfono, cargo
--
-- PASO 3: Seguridad
--   - Email de acceso
--   - Contraseña (validador de fortaleza)
--   - Generador de contraseña segura
--
-- PASO 4: Selección de Plan
--   - 4 planes (Starter, Profesional, Empresa, Corporativo)
--   - Precios en moneda local
--   - Resumen de características
--
-- PASO 5: Confirmación
--   - Resumen completo
--   - Aceptación de términos y condiciones
--   - Crear empresa + usuario + inicio trial
--
-- Al completar el registro:
-- - Se crea la empresa
-- - Se crea el usuario administrador
-- - Se inicia el trial de 14 días
-- - Se envía email de bienvenida
-- - Se redirige al dashboard
--
-- ================================================================
