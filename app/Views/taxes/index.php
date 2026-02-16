<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-receipt"></i> Belastingen</h1>
    <p class="text-muted">Italiaanse belastinginstellingen</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="/taxes/save" method="post">
                    <?= csrf_field() ?>

                    <h5 class="mb-3">Forfettario Regeling</h5>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="forfettario_enabled" 
                                   name="forfettario_enabled" <?= ($taxes['forfettario_enabled'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="forfettario_enabled">
                                Forfettario regeling actief
                            </label>
                        </div>
                        <small class="text-muted">Voor kleine ondernemers, vereenvoudigde belastingaangifte</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="forfettario_percentage" class="form-label">Forfettario percentage</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="forfettario_percentage" 
                                       name="forfettario_percentage" value="<?= $taxes['forfettario_percentage'] ?? 15 ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Standaard 15%, eerste 5 jaar mogelijk 5%</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="normal_tax_percentage" class="form-label">Normaal belastingpercentage</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="normal_tax_percentage" 
                                       name="normal_tax_percentage" value="<?= $taxes['normal_tax_percentage'] ?? 23 ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Vastgoedbelastingen</h5>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Let op:</strong> TARI hoofdwoning vul je hier in. IMU en TARI voor tweede woning vul je in bij <a href="/property">Vastgoed</a>.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tari_yearly" class="form-label">TARI hoofdwoning (jaar)</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="tari_yearly" 
                                       name="tari_yearly" value="<?= $taxes['tari_yearly'] ?? 250 ?>" required>
                            </div>
                            <small class="text-muted">Afvalbelasting hoofdwoning per jaar</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="imu_percentage" class="form-label">IMU percentage (referentie)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="imu_percentage" 
                                       name="imu_percentage" value="<?= $taxes['imu_percentage'] ?? 0.76 ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Richtlijn: 0.76% van cadastrale waarde tweede woning. Vul het berekende bedrag in bij <a href="/property">Vastgoed</a>.</small>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Overige</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="social_contributions" class="form-label">Sociale bijdragen (per maand)</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="social_contributions" 
                                       name="social_contributions" value="<?= $taxes['social_contributions'] ?? 0 ?>">
                            </div>
                            <small class="text-muted">Voor zelfstandigen (INPS)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="road_tax_yearly" class="form-label">Wegenbelasting (jaar)</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="road_tax_yearly" 
                                       name="road_tax_yearly" value="<?= $taxes['road_tax_yearly'] ?? 0 ?>">
                            </div>
                            <small class="text-muted">Bollo auto (jaarlijks)</small>
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
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-info-circle"></i> Info</h5>
                <p class="small">
                    <strong>Forfettario:</strong> Voor ondernemers met omzet tot €85.000. 
                    Vereenvoudigde administratie, vast belastingpercentage.
                </p>
                <p class="small">
                    <strong>IMU:</strong> Gemeentelijke vermogensbelasting op tweede woningen. 
                    Hoofdwoning is vaak vrijgesteld.
                </p>
                <p class="small">
                    <strong>TARI:</strong> Afvalbelasting, varieert per gemeente en grootte woning.
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
