-- ================================================================
-- MÓDULO: PAÍSES E IDIOMAS - CONFIGURACIÓN GLOBAL
-- ================================================================
-- Sistema multi-país, multi-moneda, multi-idioma
-- Define TODA la lógica legal, monetaria y tributaria por país
-- ================================================================

-- ================================================================
-- IDIOMAS SOPORTADOS (10 IDIOMAS)
-- ================================================================

CREATE TABLE IF NOT EXISTS idiomas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(10) UNIQUE NOT NULL COMMENT 'Código ISO 639-1',
    nombre VARCHAR(100) NOT NULL,
    nombre_nativo VARCHAR(100) NOT NULL,
    bandera VARCHAR(10) COMMENT 'Emoji bandera',
    direccion_texto ENUM('ltr', 'rtl') DEFAULT 'ltr',
    activo BOOLEAN DEFAULT TRUE,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_idiomas_codigo (codigo),
    INDEX idx_idiomas_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO idiomas (codigo, nombre, nombre_nativo, bandera, direccion_texto, activo, orden) VALUES
('es', 'Español', 'Español', '🇪🇸', 'ltr', TRUE, 1),
('en', 'Inglés', 'English', '🇬🇧', 'ltr', TRUE, 2),
('pt', 'Portugués', 'Português', '🇵🇹', 'ltr', TRUE, 3),
('fr', 'Francés', 'Français', '🇫🇷', 'ltr', TRUE, 4),
('de', 'Alemán', 'Deutsch', '🇩🇪', 'ltr', TRUE, 5),
('it', 'Italiano', 'Italiano', '🇮🇹', 'ltr', TRUE, 6),
('zh', 'Chino', '中文', '🇨🇳', 'ltr', TRUE, 7),
('ja', 'Japonés', '日本語', '🇯🇵', 'ltr', TRUE, 8),
('ko', 'Coreano', '한국어', '🇰🇷', 'ltr', TRUE, 9),
('ar', 'Árabe', 'العربية', '🇸🇦', 'rtl', TRUE, 10);

-- ================================================================
-- PAÍSES SOPORTADOS (12 PAÍSES)
-- ================================================================

CREATE TABLE IF NOT EXISTS paises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(2) UNIQUE NOT NULL COMMENT 'Código ISO 3166-1 alpha-2',
    codigo_iso3 VARCHAR(3) COMMENT 'Código ISO 3166-1 alpha-3',
    nombre VARCHAR(100) NOT NULL,
    nombre_oficial VARCHAR(255),
    bandera VARCHAR(10) COMMENT 'Emoji bandera',
    continente VARCHAR(50),
    region VARCHAR(100),
    idioma_principal VARCHAR(10) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_paises_codigo (codigo),
    INDEX idx_paises_activo (activo),
    FOREIGN KEY (idioma_principal) REFERENCES idiomas(codigo) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO paises (codigo, codigo_iso3, nombre, nombre_oficial, bandera, continente, region, idioma_principal, activo, orden) VALUES
('CL', 'CHL', 'Chile', 'República de Chile', '🇨🇱', 'América', 'América del Sur', 'es', TRUE, 1),
('AR', 'ARG', 'Argentina', 'República Argentina', '🇦🇷', 'América', 'América del Sur', 'es', TRUE, 2),
('PE', 'PER', 'Perú', 'República del Perú', '🇵🇪', 'América', 'América del Sur', 'es', TRUE, 3),
('CO', 'COL', 'Colombia', 'República de Colombia', '🇨🇴', 'América', 'América del Sur', 'es', TRUE, 4),
('MX', 'MEX', 'México', 'Estados Unidos Mexicanos', '🇲🇽', 'América', 'América del Norte', 'es', TRUE, 5),
('BR', 'BRA', 'Brasil', 'República Federativa do Brasil', '🇧🇷', 'América', 'América del Sur', 'pt', TRUE, 6),
('US', 'USA', 'Estados Unidos', 'United States of America', '🇺🇸', 'América', 'América del Norte', 'en', TRUE, 7),
('ES', 'ESP', 'España', 'Reino de España', '🇪🇸', 'Europa', 'Europa del Sur', 'es', TRUE, 8),
('FR', 'FRA', 'Francia', 'République française', '🇫🇷', 'Europa', 'Europa Occidental', 'fr', TRUE, 9),
('DE', 'DEU', 'Alemania', 'Bundesrepublik Deutschland', '🇩🇪', 'Europa', 'Europa Central', 'de', TRUE, 10),
('IT', 'ITA', 'Italia', 'Repubblica Italiana', '🇮🇹', 'Europa', 'Europa del Sur', 'it', TRUE, 11),
('GB', 'GBR', 'Reino Unido', 'United Kingdom of Great Britain and Northern Ireland', '🇬🇧', 'Europa', 'Europa del Norte', 'en', TRUE, 12);

