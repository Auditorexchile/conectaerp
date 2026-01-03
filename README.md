# CONECTA ERP - Sistema ERP Enterprise Completo

Sistema ERP de nivel empresarial comparable a SAP Business One y Softland ERP, diseñado para empresas chilenas con capacidades internacionales.

## 🎯 Características Principales

### ✅ Sistema Completo
- **Arquitectura Enterprise**: Sistema profesional de nivel SAP/Softland
- **Seguridad Nivel Bancario**: Hash Argon2id, 2FA, auditoría completa
- **Trial 14 días**: Período de prueba completo con inicio en primer login
- **Multiempresa**: Soporte para holding y grupos empresariales
- **Multimoneda**: 10+ monedas con conversión automática
- **Multiidioma**: 10 idiomas (ES, EN, PT, FR, DE, IT, ZH, JA, KO, HI)
- **Multipaís**: Configuración por país con normativas locales

### 🔐 Seguridad Avanzada

- **Hash de contraseñas**: Argon2id / Bcrypt
- **Validación fuerte**: Mínimo 12 caracteres, complejidad obligatoria
- **Rate limiting**: Protección contra fuerza bruta
- **IP Whitelist/Blacklist**: Control de acceso por IP
- **2FA opcional**: Autenticación de dos factores
- **Auditoría completa**: Registro de todas las acciones
- **CSRF Protection**: Tokens anti falsificación
- **Session Security**: Cookies seguras, regeneración automática

### 📊 Módulos del ERP

#### Núcleo del Sistema
- ✅ **Dashboard** - Panel principal con KPIs
- ✅ **Gestión de Empresas** - Configuración multiempresa
- ✅ **Usuarios y Roles** - Control de acceso granular
- ✅ **Configuración** - Parámetros del sistema

#### Entidades Maestras
- ✅ **Clientes** - Gestión completa de clientes
- ✅ **Proveedores** - Base de proveedores
- ✅ **Productos** - Catálogo de productos y servicios
- ✅ **Empleados** - Base de recursos humanos

#### Finanzas (FI)
- ✅ **Contabilidad General**
- ✅ **Cuentas por Pagar**
- ✅ **Cuentas por Cobrar**
- ✅ **Tesorería**
- ✅ **Activos Fijos**

#### Ventas (SD)
- ✅ **Pedidos y Cotizaciones**
- ✅ **Facturación Electrónica**
- ✅ **Control de Cajas**
- ✅ **Precios y Promociones**

#### Materiales (MM)
- ✅ **Inventario y Stock**
- ✅ **Compras**
- ✅ **Almacenes**
- ✅ **Movimientos**

#### Producción (PP)
- Órdenes de Fabricación
- MRP
- Costos de Producción
- Calidad

#### RRHH (HCM)
- Personal
- Nómina
- Asistencia
- Capacitación

#### CRM
- Leads y Oportunidades
- Campañas
- Tickets de Soporte

#### BI y Reportes
- Dashboards Personalizados
- KPIs
- Reportes Avanzados

### 🇨🇱 Integraciones Chile

- **SII** - Servicio de Impuestos Internos
  - DTE (Documentos Tributarios Electrónicos)
  - Libro de Compras/Ventas
  - F29
  - Cesión de Facturas

- **Previred** - Cotizaciones Previsionales
  - Archivo .REM
  - Validación de trabajadores
  - Tasas AFP/Isapres

## 📁 Estructura del Proyecto

```
conectaerp/
├── app/
│   ├── core/                  # Núcleo del sistema
│   │   ├── bootstrap.php     # Inicialización
│   │   ├── database.php      # Gestor BD
│   │   ├── security.php      # Seguridad
│   │   ├── session.php       # Sesiones
│   │   └── helpers.php       # Funciones auxiliares
│   ├── layout/               # Layouts reutilizables
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   └── footer.php
│   ├── modules/              # Módulos del ERP
│   │   ├── dashboard/
│   │   ├── configuracion/
│   │   └── ...
│   └── router.php            # Router centralizado
├── config/                    # Configuración
│   ├── app.php
│   ├── database.php
│   └── security.php
├── database/                  # Esquemas SQL
│   ├── schema_completo.sql
│   └── ...
├── public/                    # Archivos públicos
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   ├── index.php            # Portada
│   ├── login.php
│   ├── register.php
│   └── logout.php
└── logs/                      # Logs del sistema
```

## 🚀 Instalación

### Requisitos

- PHP 8.0+
- PostgreSQL 13+ o MySQL 8.0+
- Apache/Nginx
- Extensiones PHP: PDO, pgsql/mysqli, mbstring, json

### Pasos

1. **Clonar repositorio**
```bash
git clone https://github.com/tu-usuario/conectaerp.git
cd conectaerp
```

