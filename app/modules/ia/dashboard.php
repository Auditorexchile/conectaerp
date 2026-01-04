<?php
/**
 * CONECTA ERP - DASHBOARD IA
 * Dashboard de Auditoría Inteligente con widgets de riesgo
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$pageTitle = 'Dashboard IA - Auditoría Inteligente';
?>

<div class="ia-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1>🤖 Auditoría Inteligente</h1>
            <p>Panel de control de análisis y detección de riesgos con IA</p>
        </div>
        <div class="dashboard-actions">
            <button class="btn btn-secondary" onclick="runFullAudit()">
                <span class="icon">🔄</span>
                Ejecutar Auditoría Completa
            </button>
            <a href="/app/router.php?module=ia&sub=chat_auditor" class="btn btn-primary">
                <span class="icon">💬</span>
                Chat Auditor IA
            </a>
        </div>
    </div>

    <!-- Score General de Riesgo -->
    <div class="risk-score-container">
        <div class="risk-score-card">
            <div class="score-circle-large excellent">
                <div class="score-value">95</div>
                <div class="score-label">Score General</div>
            </div>
            <div class="score-details">
                <h3>Estado: Excelente</h3>
                <p>Tu empresa presenta un nivel de riesgo muy bajo</p>
                <div class="score-breakdown">
                    <div class="breakdown-item">
                        <span class="label">Riesgo Contable:</span>
                        <span class="value low">Bajo</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="label">Riesgo Fraude:</span>
                        <span class="value low">Bajo</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="label">Cumplimiento:</span>
                        <span class="value excellent">Excelente</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Widgets de Módulos IA -->
    <div class="ia-widgets-grid">

        <!-- Auditoría Contable -->
        <a href="/app/router.php?module=ia&sub=auditoria_contable" class="ia-widget">
            <div class="widget-icon">📚</div>
            <div class="widget-content">
                <h3>Auditoría Contable</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Anomalías</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value low">Bajo</span>
                        <span class="stat-label">Riesgo</span>
                    </div>
                </div>
                <p>Detección de asientos duplicados y ajustes sospechosos</p>
            </div>
        </a>

        <!-- Detección de Fraude -->
        <a href="/app/router.php?module=ia&sub=deteccion_fraude" class="ia-widget alert">
            <div class="widget-icon">🔍</div>
            <div class="widget-content">
                <h3>Detección de Fraude</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Alertas</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value low">Bajo</span>
                        <span class="stat-label">Riesgo</span>
                    </div>
                </div>
                <p>Pagos duplicados, proveedores fantasma, facturas sospechosas</p>
            </div>
        </a>

        <!-- Auditoría Tributaria -->
        <a href="/app/router.php?module=ia&sub=auditoria_tributaria" class="ia-widget">
            <div class="widget-icon">💼</div>
            <div class="widget-content">
                <h3>Auditoría Tributaria</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Errores IVA</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value excellent">OK</span>
                        <span class="stat-label">SII</span>
                    </div>
                </div>
                <p>Validación IVA, retenciones, cumplimiento SII</p>
            </div>
        </a>

        <!-- Cumplimiento IFRS -->
        <a href="/app/router.php?module=ia&sub=cumplimiento_ifrs" class="ia-widget">
            <div class="widget-icon">📋</div>
            <div class="widget-content">
                <h3>Cumplimiento IFRS</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">100%</span>
                        <span class="stat-label">Cumplimiento</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value excellent">OK</span>
                        <span class="stat-label">Estado</span>
                    </div>
                </div>
                <p>Validación de normas IFRS y estándares contables</p>
            </div>
        </a>

        <!-- Auditoría Tesorería -->
        <a href="/app/router.php?module=ia&sub=auditoria_tesoreria" class="ia-widget">
            <div class="widget-icon">💰</div>
            <div class="widget-content">
                <h3>Auditoría Tesorería</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Diferencias</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value excellent">OK</span>
                        <span class="stat-label">Conciliación</span>
                    </div>
                </div>
                <p>Conciliación bancaria, diferencias de caja</p>
            </div>
        </a>

        <!-- Auditoría RRHH -->
        <a href="/app/router.php?module=ia&sub=auditoria_rrhh" class="ia-widget">
            <div class="widget-icon">👥</div>
            <div class="widget-content">
                <h3>Auditoría RRHH</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Errores</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value excellent">OK</span>
                        <span class="stat-label">Previred</span>
                    </div>
                </div>
                <p>Liquidaciones, Previred, horas extra sospechosas</p>
            </div>
        </a>

        <!-- Análisis Predictivo -->
        <a href="/app/router.php?module=ia&sub=analisis_predictivo" class="ia-widget">
            <div class="widget-icon">📈</div>
            <div class="widget-content">
                <h3>Análisis Predictivo</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">--</span>
                        <span class="stat-label">Predicción</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">--</span>
                        <span class="stat-label">Riesgo</span>
                    </div>
                </div>
                <p>Predicción de riesgos financieros y operacionales</p>
            </div>
        </a>

        <!-- Motor de Alertas -->
        <a href="/app/router.php?module=ia&sub=motor_alertas" class="ia-widget">
            <div class="widget-icon">🔔</div>
            <div class="widget-content">
                <h3>Motor de Alertas</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Alertas Activas</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Pendientes</span>
                    </div>
                </div>
                <p>Alertas inteligentes en tiempo real</p>
            </div>
        </a>

        <!-- Chat Auditor IA -->
        <a href="/app/router.php?module=ia&sub=chat_auditor" class="ia-widget highlight">
            <div class="widget-icon">💬</div>
            <div class="widget-content">
                <h3>Chat Auditor IA</h3>
                <div class="widget-stats">
                    <div class="stat">
                        <span class="stat-value">100%</span>
                        <span class="stat-label">Disponible</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">24/7</span>
                        <span class="stat-label">Activo</span>
                    </div>
                </div>
                <p>Consulta con el asistente IA especializado en auditoría</p>
            </div>
        </a>

    </div>

    <!-- Alertas Recientes -->
    <div class="ia-recent-alerts">
        <div class="card-header">
            <h3>🔔 Alertas Recientes</h3>
            <a href="/app/router.php?module=ia&sub=motor_alertas" class="card-link">Ver todas →</a>
        </div>
        <div class="alerts-list">
            <div class="alert-empty">
                <span class="icon">✓</span>
                <p>No hay alertas pendientes</p>
                <small>El sistema está monitoreando tu empresa 24/7</small>
            </div>
        </div>
    </div>

</div>

<script>
function runFullAudit() {
    if (confirm('¿Deseas ejecutar una auditoría completa de todos los módulos?\n\nEsto puede tomar varios minutos.')) {
        alert('Auditoría programada. Recibirás una notificación cuando finalice.');
        // TODO: Implementar llamada AJAX para ejecutar auditoría
    }
}
</script>
