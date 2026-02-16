CLI Error:

<?= esc($title ?? 'Error') ?>

<?= esc($message ?? 'An error occurred') ?>

<?php if (ENVIRONMENT === 'development' && isset($trace)): ?>

Stack Trace:
<?= esc($trace) ?>

<?php endif; ?>
