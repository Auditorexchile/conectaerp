-- ================================================================
-- MÓDULO: GESTIÓN DE MATERIALES (MM) - Materials Management
-- Sistema de Inventarios, Almacenes, MRP nivel SAP Enterprise
-- Incluye: Multi-almacén, Lotes, Series, MRP, Valoración, Trazabilidad
-- ================================================================

-- ===== ALMACENES Y UBICACIONES =====

-- Almacenes
CREATE TABLE almacenes (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(50), -- principal, sucursal, transito, cuarentena, chatarra
    direccion TEXT,
    responsable_id INTEGER REFERENCES usuarios_acceso(id),
    permite_ventas BOOLEAN DEFAULT true,
    permite_compras BOOLEAN DEFAULT true,
    permite_produccion BOOLEAN DEFAULT false,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ubicaciones dentro de almacenes
CREATE TABLE almacenes_ubicaciones (
    id SERIAL PRIMARY KEY,
    almacen_id INTEGER NOT NULL REFERENCES almacenes(id),
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(100),
    pasillo VARCHAR(20),
    estante VARCHAR(20),
    nivel VARCHAR(20),
    capacidad DECIMAL(18,2),
    unidad_capacidad VARCHAR(20),
    activo BOOLEAN DEFAULT true,
    UNIQUE(almacen_id, codigo)
);

-- ===== INVENTARIO Y STOCK =====

-- Stock por producto y almacén
CREATE TABLE inventario (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    almacen_id INTEGER NOT NULL REFERENCES almacenes(id),
    ubicacion_id INTEGER REFERENCES almacenes_ubicaciones(id),
    stock_actual DECIMAL(18,2) DEFAULT 0,
    stock_reservado DECIMAL(18,2) DEFAULT 0,
    stock_disponible DECIMAL(18,2) DEFAULT 0,
    stock_en_transito DECIMAL(18,2) DEFAULT 0,
    stock_minimo DECIMAL(18,2) DEFAULT 0,
    stock_maximo DECIMAL(18,2) DEFAULT 0,
    punto_reorden DECIMAL(18,2),
    costo_promedio DECIMAL(18,6) DEFAULT 0,
    costo_ultimo DECIMAL(18,6) DEFAULT 0,
    fecha_ultimo_movimiento TIMESTAMP,
    actualizado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(producto_id, almacen_id)
);

-- Control de lotes
CREATE TABLE inventario_lotes (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    almacen_id INTEGER NOT NULL REFERENCES almacenes(id),
    lote VARCHAR(100) NOT NULL,
    fecha_fabricacion DATE,
    fecha_vencimiento DATE,
    cantidad DECIMAL(18,2) DEFAULT 0,
    costo_unitario DECIMAL(18,6),
    proveedor_id INTEGER REFERENCES auxiliares(id),
    orden_compra_id INTEGER,
    estado VARCHAR(50) DEFAULT 'disponible', -- disponible, reservado, cuarentena, vencido
    certificado_calidad VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(producto_id, almacen_id, lote)
);

-- Control de números de serie
CREATE TABLE inventario_series (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    almacen_id INTEGER REFERENCES almacenes(id),
    numero_serie VARCHAR(100) UNIQUE NOT NULL,
    lote VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'disponible', -- disponible, vendido, en_reparacion, defectuoso
    fecha_ingreso DATE,
    fecha_salida DATE,
    proveedor_id INTEGER REFERENCES auxiliares(id),
    cliente_id INTEGER REFERENCES auxiliares(id),
    garantia_hasta DATE,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Movimientos de inventario
CREATE TABLE movimientos_inventario (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    tipo_movimiento VARCHAR(50) NOT NULL, -- entrada, salida, ajuste, transferencia
    fecha TIMESTAMP NOT NULL,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    almacen_origen_id INTEGER REFERENCES almacenes(id),
    almacen_destino_id INTEGER REFERENCES almacenes(id),
    cantidad DECIMAL(18,2) NOT NULL,
    costo_unitario DECIMAL(18,6),
    costo_total DECIMAL(18,2),
    lote VARCHAR(100),
    serie VARCHAR(100),
    documento_tipo VARCHAR(50), -- compra, venta, produccion, ajuste
    documento_id INTEGER,
    documento_numero VARCHAR(100),
    motivo VARCHAR(100),
    observaciones TEXT,
    usuario_id INTEGER REFERENCES usuarios_acceso(id),
    contabilizado BOOLEAN DEFAULT false,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kardex valorizado
CREATE TABLE kardex (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    almacen_id INTEGER NOT NULL REFERENCES almacenes(id),
    fecha DATE NOT NULL,
    movimiento_id INTEGER REFERENCES movimientos_inventario(id),
    tipo VARCHAR(20), -- entrada, salida
    cantidad DECIMAL(18,2),
    costo_unitario DECIMAL(18,6),
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    saldo_cantidad DECIMAL(18,2),
    saldo_valor DECIMAL(18,2),
    costo_promedio DECIMAL(18,6),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== COMPRAS =====

-- Órdenes de compra
CREATE TABLE ordenes_compra (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha DATE NOT NULL,
    fecha_entrega DATE,
    proveedor_id INTEGER NOT NULL REFERENCES auxiliares(id),
    proveedor_nombre VARCHAR(255),
    proveedor_rut VARCHAR(20),
    almacen_destino_id INTEGER REFERENCES almacenes(id),
    comprador_id INTEGER REFERENCES usuarios_acceso(id),
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    subtotal DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    condicion_pago VARCHAR(100),
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'borrador', -- borrador, aprobada, enviada, recepcion_parcial, recibida, facturada, cancelada
    aprobado_por INTEGER,
    fecha_aprobacion DATE,
    centro_costo_id INTEGER,
    proyecto_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle órdenes de compra
CREATE TABLE ordenes_compra_detalle (
    id SERIAL PRIMARY KEY,
    orden_compra_id INTEGER NOT NULL REFERENCES ordenes_compra(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    producto_id INTEGER REFERENCES productos(id),
    descripcion TEXT,
    cantidad DECIMAL(18,2) NOT NULL,
    cantidad_recibida DECIMAL(18,2) DEFAULT 0,
    cantidad_facturada DECIMAL(18,2) DEFAULT 0,
    cantidad_pendiente DECIMAL(18,2),
    unidad_medida VARCHAR(50),
    precio_unitario DECIMAL(18,2) NOT NULL,
    descuento DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2),
    fecha_entrega DATE,
    cuenta_contable_id INTEGER,
    centro_costo_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recepciones de compra
CREATE TABLE recepciones_compra (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha DATE NOT NULL,
    orden_compra_id INTEGER REFERENCES ordenes_compra(id),
    proveedor_id INTEGER REFERENCES auxiliares(id),
    almacen_id INTEGER NOT NULL REFERENCES almacenes(id),
    guia_despacho_proveedor VARCHAR(100),
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'borrador',
    recibido_por INTEGER REFERENCES usuarios_acceso(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Detalle recepciones
CREATE TABLE recepciones_compra_detalle (
    id SERIAL PRIMARY KEY,
    recepcion_id INTEGER NOT NULL REFERENCES recepciones_compra(id) ON DELETE CASCADE,
    orden_compra_detalle_id INTEGER REFERENCES ordenes_compra_detalle(id),
    producto_id INTEGER REFERENCES productos(id),
    cantidad DECIMAL(18,2) NOT NULL,
    lote VARCHAR(100),
    fecha_vencimiento DATE,
    ubicacion_id INTEGER REFERENCES almacenes_ubicaciones(id),
    costo_unitario DECIMAL(18,6),
    estado_calidad VARCHAR(50) DEFAULT 'aprobado', -- aprobado, rechazado, cuarentena
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== MRP - PLANIFICACIÓN DE MATERIALES =====

-- Parámetros MRP por producto
CREATE TABLE mrp_parametros (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id) UNIQUE,
    metodo_planificacion VARCHAR(50), -- lote_por_lote, cantidad_fija, periodo_fijo, EOQ
    tamaño_lote DECIMAL(18,2),
    stock_seguridad DECIMAL(18,2) DEFAULT 0,
    tiempo_suministro_dias INTEGER DEFAULT 0,
    punto_reorden DECIMAL(18,2),
    cantidad_reorden DECIMAL(18,2),
    lote_minimo DECIMAL(18,2),
    lote_multiplo DECIMAL(18,2),
    porcentaje_desperdicio DECIMAL(5,2) DEFAULT 0,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Demanda proyectada
CREATE TABLE mrp_demanda (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    fecha DATE NOT NULL,
    tipo_demanda VARCHAR(50), -- pronostico, pedido, produccion
    cantidad DECIMAL(18,2) NOT NULL,
    origen VARCHAR(50),
    documento_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Plan de requerimientos
CREATE TABLE mrp_plan (
    id SERIAL PRIMARY KEY,
    ejecucion_id INTEGER,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    fecha DATE NOT NULL,
    stock_inicial DECIMAL(18,2),
    demanda_bruta DECIMAL(18,2) DEFAULT 0,
    recepciones_programadas DECIMAL(18,2) DEFAULT 0,
    stock_proyectado DECIMAL(18,2),
    necesidades_netas DECIMAL(18,2) DEFAULT 0,
    recepcion_orden DECIMAL(18,2) DEFAULT 0,
    lanzamiento_orden DECIMAL(18,2) DEFAULT 0,
    tipo_orden VARCHAR(50), -- compra, produccion, transferencia
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Órdenes planificadas (sugerencias MRP)
CREATE TABLE mrp_ordenes_planificadas (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    tipo_orden VARCHAR(50), -- compra, produccion
    cantidad DECIMAL(18,2) NOT NULL,
    fecha_necesidad DATE NOT NULL,
    fecha_lanzamiento DATE NOT NULL,
    estado VARCHAR(50) DEFAULT 'planificada', -- planificada, convertida, cancelada
    proveedor_sugerido_id INTEGER REFERENCES auxiliares(id),
    orden_compra_id INTEGER REFERENCES ordenes_compra(id),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== VALORACIÓN DE INVENTARIO =====

-- Métodos de valoración
CREATE TABLE valoracion_metodos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100),
    metodo VARCHAR(50) NOT NULL, -- FIFO, LIFO, promedio_ponderado, precio_estandar
    descripcion TEXT,
    activo BOOLEAN DEFAULT true
);

-- Valoración de inventario
CREATE TABLE valoracion_inventario (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    producto_id INTEGER REFERENCES productos(id),
    almacen_id INTEGER REFERENCES almacenes(id),
    fecha_corte DATE NOT NULL,
    cantidad DECIMAL(18,2),
    costo_unitario DECIMAL(18,6),
    valor_total DECIMAL(18,2),
    metodo_valoracion VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== INVENTARIO FÍSICO Y AJUSTES =====

-- Tomas de inventario físico
CREATE TABLE inventario_fisico (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    almacen_id INTEGER REFERENCES almacenes(id),
    tipo VARCHAR(50), -- completo, ciclico, selectivo
    estado VARCHAR(50) DEFAULT 'planificado', -- planificado, en_proceso, finalizado, cerrado
    responsable_id INTEGER REFERENCES usuarios_acceso(id),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Detalle conteo físico
CREATE TABLE inventario_fisico_conteo (
    id SERIAL PRIMARY KEY,
    inventario_fisico_id INTEGER NOT NULL REFERENCES inventario_fisico(id),
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    ubicacion_id INTEGER REFERENCES almacenes_ubicaciones(id),
    lote VARCHAR(100),
    stock_sistema DECIMAL(18,2),
    cantidad_contada DECIMAL(18,2),
    diferencia DECIMAL(18,2),
    costo_unitario DECIMAL(18,6),
    valor_diferencia DECIMAL(18,2),
    contado_por INTEGER REFERENCES usuarios_acceso(id),
    fecha_conteo TIMESTAMP,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ajustes de inventario
CREATE TABLE inventario_ajustes (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha DATE NOT NULL,
    almacen_id INTEGER REFERENCES almacenes(id),
    inventario_fisico_id INTEGER REFERENCES inventario_fisico(id),
    motivo VARCHAR(100),
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'borrador',
    aprobado_por INTEGER,
    contabilizado BOOLEAN DEFAULT false,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle ajustes
CREATE TABLE inventario_ajustes_detalle (
    id SERIAL PRIMARY KEY,
    ajuste_id INTEGER NOT NULL REFERENCES inventario_ajustes(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad DECIMAL(18,2) NOT NULL,
    tipo VARCHAR(20), -- entrada, salida
    costo_unitario DECIMAL(18,6),
    valor DECIMAL(18,2),
    lote VARCHAR(100),
    motivo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== TRANSFERENCIAS ENTRE ALMACENES =====

-- Transferencias
CREATE TABLE transferencias_almacen (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha DATE NOT NULL,
    almacen_origen_id INTEGER NOT NULL REFERENCES almacenes(id),
    almacen_destino_id INTEGER NOT NULL REFERENCES almacenes(id),
    motivo TEXT,
    estado VARCHAR(50) DEFAULT 'borrador', -- borrador, enviada, en_transito, recibida, cancelada
    enviado_por INTEGER REFERENCES usuarios_acceso(id),
    recibido_por INTEGER REFERENCES usuarios_acceso(id),
    fecha_envio DATE,
    fecha_recepcion DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Detalle transferencias
CREATE TABLE transferencias_almacen_detalle (
    id SERIAL PRIMARY KEY,
    transferencia_id INTEGER NOT NULL REFERENCES transferencias_almacen(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad_enviada DECIMAL(18,2) NOT NULL,
    cantidad_recibida DECIMAL(18,2) DEFAULT 0,
    lote VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ÍNDICES =====

CREATE INDEX idx_inventario_producto_almacen ON inventario(producto_id, almacen_id);
CREATE INDEX idx_movimientos_inventario_producto ON movimientos_inventario(producto_id);
CREATE INDEX idx_movimientos_inventario_fecha ON movimientos_inventario(fecha);
CREATE INDEX idx_kardex_lookup ON kardex(producto_id, almacen_id, fecha);
CREATE INDEX idx_ordenes_compra_proveedor ON ordenes_compra(proveedor_id);
CREATE INDEX idx_ordenes_compra_estado ON ordenes_compra(estado);
CREATE INDEX idx_mrp_demanda_producto_fecha ON mrp_demanda(producto_id, fecha);
CREATE INDEX idx_lotes_vencimiento ON inventario_lotes(fecha_vencimiento);
