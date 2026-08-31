<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$profile = $profile ?? [];
$income = $income ?? [];
$expenses = $expenses ?? [];
$hasPartner = !empty($profile['has_partner']);
$children = (int) ($profile['children_count'] ?? 0);
$cars = (int) ($profile['cars_count'] ?? 0);
$money = static fn ($n) => number_format((float) $n, 2, '.', '');
$labels = [
    'energy' => 'Energie',
    'water' => 'Water',
    'internet' => 'Internet',
    'health_insurance' => 'Zorg',
    'car_insurance' => 'Autoverzekering',
    'car_fuel' => 'Brandstof',
    'car_maintenance' => 'Auto-onderhoud',
    'groceries' => 'Boodschappen',
    'leisure' => 'Vrije tijd',
    'unforeseen' => 'Onvoorzien',
    'other' => 'Overig',
];
?>

<div class="setup-hero mb-4">
    <p class="reno-kicker mb-1">Welkom bij EmigreerItalia</p>
    <h1 class="mb-2">Eerst de hoofdzaken</h1>
    <p class="mb-0">Een paar vragen over je huishouden. Daarna vullen we een schatting van de maandlasten in — die mag je meteen aanpassen.</p>
</div>

<div class="setup-progress mb-4" id="setupProgress" aria-label="Voortgang"></div>