-- ================================================================
-- CONFIGURACIÓN LEGAL Y TRIBUTARIA POR PAÍS
-- ================================================================

CREATE TABLE IF NOT EXISTS paises_configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pais_codigo VARCHAR(2) NOT NULL,

    -- IDENTIFICADORES TRIBUTARIOS
    identificador_tipo VARCHAR(50) NOT NULL COMMENT 'RUT, CUIT, RFC, CNPJ, EIN, NIF, etc.',
    identificador_nombre VARCHAR(100) NOT NULL,
    identificador_formato VARCHAR(100) COMMENT 'Formato visual ej: XX.XXX.XXX-X',
    identificador_regex VARCHAR(255) COMMENT 'Regex validación',
    identificador_longitud_min INT,
    identificador_longitud_max INT,
    identificador_tiene_digito_verificador BOOLEAN DEFAULT FALSE,

    -- FORMATOS REGIONALES
    formato_fecha VARCHAR(20) DEFAULT 'DD/MM/YYYY',
    formato_hora VARCHAR(20) DEFAULT 'HH:mm:ss',
    formato_numero_decimal VARCHAR(5) DEFAULT ',',
    formato_numero_miles VARCHAR(5) DEFAULT '.',
    zona_horaria VARCHAR(100),

    -- MONEDA
    moneda_codigo VARCHAR(3) NOT NULL COMMENT 'Código ISO 4217',
    moneda_nombre VARCHAR(100),
    moneda_simbolo VARCHAR(10),
    moneda_decimales INT DEFAULT 2,

    -- IMPUESTOS PRINCIPALES
    impuesto_ventas_nombre VARCHAR(100) COMMENT 'IVA, IGV, Sales Tax, etc.',
    impuesto_ventas_tasa DECIMAL(5,2) COMMENT 'Tasa estándar %',
    impuesto_ventas_tipo ENUM('incluido', 'excluido') DEFAULT 'excluido',

    -- INTEGRACIONES FISCALES
    tiene_facturacion_electronica BOOLEAN DEFAULT FALSE,
    tiene_integracion_fiscal BOOLEAN DEFAULT FALSE,
    nombre_entidad_fiscal VARCHAR(100) COMMENT 'SII, AFIP, SUNAT, SAT, DIAN, etc.',
    url_entidad_fiscal VARCHAR(255),

    -- CONTABILIDAD
    plan_cuentas_base VARCHAR(50) COMMENT 'IFRS, GAAP, Local',
    cierre_fiscal_mes INT DEFAULT 12,
    cierre_fiscal_dia INT DEFAULT 31,

    -- LABORAL
    tiene_prevision_social BOOLEAN DEFAULT FALSE,
    nombre_sistema_prevision VARCHAR(100) COMMENT 'Previred, IMSS, etc.',

    -- CONFIGURACIÓN ADICIONAL
    configuracion_json JSON COMMENT 'Configuración adicional por país',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_pais_config (pais_codigo),
    FOREIGN KEY (pais_codigo) REFERENCES paises(codigo) ON DELETE CASCADE,
    INDEX idx_paises_config_pais (pais_codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- CONFIGURACIÓN POR PAÍS - DATOS COMPLETOS
-- ================================================================

-- 🇨🇱 CHILE
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal, url_entidad_fiscal,
    plan_cuentas_base, tiene_prevision_social, nombre_sistema_prevision
) VALUES (
    'CL', 'RUT', 'RUT (Rol Único Tributario)', 'XX.XXX.XXX-X', '^[0-9]{1,2}\\.[0-9]{3}\\.[0-9]{3}-[0-9Kk]$',
    8, 10, TRUE,
    'DD/MM/YYYY', ',', '.', 'America/Santiago',
    'CLP', 'Peso Chileno', '$', 0,
    'IVA', 19.00, 'excluido',
    TRUE, TRUE, 'SII - Servicio de Impuestos Internos', 'https://www.sii.cl',
    'IFRS', TRUE, 'Previred'
);

-- 🇦🇷 ARGENTINA
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal, url_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'AR', 'CUIT', 'CUIT (Clave Única de Identificación Tributaria)', 'XX-XXXXXXXX-X', '^[0-9]{2}-[0-9]{8}-[0-9]$',
    11, 13, TRUE,
    'DD/MM/YYYY', ',', '.', 'America/Buenos_Aires',
    'ARS', 'Peso Argentino', '$', 2,
    'IVA', 21.00, 'excluido',
    TRUE, TRUE, 'AFIP - Administración Federal de Ingresos Públicos', 'https://www.afip.gob.ar',
    'IFRS'
);

