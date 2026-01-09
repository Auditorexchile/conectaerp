-- ===============================================
-- CONECTA ERP - DATOS INICIALES (SEEDS)
-- ===============================================

-- ===============================================
-- PAÍSES (12 países soportados)
-- ===============================================
INSERT INTO paises (codigo_iso, nombre_es, nombre_en, codigo_telefono, formato_identificador, tipo_identificador, nombre_identificador, moneda_default, zona_horaria, formato_fecha, impuesto_principal, tasa_impuesto, activo) VALUES
('CL', 'Chile', 'Chile', '+56', 'XX.XXX.XXX-X', 'RUT', 'RUT', 'CLP', 'America/Santiago', 'DD/MM/YYYY', 'IVA', 19.00, 1),
('AR', 'Argentina', 'Argentina', '+54', 'XX-XXXXXXXX-X', 'CUIT', 'CUIT', 'ARS', 'America/Buenos_Aires', 'DD/MM/YYYY', 'IVA', 21.00, 1),
('PE', 'Perú', 'Peru', '+51', 'XXXXXXXXXXX', 'RUC', 'RUC', 'PEN', 'America/Lima', 'DD/MM/YYYY', 'IGV', 18.00, 1),
('CO', 'Colombia', 'Colombia', '+57', 'XXXXXXXX-X', 'NIT', 'NIT', 'COP', 'America/Bogota', 'DD/MM/YYYY', 'IVA', 19.00, 1),
('MX', 'México', 'Mexico', '+52', 'XXXX000000XXX', 'RFC', 'RFC', 'MXN', 'America/Mexico_City', 'DD/MM/YYYY', 'IVA', 16.00, 1),
('BR', 'Brasil', 'Brazil', '+55', 'XX.XXX.XXX/0001-XX', 'CNPJ', 'CNPJ', 'BRL', 'America/Sao_Paulo', 'DD/MM/YYYY', 'ICMS', 18.00, 1),
('US', 'Estados Unidos', 'United States', '+1', 'XX-XXXXXXX', 'EIN', 'EIN', 'USD', 'America/New_York', 'MM/DD/YYYY', 'Sales Tax', 0.00, 1),
('ES', 'España', 'Spain', '+34', 'X0000000X', 'CIF', 'CIF/NIF', 'EUR', 'Europe/Madrid', 'DD/MM/YYYY', 'IVA', 21.00, 1),
('FR', 'Francia', 'France', '+33', 'XXXXXXXXXXXXXX', 'SIRET', 'SIRET', 'EUR', 'Europe/Paris', 'DD/MM/YYYY', 'TVA', 20.00, 1),
('DE', 'Alemania', 'Germany', '+49', 'DEXXXXXXXXX', 'USt-IdNr', 'USt-IdNr', 'EUR', 'Europe/Berlin', 'DD/MM/YYYY', 'MwSt', 19.00, 1),
('IT', 'Italia', 'Italy', '+39', 'XXXXXXXXXXX', 'Partita IVA', 'Partita IVA', 'EUR', 'Europe/Rome', 'DD/MM/YYYY', 'IVA', 22.00, 1),
('GB', 'Reino Unido', 'United Kingdom', '+44', 'GBXXXXXXXXX', 'VAT', 'VAT Number', 'GBP', 'Europe/London', 'DD/MM/YYYY', 'VAT', 20.00, 1);

-- ===============================================
-- IDIOMAS (10 idiomas)
-- ===============================================
INSERT INTO idiomas (codigo, nombre_nativo, nombre_es, nombre_en, activo) VALUES
('es', 'Español', 'Español', 'Spanish', 1),
('en', 'English', 'Inglés', 'English', 1),
('pt', 'Português', 'Portugués', 'Portuguese', 1),
('fr', 'Français', 'Francés', 'French', 1),
('de', 'Deutsch', 'Alemán', 'German', 1),
('it', 'Italiano', 'Italiano', 'Italian', 1),
('ru', 'Русский', 'Ruso', 'Russian', 1),
('zh', '中文', 'Chino', 'Chinese', 1),
('ja', '日本語', 'Japonés', 'Japanese', 1),
('hi', 'हिन्दी', 'Hindi', 'Hindi', 1);

-- ===============================================
-- MONEDAS
-- ===============================================
INSERT INTO monedas (codigo, nombre, simbolo, decimales, activo) VALUES
('CLP', 'Peso Chileno', '$', 0, 1),
('ARS', 'Peso Argentino', '$', 2, 1),
('PEN', 'Sol Peruano', 'S/', 2, 1),
('COP', 'Peso Colombiano', '$', 2, 1),
('MXN', 'Peso Mexicano', '$', 2, 1),
('BRL', 'Real Brasileño', 'R$', 2, 1),
('USD', 'Dólar Estadounidense', '$', 2, 1),
('EUR', 'Euro', '€', 2, 1),
('GBP', 'Libra Esterlina', '£', 2, 1);

-- ===============================================
-- PLANES
-- ===============================================
INSERT INTO planes (nombre, codigo, descripcion, precio_mensual, usuarios_max, empresas_max, trial_dias, activo) VALUES
('Starter', 'STARTER', '1 empresa, 1 usuario, funciones básicas', 0.00, 1, 1, 14, 1),
('Profesional', 'PRO', 'Hasta 5 usuarios, contabilidad completa', 49990.00, 5, 1, 14, 1),
('Empresa', 'EMPRESA', 'Usuarios ilimitados, producción y proyectos', 99990.00, 999, 1, 14, 1),
('Corporativo', 'CORP', 'Multiempresa, BI avanzado, soporte premium', 0.00, 9999, 999, 14, 1);

-- ===============================================
-- USUARIO SUPERADMIN (Auditorex Chile)
-- ===============================================
INSERT INTO empresas (pais_id, identificador, razon_social, nombre_fantasia, plan_id, estado, activo) VALUES
(1, '77.866.873-4', 'Auditorex Chile SpA', 'Auditorex', 4, 'activo', 1);

INSERT INTO usuarios (empresa_id, pais_id, identificador, nombres, apellido_paterno, email, password_hash, es_representante_legal, es_superusuario, idioma_codigo, estado, activo) VALUES
(1, 1, '77.866.873-4', 'Superadmin', 'Auditorex', 'auditorexchile@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$VmZYSklNQ0ljd09hTUxOaw$8vJKmBd7YqFJ+HqN6HQZ4yX5ZvCQJ3xT9wHKyF0rQr0', 1, 1, 'es', 'activo', 1);
-- Contraseña: Sistemas40&

INSERT INTO empresa_configuracion (empresa_id, moneda_base, idioma_default, zona_horaria, formato_fecha) VALUES
(1, 'CLP', 'es', 'America/Santiago', 'DD/MM/YYYY');
