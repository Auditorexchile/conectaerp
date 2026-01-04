<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');

$moduleName = [
    'fi' => 'Finanzas (FI)',
    'sd' => 'Ventas (SD)',
    'mm' => 'Inventario (MM)',
    'hr' => 'RRHH (HR)',
    'crm' => 'CRM'
];

$moduleIcons = [
    'fi' => '📊',
    'sd' => '🛒',
    'mm' => '📦',
    'hr' => '👥',
    'crm' => '📞'
];

$currentModule = basename(dirname(__FILE__));
$pageTitle = $moduleName[$currentModule] ?? 'Módulo';
?>
<div class="page-header">
    <div class="page-title">
        <h1><?= $moduleIcons[$currentModule] ?? '📋' ?> <?= e($pageTitle) ?></h1>
        <p>Módulo en desarrollo</p>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <span class="icon" style="font-size: 4rem;">🚧</span>
            <h2>Módulo en Desarrollo</h2>
            <p style="font-size: 1.1rem; margin: 1rem 0;">
                Este módulo está siendo desarrollado y estará disponible próximamente.
            </p>
            <p style="color: var(--text-secondary);">
                <strong>Módulo:</strong> <?= e($pageTitle) ?><br>
                <strong>Estado:</strong> En construcción
            </p>
            <a href="/app/router.php?module=dashboard" class="btn btn-primary" style="margin-top: 1.5rem;">
                ← Volver al Dashboard
            </a>
        </div>
    </div>
</div>
