<?php
$steps = [
    ['start-position', 'Start'],
    ['income', 'Inkomen'],
    ['property', 'Vastgoed'],
    ['renovation', 'Verbouw'],
    ['expenses', 'Lasten'],
    ['taxes', 'Belasting'],
    ['bnb', 'B&B'],
    ['dashboard', 'Dashboard'],
];
$uri = uri_string();
?>
<nav class="wizard-bar" aria-label="Stappen">
    <?php foreach ($steps as $i => [$path, $label]): ?>
        <a class="wizard-step <?= $uri === $path || ($path === 'dashboard' && $uri === 'dashboard') ? 'active' : '' ?>" href="/<?= $path === 'dashboard' ? 'dashboard' : $path ?>">
            <span class="wizard-num"><?= $i + 1 ?></span>
            <span class="wizard-label"><?= $label ?></span>
        </a>
    <?php endforeach; ?>
</nav>
