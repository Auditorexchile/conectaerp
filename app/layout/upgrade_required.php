<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización Requerida - Conecta ERP</title>
    <link rel="stylesheet" href="<?= asset('css/base.css') ?>">
    <style>
        .upgrade-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 2rem;
        }
        .upgrade-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            padding: 3rem;
            text-align: center;
        }
        .upgrade-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .upgrade-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1rem;
        }
        .current-plan {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #edf2f7;
            border-radius: 2rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="upgrade-container">
        <div class="upgrade-card">
            <div class="upgrade-icon">🚀</div>
            <h1 class="upgrade-title">Función Premium</h1>

            <div class="current-plan">
                Tu plan actual: <?= SessionManager::getPlan() ?>
            </div>

            <p style="color: #4a5568; margin-bottom: 2rem; line-height: 1.6;">
                Esta funcionalidad requiere un plan superior.
                Actualiza tu plan para desbloquear todas las características de Conecta ERP.
            </p>

            <a href="mailto:soporte@conectaerp.cl?subject=Actualizar Plan desde <?= SessionManager::getPlan() ?>"
               class="btn-upgrade"
               style="background: var(--primary); color: white; padding: 1rem 2rem; border-radius: 0.5rem; text-decoration: none; display: inline-block; font-weight: 600;">
                Actualizar Plan
            </a>

            <br><br>

            <a href="/app/dashboard/dashboard.php" style="color: #718096; text-decoration: none;">
                ← Volver al Dashboard
            </a>
        </div>
    </div>
</body>
</html>
