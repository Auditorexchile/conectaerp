<?php
/**
 * INDEX.PHP - Portada Conecta ERP
 * Landing page profesional multiidioma
 */

require_once 'config/database.php';

// Detectar idioma (por ahora español por defecto)
$lang = 'es';

// Obtener planes para mostrar
$db = Database::getInstance();
$planes = $db->fetchAll("SELECT * FROM planes WHERE activo = 1 ORDER BY id");
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Conecta ERP - Sistema de gestión empresarial integral. Contabilidad, ventas, inventario, producción y control total en un solo sistema.">
    <title>Conecta ERP - Gestiona tu empresa en un solo sistema</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/portada.css">
</head>
<body>
    <!-- HEADER -->
    <header class="header" id="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="assets/img/logo.png" alt="Conecta ERP" class="logo-img">
                </div>

                <nav class="nav-menu">
                    <a href="#inicio" class="nav-link">Inicio</a>
                    <a href="#planes" class="nav-link">Planes</a>
                    <a href="#beneficios" class="nav-link">Funcionalidades</a>
                    <a href="#contacto" class="nav-link">Contacto</a>
                </nav>

                <div class="header-actions">
                    <a href="login.php" class="btn btn-secondary">Ingresar</a>
                    <a href="register.php" class="btn btn-primary">Crear cuenta</a>
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
                    <h1 class="hero-title">Gestiona tu empresa en un solo sistema</h1>
                    <p class="hero-subtitle">
                        Contabilidad, ventas, inventario, producción y control total en un ERP moderno diseñado para empresas en crecimiento.
                    </p>
                    <div class="hero-actions">
                        <a href="register.php" class="btn btn-primary btn-lg">
                            Crear cuenta gratis
                        </a>
                        <a href="#planes" class="btn btn-secondary btn-lg">
                            Ver planes
                        </a>
                    </div>
                    <p class="hero-note">✓ 14 días de prueba gratis. Sin tarjeta de crédito.</p>
                </div>

                <div class="hero-image">
                    <img src="assets/img/dashboard-preview.png" alt="Dashboard Conecta ERP" class="dashboard-preview">
                </div>
            </div>
        </div>
    </section>

    <!-- BENEFICIOS -->
    <section class="beneficios" id="beneficios">
        <div class="container">
            <h2 class="section-title">¿Por qué elegir Conecta ERP?</h2>

            <div class="beneficios-grid">
                <div class="beneficio-card">
                    <div class="beneficio-icon">🔗</div>
                    <h3>Todo integrado</h3>
                    <p>Un solo sistema para contabilidad, ventas, compras, inventario, producción y más.</p>
                </div>

                <div class="beneficio-card">
                    <div class="beneficio-icon">📋</div>
                    <h3>Cumplimiento tributario</h3>
                    <p>Preparado para SII Chile y organismos fiscales de 12 países. Facturación electrónica integrada.</p>
                </div>

                <div class="beneficio-card">
                    <div class="beneficio-icon">📈</div>
                    <h3>Escalable</h3>
                    <p>Crece con tu empresa. Desde 1 usuario hasta multiempresa con sucursales y holding.</p>
                </div>

                <div class="beneficio-card">
                    <div class="beneficio-icon">🔒</div>
                    <h3>Seguro</h3>
                    <p>Acceso protegido, auditoría completa y respaldos automáticos. Tus datos siempre seguros.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- PLANES -->
    <section class="planes" id="planes">
        <div class="container">
            <h2 class="section-title">Planes para cada etapa de tu empresa</h2>

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
                                <span class="precio-monto">Gratis</span>
                            <?php endif; ?>
                        </div>

                        <p class="plan-descripcion"><?= htmlspecialchars($plan['descripcion']) ?></p>

                        <ul class="plan-features">
                            <li>✓ <?= $plan['empresas_max'] ?> <?= $plan['empresas_max'] === 1 ? 'empresa' : 'empresas' ?></li>
                            <li>✓ <?= $plan['usuarios_max'] === 999 ? 'Usuarios ilimitados' : $plan['usuarios_max'] . ' usuario' . ($plan['usuarios_max'] > 1 ? 's' : '') ?></li>
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
                                <li>✓ Reportes</li>
                                <li>✓ Soporte email + chat</li>
                            <?php elseif ($plan['codigo'] === 'EMPRESA'): ?>
                                <li>✓ Producción</li>
                                <li>✓ Costos</li>
                                <li>✓ Proyectos</li>
                                <li>✓ Multi moneda</li>
                                <li>✓ Integración SII</li>
                                <li>✓ Soporte prioritario</li>
                            <?php elseif ($plan['codigo'] === 'CORP'): ?>
                                <li>✓ Multiempresa</li>
                                <li>✓ Multi sucursal</li>
                                <li>✓ Control gestión</li>
                                <li>✓ BI avanzado</li>
                                <li>✓ Integraciones</li>
                                <li>✓ SLA dedicado</li>
                                <li>✓ Soporte premium 24/7</li>
                            <?php endif; ?>
                        </ul>

                        <?php if ($plan['codigo'] === 'CORP'): ?>
                            <a href="#contacto" class="btn btn-secondary btn-block">Contactar</a>
                        <?php else: ?>
                            <a href="register.php" class="btn <?= $plan['codigo'] === 'PRO' ? 'btn-primary' : 'btn-secondary' ?> btn-block">
                                Comenzar
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="cta" id="contacto">
        <div class="container">
            <h2>Comienza hoy y controla tu empresa desde un solo lugar</h2>
            <p>Únete a cientos de empresas que ya confían en Conecta ERP</p>
            <a href="register.php" class="btn btn-primary btn-lg">Crear cuenta gratis</a>
            <p class="cta-note">✓ 14 días de prueba gratis. Sin tarjeta de crédito.</p>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Conecta ERP</h4>
                    <p>Plataforma ERP integral para empresas modernas.</p>
                </div>

                <div class="footer-col">
                    <h4>Navegación</h4>
                    <ul>
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="#planes">Planes</a></li>
                        <li><a href="#beneficios">Funcionalidades</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Crear cuenta</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contacto</h4>
                    <ul>
                        <li>📧 contacto@conectaerp.com</li>
                        <li>📞 +56 9 8574 5559</li>
                        <li>🕐 Lun-Vie 9:00-18:00</li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Términos y condiciones</a></li>
                        <li><a href="#">Política de privacidad</a></li>
                        <li><a href="#">Aviso legal</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Conecta ERP - Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/portada.js"></script>
</body>
</html>
