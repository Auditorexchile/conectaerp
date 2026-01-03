-- ================================================================
-- MÓDULOS: SCM + ECOMMERCE + API/WEBHOOKS + RELOJ CONTROL
-- Gestión Logística, Tienda Online, Integraciones y Asistencia
-- ================================================================

-- ===== SCM - SUPPLY CHAIN MANAGEMENT =====

-- Transportistas
CREATE TABLE scm_transportistas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    razon_social VARCHAR(255) NOT NULL,
    rut VARCHAR(20),
    contacto VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(100),
    sitio_web VARCHAR(255),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Flota de vehículos
CREATE TABLE scm_vehiculos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    patente VARCHAR(20) UNIQUE NOT NULL,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    anio INTEGER,
    tipo VARCHAR(50), -- camion, furgon, camioneta, moto
    capacidad_kg DECIMAL(10,2),
    capacidad_m3 DECIMAL(10,2),
    estado VARCHAR(50) DEFAULT 'disponible', -- disponible, en_ruta, mantenimiento, fuera_servicio
    kilometraje INTEGER,
    conductor_asignado_id INTEGER REFERENCES usuarios_acceso(id),
    fecha_revision_tecnica DATE,
    fecha_permiso_circulacion DATE,
    seguro_vigente BOOLEAN DEFAULT false,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rutas de distribución
CREATE TABLE scm_rutas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    origen VARCHAR(255),
    destino VARCHAR(255),
    distancia_km DECIMAL(10,2),
    tiempo_estimado_minutos INTEGER,
    costo_estimado DECIMAL(18,2),
    zona VARCHAR(100),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Envíos/Despachos
