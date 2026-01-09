-- ===============================================
-- CONECTA ERP - ESQUEMA COMPLETO BASE DE DATOS
-- Sistema Multiempresa, Multipais, Multimoneda, Multiidioma
-- ===============================================

-- ===============================================
-- 1. TABLA: PAÍSES
-- ===============================================
CREATE TABLE IF NOT EXISTS paises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_iso VARCHAR(2) UNIQUE NOT NULL,
    nombre_es VARCHAR(100) NOT NULL,
    nombre_en VARCHAR(100) NOT NULL,
    codigo_telefono VARCHAR(10),
    formato_identificador VARCHAR(50),
    tipo_identificador VARCHAR(20),
    nombre_identificador VARCHAR(50),
    moneda_default VARCHAR(3),
    zona_horaria VARCHAR(50),
    formato_fecha VARCHAR(20),
    impuesto_principal VARCHAR(50),
    tasa_impuesto DECIMAL(5,2),
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 2. TABLA: IDIOMAS
-- ===============================================
CREATE TABLE IF NOT EXISTS idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(5) UNIQUE NOT NULL,
    nombre_nativo VARCHAR(50) NOT NULL,
    nombre_es VARCHAR(50) NOT NULL,
    nombre_en VARCHAR(50) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 3. TABLA: MONEDAS
-- ===============================================
CREATE TABLE IF NOT EXISTS monedas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(3) UNIQUE NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    simbolo VARCHAR(10),
    decimales TINYINT DEFAULT 2,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 4. TABLA: PLANES
-- ===============================================
CREATE TABLE IF NOT EXISTS planes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    descripcion TEXT,
    precio_mensual DECIMAL(10,2) DEFAULT 0,
    usuarios_max INT DEFAULT 1,
    empresas_max INT DEFAULT 1,
    trial_dias INT DEFAULT 14,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 5. TABLA: EMPRESAS
-- ===============================================
CREATE TABLE IF NOT EXISTS empresas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pais_id INT NOT NULL,
    identificador VARCHAR(50) UNIQUE NOT NULL COMMENT 'RUT/CUIT/RUC/etc',
    razon_social VARCHAR(255) NOT NULL,
    nombre_fantasia VARCHAR(255),
    giro VARCHAR(255),
    actividad_principal VARCHAR(255),
    tipo_entidad ENUM('empresa','persona_natural','profesional','ong') DEFAULT 'empresa',
    tipo_contribuyente VARCHAR(50),
    regimen_tributario VARCHAR(50),
    fecha_inicio_actividades DATE,
    direccion TEXT,
    ciudad VARCHAR(100),
    region VARCHAR(100),
    codigo_postal VARCHAR(20),
    telefono VARCHAR(50),
    email VARCHAR(255),
    sitio_web VARCHAR(255),
    logo VARCHAR(255),
    plan_id INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_trial_inicio DATETIME,
    fecha_trial_fin DATETIME,
    estado ENUM('trial','activo','suspendido','bloqueado') DEFAULT 'trial',
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pais_id) REFERENCES paises(id),
    FOREIGN KEY (plan_id) REFERENCES planes(id),
    INDEX idx_identificador (identificador),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 6. TABLA: USUARIOS
-- ===============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empresa_id INT NOT NULL,
    pais_id INT NOT NULL,
    identificador VARCHAR(50) NOT NULL COMMENT 'RUT/DNI personal',
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(50),
    fecha_nacimiento DATE,
    sexo ENUM('M','F','otro') DEFAULT 'otro',
    estado_civil VARCHAR(20),
    es_representante_legal TINYINT(1) DEFAULT 0,
    es_superusuario TINYINT(1) DEFAULT 0,
    idioma_codigo VARCHAR(5) DEFAULT 'es',
    zona_horaria VARCHAR(50),
    ultimo_acceso DATETIME,
    ip_ultimo_acceso VARCHAR(45),
    estado ENUM('activo','inactivo','bloqueado') DEFAULT 'activo',
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    FOREIGN KEY (pais_id) REFERENCES paises(id),
    INDEX idx_email (email),
    INDEX idx_identificador (identificador),
    INDEX idx_empresa (empresa_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 7. TABLA: LOGIN INTENTOS (Seguridad)
-- ===============================================
CREATE TABLE IF NOT EXISTS login_intentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    exitoso TINYINT(1) DEFAULT 0,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_fecha (email, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 8. TABLA: PASSWORD RESETS
-- ===============================================
CREATE TABLE IF NOT EXISTS password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expira_en DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 9. TABLA: SESIONES
-- ===============================================
CREATE TABLE IF NOT EXISTS sesiones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expira_en DATETIME NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 10. TABLA: CONFIGURACIÓN EMPRESA
-- ===============================================
CREATE TABLE IF NOT EXISTS empresa_configuracion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empresa_id INT UNIQUE NOT NULL,
    moneda_base VARCHAR(3) NOT NULL,
    idioma_default VARCHAR(5) NOT NULL,
    zona_horaria VARCHAR(50) NOT NULL,
    formato_fecha VARCHAR(20) NOT NULL,
    separador_decimal VARCHAR(1) DEFAULT '.',
    separador_miles VARCHAR(1) DEFAULT ',',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 11. TABLA: AUDITORÍA
-- ===============================================
CREATE TABLE IF NOT EXISTS auditoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    empresa_id INT,
    accion VARCHAR(100) NOT NULL,
    tabla VARCHAR(50),
    registro_id INT,
    datos_anteriores JSON,
    datos_nuevos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_empresa (empresa_id),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
