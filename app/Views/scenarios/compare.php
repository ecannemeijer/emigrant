<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="page-hero mb-4">
    <h1>Scenario's vergelijken</h1>
    <p class="text-muted mb-0">Zet twee opgeslagen situaties naast elkaar.</p>
</div>

<form class="card mb-4" method="get" action="/scenarios/compare">
    <div class="card-body row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Scenario A</label>
            <select name="a" class="form-select" required>
                <?php foreach ($scenarios as $scenario): ?>
                    <option value="<?= $scenario['id'] ?>" <?= in_array((int) $scenario['id'], $selected, true) && $selected && (int)$selected[array_key_first($selected)] === (int)$scenario['id'] ? 'selected' : '' ?>>
                        <?= esc($scenario['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label">Scenario B</label>
            <select name="b" class="form-select" required>
                <?php foreach ($scenarios as $scenario): ?>
                    <option value="<?= $scenario['id'] ?>"><?= esc($scenario['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Vergelijk</button>
        </div>
    </div>
</form>

<?php if (count($comparisons) >= 2): ?>
<div class="table-responsive">
<table class="table table-striped">
    <thead>
        <tr>
            <th></th>
            <?php foreach ($comparisons as $row): ?>
                <th><?= esc($row['scenario']['name']) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $fields = [
            'remaining_capital' => 'Resterend vermogen',
            'total_monthly_income' => 'Maandinkomen',
            'monthly_expenses' => 'Maandkosten',
            'monthly_taxes' => 'Belasting/mnd',
            'net_disposable' => 'Netto/mnd',
            'bnb_net_income' => 'B&B netto',
            'capital_12_months' => 'Vermogen jaar 1',
            'runway_months' => 'Runway (mnd)',
        ];
        foreach ($fields as $key => $label): ?>
            <tr>
                <th><?= $label ?></th>
                <?php foreach ($comparisons as $row): ?>
                    <td>€ <?= number_format($row['calculations'][$key] ?? 0, 0, ',', '.') ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php elseif (!empty($selected)): ?>
    <div class="alert alert-info">Kies twee scenario's om te vergelijken.</div>
<?php endif; ?>
<?= $this->endSection() ?>
