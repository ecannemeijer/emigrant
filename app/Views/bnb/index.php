<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-shop"></i> B&B Module</h1>
    <p class="text-muted">Bereken de rendabiliteit van je B&B</p>
</div>

<?php if ($settings && $settings['enabled']): ?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card positive">
            <div class="card-body">
                <div class="stat-label">Maandomzet</div>
                <div class="stat-value">€ <?= number_format($calculations['monthly_revenue'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card negative">
            <div class="card-body">
                <div class="stat-label">Maandkosten</div>
                <div class="stat-value">€ <?= number_format($calculations['monthly_expenses'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card positive">
            <div class="card-body">
                <div class="stat-label">Netto Maand</div>
                <div class="stat-value">€ <?= number_format($calculations['net_monthly_income'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card neutral">
            <div class="card-body">
                <div class="stat-label">Jaaromzet</div>
                <div class="stat-value">€ <?= number_format($calculations['yearly_revenue'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <!-- B&B Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Instellingen</h5>
            </div>
            <div class="card-body">
                <form action="/bnb/settings/save" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enabled" 
                                   name="enabled" <?= ($settings['enabled'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="enabled">
                                B&B Module Actief
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="number_of_rooms" class="form-label">Aantal kamers</label>
                        <input type="number" class="form-control" id="number_of_rooms" 
                               name="number_of_rooms" value="<?= $settings['number_of_rooms'] ?? 3 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="price_per_room_per_night" class="form-label">Prijs per kamer per nacht</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="price_per_room_per_night" 
                                   name="price_per_room_per_night" value="<?= $settings['price_per_room_per_night'] ?? 75 ?>" required>
                        </div>
                    </div>

                    <h6 class="mt-4">Bezettingsgraad</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="high_season_percentage" class="form-label">Hoogseizoen</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="high_season_percentage" 
                                       name="high_season_percentage" value="<?= $settings['high_season_percentage'] ?? 80 ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="high_season_months" class="form-label">Maanden</label>
                            <input type="number" class="form-control" id="high_season_months" 
                                   name="high_season_months" value="<?= $settings['high_season_months'] ?? 4 ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="low_season_percentage" class="form-label">Laagseizoen</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="low_season_percentage" 
                                       name="low_season_percentage" value="<?= $settings['low_season_percentage'] ?? 40 ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="low_season_months" class="form-label">Maanden</label>
                            <input type="number" class="form-control" id="low_season_months" 
                                   name="low_season_months" value="<?= $settings['low_season_months'] ?? 8 ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Opslaan
                    </button>
                    <a href="/bnb/breakeven" class="btn btn-info">
                        <i class="bi bi-calculator"></i> Break-even Analyse
                    </a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- B&B Expenses -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Kosten</h5>
            </div>
            <div class="card-body">
                <form action="/bnb/expenses/save" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="extra_energy_water" class="form-label">Extra energie/water (maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="extra_energy_water" 
                                   name="extra_energy_water" value="<?= $expenses['extra_energy_water'] ?? 100 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="insurance" class="form-label">Verzekering (maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="insurance" 
                                   name="insurance" value="<?= $expenses['insurance'] ?? 50 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cleaning" class="form-label">Schoonmaak (maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="cleaning" 
                                   name="cleaning" value="<?= $expenses['cleaning'] ?? 200 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="linen_laundry" class="form-label">Linnen & was (maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="linen_laundry" 
                                   name="linen_laundry" value="<?= $expenses['linen_laundry'] ?? 100 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="platform_commission" class="form-label">Platform commissie</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="platform_commission" 
                                   name="platform_commission" value="<?= $expenses['platform_commission'] ?? 15 ?>" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="marketing" class="form-label">Marketing (maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="marketing" 
                                   name="marketing" value="<?= $expenses['marketing'] ?? 50 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="maintenance" class="form-label">Onderhoud (maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="maintenance" 
                                   name="maintenance" value="<?= $expenses['maintenance'] ?? 150 ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="administration" class="form-label">Administratie/boekhouder (maand)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="administration" 
                                   name="administration" value="<?= $expenses['administration'] ?? 100 ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Opslaan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
