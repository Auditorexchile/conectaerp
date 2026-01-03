-- ================================================================
-- MÓDULO: VENTAS Y DISTRIBUCIÓN (SD) - Sales & Distribution COMPLETO
-- Sistema de Ventas nivel SAP/Softland Enterprise
-- Incluye: DTE Chile, POS, Cajas, Cotizaciones, Comisiones, Delivery
-- ================================================================

-- ===== CONFIGURACIÓN DE VENTAS =====

-- Configuración general de ventas
CREATE TABLE ventas_configuracion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) UNIQUE,
    usa_cotizaciones BOOLEAN DEFAULT true,
    usa_pedidos BOOLEAN DEFAULT true,
    usa_guias_despacho BOOLEAN DEFAULT true,
    requiere_stock BOOLEAN DEFAULT true,
    permite_venta_negativo BOOLEAN DEFAULT false,
    usa_listas_precios BOOLEAN DEFAULT true,
    usa_descuentos BOOLEAN DEFAULT true,
    descuento_maximo_porcentaje DECIMAL(5,2) DEFAULT 20,
    usa_comisiones BOOLEAN DEFAULT true,
    tipo_calculo_comision VARCHAR(50) DEFAULT 'porcentaje_venta',
    dias_validez_cotizacion INTEGER DEFAULT 30,
    requiere_aprobacion_descuentos BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== LISTAS DE PRECIOS =====

-- Listas de precios
CREATE TABLE listas_precios (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50), -- estandar, promocional, especial, mayorista, minorista
    moneda VARCHAR(10) DEFAULT 'CLP',
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT true,
    es_default BOOLEAN DEFAULT false,
    incluye_impuesto BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Precios por producto y lista
CREATE TABLE productos_precios (
    id SERIAL PRIMARY KEY,
    lista_precios_id INTEGER NOT NULL REFERENCES listas_precios(id) ON DELETE CASCADE,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    precio DECIMAL(18,2) NOT NULL,
    precio_minimo DECIMAL(18,2),
    costo_referencia DECIMAL(18,2),
    margen_porcentaje DECIMAL(5,2),
    unidad_medida VARCHAR(50),
    cantidad_minima DECIMAL(18,2) DEFAULT 1,
    fecha_inicio DATE,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(lista_precios_id, producto_id)
);

-- Descuentos por volumen
CREATE TABLE descuentos_volumen (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    lista_precios_id INTEGER REFERENCES listas_precios(id),
    producto_id INTEGER REFERENCES productos(id),
    nombre VARCHAR(255) NOT NULL,
    cantidad_desde DECIMAL(18,2) NOT NULL,
    cantidad_hasta DECIMAL(18,2),
    tipo_descuento VARCHAR(20), -- porcentaje, monto_fijo
    descuento DECIMAL(18,2) NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Promociones y ofertas
CREATE TABLE promociones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50), -- descuento, combo, 2x1, regalo
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo BOOLEAN DEFAULT true,
    aplica_dias_semana VARCHAR(50), -- lunes,martes... o all
    hora_inicio TIME,
    hora_fin TIME,
    cantidad_maxima INTEGER,
    veces_por_cliente INTEGER,
    requiere_cupon BOOLEAN DEFAULT false,
    cupon_codigo VARCHAR(100),
    prioridad INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Detalle de promociones (productos incluidos)
