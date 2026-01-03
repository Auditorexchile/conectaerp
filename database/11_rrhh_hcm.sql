-- ================================================================
-- MÓDULO: RECURSOS HUMANOS (HCM) - Human Capital Management
-- ================================================================

-- Empleados
CREATE TABLE empleados (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) NOT NULL,
    rut VARCHAR(20) NOT NULL,
    nombres VARCHAR(255) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    fecha_nacimiento DATE,
    sexo VARCHAR(1),
    estado_civil VARCHAR(50),
    nacionalidad VARCHAR(100),
    email VARCHAR(255),
    telefono VARCHAR(50),
    direccion TEXT,
    foto_url VARCHAR(500),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, codigo),
    UNIQUE(empresa_id, rut)
);

-- Contratos
CREATE TABLE empleados_contratos (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id) ON DELETE CASCADE,
    tipo_contrato VARCHAR(50), -- indefinido, plazo_fijo, honorarios
    fecha_inicio DATE NOT NULL,
    fecha_termino DATE,
    cargo VARCHAR(255),
    departamento_id INTEGER REFERENCES departamentos(id),
    sucursal_id INTEGER REFERENCES sucursales(id),
    jefe_id INTEGER REFERENCES empleados(id),
    sueldo_base DECIMAL(18,2),
    moneda VARCHAR(10) DEFAULT 'CLP',
    jornada VARCHAR(50), -- completa, parcial
    horas_semanales DECIMAL(5,2),
    estado VARCHAR(50) DEFAULT 'vigente',
    motivo_termino TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Previsión
CREATE TABLE empleados_prevision (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    afp VARCHAR(100),
    isapre VARCHAR(100),
    plan_salud VARCHAR(100),
    uf_plan_salud DECIMAL(10,4),
    mutual VARCHAR(100),
    caja_compensacion VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cargas familiares
CREATE TABLE empleados_cargas (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id) ON DELETE CASCADE,
    rut VARCHAR(20),
    nombres VARCHAR(255) NOT NULL,
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    parentesco VARCHAR(50),
    fecha_nacimiento DATE,
    asignacion_familiar BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Liquidaciones de sueldo
CREATE TABLE liquidaciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    periodo_mes INTEGER NOT NULL,
    periodo_anio INTEGER NOT NULL,
    dias_trabajados INTEGER DEFAULT 30,
    sueldo_base DECIMAL(18,2) NOT NULL,
    total_haberes DECIMAL(18,2) DEFAULT 0,
    total_descuentos DECIMAL(18,2) DEFAULT 0,
    liquido_pagar DECIMAL(18,2) NOT NULL,
    fecha_pago DATE,
    pagado BOOLEAN DEFAULT false,
    estado VARCHAR(50) DEFAULT 'borrador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, empleado_id, periodo_anio, periodo_mes)
);

CREATE TABLE liquidaciones_haberes (
    id SERIAL PRIMARY KEY,
    liquidacion_id INTEGER NOT NULL REFERENCES liquidaciones(id) ON DELETE CASCADE,
    codigo VARCHAR(50),
    concepto VARCHAR(255) NOT NULL,
    tipo VARCHAR(50), -- fijo, variable
    cantidad DECIMAL(10,2) DEFAULT 1,
    monto DECIMAL(18,2) NOT NULL,
    imponible BOOLEAN DEFAULT true,
    tributable BOOLEAN DEFAULT true
);

CREATE TABLE liquidaciones_descuentos (
    id SERIAL PRIMARY KEY,
    liquidacion_id INTEGER NOT NULL REFERENCES liquidaciones(id) ON DELETE CASCADE,
    codigo VARCHAR(50),
    concepto VARCHAR(255) NOT NULL,
    tipo VARCHAR(50), -- prevision, salud, impuesto, prestamo
    monto DECIMAL(18,2) NOT NULL
);

-- Asistencia y marcajes
CREATE TABLE asistencias (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    fecha DATE NOT NULL,
    hora_entrada TIME,
    hora_salida TIME,
    horas_trabajadas DECIMAL(5,2),
    horas_extra DECIMAL(5,2) DEFAULT 0,
    atrasos_minutos INTEGER DEFAULT 0,
    tipo VARCHAR(50) DEFAULT 'normal', -- normal, feriado, licencia, ausente
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empleado_id, fecha)
);

CREATE TABLE marcajes (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipo VARCHAR(20), -- entrada, salida
    dispositivo_id INTEGER,
    latitud DECIMAL(10,8),
    longitud DECIMAL(11,8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Vacaciones
CREATE TABLE vacaciones (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    periodo_anio INTEGER NOT NULL,
    dias_totales DECIMAL(5,2) DEFAULT 15,
    dias_tomados DECIMAL(5,2) DEFAULT 0,
    dias_pendientes DECIMAL(5,2) DEFAULT 15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empleado_id, periodo_anio)
);

CREATE TABLE vacaciones_solicitudes (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    fecha_desde DATE NOT NULL,
    fecha_hasta DATE NOT NULL,
    dias_solicitados DECIMAL(5,2) NOT NULL,
    motivo TEXT,
    estado VARCHAR(50) DEFAULT 'pendiente',
    aprobado_por INTEGER,
    fecha_aprobacion TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Capacitaciones
CREATE TABLE capacitaciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    instructor VARCHAR(255),
    fecha_inicio DATE,
    fecha_fin DATE,
    horas DECIMAL(6,2),
    costo DECIMAL(18,2),
    estado VARCHAR(50) DEFAULT 'planificada',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE capacitaciones_participantes (
    id SERIAL PRIMARY KEY,
    capacitacion_id INTEGER NOT NULL REFERENCES capacitaciones(id) ON DELETE CASCADE,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    asistencia_porcentaje DECIMAL(5,2),
    nota DECIMAL(5,2),
    aprobado BOOLEAN DEFAULT false,
    certificado_url VARCHAR(500),
    UNIQUE(capacitacion_id, empleado_id)
);

-- Evaluaciones de desempeño
CREATE TABLE evaluaciones_desempeno (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    evaluador_id INTEGER REFERENCES empleados(id),
    periodo VARCHAR(100),
    fecha_evaluacion DATE,
    puntaje_total DECIMAL(5,2),
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'borrador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Previred
CREATE TABLE previred_envios (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    periodo_mes INTEGER NOT NULL,
    periodo_anio INTEGER NOT NULL,
    archivo_rem TEXT,
    fecha_envio TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'pendiente',
    respuesta TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empresa_id, periodo_anio, periodo_mes)
);

CREATE INDEX idx_empleados_empresa ON empleados(empresa_id);
CREATE INDEX idx_empleados_rut ON empleados(rut);
CREATE INDEX idx_liquidaciones_empleado ON liquidaciones(empleado_id);
CREATE INDEX idx_asistencias_empleado ON asistencias(empleado_id);
CREATE INDEX idx_asistencias_fecha ON asistencias(fecha);
