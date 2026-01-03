<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conecta ERP - Gestiona tu empresa en un solo sistema</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/portada.css">
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <span class="logo-icon">C</span>
                        <span class="logo-text">Conecta ERP</span>
                    </a>
                </div>
                <nav class="nav">
                    <a href="#inicio">Inicio</a>
                    <a href="#planes">Planes</a>
                    <a href="#funcionalidades">Funcionalidades</a>
                    <a href="#contacto">Contacto</a>
                </nav>
                <div class="header-actions">
                    <a href="login.php" class="btn-secondary">Ingresar</a>
                    <a href="register.php" class="btn-primary">Crear cuenta</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="hero" id="inicio">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Gestiona tu empresa en un solo sistema</h1>
                    <p>Contabilidad, ventas, inventario, producción y control total en un ERP moderno.</p>
                    <div class="hero-buttons">
                        <a href="register.php" class="btn-primary btn-large">Crear cuenta</a>
                        <a href="#planes" class="btn-outline btn-large">Ver planes</a>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="dashboard-preview"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Beneficios -->
    <section class="benefits" id="funcionalidades">
        <div class="container">
            <div class="section-header">
                <h2>¿Por qué elegir Conecta ERP?</h2>
                <p>La solución completa para tu empresa</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">🔗</div>
                    <h3>Todo integrado</h3>
                    <p>Un solo sistema para todas las operaciones de tu empresa</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">📋</div>
                    <h3>Cumplimiento tributario</h3>
                    <p>Preparado para SII y normativas chilenas</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">📈</div>
                    <h3>Escalable</h3>
                    <p>Crece con tu empresa sin límites</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🔒</div>
                    <h3>Seguro</h3>
                    <p>Acceso protegido y auditoría completa</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Planes -->
    <section class="plans" id="planes">
        <div class="container">
            <div class="section-header">
                <h2>Planes para cada tipo de empresa</h2>
                <p>Elige el plan que mejor se adapte a tus necesidades</p>
            </div>
            <div class="plans-grid">
                <!-- Starter -->
                <div class="plan-card">
                    <div class="plan-badge plan-badge-starter">STARTER</div>
                    <div class="plan-price">Gratis</div>
                    <ul class="plan-features">
                        <li>✓ 1 empresa</li>
                        <li>✓ 1 usuario</li>
                        <li>✓ Productos ilimitados</li>
                        <li>✓ Facturación básica</li>
                        <li>✓ Inventario básico</li>
                    </ul>
                    <a href="register.php?plan=starter" class="btn-primary">Comenzar</a>
                </div>
                <!-- Profesional -->
                <div class="plan-card plan-featured">
                    <div class="plan-badge plan-badge-profesional">PROFESIONAL</div>
                    <div class="plan-price">$49.990<span>/mes</span></div>
                    <ul class="plan-features">
                        <li>✓ 1 empresa</li>
                        <li>✓ Hasta 5 usuarios</li>
                        <li>✓ Contabilidad completa</li>
                        <li>✓ Inventario avanzado</li>
                        <li>✓ Reportes</li>
                    </ul>
                    <a href="register.php?plan=profesional" class="btn-primary">Elegir plan</a>
                </div>
                <!-- Empresa -->
                <div class="plan-card">
                    <div class="plan-badge plan-badge-empresa">EMPRESA</div>
                    <div class="plan-price">$99.990<span>/mes</span></div>
                    <ul class="plan-features">
                        <li>✓ 1 empresa</li>
                        <li>✓ Usuarios ilimitados</li>
                        <li>✓ Producción</li>
                        <li>✓ Multi moneda</li>
                        <li>✓ Integración SII</li>
                    </ul>
                    <a href="register.php?plan=empresa" class="btn-primary">Elegir plan</a>
                </div>
                <!-- Corporativo -->
                <div class="plan-card">
                    <div class="plan-badge plan-badge-corporativo">CORPORATIVO</div>
                    <div class="plan-price">Contactar</div>
                    <ul class="plan-features">
                        <li>✓ Multiempresa</li>
                        <li>✓ Multi sucursal</li>
                        <li>✓ BI</li>
                        <li>✓ Integraciones</li>
                        <li>✓ Soporte premium</li>
                    </ul>
                    <a href="#contacto" class="btn-primary">Contactar</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="cta" id="contacto">
        <div class="container">
            <h2>Comienza hoy y controla tu empresa desde un solo lugar</h2>
            <a href="register.php" class="btn-primary btn-large">Crear cuenta gratis</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <span class="logo-icon">C</span>
                        <span>Conecta ERP</span>
                    </div>
                    <p>Plataforma ERP integral para empresas modernas.</p>
                </div>
                <div class="footer-col">
                    <h4>Navegación</h4>
                    <ul>
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="#planes">Planes</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Crear cuenta</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contacto</h4>
                    <ul>
                        <li>📧 contacto@conectaerp.com</li>
                        <li>📞 +56 9 8574 5559</li>
                        <li>Lunes a Viernes 9:00 - 18:00</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Términos</a></li>
                        <li><a href="#">Privacidad</a></li>
                        <li><a href="#">Aviso legal</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Conecta ERP - Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
