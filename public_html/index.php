<?php
/**
 * INDEX.PHP - Portada Conecta ERP
 * Landing page profesional ERP Enterprise
 */

require_once 'config/database.php';

$lang = 'es';
$db = Database::getInstance();
$planes = $db->fetchAll("SELECT * FROM planes WHERE activo = 1 ORDER BY id");
$paises = $db->fetchAll("SELECT COUNT(*) as total FROM paises WHERE activo = 1");
$idiomas = $db->fetchAll("SELECT COUNT(*) as total FROM idiomas WHERE activo = 1");
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Conecta ERP - Sistema de gestión empresarial integral. Contabilidad, ventas, inventario, producción, RRHH y control total. Solución ERP multinacional con integración SII, Previred y más.">
    <meta name="keywords" content="ERP, sistema de gestión, contabilidad, facturación electrónica, SII, Previred, software empresarial">
    <title>Conecta ERP - Sistema de Gestión Empresarial Integral | ERP Enterprise</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/portada.css">
</head>
<body>
    <!-- HEADER -->
    <header class="header" id="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="assets/img/logo.png" alt="Conecta ERP" class="logo-img" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                    <span class="logo-text" style="display:none;font-size:1.5rem;font-weight:700;color:#fff;">Conecta ERP</span>
                </div>

                <nav class="nav-menu">
                    <a href="#inicio" class="nav-link">Inicio</a>
                    <a href="#modulos" class="nav-link">Módulos</a>
                    <a href="#planes" class="nav-link">Planes</a>
                    <a href="#integraciones" class="nav-link">Integraciones</a>
                    <a href="#contacto" class="nav-link">Contacto</a>
                </nav>

                <div class="header-actions">
                    <a href="login.php" class="btn btn-secondary">Ingresar</a>
                    <a href="register.php" class="btn btn-primary">Prueba Gratis 14 Días</a>
                </div>

                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero" id="inicio">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-badge">🏆 ERP #1 para empresas en crecimiento</div>
                    <h1 class="hero-title">Sistema ERP Integral para Gestión Empresarial Completa</h1>
                    <p class="hero-subtitle">
                        Controla contabilidad, ventas, inventario, producción, RRHH y todos los procesos de tu empresa desde una sola plataforma.
                        Integración directa con SII, Previred y organismos fiscales de 12 países.
                    </p>
                    <div class="hero-features">
                        <div class="hero-feature">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>14 días gratis sin tarjeta</span>
                        </div>
                        <div class="hero-feature">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Soporte en español 24/7</span>
                        </div>
                        <div class="hero-feature">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Actualizaciones incluidas</span>
                        </div>
                    </div>
                    <div class="hero-actions">
                        <a href="register.php" class="btn btn-primary btn-lg">
                            Comenzar Ahora →
                        </a>
                        <a href="#demo" class="btn btn-secondary btn-lg">
                            Ver Demo en Vivo
                        </a>
                    </div>
                    <p class="hero-trust">🔒 Datos seguros y encriptados. Cumplimiento total con normativas fiscales.</p>
                </div>

                <div class="hero-image">
                    <img src="assets/img/dashboard-preview.png" alt="Dashboard Conecta ERP" class="dashboard-preview" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\'%3E%3Crect fill=\'%23f3f4f6\' width=\'800\' height=\'600\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-family=\'Arial\' font-size=\'24\' fill=\'%236b7280\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EDashboard Preview%3C/text%3E%3C/svg%3E'">
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number"><?= $paises[0]['total'] ?? 12 ?></div>
                            <div class="stat-label">Países</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $idiomas[0]['total'] ?? 10 ?></div>
                            <div class="stat-label">Idiomas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">14</div>
                            <div class="stat-label">Módulos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MÓDULOS ERP -->
    <section class="modulos" id="modulos">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Módulos Empresariales Completos</h2>
                <p class="section-subtitle">Todas las herramientas que necesitas para gestionar tu empresa en un solo sistema integrado</p>
            </div>

            <div class="modulos-grid">
                <div class="modulo-card">
                    <div class="modulo-icon">📊</div>
                    <h3>Contabilidad Integral</h3>
                    <p>Plan de cuentas, asientos contables, balances, estados financieros, IFRS y cumplimiento tributario completo.</p>
                    <ul>
                        <li>Libro Diario y Mayor</li>
                        <li>Balance 8 columnas</li>
                        <li>Estados financieros</li>
                        <li>Centro de costos</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">🧾</div>
                    <h3>Ventas & Facturación</h3>
                    <p>Cotizaciones, pedidos, facturación electrónica, boletas, notas de crédito y débito. Integración SII automática.</p>
                    <ul>
                        <li>Facturación electrónica (DTE)</li>
                        <li>POS integrado</li>
                        <li>Control de folios</li>
                        <li>Libro de ventas</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">🛒</div>
                    <h3>Compras</h3>
                    <p>Órdenes de compra, recepción, facturación de proveedores, control de gastos y cuentas por pagar.</p>
                    <ul>
                        <li>Órdenes de compra</li>
                        <li>Recepción de mercadería</li>
                        <li>Libro de compras</li>
                        <li>Gestión de proveedores</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">📦</div>
                    <h3>Inventario & Logística</h3>
                    <p>Control de stock, bodegas múltiples, trazabilidad, series, lotes y valorización de inventarios.</p>
                    <ul>
                        <li>Multi-bodega</li>
                        <li>Kardex completo</li>
                        <li>Trazabilidad por lote</li>
                        <li>Inventarios físicos</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">🏭</div>
                    <h3>Producción</h3>
                    <p>Órdenes de fabricación, BOM multinivel, MRP, control de calidad y costeo de producción.</p>
                    <ul>
                        <li>Lista de materiales (BOM)</li>
                        <li>Órdenes de fabricación</li>
                        <li>Control de calidad</li>
                        <li>Costeo por orden</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">👥</div>
                    <h3>Recursos Humanos</h3>
                    <p>Gestión de personal, remuneraciones, liquidaciones, Previred y control de asistencia integrado.</p>
                    <ul>
                        <li>Contratos y personal</li>
                        <li>Liquidaciones de sueldo</li>
                        <li>Integración Previred</li>
                        <li>Control de asistencia</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">🏗️</div>
                    <h3>Proyectos</h3>
                    <p>Gestión de proyectos, control de costos, facturación por hitos y rentabilidad por proyecto.</p>
                    <ul>
                        <li>Planificación y Gantt</li>
                        <li>Control de costos</li>
                        <li>Facturación por hitos</li>
                        <li>Rentabilidad</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">🤝</div>
                    <h3>CRM & Fidelización</h3>
                    <p>Gestión de clientes, leads, oportunidades, campañas de marketing y programa de puntos.</p>
                    <ul>
                        <li>Pipeline de ventas</li>
                        <li>Gestión de leads</li>
                        <li>Campañas</li>
                        <li>Programa de puntos</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">💰</div>
                    <h3>Tesorería & Bancos</h3>
                    <p>Flujo de caja, conciliaciones bancarias, pagos, cobranzas y proyecciones financieras.</p>
                    <ul>
                        <li>Flujo de caja</li>
                        <li>Conciliación bancaria</li>
                        <li>Pagos y cobranzas</li>
                        <li>Proyecciones</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">📈</div>
                    <h3>Business Intelligence</h3>
                    <p>Dashboards personalizables, KPIs, reportes avanzados, análisis predictivo y cubos OLAP.</p>
                    <ul>
                        <li>Dashboards dinámicos</li>
                        <li>KPIs en tiempo real</li>
                        <li>Reportes personalizados</li>
                        <li>Análisis predictivo</li>
                    </ul>
                </div>

                <div class="modulo-card">
                    <div class="modulo-icon">🛍️</div>
                    <h3>E-Commerce</h3>
                    <p>Tienda online integrada, catálogo de productos, carrito de compras y sincronización automática.</p>
                    <ul>
                        <li>Tienda online</li>
                        <li>Integración inventario</li>
                        <li>Pasarelas de pago</li>
                        <li>Sincronización automática</li>
                    </ul>
                </div>

                <div class="modulo-card destacado">
                    <div class="modulo-icon">🤖</div>
                    <h3>IA - Auditoría Inteligente</h3>
                    <p>Detección automática de fraudes, errores contables, riesgos tributarios y optimización de procesos.</p>
                    <ul>
                        <li>Detección de fraude</li>
                        <li>Auditoría automática</li>
                        <li>Análisis de riesgos</li>
                        <li>Recomendaciones IA</li>
                    </ul>
                    <span class="modulo-badge">Nuevo</span>
                </div>
            </div>
        </div>
    </section>

    <!-- INTEGRACIONES -->
    <section class="integraciones" id="integraciones">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Integraciones Oficiales</h2>
                <p class="section-subtitle">Conectamos directamente con los organismos y plataformas que usas</p>
            </div>

            <div class="integraciones-grid">
                <div class="integracion-card">
                    <div class="integracion-logo">🇨🇱 SII</div>
                    <h4>Servicio de Impuestos Internos</h4>
                    <p>Facturación electrónica, libros, F29, F22 y validación en línea</p>
                </div>

                <div class="integracion-card">
                    <div class="integracion-logo">💼 Previred</div>
                    <h4>Previred Chile</h4>
                    <p>Envío automático de cotizaciones y liquidaciones</p>
                </div>

                <div class="integracion-card">
                    <div class="integracion-logo">🏦 Bancos</div>
                    <h4>Integración Bancaria</h4>
                    <p>Conciliación automática y sincronización de movimientos</p>
                </div>

                <div class="integracion-card">
                    <div class="integracion-logo">💳 Pagos</div>
                    <h4>Pasarelas de Pago</h4>
                    <p>Transbank, Mercado Pago, Stripe y más</p>
                </div>

                <div class="integracion-card">
                    <div class="integracion-logo">📧 Email</div>
                    <h4>Email & Notificaciones</h4>
                    <p>Envío automático de documentos y notificaciones</p>
                </div>

                <div class="integracion-card">
                    <div class="integracion-logo">🔌 API REST</div>
                    <h4>API REST Completa</h4>
                    <p>Integra con tus sistemas actuales vía API</p>
                </div>
            </div>
        </div>
    </section>

    <!-- PLANES -->
    <section class="planes" id="planes">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Planes para cada etapa de tu empresa</h2>
                <p class="section-subtitle">Desde emprendedores hasta corporaciones multinacionales</p>
            </div>

            <div class="planes-grid">
                <?php foreach ($planes as $plan): ?>
                    <div class="plan-card <?= $plan['codigo'] === 'PRO' ? 'destacado' : '' ?>">
                        <?php if ($plan['codigo'] === 'PRO'): ?>
                            <div class="plan-badge">Más Popular</div>
                        <?php endif; ?>

                        <h3 class="plan-nombre"><?= htmlspecialchars($plan['nombre']) ?></h3>

                        <div class="plan-precio">
                            <?php if ($plan['precio_mensual'] > 0): ?>
                                <span class="precio-monto">$<?= number_format($plan['precio_mensual'], 0, ',', '.') ?></span>
                                <span class="precio-periodo">/mes</span>
                            <?php else: ?>
                                <?php if ($plan['codigo'] === 'CORP'): ?>
                                    <span class="precio-monto">A medida</span>
                                <?php else: ?>
                                    <span class="precio-monto">Gratis</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <p class="plan-descripcion"><?= htmlspecialchars($plan['descripcion']) ?></p>

                        <ul class="plan-features">
                            <li>✓ <?= $plan['empresas_max'] ?> <?= $plan['empresas_max'] === 1 ? 'empresa' : 'empresas' ?></li>
                            <li>✓ <?= $plan['usuarios_max'] === 999 || $plan['usuarios_max'] === 9999 ? 'Usuarios ilimitados' : $plan['usuarios_max'] . ' usuario' . ($plan['usuarios_max'] > 1 ? 's' : '') ?></li>
                            <li>✓ <?= $plan['trial_dias'] ?> días de prueba gratis</li>

                            <?php if ($plan['codigo'] === 'STARTER'): ?>
                                <li>✓ Productos ilimitados</li>
                                <li>✓ Facturación básica</li>
                                <li>✓ Inventario básico</li>
                                <li>✓ Soporte email</li>
                            <?php elseif ($plan['codigo'] === 'PRO'): ?>
                                <li>✓ Contabilidad completa</li>
                                <li>✓ Inventario avanzado</li>
                                <li>✓ Compras y ventas</li>
                                <li>✓ Reportes avanzados</li>
                                <li>✓ Soporte prioritario</li>
                            <?php elseif ($plan['codigo'] === 'EMPRESA'): ?>
                                <li>✓ Producción completa</li>
                                <li>✓ Proyectos</li>
                                <li>✓ Multi moneda</li>
                                <li>✓ Integración SII</li>
                                <li>✓ BI avanzado</li>
                                <li>✓ Soporte 24/7</li>
                            <?php elseif ($plan['codigo'] === 'CORP'): ?>
                                <li>✓ Multiempresa</li>
                                <li>✓ Multi sucursal</li>
                                <li>✓ Holding</li>
                                <li>✓ IA Auditoría</li>
                                <li>✓ API completa</li>
                                <li>✓ Gerente de cuenta</li>
                                <li>✓ SLA garantizado</li>
                            <?php endif; ?>
                        </ul>

                        <?php if ($plan['codigo'] === 'CORP'): ?>
                            <a href="#contacto" class="btn btn-secondary btn-block">Contactar Ventas</a>
                        <?php else: ?>
                            <a href="register.php" class="btn <?= $plan['codigo'] === 'PRO' ? 'btn-primary' : 'btn-secondary' ?> btn-block">
                                Comenzar Ahora
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="planes-footer">
                <p>✓ Sin permanencia • ✓ Cancela cuando quieras • ✓ Soporte incluido • ✓ Actualizaciones gratis</p>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="cta" id="contacto">
        <div class="container">
            <h2>Comienza a gestionar tu empresa profesionalmente hoy</h2>
            <p>Únete a cientos de empresas que ya optimizaron sus procesos con Conecta ERP</p>
            <div class="cta-actions">
                <a href="register.php" class="btn btn-primary btn-lg">Probar Gratis 14 Días</a>
                <a href="mailto:contacto@conectaerp.com" class="btn btn-secondary btn-lg">Hablar con Ventas</a>
            </div>
            <p class="cta-note">✓ Sin tarjeta de crédito • ✓ Implementación incluida • ✓ Soporte en español</p>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Conecta ERP</h4>
                    <p>Plataforma ERP integral para empresas modernas. Gestión completa desde contabilidad hasta BI avanzado.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="LinkedIn">💼</a>
                        <a href="#" aria-label="Twitter">🐦</a>
                        <a href="#" aria-label="YouTube">📺</a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Producto</h4>
                    <ul>
                        <li><a href="#modulos">Módulos</a></li>
                        <li><a href="#planes">Planes y Precios</a></li>
                        <li><a href="#integraciones">Integraciones</a></li>
                        <li><a href="#demo">Demo en Vivo</a></li>
                        <li><a href="#">Actualizaciones</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Recursos</h4>
                    <ul>
                        <li><a href="#">Documentación</a></li>
                        <li><a href="#">Centro de Ayuda</a></li>
                        <li><a href="#">Videos Tutoriales</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Casos de Éxito</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contacto</h4>
                    <ul>
                        <li>📧 contacto@conectaerp.com</li>
                        <li>📞 +56 9 8574 5559</li>
                        <li>🕐 Lun-Vie 9:00-18:00 CLT</li>
                        <li>🌍 12 países, 10 idiomas</li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Términos y Condiciones</a></li>
                        <li><a href="#">Política de Privacidad</a></li>
                        <li><a href="#">SLA</a></li>
                        <li><a href="#">Seguridad</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Conecta ERP - Sistema de Gestión Empresarial Integral. Todos los derechos reservados.</p>
                <div class="footer-badges">
                    <span class="badge">🔒 SSL Seguro</span>
                    <span class="badge">✓ SII Certificado</span>
                    <span class="badge">🇨🇱 Hecho en Chile</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/portada.js"></script>
</body>
</html>
