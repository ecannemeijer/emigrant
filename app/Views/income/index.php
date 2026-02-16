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
                    $partnerName = $profile['partner_name'] ?? 'vrouw';
                    ?>

                    <div class="mb-3">
                        <label for="wia_wife" class="form-label">WIA <?= esc($partnerName) ?> (netto per maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="wia_wife" 
                                   name="wia_wife" value="<?= $income['wia_wife'] ?? 1550 ?>" required>
                        </div>
                        <small class="text-muted">Huidig WIA inkomen</small>
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
                    
                    <div class="alert alert-info">
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
                        <small class="text-muted">Start automatisch bij <?= esc($partnerName) ?> pensioenleeftijd, WIA stopt dan</small>
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

<?= $this->endSection() ?>
