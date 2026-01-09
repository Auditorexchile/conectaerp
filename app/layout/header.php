<!DOCTYPE html>
<html lang="<?= SessionManager::get('idioma_codigo', 'es') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Conecta ERP</title>

    <link rel="stylesheet" href="<?= asset('css/base.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/sidebar.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?= asset("css/{$css}") ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
</head>
<body class="dashboard-body">

    <div class="dashboard-container">
        <?php require BASE_PATH . '/app/layout/sidebar.php'; ?>

        <div class="main-content">
            <?php require BASE_PATH . '/app/layout/banner_dashboard.php'; ?>

            <div class="content-wrapper">
                <?php if (SessionManager::hasFlash('error')): ?>
                    <div class="alert alert-error">
                        <?= Security::preventXSS(SessionManager::getFlash('error')) ?>
                    </div>
                <?php endif; ?>

                <?php if (SessionManager::hasFlash('success')): ?>
                    <div class="alert alert-success">
                        <?= Security::preventXSS(SessionManager::getFlash('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (SessionManager::hasFlash('trial_warning')): ?>
                    <div class="alert alert-warning">
                        <?= Security::preventXSS(SessionManager::getFlash('trial_warning')) ?>
                    </div>
                <?php endif; ?>
