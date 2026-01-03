-- ================================================================
-- MÓDULO: VENTAS (SD) - Sales & Distribution
-- ================================================================

-- Cotizaciones
CREATE TABLE cotizaciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) NOT NULL,
    fecha DATE NOT NULL,
    fecha_vencimiento DATE,
    cliente_id INTEGER REFERENCES auxiliares(id),
    contacto_id INTEGER REFERENCES auxiliares_contactos(id),
    vendedor_id INTEGER,
    subtotal DECIMAL(18,2) DEFAULT 0,
    descuento DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    moneda VARCHAR(10) DEFAULT 'CLP',
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'borrador',
    convertida_pedido BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    UNIQUE(empresa_id, numero)
);

CREATE TABLE cotizaciones_detalle (
    id SERIAL PRIMARY KEY,
    cotizacion_id INTEGER NOT NULL REFERENCES cotizaciones(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    descripcion VARCHAR(500),
    cantidad DECIMAL(18,4) NOT NULL,
    precio_unitario DECIMAL(18,2) NOT NULL,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    descuento_monto DECIMAL(18,2) DEFAULT 0,
    impuesto_porcentaje DECIMAL(5,2) DEFAULT 0,
    impuesto_monto DECIMAL(18,2) DEFAULT 0,
    subtotal DECIMAL(18,2) NOT NULL,
    total DECIMAL(18,2) NOT NULL
);

-- Pedidos de venta
CREATE TABLE pedidos_venta (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) NOT NULL,
    fecha DATE NOT NULL,
    fecha_entrega DATE,
    cliente_id INTEGER REFERENCES auxiliares(id),
    vendedor_id INTEGER,
    subtotal DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'pendiente',
    facturado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    UNIQUE(empresa_id, numero)
);

CREATE TABLE pedidos_venta_detalle (
    id SERIAL PRIMARY KEY,
    pedido_id INTEGER NOT NULL REFERENCES pedidos_venta(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad DECIMAL(18,4) NOT NULL,
    cantidad_entregada DECIMAL(18,4) DEFAULT 0,
    precio_unitario DECIMAL(18,2) NOT NULL,
    subtotal DECIMAL(18,2) NOT NULL,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) NOT NULL
);

-- Guías de despacho
CREATE TABLE guias_despacho (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) NOT NULL,
    fecha DATE NOT NULL,
    cliente_id INTEGER REFERENCES auxiliares(id),
    direccion_despacho TEXT,
    pedido_id INTEGER REFERENCES pedidos_venta(id),
    estado VARCHAR(50) DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, numero)
);

CREATE TABLE guias_despacho_detalle (
    id SERIAL PRIMARY KEY,
    guia_id INTEGER NOT NULL REFERENCES guias_despacho(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad DECIMAL(18,4) NOT NULL
);

-- Facturas de venta (DTE)
CREATE TABLE facturas_venta (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    tipo_documento VARCHAR(10), -- 33=Factura, 34=Factura Exenta, 39=Boleta
    numero INTEGER NOT NULL,
    fecha_emision DATE NOT NULL,
    cliente_id INTEGER REFERENCES auxiliares(id),
    razon_social VARCHAR(255),
    rut_cliente VARCHAR(20),
    direccion TEXT,
    giro VARCHAR(255),
    subtotal DECIMAL(18,2) DEFAULT 0,
    descuento DECIMAL(18,2) DEFAULT 0,
    neto DECIMAL(18,2) DEFAULT 0,
    iva DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    moneda VARCHAR(10) DEFAULT 'CLP',
    forma_pago VARCHAR(50),
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'emitida',
    dte_xml TEXT,
    dte_pdf_url VARCHAR(500),
    track_id VARCHAR(100),
    fecha_envio_sii TIMESTAMP,
    estado_sii VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    UNIQUE(empresa_id, tipo_documento, numero)
);

CREATE TABLE facturas_venta_detalle (
    id SERIAL PRIMARY KEY,
    factura_id INTEGER NOT NULL REFERENCES facturas_venta(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    producto_id INTEGER REFERENCES productos(id),
    codigo_producto VARCHAR(100),
    descripcion VARCHAR(500) NOT NULL,
    cantidad DECIMAL(18,4) NOT NULL,
    unidad VARCHAR(50),
    precio_unitario DECIMAL(18,2) NOT NULL,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    descuento_monto DECIMAL(18,2) DEFAULT 0,
    subtotal DECIMAL(18,2) NOT NULL,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) NOT NULL
);

-- Notas de crédito
CREATE TABLE notas_credito_venta (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero INTEGER NOT NULL,
    fecha DATE NOT NULL,
    factura_id INTEGER REFERENCES facturas_venta(id),
    cliente_id INTEGER REFERENCES auxiliares(id),
    motivo TEXT,
    subtotal DECIMAL(18,2) DEFAULT 0,
    iva DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'emitida',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, numero)
);

-- POS - Punto de Venta
CREATE TABLE cajas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    sucursal_id INTEGER REFERENCES sucursales(id),
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    activa BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

CREATE TABLE cajas_movimientos (
    id SERIAL PRIMARY KEY,
    caja_id INTEGER NOT NULL REFERENCES cajas(id),
    usuario_id INTEGER,
    tipo VARCHAR(50), -- apertura, venta, ingreso, egreso, cierre
    monto DECIMAL(18,2) NOT NULL,
    documento_tipo VARCHAR(50),
    documento_id INTEGER,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ventas_pos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    caja_id INTEGER REFERENCES cajas(id),
    numero VARCHAR(50),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cliente_id INTEGER REFERENCES auxiliares(id),
    subtotal DECIMAL(18,2) DEFAULT 0,
    descuento DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    medio_pago VARCHAR(50),
    estado VARCHAR(50) DEFAULT 'completada',
    created_by INTEGER
);

CREATE TABLE ventas_pos_detalle (
    id SERIAL PRIMARY KEY,
    venta_id INTEGER NOT NULL REFERENCES ventas_pos(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad DECIMAL(18,4) NOT NULL,
    precio_unitario DECIMAL(18,2) NOT NULL,
    descuento DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) NOT NULL
);

-- Listas de precios
CREATE TABLE listas_precios (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    activa BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

CREATE TABLE precios_productos (
    id SERIAL PRIMARY KEY,
    lista_id INTEGER NOT NULL REFERENCES listas_precios(id) ON DELETE CASCADE,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    precio DECIMAL(18,2) NOT NULL,
    fecha_vigencia_desde DATE,
    fecha_vigencia_hasta DATE,
    UNIQUE(lista_id, producto_id)
);

CREATE INDEX idx_facturas_empresa ON facturas_venta(empresa_id);
CREATE INDEX idx_facturas_cliente ON facturas_venta(cliente_id);
CREATE INDEX idx_facturas_fecha ON facturas_venta(fecha_emision);
CREATE INDEX idx_pedidos_empresa ON pedidos_venta(empresa_id);
