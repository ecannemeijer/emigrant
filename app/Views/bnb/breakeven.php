<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-calculator"></i> B&B Break-even Analyse</h1>
    <p class="text-muted">Bereken de minimale bezettingsgraad om break-even te draaien</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5>Resultaten</h5>
                
                <div class="alert alert-info">
                    <h4>Minimale bezettingsgraad om break-even te draaien:</h4>
                    <div class="display-4"><?= number_format($break_even_percentage, 1, ',', '.') ?>%</div>
                </div>

                <table class="table mt-4">
                    <tr>
                        <td>Aantal kamers</td>
                        <td class="text-end"><?= $settings['number_of_rooms'] ?></td>
                    </tr>
                    <tr>
                        <td>Prijs per kamer per nacht</td>
                        <td class="text-end">€ <?= number_format($settings['price_per_room_per_night'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Vaste maandelijkse kosten</td>
                        <td class="text-end">€ <?= number_format($fixed_monthly_expenses, 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Maximale maandomzet (100% bezetting)</td>
                        <td class="text-end">€ <?= number_format($max_monthly_revenue, 2, ',', '.') ?></td>
                    </tr>
                </table>

                <?php if ($break_even_percentage > 100): ?>
                    <div class="alert alert-danger mt-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Let op!</strong> De break-even bezettingsgraad is hoger dan 100%. 
                        Dit betekent dat je met de huidige prijs en kosten niet winstgevend kunt zijn. 
                        Verhoog de prijs of verlaag de kosten.
                    </div>
                <?php elseif ($break_even_percentage > 80): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        De break-even bezettingsgraad is hoog (>80%). Er is weinig marge voor fluctuaties.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle-fill"></i>
                        De break-even bezettingsgraad is realistisch. Je hebt ruimte voor winstmarge.
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="/bnb" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Terug naar B&B Module
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5><i class="bi bi-lightbulb"></i> Tips</h5>
                <ul class="small">
                    <li>Streef naar een break-even onder de 60% voor gezonde marge</li>
                    <li>Hogere prijzen in hoogseizoen verhogen de winstgevendheid</li>
                    <li>Directe boekingen (geen commissie) verlagen break-even</li>
                    <li>Efficiënte schoonmaak en onderhoud verlagen kosten</li>
                </ul>

                <h6 class="mt-4">Scenario's</h6>
                <p class="small">Bij verschillende bezettingsgraden:</p>
                <table class="table table-sm small">
                    <?php
                    $scenarios = [40, 50, 60, 70, 80];
                    foreach ($scenarios as $percentage):
                        $revenue = ($max_monthly_revenue * $percentage) / 100;
                        $commission = ($expenses['platform_commission'] ?? 15) / 100;
                        $revenueAfterCommission = $revenue * (1 - $commission);
                        $profit = $revenueAfterCommission - $fixed_monthly_expenses;
                    ?>
                    <tr class="<?= $profit >= 0 ? 'text-success' : 'text-danger' ?>">
                        <td><?= $percentage ?>%</td>
                        <td class="text-end">€ <?= number_format($profit, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
