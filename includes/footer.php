<footer class="footer">
    <div class="container">
        <p class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo str_replace('/admin', '', dirname($_SERVER['PHP_SELF'])); ?>/assets/js/animations.js"></script>

<?php if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false): ?>
<script src="<?php echo str_replace('/admin', '', dirname($_SERVER['PHP_SELF'])); ?>/admin/actions/init.js"></script>
<?php endif; ?>

<?php if (strpos($_SERVER['PHP_SELF'], 'stocks.php') !== false): ?>
<script src="<?php echo str_replace('/admin', '', dirname($_SERVER['PHP_SELF'])); ?>/assets/js/stocks.js"></script>
<?php endif; ?>

</body>
</html>