CREATE TABLE promociones_productos (
    id SERIAL PRIMARY KEY,
    promocion_id INTEGER NOT NULL REFERENCES promociones(id) ON DELETE CASCADE,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    tipo_aplicacion VARCHAR(50), -- descuento, precio_fijo, gratis
    valor DECIMAL(18,2),
    cantidad_minima DECIMAL(18,2) DEFAULT 1,
    cantidad_regalo DECIMAL(18,2),
    producto_regalo_id INTEGER REFERENCES productos(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== COTIZACIONES =====

-- Cotizaciones
CREATE TABLE cotizaciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha DATE NOT NULL,
    fecha_vencimiento DATE,
    cliente_id INTEGER REFERENCES auxiliares(id),
    cliente_nombre VARCHAR(255),
    cliente_rut VARCHAR(20),
    cliente_direccion TEXT,
    cliente_email VARCHAR(100),
    cliente_telefono VARCHAR(50),
    contacto_nombre VARCHAR(255),
    vendedor_id INTEGER REFERENCES usuarios_acceso(id),
    vendedor_nombre VARCHAR(255),
    lista_precios_id INTEGER REFERENCES listas_precios(id),
    condicion_pago VARCHAR(100),
    dias_pago INTEGER DEFAULT 0,
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    subtotal DECIMAL(18,2) DEFAULT 0,
    descuento_global DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    observaciones TEXT,
    notas_internas TEXT,
    estado VARCHAR(50) DEFAULT 'borrador', -- borrador, enviada, aprobada, rechazada, vencida, convertida
    probabilidad_cierre DECIMAL(5,2),
    motivo_rechazo TEXT,
    fecha_aprobacion DATE,
    fecha_conversion DATE,
    pedido_id INTEGER, -- Si se convirtió en pedido
    archivo_adjunto VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle de cotizaciones
CREATE TABLE cotizaciones_detalle (
    id SERIAL PRIMARY KEY,
    cotizacion_id INTEGER NOT NULL REFERENCES cotizaciones(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    producto_id INTEGER REFERENCES productos(id),
    codigo_producto VARCHAR(50),
    nombre_producto VARCHAR(255),
    descripcion TEXT,
    cantidad DECIMAL(18,2) NOT NULL,
    unidad_medida VARCHAR(50),
    precio_unitario DECIMAL(18,2) NOT NULL,
    precio_lista DECIMAL(18,2),
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    descuento_monto DECIMAL(18,2) DEFAULT 0,
    subtotal DECIMAL(18,2),
    impuesto_porcentaje DECIMAL(5,2),
    impuesto_monto DECIMAL(18,2),
    total DECIMAL(18,2),
    costo_unitario DECIMAL(18,2),
    margen DECIMAL(18,2),
    margen_porcentaje DECIMAL(5,2),
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== PEDIDOS DE VENTA =====

-- Pedidos de venta (Sales Orders)
CREATE TABLE pedidos_venta (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha DATE NOT NULL,
    fecha_entrega DATE,
    fecha_entrega_real DATE,
    cotizacion_id INTEGER REFERENCES cotizaciones(id),
    cliente_id INTEGER NOT NULL REFERENCES auxiliares(id),
    cliente_nombre VARCHAR(255),
    cliente_rut VARCHAR(20),
    cliente_direccion TEXT,
    cliente_email VARCHAR(100),
    cliente_telefono VARCHAR(50),
    direccion_entrega TEXT,
    comuna_entrega VARCHAR(100),
    ciudad_entrega VARCHAR(100),
    region_entrega VARCHAR(100),
    contacto_entrega VARCHAR(255),
    telefono_entrega VARCHAR(50),
    vendedor_id INTEGER REFERENCES usuarios_acceso(id),
    vendedor_nombre VARCHAR(255),
    lista_precios_id INTEGER REFERENCES listas_precios(id),
    condicion_pago VARCHAR(100),
    dias_pago INTEGER DEFAULT 0,
    metodo_pago VARCHAR(50), -- contado, credito, tarjeta, transferencia
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    subtotal DECIMAL(18,2) DEFAULT 0,
    descuento_global DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    observaciones TEXT,
    notas_internas TEXT,
    instrucciones_despacho TEXT,
    estado VARCHAR(50) DEFAULT 'pendiente', -- pendiente, aprobado, preparacion, despachado, entregado, facturado, cancelado
    prioridad VARCHAR(20) DEFAULT 'normal', -- alta, normal, baja
    origen VARCHAR(50) DEFAULT 'manual', -- manual, web, pos, app, api
    requiere_aprobacion BOOLEAN DEFAULT false,
    aprobado BOOLEAN DEFAULT false,
    aprobado_por INTEGER,
    aprobado_at TIMESTAMP,
    fecha_facturacion DATE,
    factura_id INTEGER,
    centro_costo_id INTEGER,
    proyecto_id INTEGER,
    archivo_adjunto VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle de pedidos
CREATE TABLE pedidos_venta_detalle (
    id SERIAL PRIMARY KEY,
    pedido_id INTEGER NOT NULL REFERENCES pedidos_venta(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    producto_id INTEGER REFERENCES productos(id),
    codigo_producto VARCHAR(50),
    nombre_producto VARCHAR(255),
    descripcion TEXT,
    cantidad DECIMAL(18,2) NOT NULL,
    cantidad_despachada DECIMAL(18,2) DEFAULT 0,
    cantidad_facturada DECIMAL(18,2) DEFAULT 0,
    cantidad_pendiente DECIMAL(18,2),
    unidad_medida VARCHAR(50),
    precio_unitario DECIMAL(18,2) NOT NULL,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    descuento_monto DECIMAL(18,2) DEFAULT 0,
    subtotal DECIMAL(18,2),
    impuesto_porcentaje DECIMAL(5,2),
    impuesto_monto DECIMAL(18,2),
    total DECIMAL(18,2),
    costo_unitario DECIMAL(18,2),
    almacen_id INTEGER,
    lote VARCHAR(100),
    serie VARCHAR(100),
    estado_linea VARCHAR(50) DEFAULT 'pendiente', -- pendiente, despachado, facturado, cancelado
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== GUÍAS DE DESPACHO =====

-- Guías de despacho (Delivery Notes)
CREATE TABLE guias_despacho (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    folio INTEGER UNIQUE,
    tipo_dte VARCHAR(10) DEFAULT '52', -- 52 = Guía de Despacho en Chile
    fecha DATE NOT NULL,
    pedido_id INTEGER REFERENCES pedidos_venta(id),
    cliente_id INTEGER NOT NULL REFERENCES auxiliares(id),
    cliente_nombre VARCHAR(255),
    cliente_rut VARCHAR(20),
    cliente_direccion TEXT,
    direccion_despacho TEXT,
    comuna VARCHAR(100),
    ciudad VARCHAR(100),
    region VARCHAR(100),
    contacto_recepcion VARCHAR(255),
    telefono_recepcion VARCHAR(50),
    tipo_traslado VARCHAR(50), -- venta, traslado_interno, devolucion, otros
    patente_vehiculo VARCHAR(20),
    conductor_nombre VARCHAR(255),
    conductor_rut VARCHAR(20),
    bultos INTEGER,
    peso_kg DECIMAL(10,2),
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'borrador', -- borrador, emitida, enviada_sii, aceptada_sii, rechazada_sii, anulada
    dte_xml TEXT, -- XML del DTE
    dte_pdf VARCHAR(500), -- Path del PDF
    timbre TEXT, -- Timbre electrónico SII
    fecha_envio_sii TIMESTAMP,
    track_id VARCHAR(100), -- Track ID del SII
    fecha_recepcion DATE,
    recibido_por VARCHAR(255),
    facturado BOOLEAN DEFAULT false,
    factura_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle de guías de despacho
CREATE TABLE guias_despacho_detalle (
    id SERIAL PRIMARY KEY,
    guia_id INTEGER NOT NULL REFERENCES guias_despacho(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    pedido_detalle_id INTEGER REFERENCES pedidos_venta_detalle(id),
    producto_id INTEGER REFERENCES productos(id),
    codigo_producto VARCHAR(50),
    nombre_producto VARCHAR(255),
    descripcion TEXT,
    cantidad DECIMAL(18,2) NOT NULL,
    unidad_medida VARCHAR(50),
    precio_unitario DECIMAL(18,2),
    total DECIMAL(18,2),
    almacen_id INTEGER,
    lote VARCHAR(100),
    serie VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== FACTURAS DE VENTA (DTE CHILE) =====

-- Tipos de documentos tributarios electrónicos
CREATE TABLE dte_tipos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(10) UNIQUE NOT NULL, -- 33, 34, 39, 41, 43, 46, 52, 56, 61
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    es_tributario BOOLEAN DEFAULT true,
    afecta_stock BOOLEAN DEFAULT false,
    tipo_operacion VARCHAR(50), -- venta, compra, ajuste
    activo BOOLEAN DEFAULT true
);

-- Facturas de venta (DTE)
CREATE TABLE facturas_venta (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    folio INTEGER UNIQUE NOT NULL,
    tipo_dte_id INTEGER REFERENCES dte_tipos(id),
    tipo_dte_codigo VARCHAR(10) NOT NULL, -- 33=Factura Electrónica, 34=Factura Exenta, etc.
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE,
    pedido_id INTEGER REFERENCES pedidos_venta(id),
    guia_id INTEGER REFERENCES guias_despacho(id),
    cliente_id INTEGER NOT NULL REFERENCES auxiliares(id),
    cliente_nombre VARCHAR(255) NOT NULL,
    cliente_rut VARCHAR(20) NOT NULL,
    cliente_giro VARCHAR(255),
    cliente_direccion TEXT,
    cliente_comuna VARCHAR(100),
    cliente_ciudad VARCHAR(100),
    cliente_email VARCHAR(100),
    cliente_telefono VARCHAR(50),
    vendedor_id INTEGER REFERENCES usuarios_acceso(id),
    vendedor_nombre VARCHAR(255),
    condicion_pago VARCHAR(100),
    forma_pago VARCHAR(50), -- contado, credito
    medio_pago VARCHAR(50), -- efectivo, transferencia, cheque, tarjeta
    moneda VARCHAR(10) DEFAULT 'CLP',
    tipo_cambio DECIMAL(18,6) DEFAULT 1,
    monto_neto DECIMAL(18,2) DEFAULT 0,
    monto_exento DECIMAL(18,2) DEFAULT 0,
    monto_iva DECIMAL(18,2) DEFAULT 0,
    monto_total DECIMAL(18,2) DEFAULT 0,
    descuento_global DECIMAL(18,2) DEFAULT 0,
    observaciones TEXT,
    referencias TEXT, -- Referencias a otros documentos
    estado VARCHAR(50) DEFAULT 'borrador', -- borrador, emitida, enviada_sii, aceptada_sii, rechazada_sii, anulada, pagada
    dte_xml TEXT, -- XML del DTE firmado
    dte_pdf VARCHAR(500), -- Path del PDF
    ted TEXT, -- Timbre Electrónico SII
    caf_id INTEGER, -- ID del CAF (Código de Autorización de Folios) usado
    fecha_envio_sii TIMESTAMP,
    fecha_acuse_sii TIMESTAMP,
    track_id VARCHAR(100), -- Track ID del SII
    estado_sii VARCHAR(50), -- DOK=OK, DNK=Con Reparos, FAU=Fallo Autenticación, etc.
    glosa_sii TEXT,
    fecha_pago DATE,
    monto_pagado DECIMAL(18,2) DEFAULT 0,
    saldo DECIMAL(18,2),
    contabilizado BOOLEAN DEFAULT false,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    centro_costo_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle de facturas
CREATE TABLE facturas_venta_detalle (
    id SERIAL PRIMARY KEY,
    factura_id INTEGER NOT NULL REFERENCES facturas_venta(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    pedido_detalle_id INTEGER REFERENCES pedidos_venta_detalle(id),
    producto_id INTEGER REFERENCES productos(id),
    codigo_producto VARCHAR(50),
    nombre_producto VARCHAR(255),
    descripcion TEXT,
    cantidad DECIMAL(18,2) NOT NULL,
    unidad_medida VARCHAR(50),
    precio_unitario DECIMAL(18,2) NOT NULL,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    descuento_monto DECIMAL(18,2) DEFAULT 0,
    subtotal DECIMAL(18,2),
    es_exento BOOLEAN DEFAULT false,
    impuesto_porcentaje DECIMAL(5,2),
    impuesto_monto DECIMAL(18,2),
    total DECIMAL(18,2),
    cuenta_contable_id INTEGER,
    centro_costo_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CAF - Código de Autorización de Folios (SII Chile)
CREATE TABLE dte_caf (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    tipo_dte_codigo VARCHAR(10) NOT NULL,
    folio_desde INTEGER NOT NULL,
    folio_hasta INTEGER NOT NULL,
    folio_actual INTEGER,
    fecha_autorizacion DATE NOT NULL,
    fecha_vencimiento DATE,
    xml_caf TEXT NOT NULL, -- XML del CAF
    activo BOOLEAN DEFAULT true,
    agotado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, tipo_dte_codigo, folio_desde)
);

-- Libro de ventas electrónico
CREATE TABLE libro_ventas_electronico (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    anio INTEGER NOT NULL,
    mes INTEGER NOT NULL,
    tipo_operacion VARCHAR(50), -- venta, boleta
    tipo_documento VARCHAR(10),
    folio INTEGER,
    fecha_documento DATE,
    rut_cliente VARCHAR(20),
    razon_social_cliente VARCHAR(255),
    monto_neto DECIMAL(18,2),
    monto_exento DECIMAL(18,2),
    monto_iva DECIMAL(18,2),
    monto_total DECIMAL(18,2),
    estado_dte VARCHAR(50),
    anulado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== NOTAS DE CRÉDITO Y DÉBITO =====

-- Notas de crédito
CREATE TABLE notas_credito (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    folio INTEGER UNIQUE NOT NULL,
    tipo_dte_codigo VARCHAR(10) DEFAULT '61', -- 61 = Nota de Crédito Electrónica
    fecha_emision DATE NOT NULL,
    factura_referencia_id INTEGER REFERENCES facturas_venta(id),
    documento_referencia_tipo VARCHAR(10),
    documento_referencia_folio INTEGER,
    documento_referencia_fecha DATE,
    cliente_id INTEGER NOT NULL REFERENCES auxiliares(id),
    cliente_nombre VARCHAR(255),
    cliente_rut VARCHAR(20),
    motivo VARCHAR(50), -- devolucion, descuento, anulacion, correccion
    descripcion_motivo TEXT,
    monto_neto DECIMAL(18,2) DEFAULT 0,
    monto_exento DECIMAL(18,2) DEFAULT 0,
    monto_iva DECIMAL(18,2) DEFAULT 0,
    monto_total DECIMAL(18,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'borrador',
    dte_xml TEXT,
    dte_pdf VARCHAR(500),
    ted TEXT,
    fecha_envio_sii TIMESTAMP,
    track_id VARCHAR(100),
    estado_sii VARCHAR(50),
    contabilizado BOOLEAN DEFAULT false,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle notas de crédito
CREATE TABLE notas_credito_detalle (
    id SERIAL PRIMARY KEY,
    nota_credito_id INTEGER NOT NULL REFERENCES notas_credito(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    factura_detalle_id INTEGER REFERENCES facturas_venta_detalle(id),
    producto_id INTEGER REFERENCES productos(id),
    codigo_producto VARCHAR(50),
    nombre_producto VARCHAR(255),
    cantidad DECIMAL(18,2) NOT NULL,
    precio_unitario DECIMAL(18,2) NOT NULL,
    subtotal DECIMAL(18,2),
    impuesto_monto DECIMAL(18,2),
    total DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== POS Y CAJAS =====

-- Puntos de venta (POS)
CREATE TABLE pos_puntos_venta (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ubicacion VARCHAR(255),
    tipo VARCHAR(50), -- tienda, bodega, sucursal, movil
    direccion TEXT,
    telefono VARCHAR(50),
    responsable_id INTEGER REFERENCES usuarios_acceso(id),
    almacen_id INTEGER, -- Almacen asociado para stock
    lista_precios_id INTEGER REFERENCES listas_precios(id),
    permite_descuentos BOOLEAN DEFAULT true,
    descuento_maximo DECIMAL(5,2),
    impresora_fiscal VARCHAR(255),
    impresora_tickets VARCHAR(255),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cajas (Cash Registers)
CREATE TABLE pos_cajas (
    id SERIAL PRIMARY KEY,
    pos_id INTEGER NOT NULL REFERENCES pos_puntos_venta(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    monto_apertura DECIMAL(18,2) DEFAULT 0,
    monto_cierre DECIMAL(18,2),
    fecha_apertura TIMESTAMP,
    fecha_cierre TIMESTAMP,
    usuario_apertura_id INTEGER REFERENCES usuarios_acceso(id),
    usuario_cierre_id INTEGER REFERENCES usuarios_acceso(id),
    estado VARCHAR(50) DEFAULT 'cerrada', -- cerrada, abierta, arqueo
    ventas_cantidad INTEGER DEFAULT 0,
    ventas_total DECIMAL(18,2) DEFAULT 0,
    efectivo_total DECIMAL(18,2) DEFAULT 0,
    tarjeta_total DECIMAL(18,2) DEFAULT 0,
    transferencia_total DECIMAL(18,2) DEFAULT 0,
    otros_total DECIMAL(18,2) DEFAULT 0,
    diferencia DECIMAL(18,2) DEFAULT 0,
    observaciones TEXT,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Movimientos de caja
CREATE TABLE pos_cajas_movimientos (
    id SERIAL PRIMARY KEY,
    caja_id INTEGER NOT NULL REFERENCES pos_cajas(id),
    tipo VARCHAR(50) NOT NULL, -- venta, retiro, ingreso, arqueo
    fecha TIMESTAMP NOT NULL,
    concepto TEXT,
    monto DECIMAL(18,2) NOT NULL,
    forma_pago VARCHAR(50), -- efectivo, tarjeta_debito, tarjeta_credito, transferencia, cheque
    referencia VARCHAR(255), -- numero de transacción, cheque, etc.
    factura_id INTEGER REFERENCES facturas_venta(id),
    usuario_id INTEGER REFERENCES usuarios_acceso(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Boletas electrónicas (para POS)
CREATE TABLE boletas_electronicas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    pos_id INTEGER REFERENCES pos_puntos_venta(id),
    caja_id INTEGER REFERENCES pos_cajas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    folio INTEGER UNIQUE NOT NULL,
    tipo_dte_codigo VARCHAR(10) DEFAULT '39', -- 39=Boleta Electrónica, 41=Boleta Exenta
    fecha_emision TIMESTAMP NOT NULL,
    cliente_nombre VARCHAR(255),
    cliente_rut VARCHAR(20),
    monto_neto DECIMAL(18,2) DEFAULT 0,
    monto_exento DECIMAL(18,2) DEFAULT 0,
    monto_iva DECIMAL(18,2) DEFAULT 0,
    monto_total DECIMAL(18,2) DEFAULT 0,
    forma_pago VARCHAR(50),
    estado VARCHAR(50) DEFAULT 'emitida',
    dte_xml TEXT,
    ted TEXT,
    fecha_envio_sii TIMESTAMP,
    estado_sii VARCHAR(50),
    anulada BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle boletas
CREATE TABLE boletas_electronicas_detalle (
    id SERIAL PRIMARY KEY,
    boleta_id INTEGER NOT NULL REFERENCES boletas_electronicas(id) ON DELETE CASCADE,
    linea INTEGER NOT NULL,
    producto_id INTEGER REFERENCES productos(id),
    codigo_producto VARCHAR(50),
    nombre_producto VARCHAR(255),
    cantidad DECIMAL(18,2) NOT NULL,
    precio_unitario DECIMAL(18,2) NOT NULL,
    descuento DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== COMISIONES DE VENDEDORES =====

-- Esquemas de comisiones
CREATE TABLE comisiones_esquemas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo_calculo VARCHAR(50), -- porcentaje_venta, porcentaje_margen, monto_fijo, escalonado
    porcentaje DECIMAL(5,2),
    monto_fijo DECIMAL(18,2),
    base_calculo VARCHAR(50), -- venta_neta, venta_total, margen
    frecuencia_pago VARCHAR(50), -- mensual, quincenal, semanal
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Escala de comisiones (para tipo escalonado)
CREATE TABLE comisiones_escalas (
    id SERIAL PRIMARY KEY,
    esquema_id INTEGER NOT NULL REFERENCES comisiones_esquemas(id) ON DELETE CASCADE,
    monto_desde DECIMAL(18,2) NOT NULL,
    monto_hasta DECIMAL(18,2),
    porcentaje DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Asignación de esquemas a vendedores
CREATE TABLE vendedores_comisiones (
    id SERIAL PRIMARY KEY,
    vendedor_id INTEGER NOT NULL REFERENCES usuarios_acceso(id),
    esquema_id INTEGER NOT NULL REFERENCES comisiones_esquemas(id),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Comisiones calculadas
CREATE TABLE comisiones_calculadas (
    id SERIAL PRIMARY KEY,
    vendedor_id INTEGER NOT NULL REFERENCES usuarios_acceso(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    anio INTEGER NOT NULL,
    mes INTEGER NOT NULL,
    factura_id INTEGER REFERENCES facturas_venta(id),
    fecha_venta DATE,
    monto_venta DECIMAL(18,2),
    margen DECIMAL(18,2),
    porcentaje_comision DECIMAL(5,2),
    monto_comision DECIMAL(18,2),
    estado VARCHAR(50) DEFAULT 'pendiente', -- pendiente, aprobada, pagada
    fecha_pago DATE,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ESTADÍSTICAS Y ANÁLISIS =====

-- Estadísticas de ventas
CREATE TABLE ventas_estadisticas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    fecha DATE NOT NULL,
    vendedor_id INTEGER,
    producto_id INTEGER,
    cliente_id INTEGER,
    cantidad_ventas INTEGER DEFAULT 0,
    monto_neto DECIMAL(18,2) DEFAULT 0,
    monto_total DECIMAL(18,2) DEFAULT 0,
    costo DECIMAL(18,2) DEFAULT 0,
    margen DECIMAL(18,2) DEFAULT 0,
    margen_porcentaje DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ÍNDICES PARA PERFORMANCE =====

CREATE INDEX idx_cotizaciones_empresa ON cotizaciones(empresa_id);
CREATE INDEX idx_cotizaciones_cliente ON cotizaciones(cliente_id);
CREATE INDEX idx_cotizaciones_estado ON cotizaciones(estado);
CREATE INDEX idx_pedidos_empresa ON pedidos_venta(empresa_id);
CREATE INDEX idx_pedidos_cliente ON pedidos_venta(cliente_id);
CREATE INDEX idx_pedidos_estado ON pedidos_venta(estado);
CREATE INDEX idx_pedidos_fecha ON pedidos_venta(fecha);
CREATE INDEX idx_facturas_empresa ON facturas_venta(empresa_id);
CREATE INDEX idx_facturas_cliente ON facturas_venta(cliente_id);
CREATE INDEX idx_facturas_fecha ON facturas_venta(fecha_emision);
CREATE INDEX idx_facturas_folio ON facturas_venta(folio);
CREATE INDEX idx_facturas_estado ON facturas_venta(estado);
CREATE INDEX idx_guias_despacho_pedido ON guias_despacho(pedido_id);
CREATE INDEX idx_boletas_caja ON boletas_electronicas(caja_id);
CREATE INDEX idx_boletas_fecha ON boletas_electronicas(fecha_emision);
CREATE INDEX idx_comisiones_vendedor_periodo ON comisiones_calculadas(vendedor_id, anio, mes);
