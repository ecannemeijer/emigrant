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
        <a class="btn btn-outline-light" href="#reno-planning">
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

<section class="reno-cal-wrap mb-4" id="reno-planning">
    <div class="reno-cal-toolbar">
        <div>
            <p class="reno-kicker mb-1">Planning</p>
            <h2 class="h4 mb-0" id="renoCalTitle">Kalender</h2>
        </div>
        <div class="reno-cal-controls">
            <div class="btn-group" role="group" aria-label="Weergave">
                <button type="button" class="btn btn-sm btn-outline-light reno-cal-view" data-view="week">Week</button>
                <button type="button" class="btn btn-sm btn-light reno-cal-view active" data-view="month">Maand</button>
                <button type="button" class="btn btn-sm btn-outline-light reno-cal-view" data-view="year">Jaar</button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-light" id="renoCalPrev" aria-label="Vorige"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="btn btn-sm btn-outline-light" id="renoCalToday">Vandaag</button>
                <button type="button" class="btn btn-sm btn-outline-light" id="renoCalNext" aria-label="Volgende"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
    <div class="reno-cal-legend">
        <span><i class="cal-dot planned"></i> Gepland</span>
        <span><i class="cal-dot quoted"></i> Offerte</span>
        <span><i class="cal-dot in_progress"></i> Bezig</span>
        <span><i class="cal-dot done"></i> Klaar</span>
        <span><i class="cal-dot skipped"></i> Vervalt</span>
    </div>
    <div id="renoCalendar" class="reno-calendar"></div>
    <div id="renoCalUnscheduled" class="reno-cal-unscheduled"></div>
</section>

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