2. **Configurar base de datos**
```bash
# PostgreSQL
psql -U postgres -d conectaerp < database/schema_completo.sql
psql -U postgres -d conectaerp < database/06_auxiliares_y_entidades.sql
psql -U postgres -d conectaerp < database/07_core_modulos.sql
```

3. **Configurar aplicación**
```bash
cp config/database.example.php config/database.php
# Editar config/database.php con tus credenciales
```

4. **Configurar permisos**
```bash
chmod 755 logs/
chmod 755 storage/
```

5. **Acceder al sistema**
```
http://localhost/conectaerp/public/
```

## 📋 Planes Disponibles

### 🟨 Starter (Gratis)
- 1 empresa
- 1 usuario
- Productos ilimitados
- Facturación básica
- Inventario básico
- Trial 14 días

### 🟩 Profesional ($49.990/mes)
- 1 empresa
- 5 usuarios
- Contabilidad completa
- Inventario avanzado
- Reportes
- Soporte email + chat

### 🟦 Empresa ($99.990/mes)
- 1 empresa
- Usuarios ilimitados
- Producción
- Multi moneda
- Integración SII
- Soporte prioritario

### 🟥 Corporativo (Contactar)
- Multiempresa
- Multi sucursal
- BI Avanzado
- Integraciones
- Soporte premium 24/7

## 🔒 Seguridad

### Características de Seguridad

- ✅ Contraseñas hasheadas con Argon2id
- ✅ Validación de RUT chileno
- ✅ Protección CSRF
- ✅ Sesiones seguras con regeneración
- ✅ Rate limiting en login
- ✅ Bloqueo automático por intentos fallidos
- ✅ IP whitelist para superadmin
- ✅ Auditoría de todas las acciones
- ✅ 2FA opcional
- ✅ Recuperación segura de contraseñas (token único 15 min)

### Política de Contraseñas

- Mínimo 12 caracteres
- Al menos 1 mayúscula
- Al menos 1 minúscula
- Al menos 1 número
- Al menos 1 símbolo
- Prohibido reutilizar contraseñas
- Prohibido usar RUT o email en la clave

## 📊 Base de Datos

### Tablas Principales

- **Seguridad**: usuarios_acceso, sesiones_usuario, intentos_login, auditoria_eventos
- **Empresas**: empresas, sucursales, centros_costo
- **Trial**: trial_configuracion, trial_historial
- **Planes**: planes, suscripciones, suscripcion_pagos
- **Entidades**: auxiliares (clientes/proveedores), productos
- **Operaciones**: ventas, compras, movimientos_inventario
- **Auditoría**: auditoria_eventos, auditoria_cambios

## 🌍 Internacionalización

### Idiomas Soportados

- 🇪🇸 Español
- 🇬🇧 English
- 🇧🇷 Português
- 🇫🇷 Français
- 🇩🇪 Deutsch
- 🇮🇹 Italiano
- 🇨🇳 中文 (Chino)
- 🇯🇵 日本語 (Japonés)
- 🇰🇷 한국어 (Coreano)
- 🇮🇳 हिन्दी (Hindi)

### Monedas Principales

- CLP (Peso Chileno)
- USD (Dólar)
- EUR (Euro)
- UF (Unidad de Fomento)
- BRL, ARS, PEN, COP, MXN

## 🛠️ Desarrollo

### Agregar Nuevo Módulo

1. Crear carpeta en `app/modules/nombre_modulo/`
2. Crear `index.php` en la carpeta
3. Agregar en `config/app.php` módulos
4. Actualizar sidebar en `app/layout/sidebar.php`

### Agregar Traducción

1. Editar `app/lang/{idioma}.php`
2. Agregar clave => valor

## 📞 Contacto y Soporte

- **Email**: contacto@conectaerp.com
- **Teléfono**: +56 9 8574 5559
- **Horario**: Lunes a Viernes 9:00 - 18:00

## 📄 Licencia

© 2026 Conecta ERP - Todos los derechos reservados

## 🎯 Roadmap

### Fase 1 - Core (Completado)
- ✅ Sistema de autenticación
- ✅ Trial 14 días
- ✅ Gestión de empresas
- ✅ Productos y auxiliares
- ✅ Ventas y compras básicas

### Fase 2 - Avanzado
- [ ] Contabilidad completa
- [ ] Integración SII
- [ ] Integración Previred
- [ ] Reportes avanzados
- [ ] BI y dashboards

### Fase 3 - Enterprise
- [ ] CRM completo
- [ ] Producción y MRP
- [ ] Multi-empresa
- [ ] API REST
- [ ] App móvil

---

**Conecta ERP** - Sistema ERP Enterprise de nivel mundial 🚀
