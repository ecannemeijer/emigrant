<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?php
$rooms = $rooms ?? [];
$statuses = $statuses ?? [];
$priorities = $priorities ?? [];
$pct = $totals['active'] > 0 ? round($totals['done'] / $totals['active'] * 100) : 0;
$capShare = $startingCapital > 0 ? min(100, round($totals['capital'] / $startingCapital * 100)) : 0;
$lineCost = static function (array $item): float {
    if (($item['status'] ?? '') === 'skipped') {
        return 0.0;
    }
    $actual = (float) ($item['actual_cost'] ?? 0);

    return $actual > 0 ? $actual : (float) ($item['estimated_cost'] ?? 0);
};
$statusClass = [
    'planned' => 'badge-planned',
    'quoted' => 'badge-quoted',
    'in_progress' => 'badge-progress',
    'done' => 'badge-done',
    'skipped' => 'badge-skipped',
];
$prioClass = ['high' => 'prio-high', 'medium' => 'prio-mid', 'low' => 'prio-low'];
?>

<div class="reno-hero mb-4">
    <div>
        <p class="reno-kicker mb-1">Casa nuova · Cantiere</p>
        <h1 class="mb-2">Verbouwen</h1>
        <p class="mb-0 reno-lead">Maak zelf categorieën (keuken, dak, elektra) en voeg daarna posten met kosten toe. Die kosten gaan van je vermogen op het dashboard.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-light" href="/renovation/planning">
            <i class="bi bi-calendar3"></i> Planning
        </a>
        <button class="btn btn-light" type="button" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="renoNewCategory()">
            <i class="bi bi-folder-plus"></i> Nieuwe categorie
        </button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card positive h-100">
            <div class="card-body">
                <div class="stat-label">Begroot</div>
                <div class="stat-value">€ <?= number_format($totals['estimated'], 0, ',', '.') ?></div>
                <div class="small text-muted">Som van schattingen</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card neutral h-100">
            <div class="card-body">
                <div class="stat-label">Besteed</div>
                <div class="stat-value">€ <?= number_format($totals['actual'], 0, ',', '.') ?></div>
                <div class="small text-muted">Werkelijke kosten</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card negative h-100">
            <div class="card-body">
                <div class="stat-label">Prognose totaal</div>
                <div class="stat-value">€ <?= number_format($totals['forecast'], 0, ',', '.') ?></div>
                <div class="small text-muted">Inclusief <?= number_format((float) $settings['contingency_percent'], 1, ',', '.') ?>% onvoorzien</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card <?= $capShare > 40 ? 'negative' : 'neutral' ?> h-100">
            <div class="card-body">
                <div class="stat-label">Van startvermogen</div>
                <div class="stat-value"><?= $capShare ?>%</div>
                <div class="small text-muted">€ <?= number_format($totals['capital'], 0, ',', '.') ?> gaat van het vermogen af</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small text-muted">Voortgang <?= (int) $totals['done'] ?> / <?= (int) $totals['active'] ?> posten</span>
            <span class="small fw-semibold"><?= $pct ?>%</span>
        </div>
        <div class="progress reno-progress mb-0">
            <div class="progress-bar" style="width: <?= $pct ?>%"></div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="/renovation/settings" method="post" class="row g-3 align-items-end">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label" for="contingency_percent">Onvoorzien op openstaande posten</label>
                <div class="input-group">
                    <input type="number" step="0.5" min="0" max="40" class="form-control" id="contingency_percent"
                           name="contingency_percent" value="<?= esc($settings['contingency_percent'] ?? 10) ?>">
                    <span class="input-group-text">%</span>
                </div>
                <small class="text-muted">Italiaanse verbouwingen lopen vaak 10–15% uit. Dit bedrag telt mee in het vermogen op het dashboard.</small>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Opslaan</button>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="small text-muted">Onvoorzien nu</div>
                <div class="h5 mb-0">€ <?= number_format($totals['contingency'], 0, ',', '.') ?></div>
            </div>
        </form>
    </div>
</div>

<div class="reno-filters mb-3">
    <button type="button" class="chip active" data-filter="all">Alles</button>
    <?php foreach ($statuses as $key => $label): ?>
        <button type="button" class="chip" data-filter="<?= esc($key, 'attr') ?>"><?= esc($label) ?></button>
    <?php endforeach; ?>
</div>

