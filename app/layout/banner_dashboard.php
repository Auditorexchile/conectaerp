<div class="dashboard-banner">
    <div class="banner-left">
        <h1 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
        <?php if (isset($pageSubtitle)): ?>
            <p class="page-subtitle"><?= $pageSubtitle ?></p>
        <?php endif; ?>
    </div>

    <div class="banner-right">
        <div class="banner-info">
            <div class="info-item">
                <span class="info-label">Empresa:</span>
                <span class="info-value"><?= Security::preventXSS(SessionManager::get('empresa_nombre')) ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">Plan:</span>
                <span class="info-value plan-badge plan-<?= strtolower(SessionManager::getPlan()) ?>">
                    <?= SessionManager::getPlan() ?>
                </span>
            </div>

            <?php if (SessionManager::getEmpresaEstado() === 'trial'): ?>
                <?php $diasRestantes = SessionManager::getTrialDaysRemaining(); ?>
                <?php if ($diasRestantes !== null): ?>
                <div class="info-item trial-info">
                    <span class="info-label">Período de prueba:</span>
                    <span class="info-value <?= $diasRestantes <= 3 ? 'text-danger' : '' ?>">
                        <?= $diasRestantes ?> días restantes
                    </span>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="banner-actions">
            <div class="language-selector">
                <select id="languageSelector" class="form-select-sm">
                    <?php
                    $languages = require CONFIG_PATH . '/languages.php';
                    $currentLang = SessionManager::get('idioma_codigo', 'es');
                    foreach ($languages as $code => $lang):
                    ?>
                        <option value="<?= $code ?>" <?= $currentLang === $code ? 'selected' : '' ?>>
                            <?= $lang['flag'] ?> <?= $lang['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="notifications">
                <button class="btn-icon" id="notificationsBtn">
                    <span>🔔</span>
                    <span class="badge">3</span>
                </button>
            </div>
        </div>
    </div>
</div>
