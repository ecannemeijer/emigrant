<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-cash-coin"></i> Inkomsten</h1>
    <p class="text-muted">Vul je maandelijkse inkomsten in</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="/income/save" method="post">
                    <?= csrf_field() ?>

                    <?php 
                    // Get partner name from profile
                    $partnerName = $profile['partner_name'] ?? 'partner';
                    $hasWia = ($income['partner_has_wia'] ?? 1) == 1;
                    ?>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="partner_has_wia" 
                                   name="partner_has_wia" value="1" <?= $hasWia ? 'checked' : '' ?>>
                            <label class="form-check-label" for="partner_has_wia">
                                <strong><?= esc(ucfirst($partnerName)) ?> heeft WIA</strong>
                            </label>
                        </div>
                        <small class="text-muted">Vink uit indien het regulier inkomen betreft (geen WIA)</small>
                    </div>

                    <div class="mb-3">
                        <label for="wia_wife" class="form-label" id="partner_income_label">
                            <span id="income_type_text"><?= $hasWia ? 'WIA' : 'Inkomen' ?></span> <?= esc($partnerName) ?> (netto per maand)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="wia_wife" 
                                   name="wia_wife" value="<?= $income['wia_wife'] ?? 0 ?>" required>
                        </div>
                        <small class="text-muted" id="partner_income_help">
                            <?= $hasWia ? 'Huidig WIA inkomen' : 'Regulier maandinkomen' ?>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="own_income" class="form-label">Eigen inkomen (netto per maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="own_income" 
                                   name="own_income" value="<?= $income['own_income'] ?? 0 ?>" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3"><i class="bi bi-calendar-event"></i> Toekomstige Inkomsten bij Pensioen</h5>
                    
                    <div class="alert alert-info" id="wia_info_alert" style="display: <?= $hasWia ? 'block' : 'none' ?>">
                        <i class="bi bi-info-circle"></i>
                        <strong>Let op:</strong> WIA van <?= esc($partnerName) ?> stopt automatisch wanneer <?= esc($partnerName) ?> 
                        met pensioen gaat (op de leeftijd ingesteld in je profiel). Dan start de WaO van <?= esc($partnerName) ?>.
                    </div>

                    <div class="mb-3">
                        <label for="wao_future" class="form-label">WaO <?= esc($partnerName) ?> bij pensioen (netto per maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="wao_future" 
                                   name="wao_future" value="<?= $income['wao_future'] ?? 0 ?>">
                        </div>
                        <small class="text-muted" id="wao_future_help">
                            <?= $hasWia ? 'Start automatisch bij ' . esc($partnerName) . ' pensioenleeftijd, WIA stopt dan' : 'Start automatisch bij ' . esc($partnerName) . ' pensioenleeftijd' ?>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="own_wao" class="form-label">Eigen WaO bij pensioen (netto per maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="own_wao" 
                                   name="own_wao" value="<?= $income['own_wao'] ?? 0 ?>">
                        </div>
                        <small class="text-muted">Start automatisch bij jouw pensioenleeftijd</small>
                    </div>

                    <div class="mb-3">
                        <label for="pension" class="form-label">Jouw Pensioen (netto per maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="pension" 
                                   name="pension" value="<?= $income['pension'] ?? 0 ?>">
                        </div>
                        <small class="text-muted">Start bij jouw pensioenleeftijd (ingesteld in profiel)</small>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="other_income" class="form-label">Overig inkomen (netto per maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="other_income" 
                                   name="other_income" value="<?= $income['other_income'] ?? 0 ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Opslaan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">Totaal Maandinkomen</h5>
                <?php if ($income): ?>
                    <?php 
                    $total = ($income['wia_wife'] ?? 0) + 
                             ($income['own_income'] ?? 0) + 
                             ($income['wao_future'] ?? 0) + 
                             ($income['pension'] ?? 0) + 
                             ($income['other_income'] ?? 0);
                    ?>
                    <div class="display-6 text-success">
                        € <?= number_format($total, 2, ',', '.') ?>
                    </div>
                    <p class="text-muted mt-2">Per jaar: € <?= number_format($total * 12, 2, ',', '.') ?></p>
                <?php else: ?>
                    <p class="text-muted">Vul je inkomsten in</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('partner_has_wia');
    const incomeTypeText = document.getElementById('income_type_text');
    const partnerIncomeHelp = document.getElementById('partner_income_help');
    const waoFutureHelp = document.getElementById('wao_future_help');
    const wiaInfoAlert = document.getElementById('wia_info_alert');
    const partnerName = '<?= esc($partnerName) ?>';
    
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            // WIA enabled
            incomeTypeText.textContent = 'WIA';
            partnerIncomeHelp.textContent = 'Huidig WIA inkomen';
            waoFutureHelp.textContent = 'Start automatisch bij ' + partnerName + ' pensioenleeftijd, WIA stopt dan';
            wiaInfoAlert.style.display = 'block';
        } else {
            // Regular income
            incomeTypeText.textContent = 'Inkomen';
            partnerIncomeHelp.textContent = 'Regulier maandinkomen';
            waoFutureHelp.textContent = 'Start automatisch bij ' + partnerName + ' pensioenleeftijd';
            wiaInfoAlert.style.display = 'none';
        }
    });
});
</script>

<?= $this->endSection() ?>
