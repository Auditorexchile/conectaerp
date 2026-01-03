-- ================================================================
-- MÓDULO: AUXILIARES Y ENTIDADES (Clientes/Proveedores/Contactos)
-- ================================================================

-- Tipos de auxiliares
CREATE TABLE auxiliares_tipos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO auxiliares_tipos (codigo, nombre) VALUES
('cliente', 'Cliente'),
('proveedor', 'Proveedor'),
('empleado', 'Empleado'),
('otro', 'Otro');

-- Auxiliares (clientes, proveedores, etc.)
CREATE TABLE auxiliares (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    tipo_id INTEGER NOT NULL REFERENCES auxiliares_tipos(id),
    codigo VARCHAR(50),
    rut VARCHAR(20),
    razon_social VARCHAR(255) NOT NULL,
    nombre_fantasia VARCHAR(255),
    giro VARCHAR(255),
    email VARCHAR(255),
    telefono VARCHAR(50),
    sitio_web VARCHAR(255),
    pais VARCHAR(2) DEFAULT 'CL',
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

-- Direcciones de auxiliares
CREATE TABLE auxiliares_direcciones (
    id SERIAL PRIMARY KEY,
    auxiliar_id INTEGER NOT NULL REFERENCES auxiliares(id) ON DELETE CASCADE,
    tipo VARCHAR(50),
    direccion VARCHAR(500) NOT NULL,
    comuna VARCHAR(100),
    ciudad VARCHAR(100),
    region VARCHAR(100),
    pais VARCHAR(100),
    codigo_postal VARCHAR(20),
    principal BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contactos de auxiliares
CREATE TABLE auxiliares_contactos (
    id SERIAL PRIMARY KEY,
    auxiliar_id INTEGER NOT NULL REFERENCES auxiliares(id) ON DELETE CASCADE,
    nombre VARCHAR(255) NOT NULL,
    cargo VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(255),
    principal BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos bancarios de auxiliares
CREATE TABLE auxiliares_bancarios (
    id SERIAL PRIMARY KEY,
    auxiliar_id INTEGER NOT NULL REFERENCES auxiliares(id) ON DELETE CASCADE,
    banco VARCHAR(255),
    tipo_cuenta VARCHAR(50),
    numero_cuenta VARCHAR(100),
    titular VARCHAR(255),
    rut_titular VARCHAR(20),
    principal BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Condiciones de crédito
CREATE TABLE auxiliares_credito (
    id SERIAL PRIMARY KEY,
    auxiliar_id INTEGER NOT NULL REFERENCES auxiliares(id) ON DELETE CASCADE,
    dias_credito INTEGER DEFAULT 0,
    monto_limite DECIMAL(18,2),
    monto_utilizado DECIMAL(18,2) DEFAULT 0,
    bloqueado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_auxiliares_empresa ON auxiliares(empresa_id);
CREATE INDEX idx_auxiliares_tipo ON auxiliares(tipo_id);
CREATE INDEX idx_auxiliares_rut ON auxiliares(rut);
