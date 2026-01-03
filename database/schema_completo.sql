-- ================================================================
-- CONECTA ERP - ESQUEMA DE BASE DE DATOS ENTERPRISE COMPLETO
-- Sistema ERP nivel SAP Business One / Softland
-- Versión: 1.0.0
-- Fecha: 2026-01-03
-- ================================================================

-- Configuración inicial
SET client_encoding = 'UTF8';

-- ================================================================
-- 1. SEGURIDAD / ACCESO / LICENCIAMIENTO
-- ================================================================

-- Tipos de usuario
CREATE TABLE usuario_tipo (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuario_tipo (codigo, nombre, descripcion) VALUES
('superadmin', 'Super Administrador', 'Acceso total al sistema'),
('admin', 'Administrador', 'Administrador de empresa'),
('usuario', 'Usuario', 'Usuario normal'),
('limitado', 'Usuario Limitado', 'Acceso restringido');

-- Estados de usuario
CREATE TABLE usuario_estado (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    color VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuario_estado (codigo, nombre, color) VALUES
('activo', 'Activo', '#10b981'),
('inactivo', 'Inactivo', '#6b7280'),
('bloqueado', 'Bloqueado', '#ef4444'),
('pendiente', 'Pendiente Activación', '#f59e0b');

-- Usuarios de acceso
CREATE TABLE usuarios_acceso (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(255),
    tipo_usuario_id INTEGER NOT NULL REFERENCES usuario_tipo(id),
    estado_id INTEGER NOT NULL REFERENCES usuario_estado(id),
    mfa_enabled BOOLEAN DEFAULT false,
    mfa_secret VARCHAR(255),
    bloqueado BOOLEAN DEFAULT false,
    bloqueado_hasta TIMESTAMP,
    ultimo_acceso TIMESTAMP,
    ip_ultimo_acceso VARCHAR(50),
    token_sesion TEXT,
    token_expiracion TIMESTAMP,
    intentos_fallidos INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    updated_by INTEGER
);

-- Sesiones de usuario
CREATE TABLE sesiones_usuario (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL REFERENCES usuarios_acceso(id) ON DELETE CASCADE,
    token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(50),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Intentos de login
CREATE TABLE intentos_login (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255),
    usuario_id INTEGER REFERENCES usuarios_acceso(id) ON DELETE SET NULL,
    ip_address VARCHAR(50),
    user_agent TEXT,
    exitoso BOOLEAN DEFAULT false,
    motivo_fallo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recuperación de contraseñas
CREATE TABLE password_resets (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL REFERENCES usuarios_acceso(id) ON DELETE CASCADE,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    usado BOOLEAN DEFAULT false,
    usado_at TIMESTAMP,
    ip_solicitud VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tokens 2FA
CREATE TABLE tokens_2fa (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL REFERENCES usuarios_acceso(id) ON DELETE CASCADE,
    codigo VARCHAR(10) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    usado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- IP Whitelist
CREATE TABLE ip_whitelist (
    id SERIAL PRIMARY KEY,
    ip_address VARCHAR(50) UNIQUE NOT NULL,
    descripcion VARCHAR(255),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- IP Blacklist
CREATE TABLE ip_blacklist (
    id SERIAL PRIMARY KEY,
    ip_address VARCHAR(50) UNIQUE NOT NULL,
    motivo TEXT,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP
);

-- Dispositivos autorizados
CREATE TABLE dispositivos_autorizados (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL REFERENCES usuarios_acceso(id) ON DELETE CASCADE,
    device_id VARCHAR(255) UNIQUE NOT NULL,
    nombre_dispositivo VARCHAR(255),
    user_agent TEXT,
    ip_address VARCHAR(50),
    activo BOOLEAN DEFAULT true,
    ultimo_acceso TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- 2. PLANES Y SUSCRIPCIONES
-- ================================================================

-- Planes
CREATE TABLE planes (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(12,2) NOT NULL DEFAULT 0,
    moneda VARCHAR(10) DEFAULT 'CLP',
    periodo VARCHAR(50) DEFAULT 'mes',
    max_usuarios INTEGER,
    max_empresas INTEGER,
    max_sucursales INTEGER,
    activo BOOLEAN DEFAULT true,
    destacado BOOLEAN DEFAULT false,
    orden INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Características de planes
CREATE TABLE plan_caracteristicas (
    id SERIAL PRIMARY KEY,
    plan_id INTEGER NOT NULL REFERENCES planes(id) ON DELETE CASCADE,
    caracteristica VARCHAR(255) NOT NULL,
    valor TEXT,
    tipo VARCHAR(50),
    incluido BOOLEAN DEFAULT true,
    orden INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Límites de planes
CREATE TABLE plan_limites (
    id SERIAL PRIMARY KEY,
    plan_id INTEGER NOT NULL REFERENCES planes(id) ON DELETE CASCADE,
    recurso VARCHAR(100) NOT NULL,
    limite INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(plan_id, recurso)
);

-- ================================================================
-- 3. EMPRESAS Y CONFIGURACIÓN
-- ================================================================

-- Estados de empresa
CREATE TABLE empresa_estado (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    color VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO empresa_estado (codigo, nombre, color) VALUES
('activa', 'Activa', '#10b981'),
('trial', 'En Prueba', '#3b82f6'),
('suspendida', 'Suspendida', '#f59e0b'),
('bloqueada', 'Bloqueada', '#ef4444'),
('inactiva', 'Inactiva', '#6b7280');

-- Tipos de empresa
CREATE TABLE empresa_tipo (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO empresa_tipo (codigo, nombre) VALUES
('persona_natural', 'Persona Natural'),
('spa', 'SpA'),
('srl', 'SRL'),
('sa', 'S.A.'),
('ltda', 'Ltda.'),
('eirl', 'EIRL');

-- Empresas
CREATE TABLE empresas (
    id SERIAL PRIMARY KEY,
    rut VARCHAR(12) UNIQUE NOT NULL,
    razon_social VARCHAR(255) NOT NULL,
    nombre_fantasia VARCHAR(255),
    giro VARCHAR(255),
    tipo_empresa_id INTEGER REFERENCES empresa_tipo(id),
    estado_id INTEGER NOT NULL REFERENCES empresa_estado(id),
    pais VARCHAR(2) DEFAULT 'CL',
    moneda_base VARCHAR(10) DEFAULT 'CLP',
    idioma VARCHAR(10) DEFAULT 'es',
    telefono VARCHAR(50),
    email VARCHAR(255),
    sitio_web VARCHAR(500),
    fecha_constitucion DATE,
    logo_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP
);

-- Direcciones de empresa
CREATE TABLE empresa_direcciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    tipo VARCHAR(50),
    direccion VARCHAR(500) NOT NULL,
    comuna VARCHAR(100),
    ciudad VARCHAR(100),
    region VARCHAR(100),
    pais VARCHAR(100) DEFAULT 'Chile',
    codigo_postal VARCHAR(20),
    principal BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contactos de empresa
CREATE TABLE empresa_contactos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    tipo VARCHAR(50),
    nombre VARCHAR(255),
    cargo VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(255),
    principal BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Representante legal
CREATE TABLE empresa_representante_legal (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    rut VARCHAR(12) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    telefono VARCHAR(50),
    fecha_nombramiento DATE,
    fecha_termino DATE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sucursales
CREATE TABLE sucursales (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(50),
    nombre VARCHAR(255) NOT NULL,
    direccion VARCHAR(500),
    telefono VARCHAR(50),
    email VARCHAR(255),
    responsable_id INTEGER,
    activo BOOLEAN DEFAULT true,
    principal BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

-- Centros de costo
CREATE TABLE centros_costo (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    padre_id INTEGER REFERENCES centros_costo(id),
    nivel INTEGER DEFAULT 1,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

-- Departamentos
CREATE TABLE departamentos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    responsable_id INTEGER,
    padre_id INTEGER REFERENCES departamentos(id),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

-- ================================================================
-- 4. TRIAL Y SUSCRIPCIONES
-- ================================================================

-- Configuración de trial
CREATE TABLE trial_configuracion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER UNIQUE NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    dias_trial INTEGER DEFAULT 14,
    activo BOOLEAN DEFAULT true,
    primer_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Estados de trial
CREATE TABLE trial_estado (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    color VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO trial_estado (codigo, nombre, color) VALUES
('activo', 'Trial Activo', '#10b981'),
('vencido', 'Trial Vencido', '#ef4444'),
('convertido', 'Convertido a Plan Pago', '#3b82f6');

-- Historial de trial
CREATE TABLE trial_historial (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    estado_id INTEGER NOT NULL REFERENCES trial_estado(id),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    motivo TEXT,
    created_by INTEGER
);

-- Suscripciones
CREATE TABLE suscripciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    plan_id INTEGER NOT NULL REFERENCES planes(id),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    fecha_proximo_pago DATE,
    precio DECIMAL(12,2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    auto_renovacion BOOLEAN DEFAULT true,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pagos de suscripción
CREATE TABLE suscripcion_pagos (
    id SERIAL PRIMARY KEY,
    suscripcion_id INTEGER NOT NULL REFERENCES suscripciones(id) ON DELETE CASCADE,
    monto DECIMAL(12,2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    fecha_pago DATE NOT NULL,
    metodo_pago VARCHAR(100),
    referencia_pago VARCHAR(255),
    estado VARCHAR(50),
    comprobante_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Facturación de suscripciones
CREATE TABLE suscripcion_facturacion (
    id SERIAL PRIMARY KEY,
    suscripcion_id INTEGER NOT NULL REFERENCES suscripciones(id),
    numero_factura VARCHAR(100),
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE,
    monto DECIMAL(12,2) NOT NULL,
    impuesto DECIMAL(12,2),
    total DECIMAL(12,2) NOT NULL,
    pagado BOOLEAN DEFAULT false,
    fecha_pago DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bloqueos por falta de pago
CREATE TABLE suscripcion_bloqueos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    motivo TEXT,
    fecha_bloqueo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_desbloqueo TIMESTAMP,
    activo BOOLEAN DEFAULT true,
    created_by INTEGER
);

-- ================================================================
-- 5. MULTIPAÍS / MULTIMONEDA / MULTIIDIOMA
-- ================================================================

-- Países
CREATE TABLE paises (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(2) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    nombre_oficial VARCHAR(255),
    codigo_telefono VARCHAR(10),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Monedas
CREATE TABLE monedas (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(10) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    simbolo VARCHAR(10),
    decimales INTEGER DEFAULT 2,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tipos de cambio
CREATE TABLE tipos_cambio (
    id SERIAL PRIMARY KEY,
    moneda_origen VARCHAR(10) NOT NULL,
    moneda_destino VARCHAR(10) NOT NULL,
    tasa DECIMAL(18,6) NOT NULL,
    fecha_vigencia DATE NOT NULL,
    fuente VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(moneda_origen, moneda_destino, fecha_vigencia)
);

-- Historial de tipos de cambio
CREATE TABLE tipos_cambio_historial (
    id SERIAL PRIMARY KEY,
    tipo_cambio_id INTEGER NOT NULL REFERENCES tipos_cambio(id) ON DELETE CASCADE,
    tasa_anterior DECIMAL(18,6),
    tasa_nueva DECIMAL(18,6),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Idiomas
CREATE TABLE idiomas (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(10) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    nombre_nativo VARCHAR(100),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Traducciones
CREATE TABLE traducciones (
    id SERIAL PRIMARY KEY,
    idioma VARCHAR(10) NOT NULL,
    clave VARCHAR(255) NOT NULL,
    valor TEXT NOT NULL,
    modulo VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(idioma, clave, modulo)
);

-- ================================================================
-- CONTINÚA EN SIGUIENTE SECCIÓN...
-- ================================================================
