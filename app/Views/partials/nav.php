<?php
$uri = uri_string();
$items = [
    ['dashboard', '/dashboard', 'bi-speedometer2', 'Dashboard'],
    ['start-position', '/start-position', 'bi-house-door', 'Startpositie NL'],
    ['income', '/income', 'bi-cash-coin', 'Inkomsten'],
    ['property', '/property', 'bi-building', 'Vastgoed IT'],
    ['renovation', '/renovation', 'bi-hammer', 'Verbouwen'],
    ['expenses', '/expenses', 'bi-wallet2', 'Maandlasten'],
    ['taxes', '/taxes', 'bi-receipt', 'Belastingen'],
    ['bnb', '/bnb', 'bi-shop', 'B&B'],
    ['checklist', '/checklist', 'bi-check2-square', 'Checklist'],
    ['scenarios', '/scenarios', 'bi-diagram-3', 'Scenario\'s'],
    ['subscription', '/subscription', 'bi-credit-card', 'Abonnement'],
];
?>
<ul class="nav flex-column sidebar-nav">
    <?php foreach ($items as [$match, $href, $icon, $label]): ?>
        <li class="nav-item">
            <a class="nav-link <?= str_starts_with($uri, $match) ? 'active' : '' ?>" href="<?= $href ?>">
                <i class="bi <?= $icon ?>"></i> <?= $label ?>
            </a>
        </li>
    <?php endforeach; ?>
    <li class="nav-item mt-3"><hr></li>
    <li class="nav-item"><a class="nav-link <?= $uri === 'help' ? 'active' : '' ?>" href="/help"><i class="bi bi-question-circle"></i> Help</a></li>
    <li class="nav-item"><a class="nav-link <?= $uri === 'contact' ? 'active' : '' ?>" href="/contact"><i class="bi bi-envelope"></i> Contact</a></li>
    <li class="nav-item"><a class="nav-link" href="/export/csv"><i class="bi bi-download"></i> Export CSV</a></li>
    <li class="nav-item"><a class="nav-link" href="/export/pdf"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a></li>
    <?php if (session()->get('role') === 'admin'): ?>
        <li class="nav-item mt-3"><hr><small class="text-muted px-3">Admin</small></li>
        <li class="nav-item"><a class="nav-link <?= $uri === 'admin/config' ? 'active' : '' ?>" href="/admin/config"><i class="bi bi-sliders"></i> Config</a></li>
        <li class="nav-item"><a class="nav-link <?= str_starts_with($uri, 'admin/users') ? 'active' : '' ?>" href="/admin/users"><i class="bi bi-people"></i> Gebruikers</a></li>
        <li class="nav-item"><a class="nav-link <?= $uri === 'admin/payments' ? 'active' : '' ?>" href="/admin/payments"><i class="bi bi-paypal"></i> Betalingen</a></li>
        <li class="nav-item"><a class="nav-link <?= str_starts_with($uri, 'admin/audit-logs') ? 'active' : '' ?>" href="/admin/audit-logs"><i class="bi bi-journal-text"></i> Audit log</a></li>
    <?php endif; ?>
</ul>
