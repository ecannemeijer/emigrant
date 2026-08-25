<?php
$uri = uri_string();
$items = [
    ['dashboard', '/dashboard', 'bi-speedometer2', 'Dashboard'],
    ['start-position', '/start-position', 'bi-house-door', 'Startpositie NL'],
    ['income', '/income', 'bi-cash-coin', 'Inkomsten'],
    ['property', '/property', 'bi-building', 'Vastgoed IT'],
    ['renovation', '/renovation', 'bi-hammer', 'Verbouwen'],
    ['renovation/planning', '/renovation/planning', 'bi-calendar3', 'Planning'],
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
            <?php
                $isActive = $match === 'renovation'
                    ? ($uri === 'renovation')
                    : str_starts_with($uri, $match);
            ?>
            <a class="nav-link <?= $isActive ? 'active' : '' ?>" href="<?= $href ?>" title="<?= esc($label) ?>">
                <i class="bi <?= $icon ?>"></i>
                <span class="sidebar-label"><?= $label ?></span>
            </a>
        </li>
    <?php endforeach; ?>
    <li class="nav-item mt-3"><hr></li>
    <li class="nav-item"><a class="nav-link <?= $uri === 'help' ? 'active' : '' ?>" href="/help" title="Help"><i class="bi bi-question-circle"></i> <span class="sidebar-label">Help</span></a></li>
    <li class="nav-item"><a class="nav-link <?= $uri === 'contact' ? 'active' : '' ?>" href="/contact" title="Contact"><i class="bi bi-envelope"></i> <span class="sidebar-label">Contact</span></a></li>
    <li class="nav-item"><a class="nav-link" href="/export/csv" title="Export CSV"><i class="bi bi-download"></i> <span class="sidebar-label">Export CSV</span></a></li>
    <li class="nav-item"><a class="nav-link" href="/export/pdf" title="Export PDF"><i class="bi bi-file-earmark-pdf"></i> <span class="sidebar-label">Export PDF</span></a></li>
    <?php if (session()->get('role') === 'admin'): ?>
        <li class="nav-item mt-3"><hr><small class="text-muted px-3 sidebar-heading">Admin</small></li>
        <li class="nav-item"><a class="nav-link <?= $uri === 'admin/config' ? 'active' : '' ?>" href="/admin/config" title="Config"><i class="bi bi-sliders"></i> <span class="sidebar-label">Config</span></a></li>
        <li class="nav-item"><a class="nav-link <?= str_starts_with($uri, 'admin/users') ? 'active' : '' ?>" href="/admin/users" title="Gebruikers"><i class="bi bi-people"></i> <span class="sidebar-label">Gebruikers</span></a></li>
        <li class="nav-item"><a class="nav-link <?= $uri === 'admin/payments' ? 'active' : '' ?>" href="/admin/payments" title="Betalingen"><i class="bi bi-paypal"></i> <span class="sidebar-label">Betalingen</span></a></li>
        <li class="nav-item"><a class="nav-link <?= str_starts_with($uri, 'admin/audit-logs') ? 'active' : '' ?>" href="/admin/audit-logs" title="Audit log"><i class="bi bi-journal-text"></i> <span class="sidebar-label">Audit log</span></a></li>
    <?php endif; ?>
</ul>
