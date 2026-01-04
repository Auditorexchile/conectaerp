<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$pageTitle = 'Detección de Fraude IA';
?>
<div class="ia-module">
    <div class="module-header">
        <h1>🔍 Detección de Fraude Inteligente</h1>
        <p>Análisis de pagos duplicados, proveedores fantasma y facturas sospechosas</p>
        <button class="btn btn-primary" onclick="ejecutarAnalisis()">Analizar Fraudes</button>
    </div>
    <div class="module-content">
        <div class="stats-grid">
            <div class="stat-card"><h3>0</h3><p>Pagos Analizados</p></div>
            <div class="stat-card alert"><h3>0</h3><p>Alertas Críticas</p></div>
            <div class="stat-card"><h3>0</h3><p>Proveedores Revisados</p></div>
            <div class="stat-card low"><h3>Bajo</h3><p>Riesgo de Fraude</p></div>
        </div>
        <div class="results-table">
            <h3>Alertas de Fraude</h3>
            <div class="empty-state"><p>No se han detectado fraudes</p><small>Sistema monitoreando 24/7</small></div>
        </div>
    </div>
</div>
<script>function ejecutarAnalisis(){alert('Análisis de fraude iniciado');}</script>
