<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$expenses = $expenses ?? [];
$expenseItems = $expenseItems ?? [];
$expenseCategories = $expenseCategories ?? \App\Models\ExpenseItemModel::CATEGORIES;
$itemPlaceholders = \App\Models\ExpenseItemModel::CATEGORY_PLACEHOLDERS;
$itemBadges = \App\Models\ExpenseItemModel::CATEGORY_BADGES;
$itemIcons = \App\Models\ExpenseItemModel::CATEGORY_ICONS;
$itemsTotal = 0.0;
foreach ($expenseItems as $item) {
    $itemsTotal += (float) ($item['amount'] ?? 0);
}
$val = static function ($row, $key, $default = 0) {
    return (float) ($row[$key] ?? $default);
};
$utilitiesTotal = $val($expenses, 'energy', 150) + $val($expenses, 'water', 30) + $val($expenses, 'internet', 30);
$insuranceTotal = $val($expenses, 'health_insurance', 0) + $val($expenses, 'car_insurance', 80);
$carTotal = $val($expenses, 'car_fuel', 150) + $val($expenses, 'car_maintenance', 50);
$livingTotal = $val($expenses, 'groceries', 400) + $val($expenses, 'leisure', 200) + $val($expenses, 'unforeseen', 100) + $val($expenses, 'other', 0);
$total = $utilitiesTotal + $insuranceTotal + $carTotal + $livingTotal + $itemsTotal;
$fmt = static function ($n) {
    return number_format((float) $n, 2, ',', '.');
};
?>
<form action="/expenses/save" method="post">
    <?= csrf_field() ?>

    <div class="expense-page-head mb-4">
        <div>
            <h1 class="mb-1"><i class="bi bi-wallet2"></i> Maandelijkse Lasten</h1>
            <p class="text-muted mb-0">Vaste kosten per maand. Bedragen uit de setup kun je hier nog aanpassen.</p>
        </div>
        <div class="expense-total-chip">
            <span class="expense-total-chip-label">Totaal per maand</span>
            <strong class="text-danger">€ <span id="expense-grand-total"><?= $fmt($total) ?></span></strong>
            <span class="text-muted">€ <span id="expense-year-total"><?= $fmt($total * 12) ?></span> / jaar</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="expense-card-head">
                        <h2 class="h5 mb-0">Nutsvoorzieningen</h2>
                        <span class="expense-group-total">€ <span data-expense-group-total="utilities"><?= $fmt($utilitiesTotal) ?></span></span>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-0">
                            <label for="energy" class="form-label">Energie</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="energy" data-expense-group="utilities"
                                       name="energy" value="<?= esc($expenses['energy'] ?? 150) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-0">
                            <label for="water" class="form-label">Water</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="water" data-expense-group="utilities"
                                       name="water" value="<?= esc($expenses['water'] ?? 30) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-0">
                            <label for="internet" class="form-label">Internet</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="internet" data-expense-group="utilities"
                                       name="internet" value="<?= esc($expenses['internet'] ?? 30) ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="expense-card-head">
                        <h2 class="h5 mb-0">Verzekeringen</h2>
                        <span class="expense-group-total">€ <span data-expense-group-total="insurance"><?= $fmt($insuranceTotal) ?></span></span>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-0">
                            <label for="health_insurance" class="form-label">Zorgverzekering</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="health_insurance" data-expense-group="insurance"
                                       name="health_insurance" value="<?= esc($expenses['health_insurance'] ?? 0) ?>" required>
                            </div>
                            <small class="text-muted">SSN: meestal € 0. Alleen extra privé.</small>
                        </div>
                        <div class="col-md-6 mb-0">
                            <label for="car_insurance" class="form-label">Auto verzekering</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="car_insurance" data-expense-group="insurance"
                                       name="car_insurance" value="<?= esc($expenses['car_insurance'] ?? 80) ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="expense-card-head">
                        <h2 class="h5 mb-0">Auto</h2>
                        <span class="expense-group-total">€ <span data-expense-group-total="car"><?= $fmt($carTotal) ?></span></span>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-0">
                            <label for="car_fuel" class="form-label">Brandstof</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="car_fuel" data-expense-group="car"
                                       name="car_fuel" value="<?= esc($expenses['car_fuel'] ?? 150) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-0">
                            <label for="car_maintenance" class="form-label">Onderhoud</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="car_maintenance" data-expense-group="car"
                                       name="car_maintenance" value="<?= esc($expenses['car_maintenance'] ?? 50) ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="expense-card-head">
                        <h2 class="h5 mb-0">Levensonderhoud</h2>
                        <span class="expense-group-total">€ <span data-expense-group-total="living"><?= $fmt($livingTotal) ?></span></span>
                    </div>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label for="groceries" class="form-label">Boodschappen</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="groceries" data-expense-group="living"
                                       name="groceries" value="<?= esc($expenses['groceries'] ?? 400) ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="leisure" class="form-label">Vrije tijd</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="leisure" data-expense-group="living"
                                       name="leisure" value="<?= esc($expenses['leisure'] ?? 200) ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="unforeseen" class="form-label">Onvoorzien</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="unforeseen" data-expense-group="living"
                                       name="unforeseen" value="<?= esc($expenses['unforeseen'] ?? 100) ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="other" class="form-label">Overige</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="other" data-expense-group="living"
                                       name="other" value="<?= esc($expenses['other'] ?? 0) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1">Extra maandkosten</h2>
                    <p class="text-muted small mb-0">Abonnementen, extra verzekeringen, telefoon en andere vaste posten.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <strong class="text-danger">€ <span id="expense-extra-subtotal"><?= $fmt($itemsTotal) ?></span></strong>
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
                            <input type="number" step="0.01" min="0" class="form-control js-extra-amount"
                                   name="items[<?= (int) $i ?>][amount]" value="<?= esc($item['amount'] ?? 0) ?>">
                        </div>
                        <button type="button" class="btn btn-outline-danger" data-expense-remove title="Verwijderen">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="form-footer mt-3">
        <div class="text-muted">
            Totaal <strong class="text-danger">€ <span id="expense-footer-total"><?= $fmt($total) ?></span></strong> per maand
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Opslaan
        </button>
    </div>
</form>

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
        const groups = { utilities: 0, insurance: 0, car: 0, living: 0 };
        form.querySelectorAll('input[data-expense-group]').forEach(function (el) {
            const group = el.getAttribute('data-expense-group');
            if (groups[group] !== undefined) groups[group] += parseAmount(el);
        });
        Object.keys(groups).forEach(function (group) {
            const el = document.querySelector('[data-expense-group-total="' + group + '"]');
            if (el) el.textContent = formatEuro(groups[group]);
        });

        let extra = 0;
        list.querySelectorAll('.js-extra-amount').forEach(function (el) {
            extra += parseAmount(el);
        });
        const extraEl = document.getElementById('expense-extra-subtotal');
        if (extraEl) extraEl.textContent = formatEuro(extra);

        const total = groups.utilities + groups.insurance + groups.car + groups.living + extra;
        ['expense-grand-total', 'expense-footer-total'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.textContent = formatEuro(total);
        });
        const year = document.getElementById('expense-year-total');
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