<?php
$catByName = [];
foreach ($categories ?? [] as $cat) {
    $catByName[$cat['name']] = $cat;
}
?>
<?php if (empty($grouped)): ?>
    <div class="reno-empty-cats card">
        <div class="card-body text-center py-5">
            <div class="reno-empty-icon mb-3"><i class="bi bi-folder"></i></div>
            <h2 class="h5">Nog geen categorieën</h2>
            <p class="text-muted mb-3">Begin met een categorie, bijvoorbeeld Keuken of Dak. Daarna voeg je per categorie verbouwposten toe.</p>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="renoNewCategory()">Eerste categorie</button>
        </div>
    </div>
<?php endif; ?>

<?php foreach ($grouped as $room => $roomItems): ?>
    <?php
        $cat = $catByName[$room] ?? null;
        $roomSum = 0;
        foreach ($roomItems as $ri) {
            $roomSum += $lineCost($ri);
        }
    ?>
    <section class="reno-cat mb-4">
        <header class="reno-cat-head">
            <div>
                <p class="reno-cat-kicker mb-0">Categorie</p>
                <h2 class="h4 mb-0"><?= esc($room) ?></h2>
            </div>
            <div class="reno-cat-actions">
                <span class="reno-cat-sum">€ <?= number_format($roomSum, 0, ',', '.') ?></span>
                <?php if ($cat): ?>
                    <button type="button" class="btn btn-sm btn-outline-light"
                            onclick="renoEditCategory(<?= (int) $cat['id'] ?>, <?= json_encode($cat['name'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) ?>)">
                        <i class="bi bi-pencil"></i> Hernoemen
                    </button>
                    <form action="/renovation/category/delete/<?= (int) $cat['id'] ?>" method="post" class="d-inline"
                          onsubmit="return confirm('Categorie verwijderen? Posten blijven bestaan.');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="return_to" value="list">
                        <button class="btn btn-sm btn-outline-light" type="submit"><i class="bi bi-folder-x"></i></button>
                    </form>
                <?php endif; ?>
            </div>
        </header>
        <div class="reno-cat-body">
            <?php if (empty($roomItems)): ?>
                <p class="text-muted small mb-3">Nog geen posten in deze categorie.</p>
            <?php else: ?>
        <div class="row g-3">
            <?php foreach ($roomItems as $item): ?>
                <?php
                    $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($item['notes'] ?? ''))));
                    $hasNote = $plain !== '';
                    $line = $lineCost($item);
                    $editItem = $item;
                    unset($editItem['notes']);
                ?>
                <div class="col-lg-6 reno-card-wrap" data-status="<?= esc($item['status'], 'attr') ?>">
                    <article class="card reno-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between gap-2 mb-2">
                                <h3 class="h6 mb-0"><?= esc($item['title']) ?></h3>
                                <span class="badge <?= $statusClass[$item['status']] ?? 'badge-planned' ?>">
                                    <?= esc($statuses[$item['status']] ?? $item['status']) ?>
                                </span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-3 small">
                                <span class="prio <?= $prioClass[$item['priority']] ?? '' ?>">
                                    <?= esc($priorities[$item['priority']] ?? '') ?> prioriteit
                                </span>
                                <?php if (!empty($item['contractor'])): ?>
                                    <span class="text-muted"><i class="bi bi-person-badge"></i> <?= esc($item['contractor']) ?></span>
                                <?php endif; ?>
                                <?php
                                    $plannedLabel = '';
                                    if (!empty($item['planned_date']) && preg_match('/^(\d{4})-(\d{2})-(\d{2})/', (string) $item['planned_date'], $dm)) {
                                        $plannedLabel = $dm[3] . '-' . $dm[2] . '-' . $dm[1];
                                    } elseif (!empty($item['planned_year'])) {
                                        $plannedLabel = (string) (int) $item['planned_year'] . ' (nog geen dag)';
                                    }
                                ?>
                                <?php if ($plannedLabel !== ''): ?>
                                    <span class="text-muted"><i class="bi bi-calendar3"></i> <?= esc($plannedLabel) ?></span>
                                <?php endif; ?>
                                <span class="text-muted">IVA <?= number_format((float) $item['vat_rate'], 0) ?>%</span>
                                <?php if (!empty($item['include_in_capital'])): ?>
                                    <span class="text-success"><i class="bi bi-piggy-bank"></i> vermogen</span>
                                <?php endif; ?>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="reno-cost">
                                        <span>Begroot</span>
                                        <strong>€ <?= number_format((float) $item['estimated_cost'], 0, ',', '.') ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="reno-cost">
                                        <span>Werkelijk</span>
                                        <strong>€ <?= number_format((float) $item['actual_cost'], 0, ',', '.') ?></strong>
                                    </div>
                                </div>
                            </div>
                            <?php if ($hasNote): ?>
                                <p class="small text-muted mb-3"><i class="bi bi-sticky"></i> <?= esc(mb_strlen($plain) > 90 ? mb_substr($plain, 0, 90) . '…' : $plain) ?></p>
                            <?php endif; ?>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm <?= $hasNote ? 'btn-primary' : 'btn-outline-primary' ?> js-note-btn"
                                        data-id="<?= (int) $item['id'] ?>"
                                        data-title="<?= esc($item['title'], 'attr') ?>">
                                    <i class="bi bi-pencil-square"></i> Notitie
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-item="<?= esc(json_encode($editItem, JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE), 'attr') ?>"
                                        onclick="renoEditItem(this)">
                                    <i class="bi bi-sliders"></i> Bewerken
                                </button>
                                <form action="/renovation/item/delete/<?= (int) $item['id'] ?>" method="post" onsubmit="return confirm('Deze post verwijderen?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="return_to" value="list">
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
            <?php endif; ?>
            <button type="button" class="reno-add-post" data-bs-toggle="modal" data-bs-target="#itemModal"
                    onclick="renoNewItem(<?= json_encode($room, JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) ?>)">
                <i class="bi bi-plus-circle"></i>
                <span>Nieuwe post in <?= esc($room) ?></span>
                <small>Kosten, offerte, aannemer</small>
            </button>
        </div>
    </section>