-- 🇵🇪 PERÚ
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal, url_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'PE', 'RUC', 'RUC (Registro Único de Contribuyentes)', 'XXXXXXXXXXX', '^[0-9]{11}$',
    11, 11, FALSE,
    'DD/MM/YYYY', '.', ',', 'America/Lima',
    'PEN', 'Sol Peruano', 'S/', 2,
    'IGV', 18.00, 'excluido',
    TRUE, TRUE, 'SUNAT - Superintendencia Nacional de Aduanas y de Administración Tributaria', 'https://www.sunat.gob.pe',
    'IFRS'
);

-- 🇨🇴 COLOMBIA
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal, url_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'CO', 'NIT', 'NIT (Número de Identificación Tributaria)', 'XXXXXXXX-X', '^[0-9]{8,9}-[0-9]$',
    9, 11, TRUE,
    'DD/MM/YYYY', ',', '.', 'America/Bogota',
    'COP', 'Peso Colombiano', '$', 2,
    'IVA', 19.00, 'excluido',
    TRUE, TRUE, 'DIAN - Dirección de Impuestos y Aduanas Nacionales', 'https://www.dian.gov.co',
    'IFRS'
);

-- 🇲🇽 MÉXICO
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal, url_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'MX', 'RFC', 'RFC (Registro Federal de Contribuyentes)', 'XXXX000000XXX', '^[A-Z]{3,4}[0-9]{6}[A-Z0-9]{3}$',
    12, 13, TRUE,
    'DD/MM/YYYY', '.', ',', 'America/Mexico_City',
    'MXN', 'Peso Mexicano', '$', 2,
    'IVA', 16.00, 'excluido',
    TRUE, TRUE, 'SAT - Servicio de Administración Tributaria', 'https://www.sat.gob.mx',
    'IFRS'
);

-- 🇧🇷 BRASIL
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'BR', 'CNPJ', 'CNPJ (Cadastro Nacional da Pessoa Jurídica)', 'XX.XXX.XXX/0001-XX', '^[0-9]{2}\\.[0-9]{3}\\.[0-9]{3}/[0-9]{4}-[0-9]{2}$',
    14, 18, TRUE,
    'DD/MM/YYYY', ',', '.', 'America/Sao_Paulo',
    'BRL', 'Real Brasileño', 'R$', 2,
    'ICMS', 18.00, 'incluido',
    TRUE, TRUE, 'Receita Federal do Brasil',
    'IFRS'
);

-- 🇺🇸 ESTADOS UNIDOS
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'US', 'EIN', 'EIN (Employer Identification Number)', 'XX-XXXXXXX', '^[0-9]{2}-[0-9]{7}$',
    9, 10, FALSE,
    'MM/DD/YYYY', '.', ',', 'America/New_York',
    'USD', 'Dólar Estadounidense', '$', 2,
    'Sales Tax', 7.50, 'excluido',
    FALSE, TRUE, 'IRS - Internal Revenue Service',
    'GAAP'
);

-- 🇪🇸 ESPAÑA
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal, url_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'ES', 'CIF', 'CIF/NIF (Código/Número de Identificación Fiscal)', 'X0000000X', '^[A-Z][0-9]{7}[A-Z0-9]$',
    9, 9, TRUE,
    'DD/MM/YYYY', ',', '.', 'Europe/Madrid',
    'EUR', 'Euro', '€', 2,
    'IVA', 21.00, 'excluido',
    TRUE, TRUE, 'AEAT - Agencia Estatal de Administración Tributaria', 'https://www.aeat.es',
    'IFRS'
);

-- 🇫🇷 FRANCIA
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'FR', 'SIRET', 'SIRET (Système d\'Identification du Répertoire des Établissements)', 'XXXXXXXXXXXXXX', '^[0-9]{14}$',
    14, 14, TRUE,
    'DD/MM/YYYY', ',', ' ', 'Europe/Paris',
    'EUR', 'Euro', '€', 2,
    'TVA', 20.00, 'excluido',
    TRUE, TRUE, 'Direction générale des Finances publiques',
    'IFRS'
);

-- 🇩🇪 ALEMANIA
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'DE', 'USt-IdNr', 'USt-IdNr (Umsatzsteuer-Identifikationsnummer)', 'DEXXXXXXXXX', '^DE[0-9]{9}$',
    11, 11, TRUE,
    'DD.MM.YYYY', ',', '.', 'Europe/Berlin',
    'EUR', 'Euro', '€', 2,
    'MwSt', 19.00, 'excluido',
    TRUE, TRUE, 'Bundeszentralamt für Steuern',
    'IFRS'
);

