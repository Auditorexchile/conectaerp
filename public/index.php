<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conecta ERP - Gestiona tu empresa en un solo sistema</title>
    <meta name="description" content="ERP empresarial completo. Contabilidad, ventas, inventario, producción. Cumplimiento SII Chile.">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/portada.css">
</head>
<body>
    <!-- Header fijo con efecto scroll -->
    <header class="header" id="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <span class="logo-icon">📊</span>
                        <span class="logo-text">Conecta ERP</span>
                    </a>
                </div>
                <nav class="nav-menu">
                    <a href="#inicio" class="nav-link">Inicio</a>
                    <a href="#planes" class="nav-link">Planes</a>
                    <a href="#funcionalidades" class="nav-link">Funcionalidades</a>
                    <a href="#contacto" class="nav-link">Contacto</a>
                </nav>
                <div class="header-actions">
                    <a href="login.php" class="btn btn-secondary">Ingresar</a>
                    <a href="register.php" class="btn btn-primary">Crear cuenta</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Principal -->
    <section class="hero" id="inicio">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">Gestiona tu empresa en un solo sistema</h1>
                    <p class="hero-subtitle">Contabilidad, ventas, inventario, producción y control total en un ERP moderno.</p>
                    <div class="hero-buttons">
                        <a href="register.php" class="btn btn-primary btn-large">Crear cuenta</a>
                        <a href="#planes" class="btn btn-outline btn-large">Ver planes</a>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="dashboard-preview">
                        <div class="preview-header"></div>
                        <div class="preview-sidebar"></div>
                        <div class="preview-content">
                            <div class="preview-chart"></div>
                            <div class="preview-stats">
                                <div class="stat-card"></div>
                                <div class="stat-card"></div>
                                <div class="stat-card"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Beneficios (4 bloques) -->
    <section class="benefits" id="funcionalidades">
        <div class="container">
            <div class="section-header">
                <h2>¿Por qué elegir Conecta ERP?</h2>
                <p class="section-subtitle">La solución completa para tu empresa</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">🔗</div>
                    <h3>Todo integrado</h3>
                    <p>Un solo sistema para todas las operaciones de tu empresa. Sin duplicidad de datos.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">📋</div>
                    <h3>Cumplimiento tributario</h3>
                    <p>Preparado para SII y normativas chilenas. Facturación electrónica incluida.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">📈</div>
                    <h3>Escalable</h3>
                    <p>Crece con tu empresa sin límites. Desde startup hasta corporativo.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🔒</div>
                    <h3>Seguro</h3>
                    <p>Acceso protegido y auditoría completa. Tus datos siempre seguros.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Planes (4 planes detallados) -->
    <section class="plans" id="planes">
        <div class="container">
            <div class="section-header">
                <h2>Planes para cada tipo de empresa</h2>
                <p class="section-subtitle">Elige el plan que mejor se adapte a tus necesidades</p>
            </div>
            <div class="plans-grid">

                <!-- PLAN 1 - STARTER -->
                <div class="plan-card plan-starter">
                    <div class="plan-header">
                        <h3 class="plan-name">Starter</h3>
                        <div class="plan-price">
                            <span class="price-amount">Gratis</span>
                        </div>
                    </div>
                    <ul class="plan-features">
                        <li><span class="check">✓</span> 1 empresa</li>
                        <li><span class="check">✓</span> 1 usuario (representante legal)</li>
                        <li><span class="check">✓</span> Productos ilimitados</li>
                        <li><span class="check">✓</span> Facturación básica</li>
                        <li><span class="check">✓</span> Inventario básico</li>
                        <li><span class="check">✓</span> Soporte email</li>
                    </ul>
                    <div class="plan-action">
                        <a href="register.php?plan=starter" class="btn btn-primary btn-block">Comenzar</a>
                    </div>
                </div>

                <!-- PLAN 2 - PROFESIONAL (Destacado) -->
                <div class="plan-card plan-profesional plan-featured">
                    <div class="plan-badge">Más Popular</div>
                    <div class="plan-header">
                        <h3 class="plan-name">Profesional</h3>
                        <div class="plan-price">
                            <span class="price-currency">$</span>
                            <span class="price-amount">49.990</span>
                            <span class="price-period">/mes</span>
                        </div>
                    </div>
                    <ul class="plan-features">
                        <li><span class="check">✓</span> 1 empresa</li>
                        <li><span class="check">✓</span> Hasta 5 usuarios</li>
                        <li><span class="check">✓</span> Contabilidad completa</li>
                        <li><span class="check">✓</span> Inventario avanzado</li>
                        <li><span class="check">✓</span> Compras y ventas</li>
                        <li><span class="check">✓</span> Reportes</li>
                        <li><span class="check">✓</span> Soporte email + chat</li>
                    </ul>
                    <div class="plan-action">
                        <a href="register.php?plan=profesional" class="btn btn-primary btn-block">Elegir plan</a>
                    </div>
                </div>

                <!-- PLAN 3 - EMPRESA -->
                <div class="plan-card plan-empresa">
                    <div class="plan-header">
                        <h3 class="plan-name">Empresa</h3>
                        <div class="plan-price">
                            <span class="price-currency">$</span>
                            <span class="price-amount">99.990</span>
                            <span class="price-period">/mes</span>
                        </div>
                    </div>
                    <ul class="plan-features">
                        <li><span class="check">✓</span> 1 empresa</li>
                        <li><span class="check">✓</span> Usuarios ilimitados</li>
                        <li><span class="check">✓</span> Producción</li>
                        <li><span class="check">✓</span> Costos</li>
                        <li><span class="check">✓</span> Proyectos</li>
                        <li><span class="check">✓</span> Multi moneda</li>
                        <li><span class="check">✓</span> Integración SII</li>
                        <li><span class="check">✓</span> Soporte prioritario</li>
                    </ul>
                    <div class="plan-action">
                        <a href="register.php?plan=empresa" class="btn btn-primary btn-block">Elegir plan</a>
                    </div>
                </div>

                <!-- PLAN 4 - CORPORATIVO -->
                <div class="plan-card plan-corporativo">
                    <div class="plan-header">
                        <h3 class="plan-name">Corporativo</h3>
                        <div class="plan-price">
                            <span class="price-amount">Contactar</span>
                        </div>
                    </div>
                    <ul class="plan-features">
                        <li><span class="check">✓</span> Multiempresa</li>
                        <li><span class="check">✓</span> Multi sucursal</li>
                        <li><span class="check">✓</span> Control gestión</li>
                        <li><span class="check">✓</span> Business Intelligence</li>
                        <li><span class="check">✓</span> Integraciones</li>
                        <li><span class="check">✓</span> SLA dedicado</li>
                        <li><span class="check">✓</span> Soporte premium</li>
                    </ul>
                    <div class="plan-action">
                        <a href="#contacto" class="btn btn-primary btn-block">Contactar</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Sección Confianza / CTA Final -->
    <section class="cta" id="contacto">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Comienza hoy y controla tu empresa desde un solo lugar</h2>
                <p class="cta-subtitle">14 días de prueba gratis. Sin tarjeta de crédito.</p>
                <a href="register.php" class="btn btn-primary btn-large">Crear cuenta gratis</a>
            </div>
        </div>
    </section>

    <!-- Footer Global (Reutilizable) -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Columna 1: Marca -->
                <div class="footer-col">
                    <div class="footer-logo">
                        <span class="logo-icon">📊</span>
                        <span class="logo-text">Conecta ERP</span>
                    </div>
                    <p class="footer-description">Plataforma ERP integral para empresas modernas.</p>
                </div>

                <!-- Columna 2: Navegación -->
                <div class="footer-col">
                    <h4 class="footer-title">Navegación</h4>
                    <ul class="footer-links">
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="#planes">Planes</a></li>
                        <li><a href="#funcionalidades">Funcionalidades</a></li>
                        <li><a href="#contacto">Contacto</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Crear cuenta</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Contacto -->
                <div class="footer-col">
                    <h4 class="footer-title">Contacto</h4>
                    <ul class="footer-links">
                        <li>📧 contacto@conectaerp.com</li>
                        <li>📞 +56 9 8574 5559</li>
                        <li>🕒 Lunes a Viernes</li>
                        <li>9:00 AM - 6:00 PM</li>
                    </ul>
                </div>

                <!-- Columna 4: Legal -->
                <div class="footer-col">
                    <h4 class="footer-title">Legal</h4>
                    <ul class="footer-links">
                        <li><a href="#">Términos y condiciones</a></li>
                        <li><a href="#">Política de privacidad</a></li>
                        <li><a href="#">Aviso legal</a></li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Conecta ERP - Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/portada.js"></script>
</body>
</html>
