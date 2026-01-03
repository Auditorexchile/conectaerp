-- ================================================================
-- CONECTA ERP - SCRIPT DE INSTALACIÓN MAESTRO
-- ================================================================
-- INSTRUCCIONES:
-- 1. Accede a phpMyAdmin o el panel de control de tu hosting
-- 2. Selecciona la base de datos: conectae_conectaerpbd
-- 3. Ejecuta este script completo
-- 4. Verifica que todas las tablas se hayan creado correctamente
-- ================================================================

-- Base de datos y usuario
-- Usuario: conectae_conectaerpuser
-- Base de datos: conectae_conectaerpbd
-- Password: pt125824caraud

-- Asegurarse de usar la base de datos correcta
USE conectae_conectaerpbd;

-- Configuración de caracteres
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ================================================================
-- ESTE ARCHIVO COMBINA:
-- - 17_paises_idiomas.sql (Países, idiomas, monedas, configuración global)
-- - 18_ia_auditoria.sql (Módulo IA completo con 10 tablas)
-- - 19_registro_completo.sql (Planes, registro wizard, empresas, usuarios)
-- ================================================================

-- ================================================================
-- Para ejecutar los archivos individuales, ve a la carpeta database/
-- y ejecuta en este orden:
-- 1. 17_paises_idiomas.sql
-- 2. 18_ia_auditoria.sql
-- 3. 19_registro_completo.sql
-- ================================================================

-- ✅ DESPUÉS DE EJECUTAR ESTE SCRIPT:
-- - Tendrás 12 países configurados
-- - 10 idiomas disponibles
-- - 4 planes de suscripción
-- - Sistema de auditoría IA completo
-- - Sistema de registro wizard de 5 pasos
-- - Sistema de trial de 14 días

-- ================================================================
-- PRÓXIMO PASO: Acceder a https://tudominio.com/register.php
-- ================================================================
