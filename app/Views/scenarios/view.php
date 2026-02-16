<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-eye"></i> Scenario: <?= esc($scenario['name']) ?></h1>
    <p class="text-muted"><?= esc($scenario['description']) ?></p>
    <p class="small text-muted">
        <i class="bi bi-calendar"></i> Opgeslagen op: <?= date('d-m-Y H:i', strtotime($scenario['created_at'])) ?>
    </p>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> Dit is een opgeslagen scenario. De huidige data in je dashboard kan anders zijn.
</div>

<?php if ($scenarioData): ?>
<div class="row">
    <!-- Start Position -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Startpositie Nederland</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Verkoopprijs woning</td>
                        <td class="text-end">€ <?= number_format($scenarioData['start_position']['house_sale_price'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Hypotheek</td>
                        <td class="text-end">€ <?= number_format($scenarioData['start_position']['mortgage_debt'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Spaargeld</td>
                        <td class="text-end">€ <?= number_format($scenarioData['start_position']['savings'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Totaal Startvermogen</td>
                        <td class="text-end">€ <?= number_format($scenarioData['start_position']['total_starting_capital'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Income -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Maandinkomen</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>WIA vrouw</td>
                        <td class="text-end">€ <?= number_format($scenarioData['income']['wia_wife'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Eigen inkomen</td>
                        <td class="text-end">€ <?= number_format($scenarioData['income']['own_income'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php if (($scenarioData['income']['pension'] ?? 0) > 0): ?>
                    <tr>
                        <td>Pensioen</td>
                        <td class="text-end">€ <?= number_format($scenarioData['income']['pension'], 2, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="fw-bold">
                        <td>Totaal</td>
                        <td class="text-end">€ <?= number_format(
                            ($scenarioData['income']['wia_wife'] ?? 0) +
                            ($scenarioData['income']['own_income'] ?? 0) +
                            ($scenarioData['income']['wao_future'] ?? 0) +
                            ($scenarioData['income']['pension'] ?? 0) +
                            ($scenarioData['income']['other_income'] ?? 0), 2, ',', '.'
                        ) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($scenario['with_bnb']): ?>
<div class="alert alert-success">
    <i class="bi bi-shop"></i> Dit scenario bevat B&B inkomsten
</div>
<?php endif; ?>

<?php if ($scenario['with_second_property']): ?>
<div class="alert alert-info">
    <i class="bi bi-building"></i> Dit scenario bevat een tweede woning
</div>
<?php endif; ?>

<?php endif; ?>

<div class="mt-4">
    <a href="/scenarios" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Terug naar Scenario's
    </a>
</div>

<?= $this->endSection() ?>