-- 🇮🇹 ITALIA
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'IT', 'P.IVA', 'Partita IVA', 'XXXXXXXXXXX', '^[0-9]{11}$',
    11, 11, TRUE,
    'DD/MM/YYYY', ',', '.', 'Europe/Rome',
    'EUR', 'Euro', '€', 2,
    'IVA', 22.00, 'excluido',
    TRUE, TRUE, 'Agenzia delle Entrate',
    'IFRS'
);

-- 🇬🇧 REINO UNIDO
INSERT INTO paises_configuracion (
    pais_codigo, identificador_tipo, identificador_nombre, identificador_formato, identificador_regex,
    identificador_longitud_min, identificador_longitud_max, identificador_tiene_digito_verificador,
    formato_fecha, formato_numero_decimal, formato_numero_miles, zona_horaria,
    moneda_codigo, moneda_nombre, moneda_simbolo, moneda_decimales,
    impuesto_ventas_nombre, impuesto_ventas_tasa, impuesto_ventas_tipo,
    tiene_facturacion_electronica, tiene_integracion_fiscal, nombre_entidad_fiscal,
    plan_cuentas_base
) VALUES (
    'GB', 'VAT', 'VAT Number (Value Added Tax Number)', 'GBXXXXXXXXX', '^GB[0-9]{9}$',
    11, 11, FALSE,
    'DD/MM/YYYY', '.', ',', 'Europe/London',
    'GBP', 'Libra Esterlina', '£', 2,
    'VAT', 20.00, 'excluido',
    TRUE, TRUE, 'HM Revenue & Customs',
    'IFRS'
);

-- ================================================================
-- MONEDAS
-- ================================================================

CREATE TABLE IF NOT EXISTS monedas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(3) UNIQUE NOT NULL COMMENT 'Código ISO 4217',
    nombre VARCHAR(100) NOT NULL,
    simbolo VARCHAR(10),
    decimales INT DEFAULT 2,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_monedas_codigo (codigo),
    INDEX idx_monedas_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO monedas (codigo, nombre, simbolo, decimales, activo) VALUES
('CLP', 'Peso Chileno', '$', 0, TRUE),
('ARS', 'Peso Argentino', '$', 2, TRUE),
('PEN', 'Sol Peruano', 'S/', 2, TRUE),
('COP', 'Peso Colombiano', '$', 2, TRUE),
('MXN', 'Peso Mexicano', '$', 2, TRUE),
('BRL', 'Real Brasileño', 'R$', 2, TRUE),
('USD', 'Dólar Estadounidense', '$', 2, TRUE),
('EUR', 'Euro', '€', 2, TRUE),
('GBP', 'Libra Esterlina', '£', 2, TRUE);

-- ================================================================
-- TIPOS DE CAMBIO
-- ================================================================

CREATE TABLE IF NOT EXISTS tipos_cambio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    moneda_origen VARCHAR(3) NOT NULL,
    moneda_destino VARCHAR(3) NOT NULL,
    tasa DECIMAL(18,6) NOT NULL,
    fuente VARCHAR(100) COMMENT 'API, Manual, Banco Central, etc.',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tipo_cambio (fecha, moneda_origen, moneda_destino),
    FOREIGN KEY (moneda_origen) REFERENCES monedas(codigo) ON DELETE CASCADE,
    FOREIGN KEY (moneda_destino) REFERENCES monedas(codigo) ON DELETE CASCADE,
    INDEX idx_tipos_cambio_fecha (fecha),
    INDEX idx_tipos_cambio_monedas (moneda_origen, moneda_destino)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TRADUCCIONES DINÁMICAS
-- ================================================================

CREATE TABLE IF NOT EXISTS traducciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(255) NOT NULL COMMENT 'Clave de traducción (ej: menu.dashboard)',
    idioma_codigo VARCHAR(10) NOT NULL,
    texto TEXT NOT NULL,
    modulo VARCHAR(100) COMMENT 'Módulo del ERP',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_traduccion (clave, idioma_codigo),
    FOREIGN KEY (idioma_codigo) REFERENCES idiomas(codigo) ON DELETE CASCADE,
    INDEX idx_traducciones_clave (clave),
    INDEX idx_traducciones_idioma (idioma_codigo),
    INDEX idx_traducciones_modulo (modulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- NOTAS FINALES
-- ================================================================
--
-- LÓGICA DE NEGOCIO:
-- 1. Al registrarse, el usuario selecciona UN país
-- 2. Ese país define TODA la configuración automática:
--    - Tipo de identificador tributario y validación
--    - Moneda base
--    - Formato de fechas y números
--    - Impuestos aplicables
--    - Integraciones disponibles
--    - Plan de cuentas base
-- 3. NO se permite cambiar de país después del registro
-- 4. Cada empresa pertenece a UN solo país
-- 5. Multi-moneda permite transacciones en otras monedas con tipo de cambio
-- 6. Multi-idioma es solo para la interfaz de usuario
--
-- ================================================================
