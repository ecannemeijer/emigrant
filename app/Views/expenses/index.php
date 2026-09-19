<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-wallet2"></i> Maandelijkse Lasten</h1>
        <p class="text-muted">Vul je vaste maandelijkse kosten in. Bedragen die via de setup-schatting zijn ingevuld, kun je hier nog aanpassen.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
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
                                       name="health_insurance" value="<?= $expenses['health_insurance'] ?? 0 ?>" required>
                            </div>
                            <small class="text-muted">Met de carta sanitaria (SSN) geen maandpremie. Alleen een extra private verzekering telt hier.</small>
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
                            <input type="number" step="0.01" class="form-control js-expense-field" id="other" 
                                   name="other" value="<?= $expenses['other'] ?? 0 ?>">
                        </div>
                        <small class="text-muted">Eén bedrag zonder naam. Losse posten voeg je hieronder toe.</small>
                    </div>

                    <?php
                    $expenseItems = $expenseItems ?? [];
                    $expenseCategories = $expenseCategories ?? \App\Models\ExpenseItemModel::CATEGORIES;
                    $itemPlaceholders = \App\Models\ExpenseItemModel::CATEGORY_PLACEHOLDERS;
                    $itemBadges = \App\Models\ExpenseItemModel::CATEGORY_BADGES;
                    $itemIcons = \App\Models\ExpenseItemModel::CATEGORY_ICONS;
                    $itemsTotal = 0.0;
                    foreach ($expenseItems as $item) {
                        $itemsTotal += (float) ($item['amount'] ?? 0);
                    }
                    ?>
                    <div class="expense-extra mt-4 pt-3 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h5 class="mb-1">Extra maandkosten</h5>
                                <p class="text-muted small mb-0">Abonnementen, extra verzekeringen, telefoon en andere vaste posten per maand.</p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-plus-circle"></i> Kostenpost toevoegen
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <?php foreach ($expenseCategories as $catKey => $catLabel): ?>
                                        <li>
                                            <button type="button" class="dropdown-item" data-expense-add="<?= esc($catKey) ?>">
                                                <i class="bi <?= esc($itemIcons[$catKey] ?? 'bi-tag') ?>"></i>
                                                <?= esc($catLabel) ?>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div id="expense-extra-empty" class="expense-extra-empty <?= $expenseItems ? 'd-none' : '' ?>">
                            Nog geen extra posten. Kies een type via de knop hierboven.
                        </div>
                        <div id="expense-extra-list">
                            <?php foreach ($expenseItems as $i => $item): ?>
                                <?php $cat = $item['category'] ?? 'other'; ?>
                                <div class="expense-extra-row" data-category="<?= esc($cat) ?>">
                                    <span class="badge <?= esc($itemBadges[$cat] ?? 'bg-secondary') ?>">
                                        <i class="bi <?= esc($itemIcons[$cat] ?? 'bi-tag') ?>"></i>
                                        <?= esc($expenseCategories[$cat] ?? 'Overig') ?>
                                    </span>
                                    <input type="hidden" name="items[<?= (int) $i ?>][category]" value="<?= esc($cat) ?>">
                                    <input type="text" class="form-control" name="items[<?= (int) $i ?>][name]"
                                           value="<?= esc($item['name'] ?? '') ?>" maxlength="120"
                                           placeholder="<?= esc($itemPlaceholders[$cat] ?? 'Naam') ?>">
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" step="0.01" min="0" class="form-control js-expense-field js-extra-amount"
                                               name="items[<?= (int) $i ?>][amount]" value="<?= esc($item['amount'] ?? 0) ?>">
                                    </div>
                                    <button type="button" class="btn btn-outline-danger" data-expense-remove title="Verwijderen">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">Telt mee in het dashboard, geïndexeerd zoals de andere lasten.</small>
                            <strong class="text-danger">Subtotaal extra: € <span id="expense-extra-subtotal"><?= number_format($itemsTotal, 2, ',', '.') ?></span></strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">
                        <i class="bi bi-save"></i> Opslaan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
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
                             ($expenses['other'] ?? 0) +
                             $itemsTotal;
                    ?>
                    <div class="display-6 text-danger">
                        € <span id="expense-grand-total"><?= number_format($total, 2, ',', '.') ?></span>
                    </div>
                    <p class="text-muted mt-2">Per jaar: € <span id="expense-year-total"><?= number_format($total * 12, 2, ',', '.') ?></span></p>
                <?php else: ?>
                    <p class="text-muted">Vul je lasten in</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const list = document.getElementById('expense-extra-list');
    const empty = document.getElementById('expense-extra-empty');
    const form = document.querySelector('form[action="/expenses/save"]');
    if (!list || !form) return;

    const labels = <?= json_encode(\App\Models\ExpenseItemModel::CATEGORIES, JSON_UNESCAPED_UNICODE) ?>;
    const badges = <?= json_encode(\App\Models\ExpenseItemModel::CATEGORY_BADGES, JSON_UNESCAPED_UNICODE) ?>;
    const icons = <?= json_encode(\App\Models\ExpenseItemModel::CATEGORY_ICONS, JSON_UNESCAPED_UNICODE) ?>;
    const placeholders = <?= json_encode(\App\Models\ExpenseItemModel::CATEGORY_PLACEHOLDERS, JSON_UNESCAPED_UNICODE) ?>;
    let nextIndex = list.querySelectorAll('.expense-extra-row').length;

    function formatEuro(value) {
        return value.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function parseAmount(input) {
        const raw = String(input.value || '0').replace(',', '.');
        const n = parseFloat(raw);
        return Number.isFinite(n) ? n : 0;
    }

    function syncEmpty() {
        const hasRows = list.querySelectorAll('.expense-extra-row').length > 0;
        if (empty) empty.classList.toggle('d-none', hasRows);
    }

    function updateTotals() {
        let extra = 0;
        list.querySelectorAll('.js-extra-amount').forEach(function (el) {
            extra += parseAmount(el);
        });
        const extraEl = document.getElementById('expense-extra-subtotal');
        if (extraEl) extraEl.textContent = formatEuro(extra);

        let total = extra;
        form.querySelectorAll('input[type="number"]').forEach(function (el) {
            if (el.classList.contains('js-extra-amount')) return;
            total += parseAmount(el);
        });
        const grand = document.getElementById('expense-grand-total');
        const year = document.getElementById('expense-year-total');
        if (grand) grand.textContent = formatEuro(total);
        if (year) year.textContent = formatEuro(total * 12);
    }

    function addRow(category) {
        const cat = labels[category] ? category : 'other';
        const i = nextIndex++;
        const row = document.createElement('div');
        row.className = 'expense-extra-row';
        row.dataset.category = cat;
        row.innerHTML =
            '<span class="badge ' + (badges[cat] || 'bg-secondary') + '">' +
                '<i class="bi ' + (icons[cat] || 'bi-tag') + '"></i> ' + (labels[cat] || 'Overig') +
            '</span>' +
            '<input type="hidden" name="items[' + i + '][category]" value="' + cat + '">' +
            '<input type="text" class="form-control" name="items[' + i + '][name]" maxlength="120" placeholder="' + (placeholders[cat] || 'Naam') + '">' +
            '<div class="input-group">' +
                '<span class="input-group-text">€</span>' +
                '<input type="number" step="0.01" min="0" class="form-control js-extra-amount" name="items[' + i + '][amount]" value="">' +
            '</div>' +
            '<button type="button" class="btn btn-outline-danger" data-expense-remove title="Verwijderen">' +
                '<i class="bi bi-trash"></i>' +
            '</button>';
        list.appendChild(row);
        const nameInput = row.querySelector('input[type="text"]');
        if (nameInput) nameInput.focus();
        syncEmpty();
        updateTotals();
    }

    document.querySelectorAll('[data-expense-add]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            addRow(btn.getAttribute('data-expense-add'));
        });
    });

    list.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-expense-remove]');
        if (!btn) return;
        const row = btn.closest('.expense-extra-row');
        if (row) row.remove();
        syncEmpty();
        updateTotals();
    });

    form.addEventListener('input', function (e) {
        if (e.target && e.target.matches('input[type="number"]')) {
            updateTotals();
        }
    });
})();
</script>
<?= $this->endSection() ?>
