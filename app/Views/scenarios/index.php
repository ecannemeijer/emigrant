<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-diagram-3"></i> Scenario's</h1>
            <p class="text-muted">Sla verschillende financiële scenario's op en vergelijk ze</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveScenarioModal">
            <i class="bi bi-plus-circle"></i> Nieuw Scenario
        </button>
    </div>
</div>

<div class="row">
    <?php if (empty($scenarios)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Je hebt nog geen scenario's opgeslagen. 
                Klik op "Nieuw Scenario" om je huidige financiële situatie op te slaan.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($scenarios as $scenario): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($scenario['name']) ?></h5>
                        <p class="card-text text-muted"><?= esc($scenario['description']) ?></p>
                        
                        <div class="mb-2">
                            <?php if ($scenario['with_bnb']): ?>
                                <span class="badge bg-success">Met B&B</span>
                            <?php endif; ?>
                            <?php if ($scenario['with_second_property']): ?>
                                <span class="badge bg-info">Tweede woning</span>
                            <?php endif; ?>
                        </div>

                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> <?= date('d-m-Y H:i', strtotime($scenario['created_at'])) ?>
                        </small>

                        <div class="mt-3">
                            <a href="/scenarios/load/<?= $scenario['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Bekijken
                            </a>
                            <form action="/scenarios/delete/<?= $scenario['id'] ?>" method="post" class="d-inline" 
                                  onsubmit="return confirm('Weet je zeker dat je dit scenario wilt verwijderen?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Save Scenario Modal -->
<div class="modal fade" id="saveScenarioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scenario Opslaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/scenarios/save" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Naam</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Beschrijving</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small>Je huidige financiële gegevens worden opgeslagen in dit scenario.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
