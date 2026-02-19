<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
    <p class="text-muted">Overzicht van je financiële situatie na emigratie</p>
</div>

<?php if (empty($profile['date_of_birth']) || empty($profile['emigration_date'])): ?>
<div class="alert alert-warning">
    <h5><i class="bi bi-exclamation-triangle-fill"></i> Profiel incompleet</h5>
    <p>Je profiel is nog niet volledig ingevuld. Vul eerst de volgende gegevens in om de volledige dashboard-functionaliteit te gebruiken:</p>
    <ul class="mb-2">
        <?php if (empty($profile['date_of_birth'])): ?>
        <li><strong>Geboortedatum</strong> — voor leeftijdsberekeningen en pensioenprognoses</li>
        <?php endif; ?>
        <?php if (empty($profile['emigration_date'])): ?>
        <li><strong>Emigratiedatum</strong> — voor AOW-reductie berekeningen</li>
        <?php endif; ?>
    </ul>
    <a href="/profile" class="btn btn-warning">
        <i class="bi bi-person-fill"></i> Ga naar Profiel
    </a>
</div>
<?php endif; ?>

<!-- Key Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card neutral">
            <div class="card-body">
                <div class="stat-label">Resterend Vermogen</div>
                <div class="stat-value">€ <?= number_format($calculations['remaining_capital'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card positive">
            <div class="card-body">
                <div class="stat-label">Maandinkomen</div>
                <div class="stat-value">€ <?= number_format($calculations['total_monthly_income'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card negative">
            <div class="card-body">
                <div class="stat-label">Maandkosten</div>
                <div class="stat-value">€ <?= number_format($calculations['monthly_expenses'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card <?= ($calculations['net_disposable'] ?? 0) >= 0 ? 'positive' : 'negative' ?>">
            <div class="card-body">
                <div class="stat-label">Netto Maandelijks</div>
                <div class="stat-value">€ <?= number_format($calculations['net_disposable'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>

<?php if (($calculations['net_disposable'] ?? 0) < 0): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i> 
        <strong>Let op!</strong> Je maandelijkse uitgaven zijn hoger dan je inkomsten. 
        Je trekt € <?= number_format(abs($calculations['net_disposable']), 0, ',', '.') ?> per maand van je vermogen af.
    </div>
<?php endif; ?>

<?php 
// Show AOW reduction notice if emigration date affects AOW
if (!empty($profile['emigration_date']) && !empty($profile['partner_date_of_birth'])): 
    $AOWPercentage = calculate_AOW_percentage(
        $profile['emigration_date'],
        $profile['partner_date_of_birth'],
        $profile['partner_retirement_age'] ?? 67
    );
    if ($AOWPercentage < 100):
?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle-fill"></i> 
        <strong>AOW Reductie:</strong> Op basis van je emigratiedatum (<?= date('d-m-Y', strtotime($profile['emigration_date'])) ?>) 
        ontvang je <?= number_format($AOWPercentage, 1) ?>% van de volledige AOW. 
        Dit is verwerkt in alle berekeningen.
    </div>
<?php endif; endif; ?>

<div class="row">
    <!-- Income vs Expenses Chart -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Inkomsten vs Uitgaven</h5>
            </div>
            <div class="card-body">
                <canvas id="incomeExpenseChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Capital Over Time Chart -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Vermogen over tijd</h5>
            </div>
            <div class="card-body">
                <canvas id="capitalChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Breakdown -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-cash-coin"></i> Inkomsten Breakdown</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <?php 
                    // Use first year projection for realistic income breakdown
                    $firstYear = $yearlyProjections[0] ?? null;
                    $totalIncome = 0;
                    ?>
                    
                    <?php if ($income['own_income'] ?? 0 > 0): ?>
                    <tr>
                        <td>Eigen inkomen</td>
                        <td class="text-end">€ <?= number_format($income['own_income'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($income['own_income'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <?php if ($firstYear && $firstYear['has_wia'] && ($firstYear['wia_amount'] ?? 0) > 0): ?>
                    <tr>
                        <td>WIA (<?= esc($profile['partner_name'] ?? 'Partner') ?>)</td>
                        <td class="text-end">€ <?= number_format($firstYear['wia_amount'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($firstYear['wia_amount'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <?php if ($firstYear && $firstYear['has_partner_income'] && ($firstYear['partner_income_amount'] ?? 0) > 0): ?>
                    <tr>
                        <td>Inkomen (<?= esc($profile['partner_name'] ?? 'Partner') ?>)</td>
                        <td class="text-end">€ <?= number_format($firstYear['partner_income_amount'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($firstYear['partner_income_amount'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <?php if ($firstYear && $firstYear['has_partner_aow'] && ($firstYear['partner_aow_amount'] ?? 0) > 0): ?>
                    <tr>
                        <td>AOW (<?= esc($profile['partner_name'] ?? 'Partner') ?>)</td>
                        <td class="text-end">€ <?= number_format($firstYear['partner_aow_amount'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($firstYear['partner_aow_amount'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <?php if ($firstYear && $firstYear['has_own_aow'] && ($firstYear['own_aow_amount'] ?? 0) > 0): ?>
                    <tr>
                        <td>Eigen AOW</td>
                        <td class="text-end">€ <?= number_format($firstYear['own_aow_amount'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($firstYear['own_aow_amount'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <?php if ($firstYear && $firstYear['has_own_pension'] && ($firstYear['pension_amount'] ?? 0) > 0): ?>
                    <tr>
                        <td>Pensioen</td>
                        <td class="text-end">€ <?= number_format($firstYear['pension_amount'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($firstYear['pension_amount'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <?php if ($income['other_income'] ?? 0 > 0): ?>
                    <tr>
                        <td>Overig inkomen</td>
                        <td class="text-end">€ <?= number_format($income['other_income'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($income['other_income'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <?php if ($firstYear && ($firstYear['monthly_interest'] ?? 0) > 0): ?>
                    <tr class="text-info">
                        <td><i class="bi bi-graph-up-arrow"></i> Interest op vermogen</td>
                        <td class="text-end">€ <?= number_format($firstYear['monthly_interest'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($firstYear['monthly_interest'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <?php if (($calculations['bnb_net_income'] ?? 0) > 0): ?>
                    <tr>
                        <td>B&B netto inkomen</td>
                        <td class="text-end">€ <?= number_format($calculations['bnb_net_income'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php $totalIncome += ($calculations['bnb_net_income'] ?? 0); ?>
                    <?php endif; ?>
                    
                    <tr class="fw-bold border-top">
                        <td>Totaal</td>
                        <td class="text-end">€ <?= number_format($totalIncome, 2, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-wallet2"></i> Uitgaven Breakdown</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Maandelijkse lasten</td>
                        <td class="text-end">€ <?= number_format($calculations['monthly_expenses'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Belastingen</td>
                        <td class="text-end">€ <?= number_format($calculations['monthly_taxes'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Totaal</td>
                        <td class="text-end">€ <?= number_format(($calculations['monthly_expenses'] ?? 0) + ($calculations['monthly_taxes'] ?? 0), 2, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (($calculations['bnb_revenue'] ?? 0) > 0): ?>
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shop"></i> B&B Overzicht</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-muted small">Maandomzet</div>
                        <div class="h4">€ <?= number_format($calculations['bnb_revenue'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Kosten</div>
                        <div class="h4">€ <?= number_format($calculations['bnb_expenses'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Netto Inkomen</div>
                        <div class="h4 text-success">€ <?= number_format($calculations['bnb_net_income'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-md-3">
                        <a href="/bnb/breakeven" class="btn btn-primary">Break-even Analyse</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Multi-Year Projection Table -->
<?php if (!empty($yearlyProjections) && !empty($profile['date_of_birth'])): ?>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Financiële Projectie per Jaar</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jaar</th>
                                <th>Jouw Leeftijd</th>
                                <?php if (!empty($profile['partner_date_of_birth'])): ?>
                                <th><?= esc($profile['partner_name'] ?? 'Partner') ?> Leeftijd</th>
                                <?php endif; ?>
                                <th class="text-end">Inkomen/mnd</th>
                                <?php if ($calculations['bnb_net_income'] > 0): ?>
                                <th class="text-end"><small>waarvan B&B</small></th>
                                <?php endif; ?>
                                <th class="text-end">Kosten/mnd</th>
                                <th class="text-end">Belasting/mnd</th>
                                <th class="text-end">Netto/mnd</th>
                                <th class="text-end">Netto/jr</th>
                                <th class="text-end">Vermogen</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($yearlyProjections as $index => $projection): ?>
                            <?php
                            // Calculate individual components for modal
                            $ownIncomeAmount = $income['own_income'] ?? 0;
                            $wiaAmount = $projection['wia_amount'] ?? 0;
                            $partnerIncomeAmount = $projection['partner_income_amount'] ?? 0;
                            $partnerAowAmount = $projection['partner_aow_amount'] ?? 0;
                            $ownAowAmount = $projection['own_aow_amount'] ?? 0;
                            $pensionAmount = $projection['pension_amount'] ?? 0;
                            $bnbAmount = $projection['bnb_monthly'] ?? 0;
                            $monthlyInterest = $projection['monthly_interest'] ?? 0;
                            ?>
                            <tr class="projection-row" style="cursor: pointer;" 
                                data-year="<?= $projection['year'] ?>"
                                data-user-age="<?= $projection['user_age'] ?? '-' ?>"
                                data-partner-age="<?= $projection['partner_age'] ?? '-' ?>"
                                data-partner-name="<?= esc($profile['partner_name'] ?? 'Partner') ?>"
                                data-own-income="<?= $ownIncomeAmount ?>"
                                data-wia="<?= $wiaAmount ?>"
                                data-partner-income="<?= $partnerIncomeAmount ?>"
                                data-partner-aow="<?= $partnerAowAmount ?>"
                                data-own-aow="<?= $ownAowAmount ?>"
                                data-pension="<?= $pensionAmount ?>"
                                data-bnb="<?= $bnbAmount ?>"
                                data-monthly-interest="<?= $monthlyInterest ?>"
                                data-monthly-income="<?= $projection['monthly_income'] ?>"
                                data-monthly-expenses="<?= $projection['yearly_expenses'] / 12 ?>"
                                data-monthly-taxes="<?= $projection['yearly_taxes'] / 12 ?>"
                                data-monthly-net="<?= $projection['monthly_net'] ?>"
                                data-yearly-net="<?= $projection['yearly_net'] ?>"
                                data-capital="<?= $projection['capital'] ?>"
                                data-has-partner-retired="<?= $projection['has_partner_retired'] ? 'true' : 'false' ?>"
                                data-has-user-retired="<?= $projection['has_user_retired'] ? 'true' : 'false' ?>"
                                title="Klik voor gedetailleerde berekening"
                                class="<?= $projection['yearly_net'] < 0 ? 'table-danger' : '' ?>">
                                <td><strong><?= $projection['year'] ?></strong></td>
                                <td><?= $projection['user_age'] ?? '-' ?></td>
                                <?php if (!empty($profile['partner_date_of_birth'])): ?>
                                <td><?= $projection['partner_age'] ?? '-' ?></td>
                                <?php endif; ?>
                                <td class="text-end">€ <?= number_format($projection['monthly_income'], 0, ',', '.') ?></td>
                                <?php if ($calculations['bnb_net_income'] > 0): ?>
                                <td class="text-end text-muted"><small>€ <?= number_format($projection['bnb_monthly'], 0, ',', '.') ?></small></td>
                                <?php endif; ?>
                                <td class="text-end text-danger">€ <?= number_format($projection['yearly_expenses'] / 12, 0, ',', '.') ?></td>
                                <td class="text-end text-warning">€ <?= number_format($projection['yearly_taxes'] / 12, 0, ',', '.') ?></td>
                                <td class="text-end <?= $projection['monthly_net'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <strong>€ <?= number_format($projection['monthly_net'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-end <?= $projection['yearly_net'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <strong>€ <?= number_format($projection['yearly_net'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-end <?= $projection['capital'] < 0 ? 'text-danger' : 'text-primary' ?>">
                                    <strong>€ <?= number_format($projection['capital'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php if ($projection['has_wia']): ?>
                                        <?php if ($projection['wia_amount'] > 0): ?>
                                            <span class="badge bg-primary" title="<?= esc($profile['partner_name'] ?? 'Partner') ?> WIA: € <?= number_format($projection['wia_amount'], 0, ',', '.') ?>/mnd">
                                                <i class="bi bi-check-circle-fill"></i> WIA <?= esc($profile['partner_name'] ?? 'Partner') ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($projection['has_partner_income']): ?>
                                        <?php if ($projection['partner_income_amount'] > 0): ?>
                                            <span class="badge bg-secondary" title="<?= esc($profile['partner_name'] ?? 'Partner') ?> Inkomen: € <?= number_format($projection['partner_income_amount'], 0, ',', '.') ?>/mnd">
                                                <i class="bi bi-cash"></i> Inkomen <?= esc($profile['partner_name'] ?? 'Partner') ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($projection['has_own_pension']): ?>
                                        <?php if ($projection['pension_amount'] > 0): ?>
                                            <span class="badge bg-success" title="Jouw Pensioen: € <?= number_format($projection['pension_amount'], 0, ',', '.') ?>/mnd">
                                                <i class="bi bi-check-circle-fill"></i> Pensioen
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark" title="Pensioenleeftijd bereikt, maar bedrag is € 0">
                                                <i class="bi bi-exclamation-circle"></i> Pensioen € 0
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($projection['has_partner_aow']): ?>
                                        <?php if ($projection['partner_aow_amount'] > 0): ?>
                                            <span class="badge bg-info" title="<?= esc($profile['partner_name'] ?? 'Partner') ?> AOW: € <?= number_format($projection['partner_aow_amount'], 0, ',', '.') ?>/mnd">
                                                <i class="bi bi-check-circle-fill"></i> Partner AOW
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark" title="Partner pensioenleeftijd bereikt, maar AOW is € 0">
                                                <i class="bi bi-exclamation-circle"></i> Partner AOW € 0
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($projection['has_own_aow']): ?>
                                        <span class="badge bg-primary" title="Eigen AOW: € <?= number_format($projection['own_aow_amount'], 0, ',', '.') ?>/mnd">
                                            <i class="bi bi-check-circle-fill"></i> Eigen AOW
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-muted">
                <small>
                    <i class="bi bi-info-circle"></i> 
                    Deze projectie toont de verwachte financiële situatie 
                    <?php if (!empty($profile['partner_date_of_birth'])): ?>
                        tot <?= esc($profile['partner_name'] ?? 'partner') ?> 68 jaar is
                    <?php else: ?>
                        voor de komende 15 jaar
                    <?php endif; ?>, inclusief AOW en pensioen op de ingestelde leeftijden.
                    <?php if ($calculations['bnb_net_income'] > 0): ?>
                    <strong>Inkomen/mnd</strong> toont het totale inkomen inclusief B&B. <strong>Netto/mnd</strong> is wat je overhoudt na alle kosten en belastingen.
                    <?php endif; ?>
                    <br><strong>Tip:</strong> Klik op een rij voor de gedetailleerde berekening.
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Calculation Detail Modal -->
<div class="modal fade" id="calculationModal" tabindex="-1" aria-labelledby="calculationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="calculationModalLabel">
                    <i class="bi bi-calculator"></i> Gedetailleerde Berekening voor Jaar <span id="modal-year"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Leeftijden</h6>
                        <p class="mb-1"><strong>Jouw leeftijd:</strong> <span id="modal-user-age"></span> jaar</p>
                        <p class="mb-1"><strong><span id="modal-partner-name-display"></span> leeftijd:</strong> <span id="modal-partner-age"></span> jaar</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Status</h6>
                        <p class="mb-1" id="modal-retirement-status"></p>
                    </div>
                </div>

                <hr>

                <!-- Income Breakdown -->
                <h5 class="mb-3"><i class="bi bi-cash-stack"></i> Inkomsten (per maand)</h5>
                <table class="table table-bordered">
                    <tbody id="income-breakdown">
                        <!-- Filled by JavaScript -->
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td><strong>Totaal Maandinkomen</strong></td>
                            <td class="text-end"><strong id="modal-total-income"></strong></td>
                        </tr>
                    </tfoot>
                </table>

                <hr>

                <!-- Expenses Breakdown -->
                <h5 class="mb-3"><i class="bi bi-cart"></i> Uitgaven (per maand)</h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Maandelijkse vaste kosten</td>
                            <td class="text-end text-danger" id="modal-expenses"></td>
                        </tr>
                        <tr>
                            <td>Belastingen & heffingen</td>
                            <td class="text-end text-warning" id="modal-taxes"></td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td><strong>Totaal Uitgaven per maand</strong></td>
                            <td class="text-end"><strong id="modal-total-expenses"></strong></td>
                        </tr>
                    </tfoot>
                </table>

                <hr>

                <!-- Net Result -->
                <h5 class="mb-3"><i class="bi bi-graph-up-arrow"></i> Netto Resultaat</h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Netto per maand (inkomen - uitgaven)</td>
                            <td class="text-end" id="modal-net-monthly"></td>
                        </tr>
                        <tr>
                            <td>Netto per jaar</td>
                            <td class="text-end" id="modal-net-yearly"></td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong>Totaal Vermogen eind jaar</strong></td>
                            <td class="text-end"><strong id="modal-capital"></strong></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Calculation Info -->
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Berekening:</strong> Bij deze berekening is rekening gehouden met emigratiereductie op AOW rechten. 
                    Het vermogen wordt berekend door het netto jaarbedrag op te tellen bij het vermogen van het vorige jaar.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Sluiten
                </button>
                <button type="button" class="btn btn-danger" id="exportPdfBtn">
                    <i class="bi bi-file-pdf"></i> Export naar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Income Breakdown per Year (expandable) -->
<div class="row mt-3">
    <div class="col-12">
        <div class="accordion" id="incomeBreakdownAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#incomeBreakdown">
                        <i class="bi bi-list-check"></i>&nbsp; Gedetailleerde Inkomensopbouw per Jaar (uitklappen)
                    </button>
                </h2>
                <div id="incomeBreakdown" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jaar</th>
                                        <th>Leeft.</th>
                                        <th class="text-end">Eigen Ink.</th>
                                        <th class="text-end">WIA <?= esc($profile['partner_name'] ?? 'Partner') ?></th>
                                        <th class="text-end">AOW <?= esc($profile['partner_name'] ?? 'Partner') ?></th>
                                        <th class="text-end">Jouw Pensioen</th>
                                        <th class="text-end">Jouw AOW</th>
                                        <th class="text-end">B&B</th>
                                        <th class="text-end">Spaarrente</th>
                                        <th class="text-end">Totaal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Get income data for breakdown
                                    $ownIncomeData = $income ?? [];
                                    ?>
                                    <?php foreach ($yearlyProjections as $projection): ?>
                                    <?php
                                    // Calculate individual components
                                    $ownIncomeAmount = $ownIncomeData['own_income'] ?? 0;
                                    $wiaAmount = 0;
                                    $displayPartnerAow = 0;
                                    $displayOwnAow = 0;
                                    $displayPension = 0;
                                    
                                    if (!$projection['has_partner_retired']) {
                                        $wiaAmount = $ownIncomeData['wia_wife'] ?? 0;
                                    }
                                    
                                    if ($projection['has_partner_aow']) {
                                        $displayPartnerAow = $projection['partner_aow_amount'];
                                    }
                                    
                                    if ($projection['has_own_aow']) {
                                        $displayOwnAow = $projection['own_aow_amount'];
                                    }
                                    
                                    if ($projection['has_own_pension']) {
                                        $displayPension = $projection['pension_amount'];
                                    }
                                    
                                    $totalWithoutBnb = $ownIncomeAmount + $wiaAmount + $displayPartnerAow + $displayOwnAow + $displayPension;
                                    $monthlyInterest = $projection['monthly_interest'] ?? 0;
                                    ?>
                                    <tr>
                                        <td><?= $projection['year'] ?></td>
                                        <td><?= $projection['user_age'] ?> / <?= $projection['partner_age'] ?></td>
                                        <td class="text-end">€ <?= number_format($ownIncomeAmount, 0, ',', '.') ?></td>
                                        <td class="text-end <?= $wiaAmount > 0 ? '' : 'text-muted' ?>">€ <?= number_format($wiaAmount, 0, ',', '.') ?></td>
                                        <td class="text-end <?= $displayPartnerAow > 0 ? 'text-info fw-bold' : 'text-muted' ?>">€ <?= number_format($displayPartnerAow, 0, ',', '.') ?></td>
                                        <td class="text-end <?= $displayPension > 0 ? 'text-success fw-bold' : 'text-muted' ?>">€ <?= number_format($displayPension, 0, ',', '.') ?></td>
                                        <td class="text-end <?= $displayOwnAow > 0 ? 'text-primary fw-bold' : 'text-muted' ?>">€ <?= number_format($displayOwnAow, 0, ',', '.') ?></td>
                                        <td class="text-end <?= $projection['bnb_monthly'] > 0 ? '' : 'text-muted' ?>">€ <?= number_format($projection['bnb_monthly'], 0, ',', '.') ?></td>
                                        <td class="text-end text-success">€ <?= number_format($monthlyInterest, 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold">€ <?= number_format($projection['monthly_income'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($calculations['bnb_net_income'] > 0): ?>
<div class="row mt-3">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info bg-opacity-10">
                <h6 class="mb-0"><i class="bi bi-house-door"></i> Impact B&B op Financiën</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Situatie MET B&B</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Inkomen/mnd eerste jaar:</td>
                                <td class="text-end"><strong>€ <?= number_format($yearlyProjections[0]['monthly_income'], 0, ',', '.') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Netto/mnd eerste jaar:</td>
                                <td class="text-end <?= $yearlyProjections[0]['monthly_net'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <strong>€ <?= number_format($yearlyProjections[0]['monthly_net'], 0, ',', '.') ?></strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning">Situatie ZONDER B&B</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Inkomen/mnd eerste jaar:</td>
                                <td class="text-end"><strong>€ <?= number_format($yearlyProjections[0]['monthly_income_without_bnb'], 0, ',', '.') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Netto/mnd eerste jaar:</td>
                                <td class="text-end <?= $yearlyProjections[0]['monthly_net_without_bnb'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <strong>€ <?= number_format($yearlyProjections[0]['monthly_net_without_bnb'], 0, ',', '.') ?></strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="alert alert-info mb-0 mt-2">
                    <i class="bi bi-calculator"></i> 
                    <strong>B&B voegt toe:</strong> 
                    € <?= number_format($calculations['bnb_net_income'], 0, ',', '.') ?> netto/maand 
                    (€ <?= number_format($yearlyProjections[0]['monthly_net'] - $yearlyProjections[0]['monthly_net_without_bnb'], 0, ',', '.') ?> verschil in netto na alle kosten)
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Include jsPDF for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
// Calculation Modal Handler
document.addEventListener('DOMContentLoaded', function() {
    const projectionRows = document.querySelectorAll('.projection-row');
    const calculationModal = new bootstrap.Modal(document.getElementById('calculationModal'));
    
    projectionRows.forEach(row => {
        row.addEventListener('click', function() {
            // Get data from row
            const data = {
                year: this.dataset.year,
                userAge: this.dataset.userAge,
                partnerAge: this.dataset.partnerAge,
                partnerName: this.dataset.partnerName,
                ownIncome: parseFloat(this.dataset.ownIncome),
                wia: parseFloat(this.dataset.wia),
                partnerIncome: parseFloat(this.dataset.partnerIncome),
                partnerAow: parseFloat(this.dataset.partnerAow),
                ownAow: parseFloat(this.dataset.ownAow),
                pension: parseFloat(this.dataset.pension),
                bnb: parseFloat(this.dataset.bnb),
                monthlyInterest: parseFloat(this.dataset.monthlyInterest),
                monthlyIncome: parseFloat(this.dataset.monthlyIncome),
                monthlyExpenses: parseFloat(this.dataset.monthlyExpenses),
                monthlyTaxes: parseFloat(this.dataset.monthlyTaxes),
                monthlyNet: parseFloat(this.dataset.monthlyNet),
                yearlyNet: parseFloat(this.dataset.yearlyNet),
                capital: parseFloat(this.dataset.capital),
                hasPartnerRetired: this.dataset.hasPartnerRetired === 'true',
                hasUserRetired: this.dataset.hasUserRetired === 'true'
            };
            
            // Fill modal
            fillCalculationModal(data);
            
            // Show modal
            calculationModal.show();
        });
    });
    
    function fillCalculationModal(data) {
        // Header
        document.getElementById('modal-year').textContent = data.year;
        document.getElementById('modal-user-age').textContent = data.userAge;
        document.getElementById('modal-partner-age').textContent = data.partnerAge;
        document.getElementById('modal-partner-name-display').textContent = data.partnerName;
        
        // Retirement status
        let statusHtml = '';
        if (data.hasUserRetired) {
            statusHtml += '<span class=\"badge bg-success\"><i class=\"bi bi-check-circle\"></i> Jij met pensioen</span> ';
        }
        if (data.hasPartnerRetired) {
            statusHtml += '<span class=\"badge bg-info\"><i class=\"bi bi-check-circle\"></i> ' + data.partnerName + ' met pensioen</span>';
        }
        if (!data.hasUserRetired && !data.hasPartnerRetired) {
            statusHtml = '<span class=\"badge bg-secondary\">Nog niet met pensioen</span>';
        }
        document.getElementById('modal-retirement-status').innerHTML = statusHtml;
        
        // Income breakdown
        let incomeHtml = '';
        if (data.ownIncome > 0) {
            incomeHtml += `<tr><td>Eigen inkomen</td><td class=\"text-end\">€ ${formatNumber(data.ownIncome)}</td></tr>`;
        }
        if (data.wia > 0) {
            incomeHtml += `<tr><td>WIA ${data.partnerName}</td><td class=\"text-end\">€ ${formatNumber(data.wia)}</td></tr>`;
        }
        if (data.partnerIncome > 0) {
            incomeHtml += `<tr><td>Inkomen ${data.partnerName}</td><td class=\"text-end\">€ ${formatNumber(data.partnerIncome)}</td></tr>`;
        }
        if (data.partnerAow > 0) {
            incomeHtml += `<tr><td>AOW ${data.partnerName} <small class=\"text-muted\">(met emigratie reductie)</small></td><td class=\"text-end text-info\"><strong>€ ${formatNumber(data.partnerAow)}</strong></td></tr>`;
        }
        if (data.pension > 0) {
            incomeHtml += `<tr><td>Jouw Pensioen</td><td class=\"text-end text-success\"><strong>€ ${formatNumber(data.pension)}</strong></td></tr>`;
        }
        if (data.ownAow > 0) {
            incomeHtml += `<tr><td>Jouw AOW <small class=\"text-muted\">(met emigratie reductie)</small></td><td class=\"text-end text-primary\"><strong>€ ${formatNumber(data.ownAow)}</strong></td></tr>`;
        }
        if (data.bnb > 0) {
            incomeHtml += `<tr><td>B&B Netto inkomen</td><td class=\"text-end\">€ ${formatNumber(data.bnb)}</td></tr>`;
        }
        if (data.monthlyInterest > 0) {
            incomeHtml += `<tr><td>Spaarrente</td><td class="text-end text-success">€ ${formatNumber(data.monthlyInterest)}</td></tr>`;
        }
        document.getElementById('income-breakdown').innerHTML = incomeHtml;
        
        // Totals
        document.getElementById('modal-total-income').textContent = '€ ' + formatNumber(data.monthlyIncome);
        document.getElementById('modal-expenses').textContent = '€ ' + formatNumber(data.monthlyExpenses);
        document.getElementById('modal-taxes').textContent = '€ ' + formatNumber(data.monthlyTaxes);
        
        const totalExpenses = data.monthlyExpenses + data.monthlyTaxes;
        document.getElementById('modal-total-expenses').textContent = '€ ' + formatNumber(totalExpenses);
        
        // Net result
        const netMonthlyElement = document.getElementById('modal-net-monthly');
        netMonthlyElement.textContent = '€ ' + formatNumber(data.monthlyNet);
        netMonthlyElement.className = 'text-end ' + (data.monthlyNet >= 0 ? 'text-success' : 'text-danger');
        
        const netYearlyElement = document.getElementById('modal-net-yearly');
        netYearlyElement.textContent = '€ ' + formatNumber(data.yearlyNet);
        netYearlyElement.className = 'text-end ' + (data.yearlyNet >= 0 ? 'text-success' : 'text-danger');
        
        const capitalElement = document.getElementById('modal-capital');
        capitalElement.textContent = '€ ' + formatNumber(data.capital);
        capitalElement.className = 'text-end ' + (data.capital >= 0 ? 'text-primary' : 'text-danger');
        
        // Store data for PDF export
        window.currentCalculationData = data;
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat('nl-NL', { 
            minimumFractionDigits: 0, 
            maximumFractionDigits: 0 
        }).format(num);
    }
    
    // PDF Export
    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        let yPos = 20;
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const margin = 15;
        
        // Helper function to check if we need a new page
        function checkNewPage(neededSpace = 30) {
            if (yPos + neededSpace > pageHeight - 20) {
                doc.addPage();
                yPos = 20;
                return true;
            }
            return false;
        }
        
        // Title
        doc.setFillColor(0, 51, 102);
        doc.rect(0, 0, pageWidth, 35, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(22);
        doc.setFont(undefined, 'bold');
        doc.text('Financieel Emigratieplan', margin, 20);
        doc.setFontSize(11);
        doc.setFont(undefined, 'normal');
        doc.text('Gegenereerd op ' + new Date().toLocaleDateString('nl-NL'), margin, 28);
        
        // Reset colors
        doc.setTextColor(0, 0, 0);
        yPos = 45;
        
        // ==================== HUIDIGE SITUATIE ====================
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(0, 51, 102);
        doc.text('Huidige Financiële Situatie', margin, yPos);
        yPos += 3;
        doc.setLineWidth(0.5);
        doc.setDrawColor(0, 51, 102);
        doc.line(margin, yPos, pageWidth - margin, yPos);
        yPos += 10;
        doc.setTextColor(0, 0, 0);
        
        // Summary boxes
        doc.setFontSize(10);
        const boxWidth = (pageWidth - (margin * 2) - 6) / 3;
        const boxHeight = 18;
        
        // Income box
        doc.setFillColor(222, 247, 236);
        doc.roundedRect(margin, yPos, boxWidth, boxHeight, 2, 2, 'FD');
        doc.setFont(undefined, 'normal');
        doc.text('Maandinkomen', margin + 3, yPos + 6);
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(40, 167, 69);
        doc.text('\ ' + formatNumber(<?= $calculations['total_monthly_income'] ?? 0 ?>), margin + 3, yPos + 14);
        
        // Expenses box
        doc.setTextColor(0, 0, 0);
        doc.setFillColor(254, 243, 243);
        doc.roundedRect(margin + boxWidth + 3, yPos, boxWidth, boxHeight, 2, 2, 'FD');
        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');
        doc.text('Maandkosten', margin + boxWidth + 6, yPos + 6);
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(220, 53, 69);
        doc.text('\ ' + formatNumber(<?= ($calculations['monthly_expenses'] ?? 0) + ($calculations['monthly_taxes'] ?? 0) ?>), margin + boxWidth + 6, yPos + 14);
        
        // Net box
        doc.setTextColor(0, 0, 0);
        const netValue = <?= $calculations['net_disposable'] ?? 0 ?>;
        doc.setFillColor(netValue >= 0 ? 222 : 254, netValue >= 0 ? 247 : 243, netValue >= 0 ? 236 : 243);
        doc.roundedRect(margin + (boxWidth * 2) + 6, yPos, boxWidth, boxHeight, 2, 2, 'FD');
        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');
        doc.text('Netto Maandelijks', margin + (boxWidth * 2) + 9, yPos + 6);
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(netValue >= 0 ? 40 : 220, netValue >= 0 ? 167 : 53, netValue >= 0 ? 69 : 69);
        doc.text('\ ' + formatNumber(netValue), margin + (boxWidth * 2) + 9, yPos + 14);
        doc.setTextColor(0, 0, 0);
        
        yPos += 25;
        checkNewPage();
        
        // ==================== INKOMSTEN DETAILS ====================
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(0, 51, 102);
        doc.text('Inkomsten (per maand)', margin, yPos);
        yPos += 8;
        doc.setTextColor(0, 0, 0);
        
        const incomeData = [];
        <?php if (($income['own_income'] ?? 0) > 0): ?>
        incomeData.push(['Eigen inkomen', '\ ' + formatNumber(<?= $income['own_income'] ?>)]);
        <?php endif; ?>
        <?php if (($income['wia_wife'] ?? 0) > 0): ?>
        incomeData.push(['WIA <?= esc($profile['partner_name'] ?? 'Partner') ?>', '\ ' + formatNumber(<?= $income['wia_wife'] ?>)]);
        <?php endif; ?>
        <?php if (($income['aow_future'] ?? 0) > 0): ?>
        incomeData.push(['AOW <?= esc($profile['partner_name'] ?? 'Partner') ?> (met reductie)', '\ ' + formatNumber(<?= ($income['aow_future'] ?? 0) * (calculate_AOW_percentage($profile['emigration_date'] ?? date('Y-m-d'), $profile['partner_date_of_birth'] ?? date('Y-m-d'), $profile['partner_retirement_age'] ?? 67) / 100) ?>)]);
        <?php endif; ?>
        <?php if (($income['pension'] ?? 0) > 0): ?>
        incomeData.push(['Pensioen', '\ ' + formatNumber(<?= $income['pension'] ?>)]);
        <?php endif; ?>
        <?php if (($income['own_aow'] ?? 0) > 0): ?>
        incomeData.push(['Eigen AOW (met reductie)', '\ ' + formatNumber(<?= ($income['own_aow'] ?? 0) * (calculate_AOW_percentage($profile['emigration_date'] ?? date('Y-m-d'), $profile['date_of_birth'] ?? date('Y-m-d'), $profile['retirement_age'] ?? 67) / 100) ?>)]);
        <?php endif; ?>
        <?php if (($income['other_income'] ?? 0) > 0): ?>
        incomeData.push(['Overig inkomen', '\ ' + formatNumber(<?= $income['other_income'] ?>)]);
        <?php endif; ?>
        <?php if (($calculations['bnb_net_income'] ?? 0) > 0): ?>
        incomeData.push(['B&B netto inkomen', '\ ' + formatNumber(<?= $calculations['bnb_net_income'] ?>)]);
        <?php endif; ?>
        <?php if (($startPosition['interest_rate'] ?? 0) > 0): ?>
        const yearlyInterest = <?= $startPosition['total_starting_capital'] ?? 0 ?> * (<?= $startPosition['interest_rate'] ?? 2 ?> / 100);
        const monthlyInterest = yearlyInterest / 12;
        incomeData.push(['Spaarrente (<?= $startPosition['interest_rate'] ?? 2 ?>%)', '\ ' + formatNumber(monthlyInterest)]);
        <?php endif; ?>
        
        if (incomeData.length > 0) {
            doc.autoTable({
                startY: yPos,
                head: [],
                body: incomeData,
                theme: 'plain',
                styles: { fontSize: 10, cellPadding: 2 },
                columnStyles: {
                    0: { cellWidth: 120 },
                    1: { halign: 'right', fontStyle: 'bold' }
                },
                margin: { left: margin }
            });
            yPos = doc.lastAutoTable.finalY + 2;
        }
        
        // Total Income
        doc.setFillColor(40, 167, 69);
        doc.rect(margin, yPos, pageWidth - (margin * 2), 8, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFont(undefined, 'bold');
        doc.setFontSize(11);
        doc.text('Totaal Inkomen', margin + 3, yPos + 5.5);
        doc.text('\ ' + formatNumber(<?= $calculations['total_monthly_income'] ?? 0 ?>), pageWidth - margin - 3, yPos + 5.5, { align: 'right' });
        doc.setTextColor(0, 0, 0);
        
        yPos += 15;
        checkNewPage(80);
        
        // ==================== UITGAVEN DETAILS ====================
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(0, 51, 102);
        doc.text('Maandelijkse Uitgaven', margin, yPos);
        yPos += 8;
        doc.setTextColor(0, 0, 0);
        
        const expenseData = [];
        <?php if (!empty($expenses)): ?>
        <?php if (($expenses['energy'] ?? 0) > 0): ?>expenseData.push(['Energie', '\ ' + formatNumber(<?= $expenses['energy'] ?>)]);<?php endif; ?>
        <?php if (($expenses['water'] ?? 0) > 0): ?>expenseData.push(['Water', '\ ' + formatNumber(<?= $expenses['water'] ?>)]);<?php endif; ?>
        <?php if (($expenses['internet'] ?? 0) > 0): ?>expenseData.push(['Internet', '\ ' + formatNumber(<?= $expenses['internet'] ?>)]);<?php endif; ?>
        <?php if (($expenses['health_insurance'] ?? 0) > 0): ?>expenseData.push(['Zorgverzekering', '\ ' + formatNumber(<?= $expenses['health_insurance'] ?>)]);<?php endif; ?>
        <?php if (($expenses['car_insurance'] ?? 0) > 0): ?>expenseData.push(['Autoverzekering', '\ ' + formatNumber(<?= $expenses['car_insurance'] ?>)]);<?php endif; ?>
        <?php if (($expenses['car_fuel'] ?? 0) > 0): ?>expenseData.push(['Brandstof', '\ ' + formatNumber(<?= $expenses['car_fuel'] ?>)]);<?php endif; ?>
        <?php if (($expenses['car_maintenance'] ?? 0) > 0): ?>expenseData.push(['Auto onderhoud', '\ ' + formatNumber(<?= $expenses['car_maintenance'] ?>)]);<?php endif; ?>
        <?php if (($expenses['groceries'] ?? 0) > 0): ?>expenseData.push(['Boodschappen', '\ ' + formatNumber(<?= $expenses['groceries'] ?>)]);<?php endif; ?>
        <?php if (($expenses['leisure'] ?? 0) > 0): ?>expenseData.push(['Vrije tijd', '\ ' + formatNumber(<?= $expenses['leisure'] ?>)]);<?php endif; ?>
        <?php if (($expenses['unforeseen'] ?? 0) > 0): ?>expenseData.push(['Onvoorzien', '\ ' + formatNumber(<?= $expenses['unforeseen'] ?>)]);<?php endif; ?>
        <?php if (($expenses['other'] ?? 0) > 0): ?>expenseData.push(['Overige kosten', '\ ' + formatNumber(<?= $expenses['other'] ?>)]);<?php endif; ?>
        <?php endif; ?>
        
        <?php if (!empty($mainProperty)): ?>
        <?php if (($mainProperty['annual_costs'] ?? 0) > 0): ?>
        expenseData.push(['Hoofdwoning vaste lasten (jaar)', '\ ' + formatNumber(<?= ($mainProperty['annual_costs'] ?? 0) / 12 ?>)]);
        <?php endif; ?>
        <?php if (($mainProperty['maintenance_yearly'] ?? 0) > 0): ?>
        expenseData.push(['Hoofdwoning onderhoud (jaar)', '\ ' + formatNumber(<?= ($mainProperty['maintenance_yearly'] ?? 0) / 12 ?>)]);
        <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!empty($secondProperty)): ?>
        <?php if (($secondProperty['annual_costs'] ?? 0) > 0): ?>
        expenseData.push(['2e woning vaste lasten (jaar)', '\ ' + formatNumber(<?= ($secondProperty['annual_costs'] ?? 0) / 12 ?>)]);
        <?php endif; ?>
        <?php if (($secondProperty['maintenance_yearly'] ?? 0) > 0): ?>
        expenseData.push(['2e woning onderhoud (jaar)', '\ ' + formatNumber(<?= ($secondProperty['maintenance_yearly'] ?? 0) / 12 ?>)]);
        <?php endif; ?>
        <?php if (($secondProperty['energy_monthly'] ?? 0) > 0): ?>
        expenseData.push(['2e woning energie', '\ ' + formatNumber(<?= $secondProperty['energy_monthly'] ?>)]);
        <?php endif; ?>
        <?php if (($secondProperty['other_monthly_costs'] ?? 0) > 0): ?>
        expenseData.push(['2e woning overige kosten', '\ ' + formatNumber(<?= $secondProperty['other_monthly_costs'] ?>)]);
        <?php endif; ?>
        <?php endif; ?>
        
        if (expenseData.length > 0) {
            doc.autoTable({
                startY: yPos,
                head: [],
                body: expenseData,
                theme: 'plain',
                styles: { fontSize: 10, cellPadding: 2 },
                columnStyles: {
                    0: { cellWidth: 120 },
                    1: { halign: 'right', fontStyle: 'bold' }
                },
                margin: { left: margin }
            });
            yPos = doc.lastAutoTable.finalY + 2;
        }
        
        doc.setFont(undefined, 'bold');
        doc.setFontSize(10);
        doc.text('Subtotaal Uitgaven:', margin + 3, yPos + 5);
        doc.text('\ ' + formatNumber(<?= $calculations['monthly_expenses'] ?? 0 ?>), pageWidth - margin - 3, yPos + 5, { align: 'right' });
        
        yPos += 12;
        checkNewPage(50);
        
        // ==================== BELASTINGEN ====================
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(0, 51, 102);
        doc.text('Belastingen', margin, yPos);
        yPos += 8;
        doc.setTextColor(0, 0, 0);
        
        const taxData = [];
        <?php if (!empty($taxes)): ?>
        <?php if (($taxes['tari_yearly'] ?? 0) > 0): ?>
        taxData.push(['TARI hoofdwoning', '\ ' + formatNumber(<?= ($taxes['tari_yearly'] ?? 0) / 12 ?>)]);
        <?php endif; ?>
        <?php if (($taxes['social_contributions'] ?? 0) > 0): ?>
        taxData.push(['Sociale bijdragen (INPS)', '\ ' + formatNumber(<?= $taxes['social_contributions'] ?>)]);
        <?php endif; ?>
        <?php if (($taxes['road_tax_yearly'] ?? 0) > 0): ?>
        taxData.push(['Wegenbelasting (Bollo auto)', '\ ' + formatNumber(<?= ($taxes['road_tax_yearly'] ?? 0) / 12 ?>)]);
        <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($secondProperty)): ?>
        <?php if (($secondProperty['tari_yearly'] ?? 0) > 0): ?>
        taxData.push(['TARI 2e woning', '\ ' + formatNumber(<?= ($secondProperty['tari_yearly'] ?? 0) / 12 ?>)]);
        <?php endif; ?>
        <?php if (($secondProperty['imu_tax'] ?? 0) > 0): ?>
        taxData.push(['IMU 2e woning', '\ ' + formatNumber(<?= ($secondProperty['imu_tax'] ?? 0) / 12 ?>)]);
        <?php endif; ?>
        <?php endif; ?>
        <?php if (($calculations['bnb_net_income'] ?? 0) > 0 && !empty($taxes) && ($taxes['forfettario_enabled'] ?? 0)): ?>
        taxData.push(['B&B belasting (Forfettario)', '\ ' + formatNumber((<?= $calculations['bnb_revenue'] ?? 0 ?> * <?= $taxes['forfettario_percentage'] ?? 15 ?>) / 100)]);
        <?php endif; ?>
        
        if (taxData.length > 0) {
            doc.autoTable({
                startY: yPos,
                head: [],
                body: taxData,
                theme: 'plain',
                styles: { fontSize: 10, cellPadding: 2 },
                columnStyles: {
                    0: { cellWidth: 120 },
                    1: { halign: 'right', fontStyle: 'bold' }
                },
                margin: { left: margin }
            });
            yPos = doc.lastAutoTable.finalY + 2;
        }
        
        // Total Expenses
        doc.setFillColor(220, 53, 69);
        doc.rect(margin, yPos, pageWidth - (margin * 2), 8, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFont(undefined, 'bold');
        doc.setFontSize(11);
        doc.text('Totaal Uitgaven + Belastingen', margin + 3, yPos + 5.5);
        doc.text('\ ' + formatNumber(<?= ($calculations['monthly_expenses'] ?? 0) + ($calculations['monthly_taxes'] ?? 0) ?>), pageWidth - margin - 3, yPos + 5.5, { align: 'right' });
        doc.setTextColor(0, 0, 0);
        
        yPos += 15;
        checkNewPage(40);
        
        // ==================== NETTO RESULTAAT ====================
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(0, 51, 102);
        doc.text('Netto Resultaat', margin, yPos);
        yPos += 8;
        doc.setTextColor(0, 0, 0);
        
        const netColor = netValue >= 0 ? [40, 167, 69] : [220, 53, 69];
        doc.setFillColor(netColor[0], netColor[1], netColor[2]);
        doc.rect(margin, yPos, pageWidth - (margin * 2), 10, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(12);
        doc.text('Netto per maand:', margin + 3, yPos + 7);
        doc.text('\ ' + formatNumber(netValue), pageWidth - margin - 3, yPos + 7, { align: 'right' });
        yPos += 12;
        
        doc.setFillColor(netColor[0], netColor[1], netColor[2]);
        doc.rect(margin, yPos, pageWidth - (margin * 2), 10, 'F');
        doc.text('Netto per jaar:', margin + 3, yPos + 7);
        doc.text('\ ' + formatNumber(netValue * 12), pageWidth - margin - 3, yPos + 7, { align: 'right' });
        yPos += 12;
        
        doc.setFillColor(0, 102, 204);
        doc.rect(margin, yPos, pageWidth - (margin * 2), 10, 'F');
        doc.text('Huidig vermogen:', margin + 3, yPos + 7);
        doc.text('\ ' + formatNumber(<?= $calculations['remaining_capital'] ?? 0 ?>), pageWidth - margin - 3, yPos + 7, { align: 'right' });
        
        yPos += 20;
        checkNewPage(80);
        doc.setTextColor(0, 0, 0);
        
        // ==================== PROJECTIE ====================        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(0, 51, 102);
        doc.text('Meerjarige Projectie', margin, yPos);
        yPos += 8;
        doc.setTextColor(0, 0, 0);
        
        const projectionData = [];
        <?php foreach ($yearlyProjections as $projection): ?>
        projectionData.push([
            '<?= $projection['year'] ?>',
            '<?= $projection['user_age'] ?? '-' ?>',
            '\ ' + formatNumber(<?= $projection['monthly_income'] ?>),
            '\ ' + formatNumber(<?= $projection['yearly_expenses'] / 12 ?>),
            '\ ' + formatNumber(<?= $projection['yearly_taxes'] / 12 ?>),
            '\ ' + formatNumber(<?= $projection['monthly_net'] ?>),
            '\ ' + formatNumber(<?= $projection['capital'] ?>)
        ]);
        <?php endforeach; ?>
        
        doc.autoTable({
            startY: yPos,
            head: [['Jaar', 'Leeftijd', 'Inkomen', 'Kosten', 'Belasting', 'Netto', 'Vermogen']],
            body: projectionData,
            theme: 'grid',
            styles: { 
                fontSize: 8, 
                cellPadding: 1.5,
                halign: 'right'
            },
            headStyles: {
                fillColor: [0, 51, 102],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center'
            },
            columnStyles: {
                0: { halign: 'center', cellWidth: 20 },
                1: { halign: 'center', cellWidth: 20 },
                2: { cellWidth: 25 },
                3: { cellWidth: 25 },
                4: { cellWidth: 25 },
                5: { cellWidth: 25 },
                6: { cellWidth: 30 }
            },
            margin: { left: margin, right: margin }
        });
        
        yPos = doc.lastAutoTable.finalY + 10;
        
        // Footer note
        doc.setFontSize(9);
        doc.setFont(undefined, 'italic');
        doc.setTextColor(100, 100, 100);
        const footerText = 'Deze berekening is gebaseerd op de huidige gegevens en houdt rekening met AOW-reductie bij emigratie.';
        doc.text(footerText, pageWidth / 2, yPos, { align: 'center' });
        
        // Page numbers
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(9);
            doc.setTextColor(100, 100, 100);
            doc.text('Pagina ' + i + ' van ' + pageCount, pageWidth - margin, pageHeight - 10, { align: 'right' });
        }
        
        // Save
        const fileName = 'Financieel-Emigratieplan-' + new Date().toISOString().split('T')[0] + '.pdf';
        doc.save(fileName);
    });
});
</script>

<script>
// Income vs Expenses Chart
const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
new Chart(incomeExpenseCtx, {
    type: 'bar',
    data: {
        labels: ['Inkomsten', 'Uitgaven', 'Belastingen'],
        datasets: [{
            label: 'Bedrag (€)',
            data: [
                <?= $calculations['total_monthly_income'] ?? 0 ?>,
                <?= $calculations['monthly_expenses'] ?? 0 ?>,
                <?= $calculations['monthly_taxes'] ?? 0 ?>
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(220, 53, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)'
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(220, 53, 69, 1)',
                'rgba(255, 193, 7, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Capital Over Time Chart
const capitalCtx = document.getElementById('capitalChart').getContext('2d');
new Chart(capitalCtx, {
    type: 'line',
    data: {
        labels: ['Nu', '12 maanden', '24 maanden', '36 maanden'],
        datasets: [{
            label: 'Vermogen (€)',
            data: [
                <?= $calculations['remaining_capital'] ?? 0 ?>,
                <?= $calculations['capital_12_months'] ?? 0 ?>,
                <?= $calculations['capital_24_months'] ?? 0 ?>,
                <?= $calculations['capital_36_months'] ?? 0 ?>
            ],
            fill: false,
            borderColor: 'rgb(0, 146, 70)',
            backgroundColor: 'rgba(0, 146, 70, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: false
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
