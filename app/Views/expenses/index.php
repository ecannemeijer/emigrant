<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-wallet2"></i> Maandelijkse Lasten</h1>
    <p class="text-muted">Vul je vaste maandelijkse kosten in</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="/expenses/save" method="post">
                    <?= csrf_field() ?>

                    <h5 class="mb-3">Nutsvoorzieningen</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="energy" class="form-label">Energie</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="energy" 
                                       name="energy" value="<?= $expenses['energy'] ?? 150 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="water" class="form-label">Water</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="water" 
                                       name="water" value="<?= $expenses['water'] ?? 30 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="internet" class="form-label">Internet</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="internet" 
                                       name="internet" value="<?= $expenses['internet'] ?? 30 ?>" required>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-3">Verzekeringen</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="health_insurance" class="form-label">Zorgverzekering</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="health_insurance" 
                                       name="health_insurance" value="<?= $expenses['health_insurance'] ?? 200 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="car_insurance" class="form-label">Auto verzekering</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="car_insurance" 
                                       name="car_insurance" value="<?= $expenses['car_insurance'] ?? 80 ?>" required>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-3">Auto</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="car_fuel" class="form-label">Brandstof</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="car_fuel" 
                                       name="car_fuel" value="<?= $expenses['car_fuel'] ?? 150 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="car_maintenance" class="form-label">Onderhoud</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="car_maintenance" 
                                       name="car_maintenance" value="<?= $expenses['car_maintenance'] ?? 50 ?>" required>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-3">Levensonderhoud</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="groceries" class="form-label">Boodschappen</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="groceries" 
                                       name="groceries" value="<?= $expenses['groceries'] ?? 400 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="leisure" class="form-label">Vrije tijd</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="leisure" 
                                       name="leisure" value="<?= $expenses['leisure'] ?? 200 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unforeseen" class="form-label">Onvoorzien</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="unforeseen" 
                                       name="unforeseen" value="<?= $expenses['unforeseen'] ?? 100 ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="other" class="form-label">Overige kosten</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="other" 
                                   name="other" value="<?= $expenses['other'] ?? 0 ?>">
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
                <h5 class="card-title">Totale Maandlasten</h5>
                <?php if ($expenses): ?>
                    <?php 
                    $total = ($expenses['energy'] ?? 0) + 
                             ($expenses['water'] ?? 0) + 
                             ($expenses['internet'] ?? 0) + 
                             ($expenses['health_insurance'] ?? 0) + 
                             ($expenses['car_insurance'] ?? 0) + 
                             ($expenses['car_fuel'] ?? 0) + 
                             ($expenses['car_maintenance'] ?? 0) + 
                             ($expenses['groceries'] ?? 0) + 
                             ($expenses['leisure'] ?? 0) + 
                             ($expenses['unforeseen'] ?? 0) + 
                             ($expenses['other'] ?? 0);
                    ?>
                    <div class="display-6 text-danger">
                        € <?= number_format($total, 2, ',', '.') ?>
                    </div>
                    <p class="text-muted mt-2">Per jaar: € <?= number_format($total * 12, 2, ',', '.') ?></p>
                <?php else: ?>
                    <p class="text-muted">Vul je lasten in</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
