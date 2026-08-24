<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="page-hero mb-4">
    <div>
        <h1>Emigratie-checklist</h1>
        <p class="text-muted mb-0">Praktische stappen NL → IT. Dit is geen juridisch advies.</p>
    </div>
    <div class="hero-progress">
        <span><?= (int) $done ?> / <?= (int) $total ?></span>
        <div class="progress">
            <div class="progress-bar" style="width: <?= $total ? round($done / $total * 100) : 0 ?>%"></div>
        </div>
    </div>
</div>

<?php foreach ($grouped as $category => $items): ?>
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0"><?= esc($category) ?></h5></div>
        <div class="list-group list-group-flush">
            <?php foreach ($items as $item): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <form action="/checklist/toggle/<?= $item['id'] ?>" method="post" class="d-flex gap-2">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm <?= $item['done'] ? 'btn-success' : 'btn-outline-secondary' ?>" type="submit">
                                <i class="bi <?= $item['done'] ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                            </button>
                            <div>
                                <strong class="<?= $item['done'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                    <?= esc($item['title']) ?>
                                </strong>
                                <?php if (!empty($item['notes'])): ?>
                                    <div class="small text-muted"><?= esc($item['notes']) ?></div>
                                <?php endif; ?>
                            </div>
                        </form>
                        <form action="/checklist/note/<?= $item['id'] ?>" method="post" class="d-flex gap-2 flex-grow-1" style="max-width: 360px;">
                            <?= csrf_field() ?>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Notitie" value="<?= esc($item['notes'] ?? '') ?>">
                            <button class="btn btn-sm btn-outline-primary" type="submit">Bewaar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
<?= $this->endSection() ?>
