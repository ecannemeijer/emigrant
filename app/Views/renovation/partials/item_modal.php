<?php
$rooms = $rooms ?? [];
$statuses = $statuses ?? [];
$priorities = $priorities ?? [];
$returnTo = $returnTo ?? 'list';
$yearNow = (int) date('Y');
$months = [
    1 => 'januari', 2 => 'februari', 3 => 'maart', 4 => 'april',
    5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'augustus',
    9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'december',
];
?>
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/renovation/item" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="item_id" value="">
                <input type="hidden" name="return_to" value="<?= esc($returnTo, 'attr') ?>">
                <input type="hidden" name="planned_date" id="item_date" value="">
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
                            <label class="form-label">Geplande datum</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="visually-hidden" for="item_day">Dag</label>
                                    <select class="form-select" name="planned_day" id="item_day">
                                        <option value="">Dag</option>
                                        <?php for ($d = 1; $d <= 31; $d++): ?>
                                            <option value="<?= $d ?>"><?= $d ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="visually-hidden" for="item_month">Maand</label>
                                    <select class="form-select" name="planned_month" id="item_month">
                                        <option value="">Maand</option>
                                        <?php foreach ($months as $num => $label): ?>
                                            <option value="<?= $num ?>"><?= esc($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="visually-hidden" for="item_year">Jaar</label>
                                    <select class="form-select" name="planned_year" id="item_year">
                                        <option value="">Jaar</option>
                                        <?php for ($y = $yearNow - 1; $y <= $yearNow + 12; $y++): ?>
                                            <option value="<?= $y ?>"><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted">Vul dag, maand én jaar in. Alleen een jaar is niet genoeg voor de kalender.</small>
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
