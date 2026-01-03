-- ================================================================
-- MÓDULOS: PRODUCTOS, INVENTARIO, VENTAS, COMPRAS
-- ================================================================

-- Productos
CREATE TABLE productos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(100) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50) DEFAULT 'producto',
    categoria_id INTEGER,
    unidad_medida VARCHAR(50),
    precio_venta DECIMAL(18,2) DEFAULT 0,
    precio_compra DECIMAL(18,2) DEFAULT 0,
    costo_promedio DECIMAL(18,2) DEFAULT 0,
    stock_actual DECIMAL(18,4) DEFAULT 0,
    stock_minimo DECIMAL(18,4) DEFAULT 0,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

-- Movimientos de inventario
CREATE TABLE movimientos_inventario (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    tipo_movimiento VARCHAR(50) NOT NULL,
    cantidad DECIMAL(18,4) NOT NULL,
    costo_unitario DECIMAL(18,2),
    documento_tipo VARCHAR(50),
    documento_id INTEGER,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ventas
CREATE TABLE ventas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50),
    fecha DATE NOT NULL,
    cliente_id INTEGER REFERENCES auxiliares(id),
    subtotal DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'borrador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

CREATE TABLE ventas_detalle (
    id SERIAL PRIMARY KEY,
    venta_id INTEGER NOT NULL REFERENCES ventas(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad DECIMAL(18,4) NOT NULL,
    precio_unitario DECIMAL(18,2) NOT NULL,
    subtotal DECIMAL(18,2) NOT NULL,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) NOT NULL
);

-- Compras
CREATE TABLE compras (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50),
    fecha DATE NOT NULL,
    proveedor_id INTEGER REFERENCES auxiliares(id),
    subtotal DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'borrador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

CREATE TABLE compras_detalle (
    id SERIAL PRIMARY KEY,
    compra_id INTEGER NOT NULL REFERENCES compras(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad DECIMAL(18,4) NOT NULL,
    precio_unitario DECIMAL(18,2) NOT NULL,
    subtotal DECIMAL(18,2) NOT NULL,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) NOT NULL
);

-- ================================================================
-- AUDITORÍA
-- ================================================================

CREATE TABLE auditoria_eventos (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios_acceso(id) ON DELETE SET NULL,
    empresa_id INTEGER REFERENCES empresas(id) ON DELETE CASCADE,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ip_address VARCHAR(50),
    user_agent TEXT,
    metadata JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE auditoria_cambios (
    id SERIAL PRIMARY KEY,
    evento_id INTEGER REFERENCES auditoria_eventos(id),
    tabla VARCHAR(100),
    registro_id INTEGER,
    campo VARCHAR(100),
    valor_anterior TEXT,
    valor_nuevo TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- ÍNDICES
-- ================================================================

CREATE INDEX idx_usuarios_email ON usuarios_acceso(email);
CREATE INDEX idx_usuarios_empresa ON usuarios_acceso(empresa_id);
CREATE INDEX idx_empresas_rut ON empresas(rut);
CREATE INDEX idx_productos_empresa ON productos(empresa_id);
CREATE INDEX idx_productos_codigo ON productos(codigo);
CREATE INDEX idx_ventas_empresa ON ventas(empresa_id);
CREATE INDEX idx_compras_empresa ON compras(empresa_id);
CREATE INDEX idx_auditoria_usuario ON auditoria_eventos(usuario_id);
CREATE INDEX idx_auditoria_fecha ON auditoria_eventos(created_at);

-- ================================================================
-- DATOS INICIALES
-- ================================================================

-- Insertar planes
INSERT INTO planes (codigo, nombre, descripcion, precio, max_usuarios, max_empresas, max_sucursales, orden) VALUES
('starter', 'Starter', 'Plan gratuito para comenzar', 0, 1, 1, 1, 1),
('profesional', 'Profesional', 'Para pequeñas empresas', 49990, 5, 1, 1, 2),
('empresa', 'Empresa', 'Para empresas en crecimiento', 99990, NULL, 1, 5, 3),
('corporativo', 'Corporativo', 'Solución enterprise', NULL, NULL, NULL, NULL, 4);

-- Insertar idiomas
INSERT INTO idiomas (codigo, nombre, nombre_nativo) VALUES
('es', 'Español', 'Español'),
('en', 'English', 'English'),
('pt', 'Português', 'Português'),
('fr', 'Français', 'Français'),
('de', 'Deutsch', 'Deutsch'),
('it', 'Italiano', 'Italiano'),
('zh', 'Chinese', '中文'),
('ja', 'Japanese', '日本語'),
('ko', 'Korean', '한국어'),
('hi', 'Hindi', 'हिन्दी');

-- Insertar monedas principales
INSERT INTO monedas (codigo, nombre, simbolo, decimales) VALUES
('CLP', 'Peso Chileno', '$', 0),
('USD', 'Dólar', 'USD', 2),
('EUR', 'Euro', '€', 2),
('UF', 'Unidad de Fomento', 'UF', 2),
('BRL', 'Real Brasileño', 'R$', 2),
('ARS', 'Peso Argentino', 'ARS', 2);
