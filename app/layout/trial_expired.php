<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Período de Prueba Expirado - Conecta ERP</title>
    <link rel="stylesheet" href="<?= asset('css/base.css') ?>">
    <style>
        .expired-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }
        .expired-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            padding: 3rem;
            text-align: center;
        }
        .expired-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .expired-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1rem;
        }
        .expired-text {
            color: #4a5568;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .plan-options {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .plan-option {
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .plan-option:hover {
            border-color: var(--primary);
            background: #f7fafc;
        }
        .btn-upgrade {
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="expired-container">
        <div class="expired-card">
            <div class="expired-icon">⏰</div>
            <h1 class="expired-title">Período de Prueba Expirado</h1>
            <p class="expired-text">
                Tu período de prueba de 14 días ha finalizado. Para continuar usando Conecta ERP,
                por favor selecciona un plan que se ajuste a tus necesidades.
            </p>

            <div class="plan-options">
                <div class="plan-option">
                    <strong>Plan STARTER</strong> - Ideal para emprendedores
                </div>
                <div class="plan-option">
                    <strong>Plan PRO</strong> - Para pequeñas empresas
                </div>
                <div class="plan-option">
                    <strong>Plan EMPRESA</strong> - Solución completa
                </div>
            </div>

            <a href="mailto:soporte@conectaerp.cl?subject=Actualizar Plan" class="btn-upgrade">
                Actualizar Plan
            </a>

            <p style="margin-top: 1.5rem; font-size: 0.875rem; color: #718096;">
                ¿Necesitas ayuda? Contáctanos: <strong>soporte@conectaerp.cl</strong>
            </p>
        </div>
    </div>
</body>
</html>
