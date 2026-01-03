-- ================================================================
-- MÓDULO: CRM - Customer Relationship Management
-- ================================================================

-- Leads
CREATE TABLE crm_leads (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    empresa VARCHAR(255),
    cargo VARCHAR(100),
    email VARCHAR(255),
    telefono VARCHAR(50),
    origen VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'nuevo',
    calificacion VARCHAR(50),
    responsable_id INTEGER,
    convertido BOOLEAN DEFAULT false,
    convertido_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Oportunidades
CREATE TABLE crm_oportunidades (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    lead_id INTEGER REFERENCES crm_leads(id),
    cliente_id INTEGER REFERENCES auxiliares(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    monto_estimado DECIMAL(18,2),
    moneda VARCHAR(10) DEFAULT 'CLP',
    probabilidad INTEGER DEFAULT 50,
    fecha_cierre_estimada DATE,
    etapa VARCHAR(100) DEFAULT 'prospecto',
    estado VARCHAR(50) DEFAULT 'abierta',
    responsable_id INTEGER,
    ganada BOOLEAN DEFAULT false,
    perdida BOOLEAN DEFAULT false,
    motivo_perdida TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Actividades CRM
CREATE TABLE crm_actividades (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    tipo VARCHAR(50), -- llamada, email, reunion, tarea
    asunto VARCHAR(255),
    descripcion TEXT,
    fecha_inicio TIMESTAMP,
    fecha_fin TIMESTAMP,
    lead_id INTEGER REFERENCES crm_leads(id),
    oportunidad_id INTEGER REFERENCES crm_oportunidades(id),
    cliente_id INTEGER REFERENCES auxiliares(id),
    responsable_id INTEGER,
    completada BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Campañas de marketing
CREATE TABLE crm_campanas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(100), -- email, redes_sociales, evento
    fecha_inicio DATE,
    fecha_fin DATE,
    presupuesto DECIMAL(18,2),
    costo_real DECIMAL(18,2),
    objetivo_leads INTEGER,
    leads_generados INTEGER DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'planificada',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tickets de soporte
CREATE TABLE crm_tickets (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) NOT NULL,
    cliente_id INTEGER REFERENCES auxiliares(id),
    asunto VARCHAR(255) NOT NULL,
    descripcion TEXT,
    prioridad VARCHAR(50) DEFAULT 'media',
    categoria VARCHAR(100),
    asignado_a INTEGER,
    estado VARCHAR(50) DEFAULT 'abierto',
    fecha_cierre TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, numero)
);

-- ================================================================
-- MÓDULO: PROYECTOS (PM) - Project Management
-- ================================================================

CREATE TABLE proyectos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    cliente_id INTEGER REFERENCES auxiliares(id),
    responsable_id INTEGER,
    fecha_inicio DATE NOT NULL,
    fecha_fin_planificada DATE,
    fecha_fin_real DATE,
    presupuesto DECIMAL(18,2),
    costo_real DECIMAL(18,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'planificado',
    progreso_porcentaje DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

CREATE TABLE proyectos_tareas (
    id SERIAL PRIMARY KEY,
    proyecto_id INTEGER NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    duracion_dias INTEGER,
    predecesora_id INTEGER REFERENCES proyectos_tareas(id),
    asignado_a INTEGER,
    progreso_porcentaje DECIMAL(5,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE proyectos_hitos (
    id SERIAL PRIMARY KEY,
    proyecto_id INTEGER NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_planificada DATE NOT NULL,
    fecha_real DATE,
    completado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE proyectos_recursos (
    id SERIAL PRIMARY KEY,
    proyecto_id INTEGER NOT NULL REFERENCES proyectos(id),
    empleado_id INTEGER REFERENCES empleados(id),
    rol VARCHAR(100),
    horas_asignadas DECIMAL(10,2),
    horas_trabajadas DECIMAL(10,2) DEFAULT 0,
    costo_hora DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE proyectos_gastos (
    id SERIAL PRIMARY KEY,
    proyecto_id INTEGER NOT NULL REFERENCES proyectos(id),
    fecha DATE NOT NULL,
    concepto VARCHAR(255),
    monto DECIMAL(18,2) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'CLP',
    categoria VARCHAR(100),
    aprobado BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
-- MÓDULO: BUSINESS INTELLIGENCE (BI)
-- ================================================================

CREATE TABLE dashboards_personalizados (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    usuario_id INTEGER REFERENCES usuarios_acceso(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    configuracion JSONB,
    publico BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE widgets_dashboard (
    id SERIAL PRIMARY KEY,
    dashboard_id INTEGER NOT NULL REFERENCES dashboards_personalizados(id) ON DELETE CASCADE,
    tipo VARCHAR(100), -- grafico, kpi, tabla, mapa
    titulo VARCHAR(255),
    configuracion JSONB,
    posicion_x INTEGER,
    posicion_y INTEGER,
    ancho INTEGER,
    alto INTEGER,
    orden INTEGER DEFAULT 0
);

CREATE TABLE kpis (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(100) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    formula TEXT,
    unidad VARCHAR(50),
    meta DECIMAL(18,2),
    frecuencia VARCHAR(50), -- diario, semanal, mensual
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo)
);

CREATE TABLE kpis_valores (
    id SERIAL PRIMARY KEY,
    kpi_id INTEGER NOT NULL REFERENCES kpis(id) ON DELETE CASCADE,
    fecha DATE NOT NULL,
    valor DECIMAL(18,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(kpi_id, fecha)
);

CREATE TABLE reportes_personalizados (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(100), -- sql, api, olap
    query TEXT,
    parametros JSONB,
    formato_salida VARCHAR(50), -- pdf, excel, csv
    programado BOOLEAN DEFAULT false,
    frecuencia VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

CREATE INDEX idx_crm_leads_empresa ON crm_leads(empresa_id);
CREATE INDEX idx_crm_oportunidades_empresa ON crm_oportunidades(empresa_id);
CREATE INDEX idx_proyectos_empresa ON proyectos(empresa_id);
CREATE INDEX idx_kpis_empresa ON kpis(empresa_id);
