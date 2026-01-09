            </div><!-- .content-wrapper -->
        </div><!-- .main-content -->
    </div><!-- .dashboard-container -->

    <script src="<?= asset('js/sidebar.js') ?>"></script>
    <script src="<?= asset('js/dashboard.js') ?>"></script>
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?= asset("js/{$js}") ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
