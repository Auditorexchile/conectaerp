# Conecta ERP

Sistema ERP integral para empresas modernas en Chile.

## Características principales

- **Portada corporativa** con presentación de planes y beneficios
- **Sistema de registro** completo con validación de RUT chileno
- **Login seguro** con soporte para MFA opcional
- **4 Planes disponibles**: Starter, Profesional, Empresa y Corporativo
- **Diseño moderno** con Tailwind CSS
- **Validación en tiempo real** para formularios

## Stack tecnológico

- **Frontend**: Next.js 14 + TypeScript
- **Estilos**: Tailwind CSS
- **Validación**: React Hook Form + Zod
- **Base de datos**: PostgreSQL (esquema completo incluido)

## Estructura del proyecto

```
conectaerp/
├── src/
│   ├── app/
│   │   ├── page.tsx           # Portada principal
│   │   ├── login/             # Página de login
│   │   ├── register/          # Página de registro (5 secciones)
│   │   ├── layout.tsx         # Layout principal
│   │   └── globals.css        # Estilos globales
│   ├── components/
│   │   ├── Header.tsx         # Header reutilizable con scroll effect
│   │   └── Footer.tsx         # Footer reutilizable
│   └── utils/
│       └── rutValidator.ts    # Validador de RUT chileno
├── database/
│   └── schema.sql             # Esquema completo de BD
├── package.json
├── tsconfig.json
├── tailwind.config.ts
└── next.config.js
```

## Características implementadas

### Portada

- ✅ Header fijo con efecto transparente/sólido al scroll
- ✅ Hero principal con CTA
- ✅ Sección de beneficios (4 cards)
- ✅ Sección de planes (4 planes: Starter, Profesional, Empresa, Corporativo)
- ✅ CTA final
- ✅ Footer reutilizable

### Login

- ✅ Validación de RUT de empresa
- ✅ Campo de email
- ✅ Campo de contraseña con toggle show/hide
- ✅ Link de recuperación de contraseña
- ✅ Validación en tiempo real
- ✅ Mensajes de error contextuales

### Registro

- ✅ Formulario multi-sección (5 pasos)
- ✅ Barra de progreso visual
- ✅ **Sección 1**: Datos de empresa (RUT, razón social, nombre fantasía, giro, dirección)
- ✅ **Sección 2**: Representante legal (nombre, RUT, email, teléfono)
- ✅ **Sección 3**: Seguridad (contraseña con indicador de fortaleza, generador de contraseña)
- ✅ **Sección 4**: Selección de plan con resumen
- ✅ **Sección 5**: Confirmación y aceptación de términos
- ✅ Validación de RUT chileno con formato automático
- ✅ Navegación entre secciones

### Utilidades

- ✅ Validador de RUT chileno completo
- ✅ Formateo automático de RUT (XX.XXX.XXX-X)
- ✅ Cálculo de dígito verificador
- ✅ Validación en tiempo real

### Base de datos

- ✅ Esquema SQL completo con todas las tablas
- ✅ Tablas de portada y contenido público
- ✅ Tablas de planes y suscripciones
- ✅ Tablas de empresas y representantes legales
- ✅ Tablas de acceso y autenticación
- ✅ Sistema de auditoría completo
- ✅ Índices optimizados
- ✅ Datos iniciales (estados, planes, características)

## Instalación

```bash
# Instalar dependencias
npm install

# Ejecutar en modo desarrollo
npm run dev

# Compilar para producción
npm run build

# Ejecutar en producción
npm start
```

## Base de datos

Para crear la base de datos, ejecuta el archivo SQL:

```bash
psql -U usuario -d conectaerp < database/schema.sql
```

## Planes disponibles

### Starter (Gratis)
- 1 empresa
- 1 usuario
- Productos ilimitados
- Facturación básica
- Inventario básico
- Soporte email

### Profesional ($49.990/mes)
- 1 empresa
- Hasta 5 usuarios
- Contabilidad completa
- Inventario avanzado
- Compras y ventas
- Reportes
- Soporte email + chat

### Empresa ($99.990/mes)
- 1 empresa
- Usuarios ilimitados
- Producción
- Costos
- Proyectos
- Multi moneda
- Integración SII
- Soporte prioritario

### Corporativo (Contactar)
- Multiempresa
- Multi sucursal
- Control gestión
- BI
- Integraciones
- SLA dedicado
- Soporte premium 24/7

## Próximos pasos

- [ ] Implementar backend API con autenticación
- [ ] Conectar formularios con API
- [ ] Implementar dashboard inicial post-login
- [ ] Agregar onboarding de primera vez
- [ ] Implementar sistema de pago
- [ ] Integración con SII para facturación electrónica
- [ ] Módulos del ERP (Contabilidad, Ventas, Inventario, etc.)

## Contacto

- Email: contacto@conectaerp.com
- Teléfono: +56 9 8574 5559

## Licencia

Todos los derechos reservados - Conecta ERP 2026
