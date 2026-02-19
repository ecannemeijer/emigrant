<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-house-door"></i> Startpositie Nederland</h1>
    <p class="text-muted">Vul je financiële situatie in Nederland in</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="/start-position/save" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="house_sale_price" class="form-label">Verkoopprijs woning NL</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="house_sale_price" 
                                   name="house_sale_price" value="<?= $startPosition['house_sale_price'] ?? 350000 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="mortgage_debt" class="form-label">Hypotheekrestschuld</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="mortgage_debt" 
                                   name="mortgage_debt" value="<?= $startPosition['mortgage_debt'] ?? 100000 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="savings" class="form-label">Spaargeld</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="savings" 
                                   name="savings" value="<?= $startPosition['savings'] ?? 100000 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="interest_rate" class="form-label">Spaarrente per jaar</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="interest_rate" 
                                   name="interest_rate" value="<?= $startPosition['interest_rate'] ?? 2.00 ?>" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Rente die je bank geeft op je vermogen (gemiddeld 2%)</small>
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
                <h5 class="card-title">Berekening</h5>
                <?php if ($startPosition): ?>
                    <div class="table-responsive">
                    <table class="table table-sm">
                        <tr>
                            <td>Verkoopprijs</td>
                            <td class="text-end">€ <?= number_format($startPosition['house_sale_price'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Hypotheek</td>
                            <td class="text-end">- € <?= number_format($startPosition['mortgage_debt'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Netto overwaarde</td>
                            <td class="text-end">€ <?= number_format($startPosition['net_equity'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Spaargeld</td>
                            <td class="text-end">€ <?= number_format($startPosition['savings'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="table-success fw-bold">
                            <td>Totaal Startvermogen</td>
                            <td class="text-end">€ <?= number_format($startPosition['total_starting_capital'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                    </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Vul de gegevens in om het totaal te zien</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
