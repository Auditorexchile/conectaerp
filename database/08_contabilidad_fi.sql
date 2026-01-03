-- ================================================================
-- MÓDULO: CONTABILIDAD (FI) - Financial Accounting
-- ================================================================

-- Plan de cuentas
CREATE TABLE plan_cuentas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(50), -- activo, pasivo, patrimonio, ingreso, egreso
    nivel INTEGER DEFAULT 1,
    padre_id INTEGER REFERENCES plan_cuentas(id),
    naturaleza VARCHAR(10), -- debe, haber
    acepta_movimientos BOOLEAN DEFAULT true,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

-- Períodos contables
CREATE TABLE periodos_contables (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    anio INTEGER NOT NULL,
    mes INTEGER NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    abierto BOOLEAN DEFAULT true,
    cerrado_mensual BOOLEAN DEFAULT false,
    cerrado_anual BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, anio, mes)
);

-- Asientos contables
CREATE TABLE asientos_contables (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    numero INTEGER NOT NULL,
    fecha DATE NOT NULL,
    glosa TEXT,
    tipo VARCHAR(50), -- apertura, movimiento, ajuste, cierre
    documento_tipo VARCHAR(50),
    documento_numero VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'borrador',
    aprobado BOOLEAN DEFAULT false,
    aprobado_por INTEGER,
    aprobado_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    UNIQUE(empresa_id, numero)
);

-- Detalle de asientos
CREATE TABLE asientos_detalle (
    id SERIAL PRIMARY KEY,
    asiento_id INTEGER NOT NULL REFERENCES asientos_contables(id) ON DELETE CASCADE,
    cuenta_id INTEGER NOT NULL REFERENCES plan_cuentas(id),
    glosa VARCHAR(500),
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    centro_costo_id INTEGER REFERENCES centros_costo(id),
    auxiliar_id INTEGER REFERENCES auxiliares(id),
    documento_tipo VARCHAR(50),
    documento_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Libro diario
CREATE TABLE libro_diario (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    asiento_id INTEGER REFERENCES asientos_contables(id),
    fecha DATE NOT NULL,
    cuenta_codigo VARCHAR(50),
    cuenta_nombre VARCHAR(255),
    glosa TEXT,
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    saldo DECIMAL(18,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Libro mayor
CREATE TABLE libro_mayor (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    fecha DATE NOT NULL,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    glosa TEXT,
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    saldo DECIMAL(18,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Balance general
CREATE TABLE balance_general (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    fecha DATE NOT NULL,
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    saldo_inicial DECIMAL(18,2) DEFAULT 0,
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    saldo_final DECIMAL(18,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Estado de resultados
CREATE TABLE estado_resultados (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    monto DECIMAL(18,2) DEFAULT 0,
    tipo VARCHAR(50), -- ingreso, costo, gasto
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cuentas por cobrar
CREATE TABLE cuentas_por_cobrar (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    cliente_id INTEGER REFERENCES auxiliares(id),
    documento_tipo VARCHAR(50),
    documento_numero VARCHAR(100),
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    monto_total DECIMAL(18,2) NOT NULL,
    monto_pagado DECIMAL(18,2) DEFAULT 0,
    saldo DECIMAL(18,2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    estado VARCHAR(50) DEFAULT 'pendiente',
    dias_vencido INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cuentas por pagar
CREATE TABLE cuentas_por_pagar (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    proveedor_id INTEGER REFERENCES auxiliares(id),
    documento_tipo VARCHAR(50),
    documento_numero VARCHAR(100),
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    monto_total DECIMAL(18,2) NOT NULL,
    monto_pagado DECIMAL(18,2) DEFAULT 0,
    saldo DECIMAL(18,2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    estado VARCHAR(50) DEFAULT 'pendiente',
    dias_vencido INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Impuestos
CREATE TABLE impuestos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    porcentaje DECIMAL(5,2) NOT NULL,
    tipo VARCHAR(50), -- iva, retencion, otro
    cuenta_contable_id INTEGER REFERENCES plan_cuentas(id),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

CREATE INDEX idx_asientos_empresa ON asientos_contables(empresa_id);
CREATE INDEX idx_asientos_fecha ON asientos_contables(fecha);
CREATE INDEX idx_plan_cuentas_empresa ON plan_cuentas(empresa_id);
CREATE INDEX idx_cxc_cliente ON cuentas_por_cobrar(cliente_id);
CREATE INDEX idx_cxp_proveedor ON cuentas_por_pagar(proveedor_id);
