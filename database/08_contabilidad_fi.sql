-- ================================================================
-- MÓDULO: CONTABILIDAD (FI) - Financial Accounting COMPLETO
-- Sistema de Contabilidad nivel SAP/Softland Enterprise
-- Incluye: IFRS, Conciliación Bancaria, Flujo de Caja, Multi-moneda
-- ================================================================

-- ===== PLAN DE CUENTAS Y ESTRUCTURA =====

-- Plan de cuentas multinivel con IFRS
CREATE TABLE plan_cuentas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    nombre_ingles VARCHAR(255),
    tipo VARCHAR(50) NOT NULL, -- activo, pasivo, patrimonio, ingreso, egreso
    subtipo VARCHAR(50), -- corriente, no_corriente, operacional, no_operacional
    nivel INTEGER DEFAULT 1,
    padre_id INTEGER REFERENCES plan_cuentas(id),
    naturaleza VARCHAR(10) NOT NULL, -- debe, haber
    acepta_movimientos BOOLEAN DEFAULT true,
    requiere_auxiliar BOOLEAN DEFAULT false,
    requiere_centro_costo BOOLEAN DEFAULT false,
    requiere_proyecto BOOLEAN DEFAULT false,
    tipo_auxiliar VARCHAR(50), -- cliente, proveedor, empleado, otro
    cuenta_ifrs VARCHAR(50), -- Código IFRS equivalente
    categoria_flujo_caja VARCHAR(50), -- operacion, inversion, financiamiento
    activo BOOLEAN DEFAULT true,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    UNIQUE(empresa_id, codigo)
);

-- Equivalencias IFRS
CREATE TABLE plan_cuentas_ifrs (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    cuenta_local_id INTEGER NOT NULL REFERENCES plan_cuentas(id),
    codigo_ifrs VARCHAR(50) NOT NULL,
    nombre_ifrs VARCHAR(255) NOT NULL,
    clasificacion_ifrs VARCHAR(100),
    nivel_ifrs INTEGER,
    notas TEXT,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== PERÍODOS Y EJERCICIOS CONTABLES =====

-- Ejercicios fiscales
CREATE TABLE ejercicios_fiscales (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    anio INTEGER NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    cerrado BOOLEAN DEFAULT false,
    fecha_cierre TIMESTAMP,
    cerrado_por INTEGER,
    auditado BOOLEAN DEFAULT false,
    auditor VARCHAR(255),
    fecha_auditoria DATE,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, anio)
);

-- Períodos contables mensuales
CREATE TABLE periodos_contables (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    anio INTEGER NOT NULL,
    mes INTEGER NOT NULL,
    periodo_nombre VARCHAR(50), -- Enero 2024, Q1 2024, etc.
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    abierto BOOLEAN DEFAULT true,
    cerrado_mensual BOOLEAN DEFAULT false,
    cerrado_anual BOOLEAN DEFAULT false,
    fecha_cierre_mensual TIMESTAMP,
    fecha_cierre_anual TIMESTAMP,
    cerrado_por INTEGER,
    ajustes_permitidos BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, anio, mes)
);

-- ===== ASIENTOS CONTABLES =====

-- Tipos de asientos
CREATE TABLE asientos_tipos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    permite_edicion BOOLEAN DEFAULT true,
    requiere_aprobacion BOOLEAN DEFAULT false,
    activo BOOLEAN DEFAULT true
);

