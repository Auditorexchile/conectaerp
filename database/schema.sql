-- ================================================================
-- CONECTA ERP - ESQUEMA DE BASE DE DATOS
-- ================================================================

-- ================================================================
-- PORTADA Y CONTENIDO PÚBLICO
-- ================================================================

CREATE TABLE web_configuracion (
    id SERIAL PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descripcion TEXT,
    tipo VARCHAR(50), -- 'texto', 'numero', 'boolean', 'json'
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE web_menu (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    orden INTEGER DEFAULT 0,
    padre_id INTEGER REFERENCES web_menu(id) ON DELETE CASCADE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE web_secciones (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    titulo VARCHAR(255),
    subtitulo TEXT,
    contenido TEXT,
    imagen_url VARCHAR(500),
    orden INTEGER DEFAULT 0,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE web_beneficios (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(100), -- emoji o clase de icono
    color VARCHAR(50),
    orden INTEGER DEFAULT 0,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE web_planes (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- 'starter', 'profesional', 'empresa', 'corporativo'
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2),
    precio_texto VARCHAR(100), -- Para mostrar 'Gratis' o 'Contactar'
    moneda VARCHAR(10) DEFAULT 'CLP',
    periodo VARCHAR(50), -- 'mes', 'anual'
    destacado BOOLEAN DEFAULT false,
    color VARCHAR(50),
    orden INTEGER DEFAULT 0,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE web_plan_caracteristicas (
    id SERIAL PRIMARY KEY,
    plan_id INTEGER NOT NULL REFERENCES web_planes(id) ON DELETE CASCADE,
    descripcion VARCHAR(255) NOT NULL,
    incluido BOOLEAN DEFAULT true,
    orden INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE web_testimonios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    empresa VARCHAR(255),
    cargo VARCHAR(255),
    testimonio TEXT NOT NULL,
    foto_url VARCHAR(500),
    puntuacion INTEGER CHECK (puntuacion >= 1 AND puntuacion <= 5),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE web_cta (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    texto_boton VARCHAR(100),
    url_boton VARCHAR(255),
    tipo VARCHAR(50), -- 'principal', 'secundario'
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- PLANES Y SUSCRIPCIONES
-- ================================================================

CREATE TABLE planes (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0,
    moneda VARCHAR(10) DEFAULT 'CLP',
    periodo VARCHAR(50) DEFAULT 'mes', -- 'mes', 'anual'
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE planes_caracteristicas (
    id SERIAL PRIMARY KEY,
    plan_id INTEGER NOT NULL REFERENCES planes(id) ON DELETE CASCADE,
    caracteristica VARCHAR(255) NOT NULL,
    valor TEXT,
    tipo VARCHAR(50), -- 'boolean', 'numero', 'texto'
    orden INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE planes_limites (
    id SERIAL PRIMARY KEY,
    plan_id INTEGER NOT NULL REFERENCES planes(id) ON DELETE CASCADE,
    recurso VARCHAR(100) NOT NULL, -- 'usuarios', 'empresas', 'sucursales', etc.
    limite INTEGER, -- NULL = ilimitado
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(plan_id, recurso)
);

CREATE TABLE planes_precios (
    id SERIAL PRIMARY KEY,
    plan_id INTEGER NOT NULL REFERENCES planes(id) ON DELETE CASCADE,
    moneda VARCHAR(10) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    periodo VARCHAR(50) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE suscripciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL, -- Se relacionará con tabla empresas
    plan_id INTEGER NOT NULL REFERENCES planes(id),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    fecha_proximo_pago DATE,
    precio DECIMAL(10, 2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    auto_renovacion BOOLEAN DEFAULT true,
    estado_id INTEGER NOT NULL, -- Se relacionará con suscripcion_estado
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE suscripcion_estado (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- 'activa', 'pendiente', 'vencida', 'cancelada'
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE suscripcion_historial (
    id SERIAL PRIMARY KEY,
    suscripcion_id INTEGER NOT NULL REFERENCES suscripciones(id) ON DELETE CASCADE,
    estado_anterior_id INTEGER REFERENCES suscripcion_estado(id),
    estado_nuevo_id INTEGER NOT NULL REFERENCES suscripcion_estado(id),
    motivo TEXT,
    usuario_id INTEGER, -- Usuario que realizó el cambio
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- EMPRESAS (1 RUT = 1 CUENTA)
-- ================================================================

CREATE TABLE empresas (
    id SERIAL PRIMARY KEY,
    rut VARCHAR(12) UNIQUE NOT NULL, -- Formato: 12.345.678-9
    razon_social VARCHAR(255) NOT NULL,
    nombre_fantasia VARCHAR(255),
    giro VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(255),
    sitio_web VARCHAR(500),
    fecha_constitucion DATE,
    estado_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE empresa_estado (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- 'activa', 'suspendida', 'inactiva', 'bloqueada'
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar foreign key después de crear empresa_estado
ALTER TABLE empresas
    ADD CONSTRAINT fk_empresa_estado
    FOREIGN KEY (estado_id) REFERENCES empresa_estado(id);

-- Agregar foreign key a suscripciones
ALTER TABLE suscripciones
    ADD CONSTRAINT fk_suscripcion_empresa
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE;

ALTER TABLE suscripciones
    ADD CONSTRAINT fk_suscripcion_estado
    FOREIGN KEY (estado_id) REFERENCES suscripcion_estado(id);

CREATE TABLE empresa_direccion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    tipo VARCHAR(50), -- 'comercial', 'legal', 'facturacion'
    direccion VARCHAR(500) NOT NULL,
    comuna VARCHAR(100),
    ciudad VARCHAR(100),
    region VARCHAR(100),
    pais VARCHAR(100) DEFAULT 'Chile',
    codigo_postal VARCHAR(20),
    principal BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE empresa_contacto (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    tipo VARCHAR(50), -- 'principal', 'facturacion', 'soporte', 'comercial'
    nombre VARCHAR(255),
    cargo VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(255),
    principal BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- REPRESENTANTE LEGAL (ÚNICO ACCESO INICIAL)
-- ================================================================

CREATE TABLE representantes_legales (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    rut VARCHAR(12) UNIQUE NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefono VARCHAR(50),
    fecha_nombramiento DATE,
    fecha_termino DATE,
    estado_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE representante_estado (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- 'activo', 'inactivo', 'suspendido'
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE representantes_legales
    ADD CONSTRAINT fk_representante_estado
    FOREIGN KEY (estado_id) REFERENCES representante_estado(id);

-- ================================================================
-- ACCESO AL SISTEMA
-- ================================================================

CREATE TABLE accesos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    representante_id INTEGER REFERENCES representantes_legales(id) ON DELETE SET NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    mfa_enabled BOOLEAN DEFAULT false,
    mfa_secret VARCHAR(255),
    estado_id INTEGER NOT NULL,
    ultimo_acceso TIMESTAMP,
    ip_ultimo_acceso VARCHAR(50),
    token_sesion TEXT,
    token_expiracion TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE acceso_estado (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL, -- 'activo', 'bloqueado', 'suspendido', 'pendiente'
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE accesos
    ADD CONSTRAINT fk_acceso_estado
    FOREIGN KEY (estado_id) REFERENCES acceso_estado(id);

CREATE TABLE acceso_intentos (
    id SERIAL PRIMARY KEY,
    acceso_id INTEGER REFERENCES accesos(id) ON DELETE CASCADE,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(50),
    user_agent TEXT,
    exitoso BOOLEAN DEFAULT false,
    motivo_fallo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE acceso_recuperacion (
    id SERIAL PRIMARY KEY,
    acceso_id INTEGER NOT NULL REFERENCES accesos(id) ON DELETE CASCADE,
    token VARCHAR(255) UNIQUE NOT NULL,
    token_expiracion TIMESTAMP NOT NULL,
    utilizado BOOLEAN DEFAULT false,
    fecha_utilizacion TIMESTAMP,
    ip_solicitud VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE acceso_auditoria (
    id SERIAL PRIMARY KEY,
    acceso_id INTEGER REFERENCES accesos(id) ON DELETE SET NULL,
    empresa_id INTEGER REFERENCES empresas(id) ON DELETE CASCADE,
    accion VARCHAR(100) NOT NULL, -- 'login', 'logout', 'cambio_password', 'activacion_mfa', etc.
    descripcion TEXT,
    ip_address VARCHAR(50),
    user_agent TEXT,
    metadatos JSONB, -- Información adicional en formato JSON
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- ================================================================

-- Empresas
CREATE INDEX idx_empresas_rut ON empresas(rut);
CREATE INDEX idx_empresas_estado ON empresas(estado_id);

-- Suscripciones
CREATE INDEX idx_suscripciones_empresa ON suscripciones(empresa_id);
CREATE INDEX idx_suscripciones_plan ON suscripciones(plan_id);
CREATE INDEX idx_suscripciones_estado ON suscripciones(estado_id);
CREATE INDEX idx_suscripciones_fecha_fin ON suscripciones(fecha_fin);

-- Accesos
CREATE INDEX idx_accesos_email ON accesos(email);
CREATE INDEX idx_accesos_empresa ON accesos(empresa_id);
CREATE INDEX idx_accesos_estado ON accesos(estado_id);
CREATE INDEX idx_accesos_token ON accesos(token_sesion);

-- Representantes
CREATE INDEX idx_representantes_rut ON representantes_legales(rut);
CREATE INDEX idx_representantes_empresa ON representantes_legales(empresa_id);

-- Auditoría
CREATE INDEX idx_auditoria_acceso ON acceso_auditoria(acceso_id);
CREATE INDEX idx_auditoria_empresa ON acceso_auditoria(empresa_id);
CREATE INDEX idx_auditoria_fecha ON acceso_auditoria(created_at);

-- ================================================================
-- DATOS INICIALES
-- ================================================================

-- Estados de empresa
INSERT INTO empresa_estado (codigo, nombre, descripcion, color) VALUES
    ('activa', 'Activa', 'Empresa activa y operativa', '#10b981'),
    ('suspendida', 'Suspendida', 'Empresa temporalmente suspendida', '#f59e0b'),
    ('inactiva', 'Inactiva', 'Empresa inactiva', '#6b7280'),
    ('bloqueada', 'Bloqueada', 'Empresa bloqueada por incumplimiento', '#ef4444');

-- Estados de suscripción
INSERT INTO suscripcion_estado (codigo, nombre, descripcion, color) VALUES
    ('activa', 'Activa', 'Suscripción activa y vigente', '#10b981'),
    ('pendiente', 'Pendiente', 'Pago pendiente', '#f59e0b'),
    ('vencida', 'Vencida', 'Suscripción vencida', '#ef4444'),
    ('cancelada', 'Cancelada', 'Suscripción cancelada por el usuario', '#6b7280');

-- Estados de representante
INSERT INTO representante_estado (codigo, nombre, descripcion, color) VALUES
    ('activo', 'Activo', 'Representante legal activo', '#10b981'),
    ('inactivo', 'Inactivo', 'Representante legal inactivo', '#6b7280'),
    ('suspendido', 'Suspendido', 'Representante temporalmente suspendido', '#f59e0b');

-- Estados de acceso
INSERT INTO acceso_estado (codigo, nombre, descripcion, color) VALUES
    ('activo', 'Activo', 'Acceso activo y habilitado', '#10b981'),
    ('bloqueado', 'Bloqueado', 'Acceso bloqueado por intentos fallidos', '#ef4444'),
    ('suspendido', 'Suspendido', 'Acceso temporalmente suspendido', '#f59e0b'),
    ('pendiente', 'Pendiente', 'Acceso pendiente de activación', '#6b7280');

-- Planes
INSERT INTO planes (codigo, nombre, descripcion, precio, moneda, periodo, activo) VALUES
    ('starter', 'Starter', '1 empresa, 1 usuario, productos ilimitados, facturación e inventario básico', 0, 'CLP', 'mes', true),
    ('profesional', 'Profesional', '1 empresa, hasta 5 usuarios, contabilidad completa, inventario avanzado', 49990, 'CLP', 'mes', true),
    ('empresa', 'Empresa', '1 empresa, usuarios ilimitados, producción, costos, proyectos, multimoneda', 99990, 'CLP', 'mes', true),
    ('corporativo', 'Corporativo', 'Multiempresa, multi sucursal, control gestión, BI, integraciones, SLA', 0, 'CLP', 'mes', true);

-- Límites de planes
INSERT INTO planes_limites (plan_id, recurso, limite) VALUES
    (1, 'usuarios', 1),
    (1, 'empresas', 1),
    (1, 'sucursales', 1),
    (2, 'usuarios', 5),
    (2, 'empresas', 1),
    (2, 'sucursales', 1),
    (3, 'usuarios', NULL), -- ilimitado
    (3, 'empresas', 1),
    (3, 'sucursales', 5),
    (4, 'usuarios', NULL),
    (4, 'empresas', NULL),
    (4, 'sucursales', NULL);

-- Características de planes (Starter)
INSERT INTO planes_caracteristicas (plan_id, caracteristica, valor, tipo, orden) VALUES
    (1, 'Productos', 'Ilimitados', 'texto', 1),
    (1, 'Facturación', 'Básica', 'texto', 2),
    (1, 'Inventario', 'Básico', 'texto', 3),
    (1, 'Soporte', 'Email', 'texto', 4);

-- Características de planes (Profesional)
INSERT INTO planes_caracteristicas (plan_id, caracteristica, valor, tipo, orden) VALUES
    (2, 'Contabilidad', 'Completa', 'texto', 1),
    (2, 'Inventario', 'Avanzado', 'texto', 2),
    (2, 'Compras y Ventas', 'Sí', 'boolean', 3),
    (2, 'Reportes', 'Sí', 'boolean', 4),
    (2, 'Soporte', 'Email + Chat', 'texto', 5);

-- Características de planes (Empresa)
INSERT INTO planes_caracteristicas (plan_id, caracteristica, valor, tipo, orden) VALUES
    (3, 'Producción', 'Sí', 'boolean', 1),
    (3, 'Costos', 'Sí', 'boolean', 2),
    (3, 'Proyectos', 'Sí', 'boolean', 3),
    (3, 'Multi moneda', 'Sí', 'boolean', 4),
    (3, 'Integración SII', 'Sí', 'boolean', 5),
    (3, 'Soporte', 'Prioritario', 'texto', 6);

-- Características de planes (Corporativo)
INSERT INTO planes_caracteristicas (plan_id, caracteristica, valor, tipo, orden) VALUES
    (4, 'Multiempresa', 'Sí', 'boolean', 1),
    (4, 'Multi sucursal', 'Sí', 'boolean', 2),
    (4, 'Control gestión', 'Sí', 'boolean', 3),
    (4, 'BI', 'Sí', 'boolean', 4),
    (4, 'Integraciones', 'Sí', 'boolean', 5),
    (4, 'SLA dedicado', 'Sí', 'boolean', 6),
    (4, 'Soporte', 'Premium 24/7', 'texto', 7);

-- ================================================================
-- COMENTARIOS EN TABLAS
-- ================================================================

COMMENT ON TABLE empresas IS 'Tabla principal de empresas. 1 RUT = 1 cuenta';
COMMENT ON TABLE representantes_legales IS 'Representantes legales de las empresas. Acceso inicial al sistema';
COMMENT ON TABLE accesos IS 'Credenciales de acceso al sistema';
COMMENT ON TABLE suscripciones IS 'Suscripciones activas de las empresas a los planes';
COMMENT ON TABLE planes IS 'Planes disponibles del ERP';
COMMENT ON TABLE acceso_auditoria IS 'Registro de auditoría de todos los accesos al sistema';
