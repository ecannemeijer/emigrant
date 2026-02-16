<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-building"></i> Italiaans Vastgoed</h1>
    <p class="text-muted">Vul de gegevens van je vastgoed in Italië in</p>
</div>

<form action="/property/save" method="post">
    <?= csrf_field() ?>

    <!-- Main Property -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Hoofdwoning</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="main_purchase_price" class="form-label">Aankoopprijs</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="main_purchase_price" 
                               name="main_purchase_price" value="<?= $mainProperty['purchase_price'] ?? 160000 ?>" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="main_purchase_costs_percentage" class="form-label">Aankoopkosten (%)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="main_purchase_costs_percentage" 
                               name="main_purchase_costs_percentage" value="<?= $mainProperty['purchase_costs_percentage'] ?? 10 ?>" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="main_annual_costs" class="form-label">Jaarlijkse vaste lasten</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="main_annual_costs" 
                               name="main_annual_costs" value="<?= $mainProperty['annual_costs'] ?? 1200 ?>" required>
                    </div>
                    <small class="text-muted">Gemeentebelasting (energie/verzekeringen bij Maandlasten, TARI bij Belastingen)</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="main_maintenance_yearly" class="form-label">Jaarlijks onderhoud</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="main_maintenance_yearly" 
                               name="main_maintenance_yearly" value="<?= $mainProperty['maintenance_yearly'] ?? 1000 ?>" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Property (Optional) -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="has_second_property" name="has_second_property" 
                       <?= $secondProperty ? 'checked' : '' ?> onchange="toggleSecondProperty()">
                <label class="form-check-label" for="has_second_property">
                    <h5 class="mb-0 d-inline">Tweede woning (optioneel)</h5>
                </label>
            </div>
        </div>
        <div class="card-body" id="secondPropertyFields" style="display: <?= $secondProperty ? 'block' : 'none' ?>;">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="second_purchase_price" class="form-label">Aankoopprijs</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="second_purchase_price" 
                               name="second_purchase_price" value="<?= $secondProperty['purchase_price'] ?? 0 ?>">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="second_purchase_costs_percentage" class="form-label">Aankoopkosten (%)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="second_purchase_costs_percentage" 
                               name="second_purchase_costs_percentage" value="<?= $secondProperty['purchase_costs_percentage'] ?? 10 ?>">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="second_annual_costs" class="form-label">Jaarlijkse kosten</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="second_annual_costs" 
                               name="second_annual_costs" value="<?= $secondProperty['annual_costs'] ?? 0 ?>">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="second_energy_monthly" class="form-label">Energie (maand)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="second_energy_monthly" 
                               name="second_energy_monthly" value="<?= $secondProperty['energy_monthly'] ?? 0 ?>">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="second_other_monthly_costs" class="form-label">Overige (maand)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="second_other_monthly_costs" 
                               name="second_other_monthly_costs" value="<?= $secondProperty['other_monthly_costs'] ?? 0 ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="second_imu_tax" class="form-label">IMU belasting (jaar)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="second_imu_tax" 
                               name="second_imu_tax" value="<?= $secondProperty['imu_tax'] ?? 0 ?>">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="second_tari_yearly" class="form-label">TARI belasting (jaar)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="second_tari_yearly" 
                               name="second_tari_yearly" value="<?= $secondProperty['tari_yearly'] ?? 0 ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="second_maintenance_yearly" class="form-label">Onderhoud (jaar)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="second_maintenance_yearly" 
                               name="second_maintenance_yearly" value="<?= $secondProperty['maintenance_yearly'] ?? 0 ?>">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="second_rental_income" class="form-label">Huurinkomsten (maand)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="second_rental_income" 
                               name="second_rental_income" value="<?= $secondProperty['rental_income'] ?? 0 ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> Opslaan
    </button>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleSecondProperty() {
    const checkbox = document.getElementById('has_second_property');
    const fields = document.getElementById('secondPropertyFields');
    fields.style.display = checkbox.checked ? 'block' : 'none';
}
</script>
<?= $this->endSection() ?>
