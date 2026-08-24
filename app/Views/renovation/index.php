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
        <p class="mb-0 reno-lead">Begroting, offertes en uitvoering van je Italiaanse huis — met notities per post, kosten en impact op je vermogen.</p>
    </div>
    <button class="btn btn-light" type="button" data-bs-toggle="modal" data-bs-target="#itemModal" onclick="renoNewItem()">
        <i class="bi bi-plus-lg"></i> Nieuwe post
    </button>
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

<?php if (empty($items)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <p class="text-muted mb-3">Nog geen verbouwposten. Voeg keuken, dak, elektra of een eigen post toe.</p>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#itemModal" onclick="renoNewItem()">Eerste post</button>
        </div>
    </div>
<?php endif; ?>

<?php foreach ($grouped as $room => $roomItems): ?>
    <div class="reno-room mb-4">
        <div class="reno-room-head">
            <h2 class="h5 mb-0"><?= esc($room) ?></h2>
            <?php
                $roomSum = 0;
                foreach ($roomItems as $ri) {
                    $roomSum += $lineCost($ri);
                }
            ?>
            <span class="text-muted">€ <?= number_format($roomSum, 0, ',', '.') ?></span>
        </div>
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
                                <?php if (!empty($item['planned_year'])): ?>
                                    <span class="text-muted"><i class="bi bi-calendar3"></i> <?= (int) $item['planned_year'] ?></span>
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
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<p class="small text-muted mt-2">Kosten zijn wat je verwacht te betalen (inclusief btw tenzij je anders noteert). IVA 10% is gebruikelijk bij veel verbouwingen in Italië, 22% bij nieuw werk — check dit met je geometra. Dit is geen bouwkundig advies.</p>

<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/renovation/item" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="item_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalTitle">Verbouwpost</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="item_title">Omschrijving</label>
                        <input type="text" class="form-control" name="title" id="item_title" required placeholder="Bijv. Badkamer boven verdieping">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="item_room">Ruimte / categorie</label>
                            <select class="form-select" name="room" id="item_room">
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= esc($room, 'attr') ?>"><?= esc($room) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="item_status">Status</label>
                            <select class="form-select" name="status" id="item_status">
                                <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?= esc($key, 'attr') ?>"><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="item_priority">Prioriteit</label>
                            <select class="form-select" name="priority" id="item_priority">
                                <?php foreach ($priorities as $key => $label): ?>
                                    <option value="<?= esc($key, 'attr') ?>"><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="item_estimated">Begroot</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="estimated_cost" id="item_estimated" value="0">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="item_actual">Werkelijk</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="actual_cost" id="item_actual" value="0">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="item_vat">IVA</label>
                            <select class="form-select" name="vat_rate" id="item_vat">
                                <option value="10">10% (vaak verbouw)</option>
                                <option value="22">22% (standaard)</option>
                                <option value="4">4%</option>
                                <option value="0">0%</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="item_contractor">Aannemer / vakman</label>
                            <input type="text" class="form-control" name="contractor" id="item_contractor" placeholder="Naam of ditta">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="item_year">Jaar (gepland)</label>
                            <input type="number" class="form-control" name="planned_year" id="item_year" min="2020" max="2040" placeholder="<?= date('Y') ?>">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_in_capital" id="item_capital" value="1" checked>
                        <label class="form-check-label" for="item_capital">Meetellen in resterend vermogen op het dashboard</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="noteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form id="noteForm" method="post">
                <?= csrf_field() ?>
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
<script>
const renoNotes = <?= json_encode($notesById ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
const itemModal = document.getElementById('itemModal');

function renoNewItem() {
    document.getElementById('itemModalTitle').textContent = 'Nieuwe verbouwpost';
    document.getElementById('item_id').value = '';
    document.getElementById('item_title').value = '';
    document.getElementById('item_room').selectedIndex = 0;
    document.getElementById('item_status').value = 'planned';
    document.getElementById('item_priority').value = 'medium';
    document.getElementById('item_estimated').value = 0;
    document.getElementById('item_actual').value = 0;
    document.getElementById('item_vat').value = '10';
    document.getElementById('item_contractor').value = '';
    document.getElementById('item_year').value = '';
    document.getElementById('item_capital').checked = true;
}

function renoEditItem(btn) {
    const item = JSON.parse(btn.getAttribute('data-item'));
    document.getElementById('itemModalTitle').textContent = 'Verbouwpost bewerken';
    document.getElementById('item_id').value = item.id;
    document.getElementById('item_title').value = item.title || '';
    document.getElementById('item_room').value = item.room || 'Overig';
    document.getElementById('item_status').value = item.status || 'planned';
    document.getElementById('item_priority').value = item.priority || 'medium';
    document.getElementById('item_estimated').value = item.estimated_cost || 0;
    document.getElementById('item_actual').value = item.actual_cost || 0;
    document.getElementById('item_vat').value = String(parseFloat(item.vat_rate || 10));
    document.getElementById('item_contractor').value = item.contractor || '';
    document.getElementById('item_year').value = item.planned_year || '';
    document.getElementById('item_capital').checked = String(item.include_in_capital) === '1';
    bootstrap.Modal.getOrCreateInstance(itemModal).show();
}

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
        const url = window.prompt('Link (https://…)', 'https://');
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
