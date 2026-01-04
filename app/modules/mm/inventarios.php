<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$pageTitle = str_replace('_', ' ', ucfirst(basename(__FILE__, '.php')));
?>
<div class="page-header">
    <div class="page-title">
        <h1>📦 <?= e($pageTitle) ?></h1>
        <p>Módulo de Inventario</p>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <span class="icon" style="font-size: 3rem;">🚧</span>
            <h3>Submódulo en Desarrollo</h3>
            <p>Este submódulo estará disponible próximamente.</p>
        </div>
    </div>
</div>
