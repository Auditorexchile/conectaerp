-- ================================================================
-- INTEGRACIONES: SII Y PREVIRED (CHILE)
-- ================================================================

-- SII - Configuración
CREATE TABLE sii_configuracion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL UNIQUE REFERENCES empresas(id),
    ambiente VARCHAR(20) DEFAULT 'certificacion', -- certificacion, produccion
    rut_emisor VARCHAR(20) NOT NULL,
    razon_social VARCHAR(255) NOT NULL,
    giro VARCHAR(255),
    actividad_economica INTEGER,
    direccion_casa_matriz TEXT,
    comuna VARCHAR(100),
    ciudad VARCHAR(100),
    resolucion_sii INTEGER,
    fecha_resolucion DATE,
    certificado_digital TEXT,
    clave_certificado VARCHAR(255),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SII - Folios
CREATE TABLE sii_folios (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    tipo_documento INTEGER NOT NULL, -- 33, 34, 39, 52, 56, 61
    desde INTEGER NOT NULL,
    hasta INTEGER NOT NULL,
    archivo_caf TEXT,
    fecha_autorizacion DATE,
    fecha_vencimiento DATE,
    folio_actual INTEGER,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SII - Documentos Tributarios Electrónicos
CREATE TABLE sii_documentos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    tipo_documento INTEGER NOT NULL,
    folio INTEGER NOT NULL,
    fecha_emision TIMESTAMP NOT NULL,
    rut_receptor VARCHAR(20),
    razon_social_receptor VARCHAR(255),
    monto_neto DECIMAL(18,2),
    monto_exento DECIMAL(18,2),
    iva DECIMAL(18,2),
    monto_total DECIMAL(18,2),
    xml_documento TEXT,
    track_id VARCHAR(100),
    estado_sii VARCHAR(50),
    estado_receptor VARCHAR(50),
    fecha_envio TIMESTAMP,
    fecha_recepcion_sii TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, tipo_documento, folio)
);

-- SII - Libro de Compras
CREATE TABLE sii_libro_compras (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_mes INTEGER NOT NULL,
    periodo_anio INTEGER NOT NULL,
    tipo_documento INTEGER,
    folio INTEGER,
    fecha_emision DATE,
    rut_proveedor VARCHAR(20),
    razon_social VARCHAR(255),
    monto_neto DECIMAL(18,2),
    iva_recuperable DECIMAL(18,2),
    iva_no_recuperable DECIMAL(18,2),
    monto_total DECIMAL(18,2),
    tipo_compra VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SII - Libro de Ventas
CREATE TABLE sii_libro_ventas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_mes INTEGER NOT NULL,
    periodo_anio INTEGER NOT NULL,
    tipo_documento INTEGER,
    folio INTEGER,
    fecha_emision DATE,
    rut_cliente VARCHAR(20),
    razon_social VARCHAR(255),
    monto_neto DECIMAL(18,2),
    iva DECIMAL(18,2),
    monto_exento DECIMAL(18,2),
    monto_total DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SII - Envíos al SII
CREATE TABLE sii_envios (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    tipo_envio VARCHAR(100), -- dte, libro_compras, libro_ventas, f29
    periodo_mes INTEGER,
    periodo_anio INTEGER,
    xml_envio TEXT,
    track_id VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'pendiente',
    respuesta_sii TEXT,
    fecha_envio TIMESTAMP,
    fecha_respuesta TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SII - Cesión de documentos
CREATE TABLE sii_cesiones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    documento_id INTEGER REFERENCES sii_documentos(id),
    rut_cesionario VARCHAR(20) NOT NULL,
    razon_social_cesionario VARCHAR(255),
    fecha_cesion DATE NOT NULL,
    monto DECIMAL(18,2) NOT NULL,
    xml_cesion TEXT,
    estado VARCHAR(50) DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SII - F29 (Declaración mensual)
CREATE TABLE sii_f29 (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_mes INTEGER NOT NULL,
    periodo_anio INTEGER NOT NULL,
    debito_fiscal DECIMAL(18,2) DEFAULT 0,
    credito_fiscal DECIMAL(18,2) DEFAULT 0,
    iva_por_pagar DECIMAL(18,2) DEFAULT 0,
    remanente_mes_anterior DECIMAL(18,2) DEFAULT 0,
    ppm DECIMAL(18,2) DEFAULT 0,
    total_pagar DECIMAL(18,2) DEFAULT 0,
    fecha_declaracion DATE,
    numero_declaracion VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'borrador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, periodo_anio, periodo_mes)
);

-- ================================================================
-- PREVIRED - Cotizaciones Previsionales
-- ================================================================

CREATE TABLE previred_configuracion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL UNIQUE REFERENCES empresas(id),
    mutual VARCHAR(100),
    tasa_accidente_trabajo DECIMAL(5,4) DEFAULT 0,
    ccaf VARCHAR(100),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE previred_trabajadores (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    rut VARCHAR(20) NOT NULL,
    nombres VARCHAR(255) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    sexo VARCHAR(1),
    fecha_nacimiento DATE,
    nacionalidad VARCHAR(50),
    tipo_trabajador VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, empleado_id)
);

CREATE TABLE previred_cotizaciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    periodo_mes INTEGER NOT NULL,
    periodo_anio INTEGER NOT NULL,
    dias_trabajados INTEGER DEFAULT 30,
    tipo_linea VARCHAR(10), -- 00, 01, 02, 03
    remuneracion_imponible DECIMAL(18,2),
    cotizacion_afp DECIMAL(18,2),
    cotizacion_salud DECIMAL(18,2),
    cotizacion_sis DECIMAL(18,2),
    cotizacion_accidente DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, empleado_id, periodo_anio, periodo_mes)
);

CREATE TABLE previred_envios (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_mes INTEGER NOT NULL,
    periodo_anio INTEGER NOT NULL,
    archivo_rem TEXT,
    total_trabajadores INTEGER,
    total_remuneraciones DECIMAL(18,2),
    total_cotizaciones DECIMAL(18,2),
    fecha_envio TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'pendiente',
    respuesta TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, periodo_anio, periodo_mes)
);

-- AFPs
CREATE TABLE previred_afp (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(10) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tasa_afp DECIMAL(5,4) NOT NULL,
    tasa_sis DECIMAL(5,4) NOT NULL,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Isapres
CREATE TABLE previred_isapres (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(10) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tasa DECIMAL(5,4) NOT NULL,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO previred_afp (codigo, nombre, tasa_afp, tasa_sis) VALUES
('001', 'Capital', 0.1144, 0.0147),
('002', 'Cuprum', 0.1144, 0.0147),
('003', 'Habitat', 0.1270, 0.0140),
('004', 'Modelo', 0.1058, 0.0154),
('005', 'Planvital', 0.1016, 0.0156),
('006', 'Provida', 0.1145, 0.0147),
('007', 'Uno', 0.1069, 0.0153);

CREATE INDEX idx_sii_documentos_empresa ON sii_documentos(empresa_id);
CREATE INDEX idx_sii_folios_empresa ON sii_folios(empresa_id);
CREATE INDEX idx_previred_cotizaciones ON previred_cotizaciones(empresa_id, empleado_id);
