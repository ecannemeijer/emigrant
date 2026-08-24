<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Financieel overzicht</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 32px; color: #1b241c; }
        h1 { color: #1f6f4a; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="no-print mb-3">
        <button class="btn btn-primary" onclick="window.print()">Opslaan als PDF / printen</button>
        <a class="btn btn-outline-secondary" href="/dashboard">Terug</a>
    </div>
    <h1>Emigratie Italië — financieel overzicht</h1>
    <p><?= esc(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')) ?> · <?= date('d-m-Y') ?></p>

    <?php if (!empty($warnings)): ?>
        <ul>
            <?php foreach ($warnings as $warning): ?>
                <li><?= esc($warning) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <table class="table">
        <tr><th>Verbouw (van vermogen)</th><td>€ <?= number_format($calculations['renovation_outlay'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><th>Resterend vermogen</th><td>€ <?= number_format($calculations['remaining_capital'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><th>Maandinkomen</th><td>€ <?= number_format($calculations['total_monthly_income'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><th>Maandkosten</th><td>€ <?= number_format($calculations['monthly_expenses'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><th>Belasting</th><td>€ <?= number_format($calculations['monthly_taxes'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><th>Netto</th><td>€ <?= number_format($calculations['net_disposable'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><th>B&B netto</th><td>€ <?= number_format($calculations['bnb_net_income'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><th>AOW zelf</th><td><?= number_format($calculations['own_aow_percentage'] ?? 100, 1, ',', '.') ?>%</td></tr>
        <tr><th>AOW partner</th><td><?= number_format($calculations['partner_aow_percentage'] ?? 100, 1, ',', '.') ?>%</td></tr>
    </table>

    <h2>Projectie</h2>
    <table class="table table-sm">
        <thead><tr><th>Jaar</th><th>Leeftijd</th><th>Netto/mnd</th><th>Vermogen</th></tr></thead>
        <tbody>
        <?php foreach ($yearlyProjections as $row): ?>
            <tr>
                <td><?= $row['year'] ?></td>
                <td><?= $row['user_age'] ?></td>
                <td>€ <?= number_format($row['monthly_net'], 0, ',', '.') ?></td>
                <td>€ <?= number_format($row['capital'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p class="small text-muted">Indicatie, geen fiscaal advies. Controleer cijfers met een adviseur.</p>
</body>
</html>