-- Asientos contables
CREATE TABLE asientos_contables (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    periodo_id INTEGER REFERENCES periodos_contables(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    numero INTEGER NOT NULL,
    correlativo_periodo INTEGER,
    fecha DATE NOT NULL,
    fecha_valor DATE, -- Fecha de valor para temas de devengo
    glosa TEXT NOT NULL,
    tipo_asiento_id INTEGER REFERENCES asientos_tipos(id),
    tipo VARCHAR(50) DEFAULT 'movimiento', -- apertura, movimiento, ajuste, cierre, provision
    origen VARCHAR(50), -- manual, automatico, importado, integracion
    documento_tipo VARCHAR(50),
    documento_numero VARCHAR(100),
    documento_fecha DATE,
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    estado VARCHAR(50) DEFAULT 'borrador', -- borrador, aprobado, contabilizado, reversado
    reversado BOOLEAN DEFAULT false,
    asiento_reversa_id INTEGER REFERENCES asientos_contables(id),
    bloqueado BOOLEAN DEFAULT false,
    aprobado BOOLEAN DEFAULT false,
    aprobado_por INTEGER,
    aprobado_at TIMESTAMP,
    contabilizado BOOLEAN DEFAULT false,
    contabilizado_por INTEGER,
    contabilizado_at TIMESTAMP,
    total_debe DECIMAL(18,2) DEFAULT 0,
    total_haber DECIMAL(18,2) DEFAULT 0,
    diferencia DECIMAL(18,2) DEFAULT 0,
    cuadrado BOOLEAN DEFAULT false,
    archivo_adjunto VARCHAR(500),
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES usuarios_acceso(id),
    updated_by INTEGER,
    UNIQUE(empresa_id, numero)
);

-- Detalle de asientos (movimientos)
CREATE TABLE asientos_detalle (
    id SERIAL PRIMARY KEY,
    asiento_id INTEGER NOT NULL REFERENCES asientos_contables(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    cuenta_id INTEGER NOT NULL REFERENCES plan_cuentas(id),
    cuenta_codigo VARCHAR(50),
    cuenta_nombre VARCHAR(255),
    glosa VARCHAR(500),
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    debe_moneda_base DECIMAL(18,2) DEFAULT 0,
    haber_moneda_base DECIMAL(18,2) DEFAULT 0,
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    centro_costo_id INTEGER,
    proyecto_id INTEGER,
    auxiliar_id INTEGER REFERENCES auxiliares(id),
    auxiliar_tipo VARCHAR(50),
    auxiliar_nombre VARCHAR(255),
    documento_tipo VARCHAR(50),
    documento_numero VARCHAR(100),
    documento_id INTEGER,
    fecha_vencimiento DATE,
    referencia VARCHAR(255),
    impuesto_id INTEGER,
    base_imponible DECIMAL(18,2),
    monto_impuesto DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== LIBROS CONTABLES =====

-- Libro diario
CREATE TABLE libro_diario (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    periodo_id INTEGER REFERENCES periodos_contables(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    asiento_id INTEGER REFERENCES asientos_contables(id),
    asiento_numero INTEGER,
    fecha DATE NOT NULL,
    fecha_valor DATE,
    correlativo INTEGER NOT NULL,
    cuenta_codigo VARCHAR(50) NOT NULL,
    cuenta_nombre VARCHAR(255) NOT NULL,
    glosa TEXT,
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    saldo DECIMAL(18,2) DEFAULT 0,
    auxiliar_nombre VARCHAR(255),
    centro_costo VARCHAR(100),
    documento VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Libro mayor (Kardex por cuenta)
CREATE TABLE libro_mayor (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    periodo_id INTEGER REFERENCES periodos_contables(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    cuenta_id INTEGER NOT NULL REFERENCES plan_cuentas(id),
    cuenta_codigo VARCHAR(50) NOT NULL,
    cuenta_nombre VARCHAR(255) NOT NULL,
    fecha DATE NOT NULL,
    fecha_valor DATE,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    asiento_numero INTEGER,
    correlativo INTEGER NOT NULL,
    glosa TEXT,
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    saldo_acumulado DECIMAL(18,2) DEFAULT 0,
    saldo_mes DECIMAL(18,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Saldos por cuenta y período (para consultas rápidas)
CREATE TABLE saldos_contables (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    cuenta_id INTEGER NOT NULL REFERENCES plan_cuentas(id),
    anio INTEGER NOT NULL,
    mes INTEGER NOT NULL,
    saldo_inicial DECIMAL(18,2) DEFAULT 0,
    debe DECIMAL(18,2) DEFAULT 0,
    haber DECIMAL(18,2) DEFAULT 0,
    saldo_final DECIMAL(18,2) DEFAULT 0,
    saldo_promedio DECIMAL(18,2) DEFAULT 0,
    movimientos_count INTEGER DEFAULT 0,
    actualizado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, cuenta_id, anio, mes)
);

-- ===== ESTADOS FINANCIEROS =====

-- Balance general (Balance de 8 columnas)
CREATE TABLE balance_general (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    periodo_id INTEGER REFERENCES periodos_contables(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    fecha_corte DATE NOT NULL,
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    cuenta_codigo VARCHAR(50),
    cuenta_nombre VARCHAR(255),
    nivel INTEGER,
    tipo VARCHAR(50), -- activo, pasivo, patrimonio
    subtipo VARCHAR(50),
    saldo_inicial_debe DECIMAL(18,2) DEFAULT 0,
    saldo_inicial_haber DECIMAL(18,2) DEFAULT 0,
    movimientos_debe DECIMAL(18,2) DEFAULT 0,
    movimientos_haber DECIMAL(18,2) DEFAULT 0,
    saldo_final_debe DECIMAL(18,2) DEFAULT 0,
    saldo_final_haber DECIMAL(18,2) DEFAULT 0,
    saldo_deudor DECIMAL(18,2) DEFAULT 0,
    saldo_acreedor DECIMAL(18,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Estado de resultados
CREATE TABLE estado_resultados (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    periodo_id INTEGER REFERENCES periodos_contables(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    fecha_desde DATE NOT NULL,
    fecha_hasta DATE NOT NULL,
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    cuenta_codigo VARCHAR(50),
    cuenta_nombre VARCHAR(255),
    nivel INTEGER,
    tipo VARCHAR(50), -- ingreso, costo, gasto
    subtipo VARCHAR(50), -- operacional, no_operacional
    monto DECIMAL(18,2) DEFAULT 0,
    porcentaje_ingresos DECIMAL(5,2),
    acumulado_anio DECIMAL(18,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Estado de flujo de efectivo
CREATE TABLE flujo_efectivo (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    fecha_desde DATE NOT NULL,
    fecha_hasta DATE NOT NULL,
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    categoria VARCHAR(50) NOT NULL, -- operacion, inversion, financiamiento
    subcategoria VARCHAR(100),
    descripcion VARCHAR(255),
    monto DECIMAL(18,2) DEFAULT 0,
    tipo_flujo VARCHAR(10), -- entrada, salida
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Estado de cambios en el patrimonio
CREATE TABLE estado_patrimonio (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    concepto VARCHAR(100), -- capital, utilidades_retenidas, reservas, etc.
    saldo_inicial DECIMAL(18,2) DEFAULT 0,
    aumentos DECIMAL(18,2) DEFAULT 0,
    disminuciones DECIMAL(18,2) DEFAULT 0,
    saldo_final DECIMAL(18,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== CUENTAS POR COBRAR Y PAGAR =====

-- Cuentas por cobrar (facturas de clientes)
CREATE TABLE cuentas_por_cobrar (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    cliente_id INTEGER REFERENCES auxiliares(id),
    cliente_nombre VARCHAR(255),
    cliente_rut VARCHAR(20),
    documento_tipo VARCHAR(50) NOT NULL, -- factura, nota_credito, nota_debito
    documento_numero VARCHAR(100) NOT NULL,
    documento_folio INTEGER,
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    fecha_pago DATE,
    monto_bruto DECIMAL(18,2) NOT NULL,
    monto_descuento DECIMAL(18,2) DEFAULT 0,
    monto_impuesto DECIMAL(18,2) DEFAULT 0,
    monto_total DECIMAL(18,2) NOT NULL,
    monto_pagado DECIMAL(18,2) DEFAULT 0,
    saldo DECIMAL(18,2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    condicion_pago VARCHAR(50), -- contado, 30_dias, 60_dias, etc.
    estado VARCHAR(50) DEFAULT 'pendiente', -- pendiente, pagado_parcial, pagado, vencido, incobrable
    dias_vencido INTEGER DEFAULT 0,
    categoria_morosidad VARCHAR(50), -- al_dia, 1-30, 31-60, 61-90, 90+
    cuenta_contable_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER,
    vendedor_id INTEGER,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pagos recibidos (abonos a CxC)
CREATE TABLE cxc_pagos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    cxc_id INTEGER NOT NULL REFERENCES cuentas_por_cobrar(id),
    numero_pago VARCHAR(100),
    fecha_pago DATE NOT NULL,
    monto_pago DECIMAL(18,2) NOT NULL,
    monto_pago_moneda_base DECIMAL(18,2),
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    forma_pago VARCHAR(50), -- efectivo, transferencia, cheque, tarjeta
    numero_referencia VARCHAR(100), -- número de transferencia, cheque, etc.
    banco VARCHAR(100),
    cuenta_bancaria_id INTEGER,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Cuentas por pagar (facturas de proveedores)
CREATE TABLE cuentas_por_pagar (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    proveedor_id INTEGER REFERENCES auxiliares(id),
    proveedor_nombre VARCHAR(255),
    proveedor_rut VARCHAR(20),
    documento_tipo VARCHAR(50) NOT NULL,
    documento_numero VARCHAR(100) NOT NULL,
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    fecha_pago DATE,
    monto_bruto DECIMAL(18,2) NOT NULL,
    monto_descuento DECIMAL(18,2) DEFAULT 0,
    monto_impuesto DECIMAL(18,2) DEFAULT 0,
    monto_total DECIMAL(18,2) NOT NULL,
    monto_pagado DECIMAL(18,2) DEFAULT 0,
    saldo DECIMAL(18,2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    condicion_pago VARCHAR(50),
    estado VARCHAR(50) DEFAULT 'pendiente',
    dias_vencido INTEGER DEFAULT 0,
    categoria_vencimiento VARCHAR(50),
    cuenta_contable_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER,
    orden_compra VARCHAR(100),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pagos realizados (abonos a CxP)
CREATE TABLE cxp_pagos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    cxp_id INTEGER NOT NULL REFERENCES cuentas_por_pagar(id),
    numero_pago VARCHAR(100),
    fecha_pago DATE NOT NULL,
    monto_pago DECIMAL(18,2) NOT NULL,
    monto_pago_moneda_base DECIMAL(18,2),
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    forma_pago VARCHAR(50),
    numero_referencia VARCHAR(100),
    banco VARCHAR(100),
    cuenta_bancaria_id INTEGER,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    retencion DECIMAL(18,2) DEFAULT 0,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- ===== CONCILIACIÓN BANCARIA =====

-- Cuentas bancarias de la empresa
CREATE TABLE cuentas_bancarias_contabilidad (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    banco VARCHAR(100) NOT NULL,
    numero_cuenta VARCHAR(100) NOT NULL,
    tipo_cuenta VARCHAR(50), -- corriente, vista, ahorro
    moneda VARCHAR(10) DEFAULT 'CLP',
    cuenta_contable_id INTEGER REFERENCES plan_cuentas(id),
    saldo_actual DECIMAL(18,2) DEFAULT 0,
    saldo_conciliado DECIMAL(18,2) DEFAULT 0,
    fecha_ultima_conciliacion DATE,
    activo BOOLEAN DEFAULT true,
    titular VARCHAR(255),
    ejecutivo_cuenta VARCHAR(100),
    telefono_banco VARCHAR(50),
    email_banco VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, banco, numero_cuenta)
);

-- Movimientos bancarios
CREATE TABLE movimientos_bancarios (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    cuenta_bancaria_id INTEGER NOT NULL REFERENCES cuentas_bancarias_contabilidad(id),
    fecha DATE NOT NULL,
    fecha_valor DATE,
    numero_operacion VARCHAR(100),
    tipo_movimiento VARCHAR(50), -- deposito, cheque, transferencia_entrada, transferencia_salida, etc.
    descripcion TEXT,
    referencia VARCHAR(255),
    debe DECIMAL(18,2) DEFAULT 0, -- depósitos/entradas
    haber DECIMAL(18,2) DEFAULT 0, -- retiros/salidas
    saldo DECIMAL(18,2),
    conciliado BOOLEAN DEFAULT false,
    fecha_conciliacion DATE,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    importado BOOLEAN DEFAULT false,
    archivo_importacion VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Conciliaciones bancarias
CREATE TABLE conciliaciones_bancarias (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    cuenta_bancaria_id INTEGER NOT NULL REFERENCES cuentas_bancarias_contabilidad(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    fecha_conciliacion DATE NOT NULL,
    fecha_corte DATE NOT NULL,
    saldo_libro DECIMAL(18,2) NOT NULL, -- según contabilidad
    saldo_banco DECIMAL(18,2) NOT NULL, -- según cartola
    depositos_transito DECIMAL(18,2) DEFAULT 0,
    cheques_transito DECIMAL(18,2) DEFAULT 0,
    ajustes_banco DECIMAL(18,2) DEFAULT 0,
    ajustes_libro DECIMAL(18,2) DEFAULT 0,
    diferencia DECIMAL(18,2) DEFAULT 0,
    conciliado BOOLEAN DEFAULT false,
    observaciones TEXT,
    archivo_cartola VARCHAR(500),
    realizado_por INTEGER REFERENCES usuarios_acceso(id),
    aprobado_por INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Partidas en conciliación
CREATE TABLE partidas_conciliacion (
    id SERIAL PRIMARY KEY,
    conciliacion_id INTEGER NOT NULL REFERENCES conciliaciones_bancarias(id),
    tipo VARCHAR(50) NOT NULL, -- deposito_transito, cheque_transito, error_banco, error_libro
    fecha DATE NOT NULL,
    descripcion TEXT,
    monto DECIMAL(18,2) NOT NULL,
    movimiento_bancario_id INTEGER REFERENCES movimientos_bancarios(id),
    asiento_id INTEGER REFERENCES asientos_contables(id),
    resuelto BOOLEAN DEFAULT false,
    fecha_resolucion DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== TESORERÍA Y FLUJO DE CAJA =====

-- Proyección de flujo de caja
CREATE TABLE flujo_caja_proyectado (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    fecha DATE NOT NULL,
    semana INTEGER,
    mes INTEGER,
    anio INTEGER,
    concepto VARCHAR(255),
    categoria VARCHAR(50), -- ingreso, egreso
    subcategoria VARCHAR(100),
    monto_proyectado DECIMAL(18,2) NOT NULL,
    monto_real DECIMAL(18,2) DEFAULT 0,
    variacion DECIMAL(18,2),
    cuenta_bancaria_id INTEGER,
    origen VARCHAR(50), -- manual, automatico, cxc, cxp
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Caja chica
CREATE TABLE cajas_chicas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    responsable_id INTEGER REFERENCES usuarios_acceso(id),
    monto_fondo DECIMAL(18,2) NOT NULL,
    saldo_actual DECIMAL(18,2) DEFAULT 0,
    cuenta_contable_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Movimientos caja chica
CREATE TABLE cajas_chicas_movimientos (
    id SERIAL PRIMARY KEY,
    caja_chica_id INTEGER NOT NULL REFERENCES cajas_chicas(id),
    tipo VARCHAR(50) NOT NULL, -- gasto, reposicion
    fecha DATE NOT NULL,
    numero_comprobante VARCHAR(100),
    concepto TEXT NOT NULL,
    monto DECIMAL(18,2) NOT NULL,
    cuenta_contable_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER,
    documento_respaldo VARCHAR(500),
    aprobado_por INTEGER,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- ===== IMPUESTOS =====

-- Maestro de impuestos
CREATE TABLE impuestos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50) NOT NULL, -- iva, retencion, impuesto_especifico, otro
    subtipo VARCHAR(50), -- ventas, compras, renta, etc.
    porcentaje DECIMAL(5,2),
    monto_fijo DECIMAL(18,2),
    base_calculo VARCHAR(50), -- sobre_neto, sobre_total
    cuenta_debito_id INTEGER REFERENCES plan_cuentas(id),
    cuenta_credito_id INTEGER REFERENCES plan_cuentas(id),
    es_retencion BOOLEAN DEFAULT false,
    aplica_ventas BOOLEAN DEFAULT true,
    aplica_compras BOOLEAN DEFAULT true,
    codigo_sii VARCHAR(50), -- Código específico SII Chile
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

-- Detalle de impuestos por documento
CREATE TABLE documentos_impuestos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    documento_tipo VARCHAR(50) NOT NULL, -- factura_venta, factura_compra, etc.
    documento_id INTEGER NOT NULL,
    impuesto_id INTEGER NOT NULL REFERENCES impuestos(id),
    base_imponible DECIMAL(18,2) NOT NULL,
    tasa DECIMAL(5,2),
    monto_impuesto DECIMAL(18,2) NOT NULL,
    es_retencion BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Libro de ventas (IVA ventas)
CREATE TABLE libro_ventas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    fecha DATE NOT NULL,
    tipo_documento VARCHAR(50),
    numero_documento VARCHAR(100),
    cliente_rut VARCHAR(20),
    cliente_nombre VARCHAR(255),
    monto_neto DECIMAL(18,2) DEFAULT 0,
    monto_exento DECIMAL(18,2) DEFAULT 0,
    monto_iva DECIMAL(18,2) DEFAULT 0,
    monto_total DECIMAL(18,2) DEFAULT 0,
    anulado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Libro de compras (IVA compras)
CREATE TABLE libro_compras (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    fecha DATE NOT NULL,
    tipo_documento VARCHAR(50),
    numero_documento VARCHAR(100),
    proveedor_rut VARCHAR(20),
    proveedor_nombre VARCHAR(255),
    monto_neto DECIMAL(18,2) DEFAULT 0,
    monto_exento DECIMAL(18,2) DEFAULT 0,
    monto_iva DECIMAL(18,2) DEFAULT 0,
    monto_iva_no_recuperable DECIMAL(18,2) DEFAULT 0,
    monto_total DECIMAL(18,2) DEFAULT 0,
    anulado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== CONVERSIÓN DE MONEDA =====

-- Tipos de cambio históricos
CREATE TABLE tipos_cambio (
    id SERIAL PRIMARY KEY,
    fecha DATE NOT NULL,
    moneda_origen VARCHAR(10) NOT NULL,
    moneda_destino VARCHAR(10) NOT NULL,
    tasa_compra DECIMAL(18,6) NOT NULL,
    tasa_venta DECIMAL(18,6) NOT NULL,
    tasa_promedio DECIMAL(18,6),
    fuente VARCHAR(100), -- banco_central, manual, api
    oficial BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(fecha, moneda_origen, moneda_destino)
);

-- Diferencias de cambio
CREATE TABLE diferencias_cambio (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    fecha DATE NOT NULL,
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    documento_tipo VARCHAR(50),
    documento_id INTEGER,
    moneda VARCHAR(10),
    monto_original DECIMAL(18,2),
    tipo_cambio_original DECIMAL(18,6),
    tipo_cambio_actual DECIMAL(18,6),
    diferencia DECIMAL(18,2),
    tipo_diferencia VARCHAR(50), -- realizada, no_realizada
    asiento_id INTEGER REFERENCES asientos_contables(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ACTIVOS FIJOS =====

-- Activos fijos
CREATE TABLE activos_fijos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(100), -- edificios, vehiculos, maquinaria, equipos, etc.
    subcategoria VARCHAR(100),
    fecha_compra DATE NOT NULL,
    fecha_puesta_servicio DATE,
    proveedor_id INTEGER REFERENCES auxiliares(id),
    valor_compra DECIMAL(18,2) NOT NULL,
    valor_residual DECIMAL(18,2) DEFAULT 0,
    vida_util_anios INTEGER NOT NULL,
    vida_util_meses INTEGER,
    metodo_depreciacion VARCHAR(50) DEFAULT 'lineal', -- lineal, acelerada, unidades_produccion
    depreciacion_acumulada DECIMAL(18,2) DEFAULT 0,
    valor_libro DECIMAL(18,2),
    cuenta_activo_id INTEGER REFERENCES plan_cuentas(id),
    cuenta_depreciacion_id INTEGER REFERENCES plan_cuentas(id),
    cuenta_depreciacion_acum_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER,
    ubicacion VARCHAR(255),
    responsable_id INTEGER,
    numero_serie VARCHAR(100),
    marca VARCHAR(100),
    modelo VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'activo', -- activo, en_reparacion, dado_de_baja
    fecha_baja DATE,
    motivo_baja TEXT,
    imagen VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Depreciaciones
CREATE TABLE depreciaciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    activo_fijo_id INTEGER NOT NULL REFERENCES activos_fijos(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    anio INTEGER NOT NULL,
    mes INTEGER NOT NULL,
    fecha DATE NOT NULL,
    monto_depreciacion DECIMAL(18,2) NOT NULL,
    depreciacion_acumulada DECIMAL(18,2),
    valor_libro DECIMAL(18,2),
    asiento_id INTEGER REFERENCES asientos_contables(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== PRESUPUESTOS =====

-- Presupuesto anual
CREATE TABLE presupuestos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    nombre VARCHAR(255) NOT NULL,
    anio INTEGER NOT NULL,
    version INTEGER DEFAULT 1,
    estado VARCHAR(50) DEFAULT 'borrador', -- borrador, aprobado, vigente, cerrado
    fecha_aprobacion DATE,
    aprobado_por INTEGER,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Detalle presupuesto por cuenta
CREATE TABLE presupuestos_detalle (
    id SERIAL PRIMARY KEY,
    presupuesto_id INTEGER NOT NULL REFERENCES presupuestos(id) ON DELETE CASCADE,
    cuenta_id INTEGER NOT NULL REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER,
    enero DECIMAL(18,2) DEFAULT 0,
    febrero DECIMAL(18,2) DEFAULT 0,
    marzo DECIMAL(18,2) DEFAULT 0,
    abril DECIMAL(18,2) DEFAULT 0,
    mayo DECIMAL(18,2) DEFAULT 0,
    junio DECIMAL(18,2) DEFAULT 0,
    julio DECIMAL(18,2) DEFAULT 0,
    agosto DECIMAL(18,2) DEFAULT 0,
    septiembre DECIMAL(18,2) DEFAULT 0,
    octubre DECIMAL(18,2) DEFAULT 0,
    noviembre DECIMAL(18,2) DEFAULT 0,
    diciembre DECIMAL(18,2) DEFAULT 0,
    total_anual DECIMAL(18,2) DEFAULT 0,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Análisis presupuesto vs real
CREATE TABLE analisis_presupuesto (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    presupuesto_id INTEGER REFERENCES presupuestos(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER,
    mes INTEGER,
    anio INTEGER,
    monto_presupuestado DECIMAL(18,2) DEFAULT 0,
    monto_real DECIMAL(18,2) DEFAULT 0,
    variacion DECIMAL(18,2) DEFAULT 0,
    variacion_porcentaje DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ÍNDICES PARA PERFORMANCE =====

CREATE INDEX idx_asientos_empresa_periodo ON asientos_contables(empresa_id, periodo_id);
CREATE INDEX idx_asientos_fecha ON asientos_contables(fecha);
CREATE INDEX idx_asientos_estado ON asientos_contables(estado);
CREATE INDEX idx_asientos_detalle_cuenta ON asientos_detalle(cuenta_id);
CREATE INDEX idx_asientos_detalle_auxiliar ON asientos_detalle(auxiliar_id);
CREATE INDEX idx_plan_cuentas_empresa ON plan_cuentas(empresa_id);
CREATE INDEX idx_plan_cuentas_padre ON plan_cuentas(padre_id);
CREATE INDEX idx_libro_mayor_cuenta ON libro_mayor(cuenta_id);
CREATE INDEX idx_libro_mayor_fecha ON libro_mayor(fecha);
CREATE INDEX idx_cxc_cliente ON cuentas_por_cobrar(cliente_id);
CREATE INDEX idx_cxc_estado ON cuentas_por_cobrar(estado);
CREATE INDEX idx_cxc_vencimiento ON cuentas_por_cobrar(fecha_vencimiento);
CREATE INDEX idx_cxp_proveedor ON cuentas_por_pagar(proveedor_id);
CREATE INDEX idx_cxp_estado ON cuentas_por_pagar(estado);
CREATE INDEX idx_movimientos_bancarios_cuenta ON movimientos_bancarios(cuenta_bancaria_id);
CREATE INDEX idx_movimientos_bancarios_conciliado ON movimientos_bancarios(conciliado);
CREATE INDEX idx_saldos_contables_lookup ON saldos_contables(empresa_id, cuenta_id, anio, mes);
CREATE INDEX idx_tipos_cambio_fecha ON tipos_cambio(fecha);
CREATE INDEX idx_tipos_cambio_moneda ON tipos_cambio(moneda_origen, moneda_destino);
