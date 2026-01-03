-- ================================================================
-- MÓDULO: PRODUCCIÓN (PP) - Production Planning
-- ================================================================

-- Lista de materiales (BOM)
CREATE TABLE bom (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    version VARCHAR(50) DEFAULT '1.0',
    descripcion TEXT,
    cantidad_base DECIMAL(18,4) DEFAULT 1,
    unidad VARCHAR(50),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, producto_id, version)
);

CREATE TABLE bom_componentes (
    id SERIAL PRIMARY KEY,
    bom_id INTEGER NOT NULL REFERENCES bom(id) ON DELETE CASCADE,
    componente_id INTEGER NOT NULL REFERENCES productos(id),
    cantidad DECIMAL(18,4) NOT NULL,
    unidad VARCHAR(50),
    desperdicio_porcentaje DECIMAL(5,2) DEFAULT 0,
    obligatorio BOOLEAN DEFAULT true,
    orden INTEGER DEFAULT 0
);

-- Órdenes de fabricación
CREATE TABLE ordenes_fabricacion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) NOT NULL,
    fecha_creacion DATE NOT NULL,
    fecha_inicio DATE,
    fecha_fin_planificada DATE,
    fecha_fin_real DATE,
    producto_id INTEGER REFERENCES productos(id),
    bom_id INTEGER REFERENCES bom(id),
    cantidad_planificada DECIMAL(18,4) NOT NULL,
    cantidad_producida DECIMAL(18,4) DEFAULT 0,
    cantidad_defectuosa DECIMAL(18,4) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'planificada',
    prioridad VARCHAR(50) DEFAULT 'normal',
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    UNIQUE(empresa_id, numero)
);

CREATE TABLE ordenes_fabricacion_materiales (
    id SERIAL PRIMARY KEY,
    orden_id INTEGER NOT NULL REFERENCES ordenes_fabricacion(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad_requerida DECIMAL(18,4) NOT NULL,
    cantidad_consumida DECIMAL(18,4) DEFAULT 0,
    costo_unitario DECIMAL(18,2),
    costo_total DECIMAL(18,2)
);

-- MRP - Material Requirements Planning
CREATE TABLE mrp_planificacion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    producto_id INTEGER REFERENCES productos(id),
    fecha DATE NOT NULL,
    demanda_bruta DECIMAL(18,4) DEFAULT 0,
    recepciones_programadas DECIMAL(18,4) DEFAULT 0,
    stock_disponible DECIMAL(18,4) DEFAULT 0,
    necesidades_netas DECIMAL(18,4) DEFAULT 0,
    recepcion_ordenes DECIMAL(18,4) DEFAULT 0,
    lanzamiento_ordenes DECIMAL(18,4) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Centros de trabajo
CREATE TABLE centros_trabajo (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(50),
    capacidad_horas DECIMAL(10,2),
    costo_hora DECIMAL(18,2),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

-- Rutas de producción
CREATE TABLE rutas_produccion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    producto_id INTEGER REFERENCES productos(id),
    version VARCHAR(50) DEFAULT '1.0',
    descripcion TEXT,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rutas_operaciones (
    id SERIAL PRIMARY KEY,
    ruta_id INTEGER NOT NULL REFERENCES rutas_produccion(id) ON DELETE CASCADE,
    operacion_numero INTEGER NOT NULL,
    descripcion VARCHAR(500),
    centro_trabajo_id INTEGER REFERENCES centros_trabajo(id),
    tiempo_setup DECIMAL(10,2) DEFAULT 0,
    tiempo_operacion DECIMAL(10,2) NOT NULL,
    costo_estimado DECIMAL(18,2),
    orden INTEGER DEFAULT 0
);

-- Control de calidad
CREATE TABLE control_calidad (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    orden_fabricacion_id INTEGER REFERENCES ordenes_fabricacion(id),
    producto_id INTEGER REFERENCES productos(id),
    lote VARCHAR(100),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cantidad_inspeccionada DECIMAL(18,4),
    cantidad_aprobada DECIMAL(18,4),
    cantidad_rechazada DECIMAL(18,4),
    inspector_id INTEGER,
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'pendiente'
);

CREATE TABLE no_conformidades (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    control_calidad_id INTEGER REFERENCES control_calidad(id),
    tipo VARCHAR(100),
    descripcion TEXT,
    accion_correctiva TEXT,
    responsable_id INTEGER,
    fecha_deteccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'abierta'
);

-- Costos de producción
CREATE TABLE costos_produccion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    orden_fabricacion_id INTEGER REFERENCES ordenes_fabricacion(id),
    tipo_costo VARCHAR(50), -- material, mano_obra, overhead
    concepto VARCHAR(255),
    cantidad DECIMAL(18,4),
    costo_unitario DECIMAL(18,2),
    costo_total DECIMAL(18,2),
    fecha DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_ordenes_empresa ON ordenes_fabricacion(empresa_id);
CREATE INDEX idx_ordenes_producto ON ordenes_fabricacion(producto_id);
CREATE INDEX idx_ordenes_estado ON ordenes_fabricacion(estado);