<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content reno-cat-modal">
            <form action="/renovation/category" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="category_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Nieuwe categorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Een categorie is een groep, zoals Keuken of Dak. Posten (kosten) voeg je daarna toe.</p>
                    <label class="form-label" for="category_name">Naam</label>
                    <input type="text" class="form-control" name="name" id="category_name" required maxlength="80" placeholder="Bijv. Keuken">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-folder-plus"></i> Categorie opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                            <label class="form-label" for="item_room">Categorie</label>
                            <select class="form-select" name="room" id="item_room" required>
                                <?php if (empty($rooms)): ?>
                                    <option value="">Maak eerst een categorie</option>
                                <?php else: ?>
                                    <?php foreach ($rooms as $room): ?>
                                        <option value="<?= esc($room, 'attr') ?>"><?= esc($room) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                            <label class="form-label" for="item_date">Geplande dag</label>
                            <input type="date" class="form-control" name="planned_date" id="item_date">
                            <input type="hidden" name="planned_year" id="item_year">
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
const renoCalItems = <?= json_encode($calendarItems ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
const renoCalCats = <?= json_encode($categories ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
const itemModal = document.getElementById('itemModal');

function syncItemYearFromDate() {
    const dateVal = document.getElementById('item_date').value;
    document.getElementById('item_year').value = dateVal ? dateVal.slice(0, 4) : '';
}

function renoNewItem(room, dateStr) {
    document.getElementById('itemModalTitle').textContent = 'Nieuwe verbouwpost';
    document.getElementById('item_id').value = '';
    document.getElementById('item_title').value = '';
    const roomSelect = document.getElementById('item_room');
    if (room) {
        roomSelect.value = room;
    } else {
        roomSelect.selectedIndex = 0;
    }
    document.getElementById('item_status').value = 'planned';
    document.getElementById('item_priority').value = 'medium';
    document.getElementById('item_estimated').value = 0;
    document.getElementById('item_actual').value = 0;
    document.getElementById('item_vat').value = '10';
    document.getElementById('item_contractor').value = '';
    document.getElementById('item_date').value = dateStr || '';
    syncItemYearFromDate();
    document.getElementById('item_capital').checked = true;
}

function renoFillItem(item) {
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
    document.getElementById('item_date').value = item.planned_date || '';
    if (!item.planned_date && item.planned_year) {
        document.getElementById('item_date').value = String(item.planned_year) + '-01-01';
    }
    syncItemYearFromDate();
    document.getElementById('item_capital').checked = String(item.include_in_capital) === '1';
    bootstrap.Modal.getOrCreateInstance(itemModal).show();
}

function renoEditItem(btn) {
    renoFillItem(JSON.parse(btn.getAttribute('data-item')));
}

function renoNewCategory() {
    document.getElementById('categoryModalTitle').textContent = 'Nieuwe categorie';
    document.getElementById('category_id').value = '';
    document.getElementById('category_name').value = '';
}

function renoEditCategory(id, name) {
    document.getElementById('categoryModalTitle').textContent = 'Categorie hernoemen';
    document.getElementById('category_id').value = id;
    document.getElementById('category_name').value = name;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryModal')).show();
}

document.getElementById('item_date').addEventListener('change', syncItemYearFromDate);

(function () {
    const DAYS = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];
    const MONTHS = ['januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'];
    const root = document.getElementById('renoCalendar');
    const titleEl = document.getElementById('renoCalTitle');
    const unscheduledEl = document.getElementById('renoCalUnscheduled');
    if (!root) return;

    let view = 'month';
    let cursor = new Date();
    cursor.setHours(12, 0, 0, 0);

    function ymd(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + day;
    }
    function parseDate(item) {
        if (item.planned_date) return item.planned_date.slice(0, 10);
        return null;
    }
    function mondayOf(d) {
        const x = new Date(d);
        const day = (x.getDay() + 6) % 7;
        x.setDate(x.getDate() - day);
        return x;
    }
    function itemsOn(dateStr) {
        return renoCalItems.filter(function (it) { return parseDate(it) === dateStr; });
    }
    function catFor(name) {
        return renoCalCats.find(function (c) { return c.name === name; }) || null;
    }
    function chipHtml(item) {
        const payload = encodeURIComponent(JSON.stringify(item));
        const cat = item.room ? '<span class="cal-chip-cat">' + escapeHtml(item.room) + '</span> ' : '';
        return '<button type="button" class="cal-chip cal-' + escapeHtml(item.status || 'planned') + ' prio-' + escapeHtml(item.priority || 'medium') + '" data-payload="' + payload + '">' + cat + escapeHtml(item.title || 'Post') + '</button>';
    }
    function escapeHtml(s) {
        return String(s || '').replace(/[&<>"']/g, function (c) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]);
        });
    }

    function render() {
        const y = cursor.getFullYear();
        const m = cursor.getMonth();
        if (view === 'week') {
            const start = mondayOf(cursor);
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            titleEl.textContent = start.getDate() + '–' + end.getDate() + ' ' + MONTHS[end.getMonth()] + ' ' + end.getFullYear();
            let html = '<div class="cal-week-head">' + DAYS.map(function (d) { return '<div>' + d + '</div>'; }).join('') + '</div><div class="cal-week">';
            for (let i = 0; i < 7; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                html += dayCell(d, true);
            }
            html += '</div>';
            root.innerHTML = html;
        } else if (view === 'year') {
            titleEl.textContent = String(y);
            let html = '<div class="cal-year">';
            for (let mi = 0; mi < 12; mi++) {
                html += miniMonth(y, mi);
            }
            html += '</div>';
            root.innerHTML = html;
        } else {
            titleEl.textContent = MONTHS[m] + ' ' + y;
            const first = new Date(y, m, 1);
            const start = mondayOf(first);
            let html = '<div class="cal-month-head">' + DAYS.map(function (d) { return '<div>' + d + '</div>'; }).join('') + '</div><div class="cal-month">';
            for (let i = 0; i < 42; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                html += dayCell(d, false, d.getMonth() !== m);
            }
            html += '</div>';
            root.innerHTML = html;
        }
        bindChips();
        renderUnscheduled();
    }

    function dayCell(d, large, muted) {
        const key = ymd(d);
        const today = ymd(new Date()) === key;
        const list = itemsOn(key);
        let extra = '';
        if (large) {
            extra = list.map(chipHtml).join('');
        } else if (view === 'month') {
            extra = list.slice(0, 4).map(chipHtml).join('') + (list.length > 4 ? '<span class="cal-more">+' + (list.length - 4) + '</span>' : '');
        }
        return '<div class="cal-day' + (muted ? ' muted' : '') + (today ? ' today' : '') + (list.length ? ' has-items' : '') + '" data-date="' + key + '">' +
            '<span class="cal-num">' + d.getDate() + '</span>' + extra + '</div>';
    }

    function miniMonth(year, month) {
        const first = new Date(year, month, 1);
        const start = mondayOf(first);
        let html = '<div class="cal-mini"><h3>' + MONTHS[month] + '</h3><div class="cal-mini-grid">';
        DAYS.forEach(function (d) { html += '<span class="cal-mini-dow">' + d.charAt(0) + '</span>'; });
        for (let i = 0; i < 42; i++) {
            const d = new Date(start);
            d.setDate(start.getDate() + i);
            const outside = d.getMonth() !== month;
            const key = ymd(d);
            const list = itemsOn(key);
            const cls = ['cal-mini-day'];
            if (outside) cls.push('muted');
            if (ymd(new Date()) === key) cls.push('today');
            if (list.length) cls.push('has-items', 'cal-' + (list[0].status || 'planned'));
            html += '<button type="button" class="' + cls.join(' ') + '" data-date="' + key + '" title="' + list.map(function (it) { return it.title; }).join(', ') + '">' + d.getDate() + (list.length > 1 ? '<i>' + list.length + '</i>' : '') + '</button>';
        }
        html += '</div></div>';
        return html;
    }

    function bindChips() {
        root.querySelectorAll('.cal-chip').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const item = JSON.parse(decodeURIComponent(this.dataset.payload));
                if (e.target.classList.contains('cal-chip-cat')) {
                    const cat = catFor(item.room);
                    if (cat) {
                        renoEditCategory(cat.id, cat.name);
                        return;
                    }
                }
                renoFillItem(item);
            });
        });
        root.querySelectorAll('.cal-day').forEach(function (cell) {
            cell.addEventListener('click', function (e) {
                if (e.target.closest('.cal-chip')) return;
                const dateStr = this.dataset.date;
                const list = itemsOn(dateStr);
                if (list.length === 1) {
                    renoFillItem(list[0]);
                    return;
                }
                renoNewItem('', dateStr);
                bootstrap.Modal.getOrCreateInstance(itemModal).show();
            });
        });
        root.querySelectorAll('.cal-mini-day').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const dateStr = this.dataset.date;
                const list = itemsOn(dateStr);
                if (list.length === 1) {
                    renoFillItem(list[0]);
                    return;
                }
                if (list.length > 1) {
                    cursor = new Date(dateStr + 'T12:00:00');
                    view = 'month';
                    setViewButtons();
                    render();
                    return;
                }
                renoNewItem('', dateStr);
                bootstrap.Modal.getOrCreateInstance(itemModal).show();
            });
        });
    }

    function renderUnscheduled() {
        const loose = renoCalItems.filter(function (it) { return !parseDate(it); });
        if (!loose.length) {
            unscheduledEl.innerHTML = '';
            return;
        }
        unscheduledEl.innerHTML = '<p class="small mb-2">Nog zonder dag — klik om een datum te zetten</p><div class="d-flex flex-wrap gap-2">' +
            loose.map(chipHtml).join('') + '</div>';
        unscheduledEl.querySelectorAll('.cal-chip').forEach(function (btn) {
            btn.addEventListener('click', function () {
                renoFillItem(JSON.parse(decodeURIComponent(this.dataset.payload)));
            });
        });
    }

    function setViewButtons() {
        document.querySelectorAll('.reno-cal-view').forEach(function (b) {
            const on = b.dataset.view === view;
            b.classList.toggle('active', on);
            b.classList.toggle('btn-light', on);
            b.classList.toggle('btn-outline-light', !on);
        });
    }

    document.querySelectorAll('.reno-cal-view').forEach(function (b) {
        b.addEventListener('click', function () {
            view = this.dataset.view;
            setViewButtons();
            render();
        });
    });
    document.getElementById('renoCalPrev').addEventListener('click', function () {
        if (view === 'year') cursor.setFullYear(cursor.getFullYear() - 1);
        else if (view === 'week') cursor.setDate(cursor.getDate() - 7);
        else cursor.setMonth(cursor.getMonth() - 1);
        render();
    });
    document.getElementById('renoCalNext').addEventListener('click', function () {
        if (view === 'year') cursor.setFullYear(cursor.getFullYear() + 1);
        else if (view === 'week') cursor.setDate(cursor.getDate() + 7);
        else cursor.setMonth(cursor.getMonth() + 1);
        render();
    });
    document.getElementById('renoCalToday').addEventListener('click', function () {
        cursor = new Date();
        cursor.setHours(12, 0, 0, 0);
        render();
    });

    render();
})();

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