<form action="/setup" method="post" id="setupForm">
    <?= csrf_field() ?>

    <div class="card setup-card">
        <div class="card-body p-4">
            <section class="setup-step" data-step="0">
                <h2 class="h4 mb-3">Met hoeveel personen ga je?</h2>
                <p class="text-muted">Bijvoorbeeld jij en je partner, met of zonder kinderen. Dat bepaalt de schatting van energie, boodschappen en zorg.</p>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Volwassenen</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="has_partner" id="hh_solo" value="0" <?= $hasPartner ? '' : 'checked' ?>>
                            <label class="btn btn-outline-success" for="hh_solo">Alleen ik</label>
                            <input type="radio" class="btn-check" name="has_partner" id="hh_duo" value="1" <?= $hasPartner ? 'checked' : '' ?>>
                            <label class="btn btn-outline-success" for="hh_duo">Ik en mijn partner</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="children_count">Kinderen</label>
                        <select class="form-select" name="children_count" id="children_count">
                            <?php for ($i = 0; $i <= 6; $i++): ?>
                                <option value="<?= $i ?>" <?= $children === $i ? 'selected' : '' ?>><?= $i === 0 ? 'Geen' : $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="cars_count">Auto’s</label>
                        <select class="form-select" name="cars_count" id="cars_count">
                            <option value="0" <?= $cars === 0 ? 'selected' : '' ?>>Geen</option>
                            <option value="1" <?= $cars === 1 ? 'selected' : '' ?>>1 auto</option>
                            <option value="2" <?= $cars === 2 ? 'selected' : '' ?>>2 auto’s</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="setup-step d-none" data-step="1">
                <h2 class="h4 mb-3">Over jou</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="first_name">Voornaam</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" required maxlength="80" value="<?= esc(old('first_name', $profile['first_name'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="date_of_birth">Geboortedatum</label>
                        <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" required value="<?= esc(old('date_of_birth', $profile['date_of_birth'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="emigration_date">Emigratiedatum naar Italië</label>
                        <input type="date" class="form-control" name="emigration_date" id="emigration_date" required value="<?= esc(old('emigration_date', $profile['emigration_date'] ?? '')) ?>">
                        <div class="form-text">Ook een geplande datum is goed. Nodig voor de AOW-projectie.</div>
                    </div>
                </div>
            </section>

            <section class="setup-step d-none" data-step="2" id="partnerStep">
                <h2 class="h4 mb-3">Over je partner</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="partner_name">Voornaam partner</label>
                        <input type="text" class="form-control" name="partner_name" id="partner_name" maxlength="80" value="<?= esc(old('partner_name', $profile['partner_name'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="partner_date_of_birth">Geboortedatum partner</label>
                        <input type="date" class="form-control" name="partner_date_of_birth" id="partner_date_of_birth" value="<?= esc(old('partner_date_of_birth', $profile['partner_date_of_birth'] ?? '')) ?>">
                    </div>
                </div>
            </section>

            <section class="setup-step d-none" data-step="3">
                <h2 class="h4 mb-3">Netto loon per maand</h2>
                <p class="text-muted">Alleen het hoofdinkomen (loon of freelance). Uitkering en AOW vul je later in bij Inkomsten.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="own_other_income">Jouw loon</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" min="0" class="form-control" name="own_other_income" id="own_other_income" value="<?= esc(old('own_other_income', $money($income['own_other_income'] ?? 0))) ?>">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" id="partnerIncomeWrap">
                        <label class="form-label" for="partner_other_income">Loon partner</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" min="0" class="form-control" name="partner_other_income" id="partner_other_income" value="<?= esc(old('partner_other_income', $money($income['partner_other_income'] ?? 0))) ?>">
                        </div>
                    </div>
                </div>
            </section>

            <section class="setup-step d-none" data-step="4">
                <h2 class="h4 mb-2">Geschatte maandlasten in Italië</h2>
                <p class="text-muted">Indicatie op basis van je huishouden. Pas de bedragen aan als je het beter weet — dit is geen advies.</p>
                <div class="row">
                    <?php foreach ($labels as $key => $label): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="exp_<?= $key ?>"><?= esc($label) ?></label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" min="0" class="form-control setup-exp" name="<?= esc($key, 'attr') ?>" id="exp_<?= $key ?>" data-field="<?= esc($key, 'attr') ?>" value="<?= esc($money($expenses[$key] ?? 0)) ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="setup-total">
                    Totaal per maand: <strong id="setupTotal">€ 0</strong>
                </div>
            </section>

            <div class="d-flex flex-wrap justify-content-between gap-2 mt-4">
                <button type="button" class="btn btn-outline-secondary" id="setupPrev">Vorige</button>
                <div class="d-flex flex-wrap gap-2 ms-auto">
                    <button type="submit" form="setupSkip" class="btn btn-link text-muted">Overslaan</button>
                    <button type="button" class="btn btn-primary" id="setupNext">Volgende</button>
                    <button type="submit" class="btn btn-success d-none" id="setupSave">Opslaan en naar dashboard</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="/setup/skip" method="post" id="setupSkip" class="d-none">
    <?= csrf_field() ?>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const steps = Array.from(document.querySelectorAll('.setup-step'));
    const prevBtn = document.getElementById('setupPrev');
    const nextBtn = document.getElementById('setupNext');
    const saveBtn = document.getElementById('setupSave');
    const progress = document.getElementById('setupProgress');
    const partnerStep = document.getElementById('partnerStep');
    const partnerIncome = document.getElementById('partnerIncomeWrap');
    const dirty = {};
    let index = 0;
    const hasSavedExpenses = <?= !empty($hasSavedExpenses) ? 'true' : 'false' ?>;

    function hasPartner() {
        return document.getElementById('hh_duo').checked;
    }
    function visibleSteps() {
        return steps.filter(function (el) {
            return el !== partnerStep || hasPartner();
        });
    }
    function estimate(adults, children, cars) {
        const extra = Math.max(0, adults - 1);
        const others = extra + children;
        return {
            energy: 90 + 40 * extra + 20 * children,
            water: 20 + 8 * others,
            internet: 30,
            health_insurance: 150 * adults + 50 * children,
            car_insurance: 70 * cars,
            car_fuel: 120 * cars,
            car_maintenance: 40 * cars,
            groceries: 250 + 180 * extra + 120 * children,
            leisure: 80 + 40 * extra + 30 * children,
            unforeseen: 50 + 20 * others,
            other: 0
        };
    }
    function applyEstimate() {
        const adults = hasPartner() ? 2 : 1;
        const children = parseInt(document.getElementById('children_count').value, 10) || 0;
        const cars = parseInt(document.getElementById('cars_count').value, 10) || 0;
        const values = estimate(adults, children, cars);
        document.querySelectorAll('.setup-exp').forEach(function (input) {
            const field = input.dataset.field;
            if (dirty[field]) return;
            input.value = Number(values[field] || 0).toFixed(2);
        });
        updateTotal();
    }
    function updateTotal() {
        let sum = 0;
        document.querySelectorAll('.setup-exp').forEach(function (input) {
            sum += parseFloat(input.value) || 0;
        });
        document.getElementById('setupTotal').textContent = '€ ' + sum.toLocaleString('nl-NL', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }
    function paint() {
        const vis = visibleSteps();
        if (index >= vis.length) index = vis.length - 1;
        if (index < 0) index = 0;
        steps.forEach(function (el) { el.classList.add('d-none'); });
        vis[index].classList.remove('d-none');
        prevBtn.classList.toggle('d-none', index === 0);
        nextBtn.classList.toggle('d-none', index === vis.length - 1);
        saveBtn.classList.toggle('d-none', index !== vis.length - 1);
        partnerIncome.classList.toggle('d-none', !hasPartner());
        progress.innerHTML = vis.map(function (el, i) {
            return '<span class="' + (i === index ? 'on' : (i < index ? 'done' : '')) + '">' + (i + 1) + '</span>';
        }).join('');
        if (vis[index].dataset.step === '4') applyEstimate();
    }
    function validateCurrent() {
        const vis = visibleSteps()[index];
        const step = vis.dataset.step;
        if (step === '1') {
            if (!document.getElementById('first_name').value.trim() || !document.getElementById('date_of_birth').value || !document.getElementById('emigration_date').value) {
                alert('Vul je naam, geboortedatum en emigratiedatum in.');
                return false;
            }
        }
        if (step === '2' && hasPartner()) {
            if (!document.getElementById('partner_name').value.trim() || !document.getElementById('partner_date_of_birth').value) {
                alert('Vul de naam en geboortedatum van je partner in.');
                return false;
            }
        }
        return true;
    }

    document.querySelectorAll('.setup-exp').forEach(function (input) {
        input.addEventListener('input', function () {
            dirty[this.dataset.field] = true;
            updateTotal();
        });
    });
    ['hh_solo', 'hh_duo', 'children_count', 'cars_count'].forEach(function (id) {
        document.getElementById(id).addEventListener('change', function () {
            applyEstimate();
            paint();
        });
    });
    nextBtn.addEventListener('click', function () {
        if (!validateCurrent()) return;
        index += 1;
        paint();
    });
    prevBtn.addEventListener('click', function () {
        index -= 1;
        paint();
    });
    if (hasSavedExpenses) {
        document.querySelectorAll('.setup-exp').forEach(function (input) {
            dirty[input.dataset.field] = true;
        });
    }
    applyEstimate();
    paint();
})();
</script>
<?= $this->endSection() ?>
