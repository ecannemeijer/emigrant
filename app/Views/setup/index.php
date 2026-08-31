<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$profile = $profile ?? [];
$income = $income ?? [];
$expenses = $expenses ?? [];
$start = $startPosition ?? [];
$property = $mainProperty ?? [];
$hasPartner = !empty($profile['has_partner']);
$children = (int) ($profile['children_count'] ?? 0);
$cars = (int) ($profile['cars_count'] ?? 0);
$sellsHouse = (string) old('sells_house', (!empty($start['house_sale_price']) || empty($start['id'])) ? '1' : '0') === '1';
$hasMortgage = (string) old('has_mortgage', !empty($start['mortgage_debt']) ? '1' : '0') === '1';
$buysItaly = (string) old('buys_italy', (!empty($property['purchase_price']) || empty($property['id'])) ? '1' : '0') === '1';
$buyPct = (float) ($property['purchase_costs_percentage'] ?? 10);
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
$hints = [
    'health_insurance' => 'Met de carta sanitaria (SSN) geen maandpremie. Alleen invullen bij een extra private verzekering.',
];
?>

<div class="setup-hero mb-4">
    <p class="reno-kicker mb-1">Welkom bij EmigreerItalia</p>
    <h1 class="mb-2">Eerst de hoofdzaken</h1>
    <p class="mb-0">Huishouden, woning en spaargeld — daarna een schatting van de maandlasten die je nog kunt aanpassen.</p>
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
                <h2 class="h4 mb-3">Verkoop je je huis in Nederland?</h2>
                <p class="text-muted">De winst (verkoop min resthypotheek) telt later bij je spaargeld op.</p>
                <div class="btn-group mb-3" role="group">
                    <input type="radio" class="btn-check" name="sells_house" id="sell_yes" value="1" <?= $sellsHouse ? 'checked' : '' ?>>
                    <label class="btn btn-outline-success" for="sell_yes">Ja</label>
                    <input type="radio" class="btn-check" name="sells_house" id="sell_no" value="0" <?= $sellsHouse ? '' : 'checked' ?>>
                    <label class="btn btn-outline-success" for="sell_no">Nee</label>
                </div>
                <div id="sellHouseWrap">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="house_sale_price">Verkoopprijs</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="1000" min="0" class="form-control" name="house_sale_price" id="house_sale_price" value="<?= esc(old('house_sale_price', $money($start['house_sale_price'] ?? 0))) ?>">
                            </div>
                        </div>
                    </div>
                    <p class="mb-2">Zit er een resthypotheek op?</p>
                    <div class="btn-group mb-3" role="group">
                        <input type="radio" class="btn-check" name="has_mortgage" id="mort_yes" value="1" <?= $hasMortgage ? 'checked' : '' ?>>
                        <label class="btn btn-outline-success" for="mort_yes">Ja</label>
                        <input type="radio" class="btn-check" name="has_mortgage" id="mort_no" value="0" <?= $hasMortgage ? '' : 'checked' ?>>
                        <label class="btn btn-outline-success" for="mort_no">Nee</label>
                    </div>
                    <div class="row" id="mortgageWrap">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="mortgage_debt">Resthypotheek</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="1000" min="0" class="form-control" name="mortgage_debt" id="mortgage_debt" value="<?= esc(old('mortgage_debt', $money($start['mortgage_debt'] ?? 0))) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="setup-total" id="saleProfitBox">Winst huisverkoop: <strong id="saleProfit">€ 0</strong></div>
                </div>
            </section>

            <section class="setup-step d-none" data-step="5">
                <h2 class="h4 mb-3">Kapitaal en huis in Italië</h2>
                <p class="text-muted">Je spaargeld plus de winst van de huisverkoop is je startkapitaal. Koop je in Italië, dan gaat die aankoop daarvan af.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="savings">Spaargeld op de bank</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="1000" min="0" class="form-control" name="savings" id="savings" value="<?= esc(old('savings', $money($start['savings'] ?? 0))) ?>">
                        </div>
                    </div>
                </div>
                <p class="mb-2">Koop je een huis in Italië?</p>
                <div class="btn-group mb-3" role="group">
                    <input type="radio" class="btn-check" name="buys_italy" id="buy_yes" value="1" <?= $buysItaly ? 'checked' : '' ?>>
                    <label class="btn btn-outline-success" for="buy_yes">Ja</label>
                    <input type="radio" class="btn-check" name="buys_italy" id="buy_no" value="0" <?= $buysItaly ? '' : 'checked' ?>>
                    <label class="btn btn-outline-success" for="buy_no">Nee</label>
                </div>
                <div class="row" id="buyItalyWrap">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="purchase_price">Aankoopprijs in Italië</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="1000" min="0" class="form-control" name="purchase_price" id="purchase_price" value="<?= esc(old('purchase_price', $money($property['purchase_price'] ?? 0))) ?>">
                        </div>
                        <div class="form-text">We rekenen <?= number_format($buyPct, 0) ?>% aankoopkosten (notaris e.d.) mee. Dat kun je later bij Vastgoed aanpassen.</div>
                    </div>
                </div>
                <div class="setup-recap" id="capitalRecap">
                    <div>Winst huisverkoop <strong id="recapProfit">€ 0</strong></div>
                    <div>+ spaargeld <strong id="recapSavings">€ 0</strong></div>
                    <div>= startkapitaal <strong id="recapStart">€ 0</strong></div>
                    <div id="recapBuyLine">− huis Italië (incl. kosten) <strong id="recapBuy">€ 0</strong></div>
                    <div class="setup-recap-remain">Resterend vermogen <strong id="recapRemain">€ 0</strong></div>
                </div>
            </section>

            <section class="setup-step d-none" data-step="6">
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
                            <?php if (!empty($hints[$key])): ?>
                                <div class="form-text"><?= esc($hints[$key]) ?></div>
                            <?php endif; ?>
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
    const sellHouseWrap = document.getElementById('sellHouseWrap');
    const mortgageWrap = document.getElementById('mortgageWrap');
    const buyItalyWrap = document.getElementById('buyItalyWrap');
    const recapBuyLine = document.getElementById('recapBuyLine');
    const buyPct = <?= json_encode($buyPct) ?>;
    const dirty = {};
    let index = 0;
    const hasSavedExpenses = <?= !empty($hasSavedExpenses) ? 'true' : 'false' ?>;

    function hasPartner() {
        return document.getElementById('hh_duo').checked;
    }
    function sellsHouse() {
        return document.getElementById('sell_yes').checked;
    }
    function hasMortgage() {
        return sellsHouse() && document.getElementById('mort_yes').checked;
    }
    function buysItaly() {
        return document.getElementById('buy_yes').checked;
    }
    function moneyVal(id) {
        return parseFloat(document.getElementById(id).value) || 0;
    }
    function euro(n) {
        return '€ ' + Math.round(n).toLocaleString('nl-NL');
    }
    function saleProfit() {
        if (!sellsHouse()) return 0;
        return moneyVal('house_sale_price') - (hasMortgage() ? moneyVal('mortgage_debt') : 0);
    }
    function updateCapital() {
        sellHouseWrap.classList.toggle('d-none', !sellsHouse());
        mortgageWrap.classList.toggle('d-none', !hasMortgage());
        buyItalyWrap.classList.toggle('d-none', !buysItaly());
        recapBuyLine.classList.toggle('d-none', !buysItaly());
        const profit = saleProfit();
        const savings = moneyVal('savings');
        const start = profit + savings;
        const buy = buysItaly() ? moneyVal('purchase_price') * (1 + buyPct / 100) : 0;
        const remain = start - buy;
        const profitEl = document.getElementById('saleProfit');
        if (profitEl) profitEl.textContent = euro(profit);
        document.getElementById('recapProfit').textContent = euro(profit);
        document.getElementById('recapSavings').textContent = euro(savings);
        document.getElementById('recapStart').textContent = euro(start);
        document.getElementById('recapBuy').textContent = euro(buy);
        document.getElementById('recapRemain').textContent = euro(remain);
        document.getElementById('recapRemain').classList.toggle('text-danger', remain < 0);
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
            health_insurance: 0,
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
        if (vis[index].dataset.step === '4' || vis[index].dataset.step === '5') updateCapital();
        progress.innerHTML = vis.map(function (el, i) {
            return '<span class="' + (i === index ? 'on' : (i < index ? 'done' : '')) + '">' + (i + 1) + '</span>';
        }).join('');
        if (vis[index].dataset.step === '6') applyEstimate();
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
        if (step === '4' && sellsHouse() && moneyVal('house_sale_price') <= 0) {
            alert('Vul de verkoopprijs van je huis in, of kies Nee.');
            return false;
        }
        if (step === '4' && hasMortgage() && moneyVal('mortgage_debt') <= 0) {
            alert('Vul de resthypotheek in, of kies Nee.');
            return false;
        }
        if (step === '5' && buysItaly() && moneyVal('purchase_price') <= 0) {
            alert('Vul de aankoopprijs in Italië in, of kies Nee.');
            return false;
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
    ['sell_yes', 'sell_no', 'mort_yes', 'mort_no', 'buy_yes', 'buy_no', 'house_sale_price', 'mortgage_debt', 'savings', 'purchase_price'].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', updateCapital);
        el.addEventListener('input', updateCapital);
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
    updateCapital();
    paint();
})();
</script>
<?= $this->endSection() ?>