<?php endforeach; ?>

<p class="small text-muted mt-2">Kosten zijn wat je verwacht te betalen (inclusief btw tenzij je anders noteert). IVA 10% is gebruikelijk bij veel verbouwingen in Italië, 22% bij nieuw werk — check dit met je geometra. Dit is geen bouwkundig advies.</p>

<?= view('renovation/partials/category_modal', ['returnTo' => 'list']) ?>
<?= view('renovation/partials/item_modal', ['returnTo' => 'list', 'rooms' => $rooms, 'statuses' => $statuses, 'priorities' => $priorities]) ?>

<div class="modal fade" id="noteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form id="noteForm" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="return_to" value="list">
                <div class="modal-header">
                    <h5 class="modal-title" id="noteModalLabel">Notitie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted" id="noteModalItem"></p>
                    <div class="editor-toolbar" role="toolbar">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="bold" title="Vet"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="italic" title="Cursief"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="underline" title="Onderstrepen"><i class="bi bi-type-underline"></i></button>
                        <span class="toolbar-sep"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="insertUnorderedList" title="Opsomming"><i class="bi bi-list-ul"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="insertOrderedList" title="Nummering"><i class="bi bi-list-ol"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="formatBlock" data-value="h3" title="Kop"><i class="bi bi-type-h3"></i></button>
                        <span class="toolbar-sep"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="noteLinkBtn" title="Link"><i class="bi bi-link-45deg"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="removeFormat" title="Opmaak wissen"><i class="bi bi-eraser"></i></button>
                    </div>
                    <div id="noteEditor" class="note-editor" contenteditable="true" data-placeholder="Offertes, afspraken, materialen, CIN, geometra…"></div>
                    <textarea name="notes" id="noteHtml" class="d-none"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="/js/renovation-form.js"></script>
<script>
const renoNotes = <?= json_encode($notesById ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.reno-filters .chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.reno-filters .chip').forEach(function (c) { c.classList.remove('active'); });
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.reno-card-wrap').forEach(function (card) {
                card.style.display = (filter === 'all' || card.dataset.status === filter) ? '' : 'none';
            });
        });
    });

    const modalEl = document.getElementById('noteModal');
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('noteForm');
    const editor = document.getElementById('noteEditor');
    const hidden = document.getElementById('noteHtml');

    document.querySelectorAll('.js-note-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            form.action = '/renovation/note/' + id;
            document.getElementById('noteModalItem').textContent = this.dataset.title || '';
            editor.innerHTML = renoNotes[id] || '';
            modal.show();
            setTimeout(function () { editor.focus(); }, 250);
        });
    });

    document.querySelectorAll('.editor-toolbar [data-cmd]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            editor.focus();
            document.execCommand(this.dataset.cmd, false, this.dataset.value || null);
        });
    });
    document.getElementById('noteLinkBtn').addEventListener('click', function (e) {
        e.preventDefault();
        const url = window.prompt('Link (https://...)', 'https://');
        if (!url) return;
        editor.focus();
        document.execCommand('createLink', false, url);
    });
    form.addEventListener('submit', function () {
        hidden.value = editor.innerHTML;
    });
});
</script>
<?= $this->endSection() ?>
