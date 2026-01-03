-- ================================================================
-- MÓDULO: CONTROLLING (CO) - Management Accounting COMPLETO
-- Sistema de Control de Gestión nivel SAP/Softland Enterprise
-- Incluye: Centros de Costo, Órdenes Internas, Rentabilidad, ABC
-- ================================================================

-- ===== CENTROS DE COSTO =====

-- Jerarquía de centros de costo
CREATE TABLE centros_costo_jerarquia (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    nivel INTEGER DEFAULT 1,
    padre_id INTEGER REFERENCES centros_costo_jerarquia(id),
    tipo VARCHAR(50), -- division, departamento, seccion, centro_trabajo
    descripcion TEXT,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Centros de costo (operacionales)
CREATE TABLE centros_costo (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    jerarquia_id INTEGER REFERENCES centros_costo_jerarquia(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50) NOT NULL, -- productivo, servicios, administrativo, ventas
    categoria VARCHAR(50), -- principal, auxiliar, fijo, variable
    responsable_id INTEGER REFERENCES usuarios_acceso(id),
    responsable_nombre VARCHAR(255),
    cuenta_contable_id INTEGER REFERENCES plan_cuentas(id),
    presupuesto_anual DECIMAL(18,2) DEFAULT 0,
    permite_presupuesto BOOLEAN DEFAULT true,
    permite_distribucion BOOLEAN DEFAULT false,
    centro_beneficio_id INTEGER, -- Para análisis de rentabilidad
    ubicacion VARCHAR(255),
    fecha_inicio DATE,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT true,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Asignación de costos a centros de costo
CREATE TABLE costos_centros (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    centro_costo_id INTEGER NOT NULL REFERENCES centros_costo(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    fecha DATE NOT NULL,
    concepto VARCHAR(255),
    tipo_costo VARCHAR(50), -- directo, indirecto, fijo, variable
    categoria VARCHAR(50), -- material, mano_obra, carga_fabril, administrativo
    monto DECIMAL(18,2) NOT NULL,
    cantidad DECIMAL(18,2),
    unidad_medida VARCHAR(20),
    costo_unitario DECIMAL(18,6),
    asiento_id INTEGER REFERENCES asientos_contables(id),
    documento_tipo VARCHAR(50),
    documento_id INTEGER,
    origen VARCHAR(50), -- manual, automatico, distribucion
    distribucion_id INTEGER,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Distribución de costos entre centros
CREATE TABLE distribucion_costos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    nombre VARCHAR(255) NOT NULL,
    fecha DATE NOT NULL,
    centro_costo_origen_id INTEGER REFERENCES centros_costo(id),
    monto_total DECIMAL(18,2) NOT NULL,
    criterio_distribucion VARCHAR(50), -- porcentaje, cantidad, valor, custom
    descripcion TEXT,
    estado VARCHAR(50) DEFAULT 'borrador', -- borrador, procesado, contabilizado
    procesado BOOLEAN DEFAULT false,
    asiento_id INTEGER REFERENCES asientos_contables(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Detalle de distribución
CREATE TABLE distribucion_costos_detalle (
    id SERIAL PRIMARY KEY,
    distribucion_id INTEGER NOT NULL REFERENCES distribucion_costos(id) ON DELETE CASCADE,
    centro_costo_destino_id INTEGER NOT NULL REFERENCES centros_costo(id),
    porcentaje DECIMAL(5,2),
    monto DECIMAL(18,2) NOT NULL,
    base_distribucion DECIMAL(18,2), -- ej: metros cuadrados, horas hombre, etc.
    unidad VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Presupuesto por centro de costo
CREATE TABLE centros_costo_presupuesto (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    centro_costo_id INTEGER NOT NULL REFERENCES centros_costo(id),
    ejercicio_id INTEGER REFERENCES ejercicios_fiscales(id),
    anio INTEGER NOT NULL,
    version INTEGER DEFAULT 1,
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
    aprobado BOOLEAN DEFAULT false,
    fecha_aprobacion DATE,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Análisis presupuesto vs real por centro de costo
CREATE TABLE centros_costo_analisis (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    centro_costo_id INTEGER REFERENCES centros_costo(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    anio INTEGER,
    mes INTEGER,
    presupuesto DECIMAL(18,2) DEFAULT 0,
    real DECIMAL(18,2) DEFAULT 0,
    variacion DECIMAL(18,2) DEFAULT 0,
    variacion_porcentaje DECIMAL(5,2) DEFAULT 0,
    acumulado_presupuesto DECIMAL(18,2) DEFAULT 0,
    acumulado_real DECIMAL(18,2) DEFAULT 0,
    acumulado_variacion DECIMAL(18,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ÓRDENES INTERNAS =====

-- Tipos de órdenes internas
CREATE TABLE ordenes_internas_tipos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    requiere_presupuesto BOOLEAN DEFAULT true,
    permite_sobregiro BOOLEAN DEFAULT false,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Órdenes internas (para proyectos internos, inversiones, etc.)
CREATE TABLE ordenes_internas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    tipo_id INTEGER REFERENCES ordenes_internas_tipos(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    centro_costo_id INTEGER REFERENCES centros_costo(id),
    responsable_id INTEGER REFERENCES usuarios_acceso(id),
    responsable_nombre VARCHAR(255),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    fecha_cierre DATE,
    presupuesto_original DECIMAL(18,2) DEFAULT 0,
    presupuesto_ajustado DECIMAL(18,2) DEFAULT 0,
    costo_real DECIMAL(18,2) DEFAULT 0,
    costo_comprometido DECIMAL(18,2) DEFAULT 0, -- Órdenes de compra pendientes
    disponible DECIMAL(18,2) DEFAULT 0,
    porcentaje_avance DECIMAL(5,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'abierta', -- abierta, en_proceso, cerrada, cancelada
    categoria VARCHAR(50), -- inversion, mantenimiento, proyecto, evento
    prioridad VARCHAR(20), -- alta, media, baja
    cuenta_contable_id INTEGER REFERENCES plan_cuentas(id),
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Movimientos de órdenes internas
CREATE TABLE ordenes_internas_movimientos (
    id SERIAL PRIMARY KEY,
    orden_interna_id INTEGER NOT NULL REFERENCES ordenes_internas(id),
    fecha DATE NOT NULL,
    tipo_movimiento VARCHAR(50), -- costo_real, presupuesto, compromiso
    concepto VARCHAR(255),
    monto DECIMAL(18,2) NOT NULL,
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER REFERENCES centros_costo(id),
    asiento_id INTEGER REFERENCES asientos_contables(id),
    documento_tipo VARCHAR(50),
    documento_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- ===== CENTROS DE BENEFICIO (PROFIT CENTERS) =====

-- Centros de beneficio para análisis de rentabilidad
CREATE TABLE centros_beneficio (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50), -- negocio, producto, servicio, region, cliente
    nivel INTEGER DEFAULT 1,
    padre_id INTEGER REFERENCES centros_beneficio(id),
    responsable_id INTEGER REFERENCES usuarios_acceso(id),
    fecha_inicio DATE,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Análisis de rentabilidad por centro de beneficio
CREATE TABLE rentabilidad_centros_beneficio (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    centro_beneficio_id INTEGER NOT NULL REFERENCES centros_beneficio(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    anio INTEGER,
    mes INTEGER,
    ingresos DECIMAL(18,2) DEFAULT 0,
    costo_ventas DECIMAL(18,2) DEFAULT 0,
    margen_bruto DECIMAL(18,2) DEFAULT 0,
    gastos_operacionales DECIMAL(18,2) DEFAULT 0,
    resultado_operacional DECIMAL(18,2) DEFAULT 0,
    otros_ingresos DECIMAL(18,2) DEFAULT 0,
    otros_egresos DECIMAL(18,2) DEFAULT 0,
    resultado_neto DECIMAL(18,2) DEFAULT 0,
    margen_bruto_porcentaje DECIMAL(5,2),
    margen_operacional_porcentaje DECIMAL(5,2),
    margen_neto_porcentaje DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ANÁLISIS DE RENTABILIDAD =====

-- Análisis de rentabilidad por producto
CREATE TABLE rentabilidad_productos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    anio INTEGER,
    mes INTEGER,
    cantidad_vendida DECIMAL(18,2) DEFAULT 0,
    ingresos_ventas DECIMAL(18,2) DEFAULT 0,
    costo_materia_prima DECIMAL(18,2) DEFAULT 0,
    costo_mano_obra DECIMAL(18,2) DEFAULT 0,
    costo_indirecto DECIMAL(18,2) DEFAULT 0,
    costo_total DECIMAL(18,2) DEFAULT 0,
    margen_contribucion DECIMAL(18,2) DEFAULT 0,
    margen_contribucion_porcentaje DECIMAL(5,2),
    gastos_comerciales DECIMAL(18,2) DEFAULT 0,
    resultado_neto DECIMAL(18,2) DEFAULT 0,
    margen_neto_porcentaje DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Análisis de rentabilidad por cliente
CREATE TABLE rentabilidad_clientes (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    cliente_id INTEGER NOT NULL REFERENCES auxiliares(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    anio INTEGER,
    mes INTEGER,
    ventas_netas DECIMAL(18,2) DEFAULT 0,
    costo_ventas DECIMAL(18,2) DEFAULT 0,
    margen_bruto DECIMAL(18,2) DEFAULT 0,
    gastos_atencion DECIMAL(18,2) DEFAULT 0,
    gastos_logistica DECIMAL(18,2) DEFAULT 0,
    gastos_comerciales DECIMAL(18,2) DEFAULT 0,
    resultado_cliente DECIMAL(18,2) DEFAULT 0,
    margen_porcentaje DECIMAL(5,2),
    cantidad_pedidos INTEGER DEFAULT 0,
    ticket_promedio DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== COSTEO ABC (Activity Based Costing) =====

-- Actividades para costeo ABC
CREATE TABLE abc_actividades (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50), -- primaria, secundaria, soporte
    centro_costo_id INTEGER REFERENCES centros_costo(id),
    driver_costo VARCHAR(100), -- ej: horas_maquina, numero_pedidos, metros_cuadrados
    unidad_medida VARCHAR(50),
    costo_unitario DECIMAL(18,6),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Drivers de costo (cost drivers)
CREATE TABLE abc_cost_drivers (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50), -- volumen, transaccional, complejidad
    unidad_medida VARCHAR(50),
    formula_calculo TEXT,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pool de costos por actividad
CREATE TABLE abc_pool_costos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    actividad_id INTEGER NOT NULL REFERENCES abc_actividades(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    anio INTEGER,
    mes INTEGER,
    costo_total DECIMAL(18,2) NOT NULL,
    volumen_driver DECIMAL(18,2), -- Cantidad del driver (ej: 1000 horas máquina)
    costo_por_driver DECIMAL(18,6), -- Costo por unidad de driver
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Asignación ABC a objetos de costo (productos, servicios, clientes)
CREATE TABLE abc_asignacion_costos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    actividad_id INTEGER REFERENCES abc_actividades(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    objeto_costo_tipo VARCHAR(50), -- producto, servicio, cliente, pedido
    objeto_costo_id INTEGER NOT NULL,
    objeto_costo_nombre VARCHAR(255),
    consumo_driver DECIMAL(18,2), -- Cantidad consumida del driver
    costo_asignado DECIMAL(18,2),
    fecha DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== REPORTES Y KPIs DE CONTROLLING =====

-- KPIs de control de gestión
CREATE TABLE controlling_kpis (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50), -- financiero, operacional, calidad, rrhh
    formula TEXT,
    unidad_medida VARCHAR(50),
    tipo_valor VARCHAR(20), -- numero, porcentaje, moneda, ratio
    meta_anual DECIMAL(18,2),
    frecuencia VARCHAR(20), -- diario, semanal, mensual, trimestral, anual
    responsable_id INTEGER REFERENCES usuarios_acceso(id),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Valores de KPIs
CREATE TABLE controlling_kpis_valores (
    id SERIAL PRIMARY KEY,
    kpi_id INTEGER NOT NULL REFERENCES controlling_kpis(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    fecha DATE NOT NULL,
    valor_real DECIMAL(18,2),
    valor_meta DECIMAL(18,2),
    variacion DECIMAL(18,2),
    variacion_porcentaje DECIMAL(5,2),
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Tablero de control (Balanced Scorecard)
CREATE TABLE balanced_scorecard (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    perspectiva VARCHAR(50), -- financiera, clientes, procesos_internos, aprendizaje_crecimiento
    objetivo_estrategico TEXT,
    kpi_id INTEGER REFERENCES controlling_kpis(id),
    peso_porcentaje DECIMAL(5,2),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ANÁLISIS DE VARIACIONES =====

-- Análisis de variaciones (presupuesto vs real)
CREATE TABLE analisis_variaciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    tipo_analisis VARCHAR(50), -- centro_costo, orden_interna, cuenta, producto
    objeto_id INTEGER NOT NULL,
    objeto_nombre VARCHAR(255),
    concepto VARCHAR(255),
    monto_presupuestado DECIMAL(18,2) DEFAULT 0,
    monto_real DECIMAL(18,2) DEFAULT 0,
    variacion_absoluta DECIMAL(18,2) DEFAULT 0,
    variacion_porcentaje DECIMAL(5,2) DEFAULT 0,
    tipo_variacion VARCHAR(20), -- favorable, desfavorable, neutral
    categoria_variacion VARCHAR(50), -- precio, volumen, eficiencia, mixta
    causa_raiz TEXT,
    accion_correctiva TEXT,
    responsable_id INTEGER,
    estado VARCHAR(50), -- identificada, en_analisis, resuelta
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- ===== TRANSFERENCIAS INTERNAS =====

-- Transferencias entre centros de costo
CREATE TABLE transferencias_internas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha DATE NOT NULL,
    centro_costo_origen_id INTEGER REFERENCES centros_costo(id),
    centro_costo_destino_id INTEGER REFERENCES centros_costo(id),
    concepto TEXT,
    monto DECIMAL(18,2) NOT NULL,
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    tipo_transferencia VARCHAR(50), -- costo, servicio, recurso
    asiento_id INTEGER REFERENCES asientos_contables(id),
    aprobado BOOLEAN DEFAULT false,
    aprobado_por INTEGER,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- ===== FORECAST Y PROYECCIONES =====

-- Forecast financiero
CREATE TABLE forecast_financiero (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_id INTEGER REFERENCES periodos_contables(id),
    nombre VARCHAR(255) NOT NULL,
    version INTEGER DEFAULT 1,
    fecha_creacion DATE NOT NULL,
    anio INTEGER NOT NULL,
    mes INTEGER NOT NULL,
    tipo VARCHAR(50), -- ingresos, costos, gastos, resultado
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER REFERENCES centros_costo(id),
    monto_proyectado DECIMAL(18,2) NOT NULL,
    monto_real DECIMAL(18,2),
    supuestos TEXT,
    metodo_proyeccion VARCHAR(50), -- historico, tendencia, regresion, manual
    confianza_porcentaje DECIMAL(5,2),
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Escenarios de planificación
CREATE TABLE escenarios_planificacion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50), -- optimista, pesimista, realista, custom
    anio INTEGER NOT NULL,
    probabilidad DECIMAL(5,2),
    supuestos_clave TEXT,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos de escenarios
CREATE TABLE escenarios_datos (
    id SERIAL PRIMARY KEY,
    escenario_id INTEGER NOT NULL REFERENCES escenarios_planificacion(id) ON DELETE CASCADE,
    cuenta_id INTEGER REFERENCES plan_cuentas(id),
    centro_costo_id INTEGER REFERENCES centros_costo(id),
    mes INTEGER,
    concepto VARCHAR(255),
    monto DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== REPORTES DE GESTIÓN =====

-- Plantillas de reportes personalizados
CREATE TABLE reportes_gestion (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50), -- costos, rentabilidad, presupuesto, kpis
    tipo_reporte VARCHAR(50), -- tabla, grafico, dashboard
    filtros_default JSON,
    columnas_config JSON,
    periodicidad VARCHAR(20), -- diario, semanal, mensual
    destinatarios TEXT[], -- Array de emails
    auto_envio BOOLEAN DEFAULT false,
    dia_envio INTEGER,
    hora_envio TIME,
    formato_salida VARCHAR(20), -- pdf, excel, html
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Historial de ejecución de reportes
CREATE TABLE reportes_historial (
    id SERIAL PRIMARY KEY,
    reporte_id INTEGER NOT NULL REFERENCES reportes_gestion(id),
    fecha_ejecucion TIMESTAMP NOT NULL,
    parametros JSON,
    archivo_generado VARCHAR(500),
    tiempo_ejecucion INTEGER, -- milisegundos
    registros_procesados INTEGER,
    estado VARCHAR(50), -- exitoso, error, timeout
    mensaje_error TEXT,
    ejecutado_por INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ÍNDICES PARA PERFORMANCE =====

CREATE INDEX idx_centros_costo_empresa ON centros_costo(empresa_id);
CREATE INDEX idx_centros_costo_activo ON centros_costo(activo);
CREATE INDEX idx_costos_centros_lookup ON costos_centros(empresa_id, centro_costo_id, fecha);
CREATE INDEX idx_ordenes_internas_empresa ON ordenes_internas(empresa_id);
CREATE INDEX idx_ordenes_internas_estado ON ordenes_internas(estado);
CREATE INDEX idx_rentabilidad_productos_lookup ON rentabilidad_productos(empresa_id, producto_id, anio, mes);
CREATE INDEX idx_rentabilidad_clientes_lookup ON rentabilidad_clientes(empresa_id, cliente_id, anio, mes);
CREATE INDEX idx_kpis_valores_lookup ON controlling_kpis_valores(kpi_id, fecha);
CREATE INDEX idx_analisis_variaciones_periodo ON analisis_variaciones(periodo_id);
CREATE INDEX idx_forecast_financiero_lookup ON forecast_financiero(empresa_id, anio, mes);
