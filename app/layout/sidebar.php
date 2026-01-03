<?php
/**
 * CONECTA ERP - SIDEBAR
 * Menú lateral con todos los módulos del sistema
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$currentModule = $_GET['module'] ?? 'dashboard';
$currentSub = $_GET['sub'] ?? '';
?>
<aside class="erp-sidebar" id="erpSidebar">
    <div class="sidebar-content">
        <!-- Menú de navegación -->
        <nav class="sidebar-nav">

            <!-- Dashboard -->
            <a href="/app/router.php?module=dashboard" class="nav-item <?= $currentModule === 'dashboard' ? 'active' : '' ?>">
                <span class="nav-icon">🏠</span>
                <span class="nav-text">Dashboard</span>
            </a>

            <!-- MÓDULO IA - Auditoría Inteligente -->
            <div class="nav-section <?= $currentModule === 'ia' ? 'active' : '' ?>">
                <div class="nav-section-title">
                    <span class="nav-icon">🤖</span>
                    <span class="nav-text">Auditoría IA</span>
                    <span class="nav-arrow">›</span>
                </div>
                <div class="nav-submenu">
                    <a href="/app/router.php?module=ia&sub=dashboard" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'dashboard' ? 'active' : '' ?>">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">Dashboard IA</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=auditoria_contable" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'auditoria_contable' ? 'active' : '' ?>">
                        <span class="nav-icon">📚</span>
                        <span class="nav-text">Auditoría Contable</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=deteccion_fraude" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'deteccion_fraude' ? 'active' : '' ?>">
                        <span class="nav-icon">🔍</span>
                        <span class="nav-text">Detección de Fraude</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=auditoria_tributaria" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'auditoria_tributaria' ? 'active' : '' ?>">
                        <span class="nav-icon">💼</span>
                        <span class="nav-text">Auditoría Tributaria</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=cumplimiento_ifrs" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'cumplimiento_ifrs' ? 'active' : '' ?>">
                        <span class="nav-icon">📋</span>
                        <span class="nav-text">Cumplimiento IFRS</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=auditoria_tesoreria" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'auditoria_tesoreria' ? 'active' : '' ?>">
                        <span class="nav-icon">💰</span>
                        <span class="nav-text">Auditoría Tesorería</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=auditoria_rrhh" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'auditoria_rrhh' ? 'active' : '' ?>">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">Auditoría RRHH</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=analisis_predictivo" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'analisis_predictivo' ? 'active' : '' ?>">
                        <span class="nav-icon">📈</span>
                        <span class="nav-text">Análisis Predictivo</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=motor_alertas" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'motor_alertas' ? 'active' : '' ?>">
                        <span class="nav-icon">🔔</span>
                        <span class="nav-text">Motor de Alertas</span>
                    </a>
                    <a href="/app/router.php?module=ia&sub=chat_auditor" class="nav-subitem <?= $currentModule === 'ia' && $currentSub === 'chat_auditor' ? 'active' : '' ?>">
                        <span class="nav-icon">💬</span>
                        <span class="nav-text">Chat Auditor IA</span>
                    </a>
                </div>
            </div>

            <!-- FI - Contabilidad -->
            <div class="nav-section <?= $currentModule === 'fi' ? 'active' : '' ?>">
                <div class="nav-section-title">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Contabilidad (FI)</span>
                    <span class="nav-arrow">›</span>
                </div>
                <div class="nav-submenu">
                    <a href="/app/router.php?module=fi&sub=plan_cuentas" class="nav-subitem <?= $currentModule === 'fi' && $currentSub === 'plan_cuentas' ? 'active' : '' ?>">
                        <span class="nav-text">Plan de Cuentas</span>
                    </a>
                    <a href="/app/router.php?module=fi&sub=comprobantes" class="nav-subitem <?= $currentModule === 'fi' && $currentSub === 'comprobantes' ? 'active' : '' ?>">
                        <span class="nav-text">Comprobantes</span>
                    </a>
                    <a href="/app/router.php?module=fi&sub=libro_diario" class="nav-subitem <?= $currentModule === 'fi' && $currentSub === 'libro_diario' ? 'active' : '' ?>">
                        <span class="nav-text">Libro Diario</span>
                    </a>
                    <a href="/app/router.php?module=fi&sub=libro_mayor" class="nav-subitem <?= $currentModule === 'fi' && $currentSub === 'libro_mayor' ? 'active' : '' ?>">
                        <span class="nav-text">Libro Mayor</span>
                    </a>
                    <a href="/app/router.php?module=fi&sub=balance" class="nav-subitem <?= $currentModule === 'fi' && $currentSub === 'balance' ? 'active' : '' ?>">
                        <span class="nav-text">Balance</span>
                    </a>
                    <a href="/app/router.php?module=fi&sub=estados_financieros" class="nav-subitem <?= $currentModule === 'fi' && $currentSub === 'estados_financieros' ? 'active' : '' ?>">
                        <span class="nav-text">Estados Financieros</span>
                    </a>
                </div>
            </div>

            <!-- SD - Ventas -->
            <div class="nav-section <?= $currentModule === 'sd' ? 'active' : '' ?>">
                <div class="nav-section-title">
                    <span class="nav-icon">🛒</span>
                    <span class="nav-text">Ventas (SD)</span>
                    <span class="nav-arrow">›</span>
                </div>
                <div class="nav-submenu">
                    <a href="/app/router.php?module=sd&sub=clientes" class="nav-subitem <?= $currentModule === 'sd' && $currentSub === 'clientes' ? 'active' : '' ?>">
                        <span class="nav-text">Clientes</span>
                    </a>
                    <a href="/app/router.php?module=sd&sub=cotizaciones" class="nav-subitem <?= $currentModule === 'sd' && $currentSub === 'cotizaciones' ? 'active' : '' ?>">
                        <span class="nav-text">Cotizaciones</span>
                    </a>
                    <a href="/app/router.php?module=sd&sub=pedidos" class="nav-subitem <?= $currentModule === 'sd' && $currentSub === 'pedidos' ? 'active' : '' ?>">
                        <span class="nav-text">Pedidos de Venta</span>
                    </a>
                    <a href="/app/router.php?module=sd&sub=facturas" class="nav-subitem <?= $currentModule === 'sd' && $currentSub === 'facturas' ? 'active' : '' ?>">
                        <span class="nav-text">Facturas</span>
                    </a>
                    <a href="/app/router.php?module=sd&sub=notas_credito" class="nav-subitem <?= $currentModule === 'sd' && $currentSub === 'notas_credito' ? 'active' : '' ?>">
                        <span class="nav-text">Notas de Crédito</span>
                    </a>
                    <a href="/app/router.php?module=sd&sub=guias_despacho" class="nav-subitem <?= $currentModule === 'sd' && $currentSub === 'guias_despacho' ? 'active' : '' ?>">
                        <span class="nav-text">Guías de Despacho</span>
                    </a>
                </div>
            </div>

            <!-- MM - Materiales/Inventario -->
            <div class="nav-section <?= $currentModule === 'mm' ? 'active' : '' ?>">
                <div class="nav-section-title">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Inventario (MM)</span>
                    <span class="nav-arrow">›</span>
                </div>
                <div class="nav-submenu">
                    <a href="/app/router.php?module=mm&sub=productos" class="nav-subitem <?= $currentModule === 'mm' && $currentSub === 'productos' ? 'active' : '' ?>">
                        <span class="nav-text">Productos</span>
                    </a>
                    <a href="/app/router.php?module=mm&sub=categorias" class="nav-subitem <?= $currentModule === 'mm' && $currentSub === 'categorias' ? 'active' : '' ?>">
                        <span class="nav-text">Categorías</span>
                    </a>
                    <a href="/app/router.php?module=mm&sub=bodegas" class="nav-subitem <?= $currentModule === 'mm' && $currentSub === 'bodegas' ? 'active' : '' ?>">
                        <span class="nav-text">Bodegas</span>
                    </a>
                    <a href="/app/router.php?module=mm&sub=stock" class="nav-subitem <?= $currentModule === 'mm' && $currentSub === 'stock' ? 'active' : '' ?>">
                        <span class="nav-text">Control de Stock</span>
                    </a>
                    <a href="/app/router.php?module=mm&sub=movimientos" class="nav-subitem <?= $currentModule === 'mm' && $currentSub === 'movimientos' ? 'active' : '' ?>">
                        <span class="nav-text">Movimientos</span>
                    </a>
                    <a href="/app/router.php?module=mm&sub=inventarios" class="nav-subitem <?= $currentModule === 'mm' && $currentSub === 'inventarios' ? 'active' : '' ?>">
                        <span class="nav-text">Inventarios Físicos</span>
                    </a>
                </div>
            </div>

            <!-- HR - Recursos Humanos -->
            <div class="nav-section <?= $currentModule === 'hr' ? 'active' : '' ?>">
                <div class="nav-section-title">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">RRHH (HR)</span>
                    <span class="nav-arrow">›</span>
                </div>
                <div class="nav-submenu">
                    <a href="/app/router.php?module=hr&sub=empleados" class="nav-subitem <?= $currentModule === 'hr' && $currentSub === 'empleados' ? 'active' : '' ?>">
                        <span class="nav-text">Empleados</span>
                    </a>
                    <a href="/app/router.php?module=hr&sub=contratos" class="nav-subitem <?= $currentModule === 'hr' && $currentSub === 'contratos' ? 'active' : '' ?>">
                        <span class="nav-text">Contratos</span>
                    </a>
                    <a href="/app/router.php?module=hr&sub=asistencia" class="nav-subitem <?= $currentModule === 'hr' && $currentSub === 'asistencia' ? 'active' : '' ?>">
                        <span class="nav-text">Asistencia</span>
                    </a>
                    <a href="/app/router.php?module=hr&sub=liquidaciones" class="nav-subitem <?= $currentModule === 'hr' && $currentSub === 'liquidaciones' ? 'active' : '' ?>">
                        <span class="nav-text">Liquidaciones</span>
                    </a>
                    <a href="/app/router.php?module=hr&sub=vacaciones" class="nav-subitem <?= $currentModule === 'hr' && $currentSub === 'vacaciones' ? 'active' : '' ?>">
                        <span class="nav-text">Vacaciones</span>
                    </a>
                    <a href="/app/router.php?module=hr&sub=previred" class="nav-subitem <?= $currentModule === 'hr' && $currentSub === 'previred' ? 'active' : '' ?>">
                        <span class="nav-text">Previred/AFP</span>
                    </a>
                </div>
            </div>

            <!-- CRM -->
            <a href="/app/router.php?module=crm" class="nav-item <?= $currentModule === 'crm' ? 'active' : '' ?>">
                <span class="nav-icon">📞</span>
                <span class="nav-text">CRM</span>
            </a>

            <!-- Proyectos -->
            <a href="/app/router.php?module=proyectos" class="nav-item <?= $currentModule === 'proyectos' ? 'active' : '' ?>">
                <span class="nav-icon">📁</span>
                <span class="nav-text">Proyectos</span>
            </a>

            <!-- BI - Business Intelligence -->
            <a href="/app/router.php?module=bi" class="nav-item <?= $currentModule === 'bi' ? 'active' : '' ?>">
                <span class="nav-icon">📈</span>
                <span class="nav-text">BI / Reportes</span>
            </a>

            <!-- Configuración -->
            <div class="nav-section <?= $currentModule === 'config' ? 'active' : '' ?>">
                <div class="nav-section-title">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-text">Configuración</span>
                    <span class="nav-arrow">›</span>
                </div>
                <div class="nav-submenu">
                    <a href="/app/router.php?module=config&sub=empresa" class="nav-subitem <?= $currentModule === 'config' && $currentSub === 'empresa' ? 'active' : '' ?>">
                        <span class="nav-text">Empresa</span>
                    </a>
                    <a href="/app/router.php?module=config&sub=usuarios" class="nav-subitem <?= $currentModule === 'config' && $currentSub === 'usuarios' ? 'active' : '' ?>">
                        <span class="nav-text">Usuarios</span>
                    </a>
                    <a href="/app/router.php?module=config&sub=sucursales" class="nav-subitem <?= $currentModule === 'config' && $currentSub === 'sucursales' ? 'active' : '' ?>">
                        <span class="nav-text">Sucursales</span>
                    </a>
                    <a href="/app/router.php?module=config&sub=tipos_cambio" class="nav-subitem <?= $currentModule === 'config' && $currentSub === 'tipos_cambio' ? 'active' : '' ?>">
                        <span class="nav-text">Tipos de Cambio</span>
                    </a>
                    <a href="/app/router.php?module=config&sub=plan" class="nav-subitem <?= $currentModule === 'config' && $currentSub === 'plan' ? 'active' : '' ?>">
                        <span class="nav-text">Plan y Facturación</span>
                    </a>
                </div>
            </div>

        </nav>
    </div>

    <!-- Footer del sidebar -->
    <div class="sidebar-footer">
        <button class="focus-mode-btn" onclick="toggleFocusMode()" title="Modo enfoque">
            <span class="icon">🎯</span>
            <span class="text">Modo Enfoque</span>
        </button>
    </div>
</aside>

<script>
// Toggle submenu
document.querySelectorAll('.nav-section-title').forEach(title => {
    title.addEventListener('click', function() {
        const section = this.closest('.nav-section');
        const isActive = section.classList.contains('active');

        // Close all sections
        document.querySelectorAll('.nav-section').forEach(s => s.classList.remove('active'));

        // Toggle current section
        if (!isActive) {
            section.classList.add('active');
        }
    });
});

// Focus mode
function toggleFocusMode() {
    document.body.classList.toggle('focus-mode');
    localStorage.setItem('focusMode', document.body.classList.contains('focus-mode'));
}

// Restore focus mode
if (localStorage.getItem('focusMode') === 'true') {
    document.body.classList.add('focus-mode');
}
</script>