CREATE TABLE scm_envios (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha_programada DATE NOT NULL,
    fecha_real DATE,
    guia_despacho_id INTEGER REFERENCES guias_despacho(id),
    pedido_id INTEGER REFERENCES pedidos_venta(id),
    cliente_id INTEGER REFERENCES auxiliares(id),
    direccion_destino TEXT NOT NULL,
    transportista_id INTEGER REFERENCES scm_transportistas(id),
    vehiculo_id INTEGER REFERENCES scm_vehiculos(id),
    conductor_id INTEGER REFERENCES usuarios_acceso(id),
    ruta_id INTEGER REFERENCES scm_rutas(id),
    bultos INTEGER,
    peso_kg DECIMAL(10,2),
    volumen_m3 DECIMAL(10,2),
    costo_flete DECIMAL(18,2),
    tracking_number VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'planificado', -- planificado, cargado, en_ruta, entregado, fallido
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tracking de envíos
CREATE TABLE scm_envios_tracking (
    id SERIAL PRIMARY KEY,
    envio_id INTEGER NOT NULL REFERENCES scm_envios(id),
    fecha_hora TIMESTAMP NOT NULL,
    estado VARCHAR(50),
    ubicacion VARCHAR(255),
    latitud DECIMAL(10,7),
    longitud DECIMAL(10,7),
    observacion TEXT,
    usuario_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ECOMMERCE - TIENDA ONLINE =====

-- Configuración tienda online
CREATE TABLE ecommerce_config (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id) UNIQUE,
    nombre_tienda VARCHAR(255),
    dominio VARCHAR(255),
    activo BOOLEAN DEFAULT false,
    permite_registro_clientes BOOLEAN DEFAULT true,
    requiere_aprobacion_clientes BOOLEAN DEFAULT false,
    metodos_pago_activos TEXT[], -- ['webpay', 'mercadopago', 'transferencia']
    envio_gratis_sobre DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categorías web
CREATE TABLE ecommerce_categorias (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    descripcion TEXT,
    padre_id INTEGER REFERENCES ecommerce_categorias(id),
    orden INTEGER DEFAULT 0,
    imagen VARCHAR(500),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Productos en tienda online
CREATE TABLE ecommerce_productos (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id) UNIQUE,
    slug VARCHAR(255) UNIQUE NOT NULL,
    descripcion_corta TEXT,
    descripcion_larga TEXT,
    categoria_id INTEGER REFERENCES ecommerce_categorias(id),
    imagenes TEXT[], -- URLs de imágenes
    video_url VARCHAR(500),
    precio_web DECIMAL(18,2),
    precio_oferta DECIMAL(18,2),
    fecha_oferta_desde DATE,
    fecha_oferta_hasta DATE,
    destacado BOOLEAN DEFAULT false,
    nuevo BOOLEAN DEFAULT false,
    orden INTEGER DEFAULT 0,
    publicado BOOLEAN DEFAULT false,
    fecha_publicacion TIMESTAMP,
    seo_title VARCHAR(255),
    seo_description TEXT,
    seo_keywords TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Carritos de compra
CREATE TABLE ecommerce_carritos (
    id SERIAL PRIMARY KEY,
    session_id VARCHAR(255),
    cliente_id INTEGER REFERENCES auxiliares(id),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'activo', -- activo, abandonado, convertido
    subtotal DECIMAL(18,2) DEFAULT 0,
    descuento DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0
);

-- Detalle carritos
CREATE TABLE ecommerce_carritos_detalle (
    id SERIAL PRIMARY KEY,
    carrito_id INTEGER NOT NULL REFERENCES ecommerce_carritos(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad DECIMAL(18,2) NOT NULL,
    precio_unitario DECIMAL(18,2) NOT NULL,
    subtotal DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pedidos web
CREATE TABLE ecommerce_pedidos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    numero VARCHAR(50) UNIQUE NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cliente_id INTEGER REFERENCES auxiliares(id),
    cliente_email VARCHAR(100),
    cliente_nombre VARCHAR(255),
    cliente_telefono VARCHAR(50),
    direccion_envio TEXT,
    metodo_pago VARCHAR(50),
    costo_envio DECIMAL(18,2) DEFAULT 0,
    subtotal DECIMAL(18,2) DEFAULT 0,
    descuento DECIMAL(18,2) DEFAULT 0,
    impuesto DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2) DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'pendiente', -- pendiente, pagado, preparacion, enviado, entregado, cancelado
    estado_pago VARCHAR(50) DEFAULT 'pendiente', -- pendiente, aprobado, rechazado, reembolsado
    transaccion_id VARCHAR(255),
    pedido_erp_id INTEGER REFERENCES pedidos_venta(id),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Detalle pedidos web
CREATE TABLE ecommerce_pedidos_detalle (
    id SERIAL PRIMARY KEY,
    pedido_id INTEGER NOT NULL REFERENCES ecommerce_pedidos(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    nombre_producto VARCHAR(255),
    cantidad DECIMAL(18,2) NOT NULL,
    precio_unitario DECIMAL(18,2) NOT NULL,
    descuento DECIMAL(18,2) DEFAULT 0,
    total DECIMAL(18,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reseñas de productos
CREATE TABLE ecommerce_resenas (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id),
    cliente_id INTEGER REFERENCES auxiliares(id),
    pedido_id INTEGER REFERENCES ecommerce_pedidos(id),
    calificacion INTEGER CHECK (calificacion >= 1 AND calificacion <= 5),
    titulo VARCHAR(255),
    comentario TEXT,
    aprobado BOOLEAN DEFAULT false,
    fecha_aprobacion TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== API Y WEBHOOKS =====

-- Tokens de API
CREATE TABLE api_tokens (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    tipo VARCHAR(50) DEFAULT 'api_key', -- api_key, oauth, jwt
    scopes TEXT[], -- ['ventas:read', 'productos:write', etc.]
    expira_en TIMESTAMP,
    activo BOOLEAN DEFAULT true,
    ultimo_uso TIMESTAMP,
    ip_permitidas TEXT[],
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES usuarios_acceso(id)
);

-- Log de requests API
CREATE TABLE api_requests_log (
    id SERIAL PRIMARY KEY,
    token_id INTEGER REFERENCES api_tokens(id),
    endpoint VARCHAR(255),
    metodo VARCHAR(10), -- GET, POST, PUT, DELETE
    request_body TEXT,
    response_code INTEGER,
    response_body TEXT,
    ip_origen VARCHAR(50),
    user_agent TEXT,
    tiempo_ejecucion_ms INTEGER,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Webhooks configurados
CREATE TABLE webhooks (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    url VARCHAR(500) NOT NULL,
    evento VARCHAR(100) NOT NULL, -- venta.creada, producto.actualizado, etc.
    metodo VARCHAR(10) DEFAULT 'POST',
    headers JSON,
    secret VARCHAR(255),
    activo BOOLEAN DEFAULT true,
    reintentos_max INTEGER DEFAULT 3,
    timeout_segundos INTEGER DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Log de webhooks enviados
CREATE TABLE webhooks_log (
    id SERIAL PRIMARY KEY,
    webhook_id INTEGER REFERENCES webhooks(id),
    evento VARCHAR(100),
    payload TEXT,
    response_code INTEGER,
    response_body TEXT,
    intento INTEGER DEFAULT 1,
    exitoso BOOLEAN DEFAULT false,
    error TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Integraciones con servicios externos
CREATE TABLE integraciones (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    servicio VARCHAR(100) NOT NULL, -- mercadolibre, shopify, woocommerce, etc.
    config JSON, -- Configuración específica del servicio
    credenciales_encrypted TEXT,
    activo BOOLEAN DEFAULT false,
    ultima_sincronizacion TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== RELOJ CONTROL Y ASISTENCIA =====

-- Dispositivos de control de asistencia
CREATE TABLE reloj_dispositivos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(50), -- biometrico, tarjeta, facial, qr
    ubicacion VARCHAR(255),
    ip VARCHAR(50),
    puerto INTEGER,
    modelo VARCHAR(100),
    numero_serie VARCHAR(100),
    activo BOOLEAN DEFAULT true,
    ultima_comunicacion TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Registro de marcajes (entrada/salida)
CREATE TABLE reloj_marcajes (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    dispositivo_id INTEGER REFERENCES reloj_dispositivos(id),
    fecha_hora TIMESTAMP NOT NULL,
    tipo VARCHAR(50), -- entrada, salida, entrada_almuerzo, salida_almuerzo
    metodo VARCHAR(50), -- huella, rostro, tarjeta, manual
    foto VARCHAR(500), -- Si tiene captura de rostro
    validado BOOLEAN DEFAULT true,
    latitud DECIMAL(10,7),
    longitud DECIMAL(10,7),
    ip_origen VARCHAR(50),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Turnos de trabajo
CREATE TABLE reloj_turnos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    hora_entrada TIME NOT NULL,
    hora_salida TIME NOT NULL,
    hora_almuerzo_inicio TIME,
    hora_almuerzo_fin TIME,
    tolerancia_minutos INTEGER DEFAULT 15,
    horas_totales DECIMAL(5,2),
    dias_semana VARCHAR(50), -- lunes,martes,miercoles...
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Asignación de turnos a empleados
CREATE TABLE reloj_empleados_turnos (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    turno_id INTEGER NOT NULL REFERENCES reloj_turnos(id),
    fecha_desde DATE NOT NULL,
    fecha_hasta DATE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Resumen de asistencia diaria
CREATE TABLE reloj_asistencia_diaria (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    fecha DATE NOT NULL,
    turno_id INTEGER REFERENCES reloj_turnos(id),
    hora_entrada TIME,
    hora_salida TIME,
    minutos_atraso INTEGER DEFAULT 0,
    minutos_extra INTEGER DEFAULT 0,
    horas_trabajadas DECIMAL(5,2),
    estado VARCHAR(50), -- presente, ausente, atraso, falta, permiso, vacaciones, licencia
    marcaje_entrada_id INTEGER REFERENCES reloj_marcajes(id),
    marcaje_salida_id INTEGER REFERENCES reloj_marcajes(id),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(empleado_id, fecha)
);

-- Solicitudes de permiso/ausencia
CREATE TABLE reloj_permisos (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    tipo VARCHAR(50), -- permiso, vacaciones, licencia_medica, compensatorio
    fecha_desde DATE NOT NULL,
    fecha_hasta DATE NOT NULL,
    dias_solicitados DECIMAL(5,2),
    motivo TEXT,
    estado VARCHAR(50) DEFAULT 'pendiente', -- pendiente, aprobado, rechazado
    aprobado_por INTEGER REFERENCES usuarios_acceso(id),
    fecha_aprobacion DATE,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER
);

-- Excepciones y justificaciones
CREATE TABLE reloj_excepciones (
    id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL REFERENCES empleados(id),
    fecha DATE NOT NULL,
    tipo VARCHAR(50), -- olvido_marcar, salida_temprana, entrada_tardia
    justificacion TEXT,
    aprobado BOOLEAN DEFAULT false,
    aprobado_por INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== FIDELIZACIÓN Y LEALTAD =====

-- Programas de fidelización
CREATE TABLE fidelizacion_programas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER NOT NULL REFERENCES empresas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    puntos_por_peso DECIMAL(5,2) DEFAULT 1, -- 1 punto por cada $1
    pesos_por_punto DECIMAL(5,2) DEFAULT 1, -- $1 por cada punto
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Clientes en programa
CREATE TABLE fidelizacion_clientes (
    id SERIAL PRIMARY KEY,
    programa_id INTEGER NOT NULL REFERENCES fidelizacion_programas(id),
    cliente_id INTEGER NOT NULL REFERENCES auxiliares(id),
    numero_tarjeta VARCHAR(50) UNIQUE,
    puntos_acumulados INTEGER DEFAULT 0,
    puntos_canjeados INTEGER DEFAULT 0,
    puntos_disponibles INTEGER DEFAULT 0,
    nivel VARCHAR(50) DEFAULT 'bronce', -- bronce, plata, oro, platino
    fecha_registro DATE NOT NULL,
    activo BOOLEAN DEFAULT true,
    UNIQUE(programa_id, cliente_id)
);

-- Movimientos de puntos
CREATE TABLE fidelizacion_movimientos (
    id SERIAL PRIMARY KEY,
    fidelizacion_cliente_id INTEGER NOT NULL REFERENCES fidelizacion_clientes(id),
    tipo VARCHAR(50), -- acumulacion, canje, expiracion, ajuste
    puntos INTEGER NOT NULL,
    saldo_anterior INTEGER,
    saldo_nuevo INTEGER,
    concepto TEXT,
    documento_tipo VARCHAR(50),
    documento_id INTEGER,
    fecha_expiracion DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recompensas canjeables
CREATE TABLE fidelizacion_recompensas (
    id SERIAL PRIMARY KEY,
    programa_id INTEGER NOT NULL REFERENCES fidelizacion_programas(id),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    puntos_requeridos INTEGER NOT NULL,
    tipo VARCHAR(50), -- descuento, producto, servicio, cupon
    valor DECIMAL(18,2),
    producto_id INTEGER REFERENCES productos(id),
    stock_disponible INTEGER,
    imagen VARCHAR(500),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== ÍNDICES =====

CREATE INDEX idx_scm_envios_estado ON scm_envios(estado);
CREATE INDEX idx_scm_vehiculos_estado ON scm_vehiculos(estado);
CREATE INDEX idx_ecommerce_pedidos_estado ON ecommerce_pedidos(estado);
CREATE INDEX idx_ecommerce_carritos_session ON ecommerce_carritos(session_id);
CREATE INDEX idx_api_tokens_token ON api_tokens(token);
CREATE INDEX idx_api_requests_fecha ON api_requests_log(fecha_hora);
CREATE INDEX idx_webhooks_evento ON webhooks(evento);
CREATE INDEX idx_marcajes_empleado_fecha ON reloj_marcajes(empleado_id, fecha_hora);
CREATE INDEX idx_asistencia_empleado_fecha ON reloj_asistencia_diaria(empleado_id, fecha);
CREATE INDEX idx_fidelizacion_cliente ON fidelizacion_clientes(cliente_id);
