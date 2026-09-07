<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$settings = $settings ?? [];
$expenses = $expenses ?? [];
$s = $snapshot ?? [];
$money = static fn ($n, $dec = 0) => number_format((float) $n, $dec, ',', '.');
$enabled = !empty($settings['enabled']);
$nightsYear = (float) ($s['nights'] ?? 0);
$highShare = $nightsYear > 0 ? ((float) ($s['high_nights'] ?? 0) / $nightsYear) * 100 : 50;
$lowShare = 100 - $highShare;
$occ = (float) ($s['occupancy'] ?? 0);
$be = (float) ($s['break_even'] ?? 0);
$js = $s['js'] ?? ['days' => 30.4375, 'forfettario' => 0, 'taxRate' => 23, 'coeff' => 0.67, 'limit' => 85000];
?>

<div class="reno-hero mb-4">
    <div>
        <p class="reno-kicker mb-1">Ospitalità · Affittacamere</p>
        <h1 class="mb-2">Bed &amp; breakfast</h1>
        <p class="mb-0 reno-lead">Reken kamers, seizoen en kosten door. Als de module aan staat, telt het netto (na forfettario of gewoon tarief) mee in de jaarprojectie op het dashboard.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-light" href="/taxes">Belastingregeling</a>
        <a class="btn btn-light" href="#bnb-form">Gegevens aanpassen</a>
    </div>
</div>

<?php if (!$enabled): ?>
    <div class="alert alert-light border mb-4">
        De cijfers hieronder zijn een <strong>voorproef</strong>. Zet onderaan “B&amp;B meenemen in de projectie” aan om ze op het dashboard te laten meetellen.
    </div>
<?php endif; ?>

<div class="row g-3 mb-4" id="bnbKpis">
    <div class="col-6 col-lg-3">
        <div class="card stat-card positive h-100">
            <div class="card-body">
                <div class="stat-label">Verhuurde nachten / jaar</div>
                <div class="stat-value" id="kpiNights"><?= $money($s['nights'] ?? 0) ?></div>
                <div class="small text-muted">Kamernachten</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card neutral h-100">
            <div class="card-body">
                <div class="stat-label">Bezetting</div>
                <div class="stat-value" id="kpiOcc"><?= $money($occ, 0) ?>%</div>
                <div class="small text-muted">Gewogen over het jaar</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card <?= (($s['net_monthly'] ?? 0) >= 0) ? 'positive' : 'negative' ?> h-100" id="kpiNetCard">
            <div class="card-body">
                <div class="stat-label">Netto per maand</div>
                <div class="stat-value" id="kpiNet">€ <?= $money($s['net_monthly'] ?? 0) ?></div>
                <div class="small text-muted">Omzet min kosten, vóór belasting</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card <?= ($be > 80) ? 'negative' : 'neutral' ?> h-100" id="kpiBeCard">
            <div class="card-body">
                <div class="stat-label">Break-even bezetting</div>
                <div class="stat-value" id="kpiBe"><?= $be > 100 ? '&gt;100%' : $money($be, 0) . '%' ?></div>
                <div class="small text-muted">Inclusief belasting</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card setup-card h-100">
            <div class="card-body">
                <div class="stat-label mb-1">Maandomzet</div>
                <div class="h4 mb-1" id="kpiRev">€ <?= $money($s['monthly_revenue'] ?? 0) ?></div>
                <div class="small text-muted">€ <span id="kpiRevYear"><?= $money($s['yearly_revenue'] ?? 0) ?></span> per jaar</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card setup-card h-100">
            <div class="card-body">
                <div class="stat-label mb-1">Maandkosten</div>
                <div class="h4 mb-1" id="kpiExp">€ <?= $money($s['monthly_expenses'] ?? 0) ?></div>
                <div class="small text-muted">Vast + commissie + ontbijt</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card setup-card h-100">
            <div class="card-body">
                <div class="stat-label mb-1">Na belasting / maand</div>
                <div class="h4 mb-1" id="kpiNetTax">€ <?= $money($s['net_after_tax'] ?? 0) ?></div>
                <div class="small text-muted">Belasting € <span id="kpiTax"><?= $money($s['monthly_tax'] ?? 0) ?></span></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card setup-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Seizoen</h2>
                <p class="text-muted small">Verdeling van verhuurde kamernachten en omzet.</p>
                <div class="bnb-bar mb-2" aria-hidden="true">
                    <span class="bnb-bar-high" id="barHigh" style="width: <?= $highShare ?>%"></span>
                    <span class="bnb-bar-low" id="barLow" style="width: <?= $lowShare ?>%"></span>
                </div>
                <div class="d-flex justify-content-between small mb-3">
                    <span><span class="bnb-dot high"></span> Hoogseizoen</span>
                    <span><span class="bnb-dot low"></span> Laagseizoen</span>
                </div>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th></th><th class="text-end">Nachten</th><th class="text-end">Omzet / jaar</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Hoogseizoen</td>
                            <td class="text-end" id="tblHighNights"><?= $money($s['high_nights'] ?? 0) ?></td>
                            <td class="text-end" id="tblHighRev">€ <?= $money($s['high_revenue_year'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td>Laagseizoen</td>
                            <td class="text-end" id="tblLowNights"><?= $money($s['low_nights'] ?? 0) ?></td>
                            <td class="text-end" id="tblLowRev">€ <?= $money($s['low_revenue_year'] ?? 0) ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-text mt-2 <?= ((float) ($s['months_total'] ?? 12) === 12.0) ? 'd-none' : '' ?>" id="monthWarn">
                    Hoog- en laagseizoen tellen samen niet op tot 12 maanden. De berekening gebruikt de ingevulde maanden.
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card setup-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Kostenopbouw per maand</h2>
                <ul class="bnb-costs list-unstyled mb-3" id="costList">
                    <?php foreach (($s['cost_rows'] ?? []) as $row): ?>
                        <?php if ((float) $row['amount'] <= 0) continue; ?>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span><?= esc($row['label']) ?></span>
                            <strong>€ <?= $money($row['amount']) ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="small text-muted" id="costEmpty" <?= array_sum(array_column($s['cost_rows'] ?? [], 'amount')) > 0 ? 'style="display:none"' : '' ?>>Nog geen kosten ingevuld.</div>
                <p class="small text-muted mb-0" id="taxNote">
                    <?php if (!empty($s['forfettario'])): ?>
                        Forfettario <?= $money($s['tax_rate'] ?? 15, 0) ?>% over <?= $money($s['profitability_coefficient'] ?? 67, 0) ?>% van de omzet
                        <?= !empty($s['startup_rate']) ? '(starttarief, eerste 5 jaar)' : '' ?>.
                        Plafond € <?= $money($s['forfettario_limit'] ?? 85000) ?> / jaar.
                    <?php else: ?>
                        Gewoon tarief <?= $money($s['tax_rate'] ?? 23, 0) ?>% over de winst. Zet forfettario aan bij Belastingen als dat van toepassing is.
                    <?php endif; ?>
                </p>
                <div class="alert alert-warning py-2 mt-2 mb-0 <?= empty($s['over_limit']) ? 'd-none' : '' ?>" id="limitWarn">
                    Jaaromzet boven het forfettario-plafond (€ <?= $money($s['forfettario_limit'] ?? 85000) ?>).
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card setup-card mb-4" id="breakeven">
    <div class="card-body">
        <h2 class="h5 mb-2">Break-even</h2>
        <p class="text-muted">Minimale bezetting (gelijk over het jaar) waarbij omzet de kosten én belasting dekt. Streef onder de 60%.</p>
        <div class="bnb-be-track mb-2">
            <div class="bnb-be-fill" id="beFill" style="width: <?= min(100, max(4, $be)) ?>%"></div>
            <div class="bnb-be-mark" id="beMark" style="left: <?= min(96, max(2, $occ)) ?>%" title="Huidige bezetting"></div>
        </div>
        <div class="d-flex justify-content-between small text-muted mb-3">
            <span>Break-even <strong id="beLabel"><?= $be > 100 ? '&gt;100%' : $money($be, 1) . '%' ?></strong></span>
            <span>Nu <strong id="occLabel"><?= $money($occ, 1) ?>%</strong></span>
        </div>
        <div id="beAlert">
            <?php if ($be > 100): ?>
                <div class="alert alert-danger mb-3">Met deze prijs en kosten haal je geen break-even, ook niet bij 100% bezetting.</div>
            <?php elseif ($be > 80): ?>
                <div class="alert alert-warning mb-3">Break-even is hoog (&gt;80%). Weinig ruimte voor een mindere maand.</div>
            <?php else: ?>
                <div class="alert alert-success mb-3">Break-even is haalbaar. Er blijft marge over je huidige bezetting.</div>
            <?php endif; ?>
        </div>
        <h3 class="h6">Netto per maand bij een vlakke bezetting</h3>
        <div class="table-responsive">
            <table class="table table-sm mb-0" id="scenarioTable">
                <thead>
                    <tr>
                        <th>Bezetting</th>
                        <th class="text-end">Omzet</th>
                        <th class="text-end">Kosten</th>
                        <th class="text-end">Belasting</th>
                        <th class="text-end">Netto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($s['scenarios'] ?? []) as $sc): ?>
                        <tr class="<?= ($sc['net'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                            <td><?= (int) $sc['occupancy'] ?>%</td>
                            <td class="text-end">€ <?= $money($sc['revenue']) ?></td>
                            <td class="text-end">€ <?= $money($sc['expenses']) ?></td>
                            <td class="text-end">€ <?= $money($sc['tax']) ?></td>
                            <td class="text-end">€ <?= $money($sc['net']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<form action="/bnb/save" method="post" id="bnb-form" class="card setup-card">
    <?= csrf_field() ?>
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="h4 mb-0">Gegevens</h2>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="enabled" name="enabled" value="1" <?= $enabled ? 'checked' : '' ?>>
                <label class="form-check-label" for="enabled"><strong>B&amp;B meenemen in de projectie</strong></label>
            </div>
        </div>

        <h3 class="h6 text-uppercase text-muted">Aanbod</h3>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label" for="number_of_rooms">Aantal kamers</label>
                <input type="number" min="0" max="20" class="form-control bnb-live" id="number_of_rooms" name="number_of_rooms" value="<?= esc(old('number_of_rooms', $settings['number_of_rooms'] ?? 3)) ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label" for="price_per_room_per_night">Prijs per kamer per nacht</label>
                <div class="input-group">
                    <span class="input-group-text">€</span>
                    <input type="number" step="0.01" min="0" class="form-control bnb-live" id="price_per_room_per_night" name="price_per_room_per_night" value="<?= esc(old('price_per_room_per_night', $settings['price_per_room_per_night'] ?? 75)) ?>">
                </div>
                <div class="form-text">Gemiddelde; hoog- en laagseizoen gebruiken dezelfde prijs.</div>
            </div>
        </div>

        <h3 class="h6 text-uppercase text-muted mt-2">Seizoen</h3>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label" for="high_season_months">Hoogseizoen (maanden)</label>
                <input type="number" min="0" max="12" class="form-control bnb-live" id="high_season_months" name="high_season_months" value="<?= esc(old('high_season_months', $settings['high_season_months'] ?? 4)) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label" for="high_season_percentage">Bezetting hoog</label>
                <div class="input-group">
                    <input type="number" step="0.1" min="0" max="100" class="form-control bnb-live" id="high_season_percentage" name="high_season_percentage" value="<?= esc(old('high_season_percentage', $settings['high_season_percentage'] ?? 80)) ?>">
                    <span class="input-group-text">%</span>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label" for="low_season_months">Laagseizoen (maanden)</label>
                <input type="number" min="0" max="12" class="form-control bnb-live" id="low_season_months" name="low_season_months" value="<?= esc(old('low_season_months', $settings['low_season_months'] ?? 8)) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label" for="low_season_percentage">Bezetting laag</label>
                <div class="input-group">
                    <input type="number" step="0.1" min="0" max="100" class="form-control bnb-live" id="low_season_percentage" name="low_season_percentage" value="<?= esc(old('low_season_percentage', $settings['low_season_percentage'] ?? 40)) ?>">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>

        <h3 class="h6 text-uppercase text-muted mt-2">Vaste kosten per maand</h3>
        <p class="small text-muted">Ongeacht hoeveel nachten je verhuurt. Energie van het huis zelf staat bij Maandlasten; hier alleen extra B&amp;B-verbruik.</p>
        <div class="row">
            <?php
            $fixedFields = [
                'extra_energy_water' => ['Extra energie/water', 100],
                'insurance' => ['Verzekering', 50],
                'cleaning' => ['Schoonmaak', 200],
                'linen_laundry' => ['Linnen & was', 100],
                'marketing' => ['Marketing', 50],
                'maintenance' => ['Onderhoud', 150],
                'administration' => ['Administratie / boekhouder', 100],
            ];
            foreach ($fixedFields as $name => [$label, $default]):
            ?>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="<?= $name ?>"><?= esc($label) ?></label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" min="0" class="form-control bnb-live" id="<?= $name ?>" name="<?= $name ?>" value="<?= esc(old($name, $expenses[$name] ?? $default)) ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h3 class="h6 text-uppercase text-muted mt-2">Variabele kosten</h3>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="platform_commission">Platformcommissie</label>
                <div class="input-group">
                    <input type="number" step="0.1" min="0" max="40" class="form-control bnb-live" id="platform_commission" name="platform_commission" value="<?= esc(old('platform_commission', $expenses['platform_commission'] ?? 15)) ?>">
                    <span class="input-group-text">%</span>
                </div>
                <div class="form-text">Airbnb / Booking over de omzet. Directe boekingen: 0.</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" for="breakfast_per_guest">Ontbijt per kamernacht</label>
                <div class="input-group">
                    <span class="input-group-text">€</span>
                    <input type="number" step="0.01" min="0" class="form-control bnb-live" id="breakfast_per_guest" name="breakfast_per_guest" value="<?= esc(old('breakfast_per_guest', $expenses['breakfast_per_guest'] ?? 5)) ?>">
                </div>
                <div class="form-text">Per verhuurde kamernacht (niet per extra gast).</div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> Opslaan
        </button>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const DAYS = <?= json_encode((float) $js['days']) ?>;
    const FORFETTARIO = <?= (int) ($js['forfettario'] ?? 0) ?>;
    const TAX_RATE = <?= json_encode((float) $js['taxRate']) ?>;
    const COEFF = <?= json_encode((float) $js['coeff']) ?>;
    const LIMIT = <?= json_encode((float) $js['limit']) ?>;

    function num(id) {
        return parseFloat(document.getElementById(id).value) || 0;
    }
    function euro(n) {
        return '€ ' + Math.round(n).toLocaleString('nl-NL');
    }
    function nights(months, pct, rooms) {
        return months * DAYS * (pct / 100) * rooms;
    }
    function taxOf(rev, exp) {
        if (rev <= 0) return 0;
        if (FORFETTARIO) return rev * COEFF * (TAX_RATE / 100);
        return Math.max(0, rev - exp) * (TAX_RATE / 100);
    }
    function pack(occHigh, occLow, mHigh, mLow) {
        const rooms = num('number_of_rooms');
        const price = num('price_per_room_per_night');
        const hiN = nights(mHigh, occHigh, rooms);
        const loN = nights(mLow, occLow, rooms);
        const n = hiN + loN;
        const revY = (hiN + loN) * price;
        const rev = revY / 12;
        const fixed = num('extra_energy_water') + num('insurance') + num('cleaning') + num('linen_laundry')
            + num('marketing') + num('maintenance') + num('administration');
        const commission = rev * (num('platform_commission') / 100);
        const breakfast = n * num('breakfast_per_guest') / 12;
        const exp = fixed + commission + breakfast;
        const tax = taxOf(rev, exp);
        return { rooms, price, hiN, loN, n, rev, revY, hiRev: hiN * price, loRev: loN * price, fixed, commission, breakfast, exp, tax, net: rev - exp, netTax: rev - exp - tax };
    }
    function breakEven() {
        if (num('number_of_rooms') <= 0 || num('price_per_room_per_night') <= 0) return 0;
        if (pack(100, 100, 6, 6).netTax < 0) return 101;
        let low = 0, high = 100, best = 100;
        for (let i = 0; i < 40; i++) {
            const mid = (low + high) / 2;
            const p = pack(mid, mid, 6, 6);
            if (p.netTax >= 0) { best = mid; high = mid; }
            else low = mid;
        }
        return best;
    }
    function render() {
        const mH = num('high_season_months');
        const mL = num('low_season_months');
        const p = pack(num('high_season_percentage'), num('low_season_percentage'), mH, mL);
        const months = mH + mL;
        const occ = months > 0
            ? ((num('high_season_percentage') * mH) + (num('low_season_percentage') * mL)) / months
            : 0;
        const be = breakEven();
        document.getElementById('kpiNights').textContent = Math.round(p.n).toLocaleString('nl-NL');
        document.getElementById('kpiOcc').textContent = Math.round(occ) + '%';
        document.getElementById('kpiNet').textContent = euro(p.net);
        document.getElementById('kpiBe').textContent = be > 100 ? '>100%' : Math.round(be) + '%';
        document.getElementById('kpiRev').textContent = euro(p.rev);
        document.getElementById('kpiRevYear').textContent = Math.round(p.revY).toLocaleString('nl-NL');
        document.getElementById('kpiExp').textContent = euro(p.exp);
        document.getElementById('kpiNetTax').textContent = euro(p.netTax);
        document.getElementById('kpiTax').textContent = Math.round(p.tax).toLocaleString('nl-NL');
        const netCard = document.getElementById('kpiNetCard');
        netCard.classList.toggle('positive', p.net >= 0);
        netCard.classList.toggle('negative', p.net < 0);
        const beCard = document.getElementById('kpiBeCard');
        beCard.classList.toggle('negative', be > 80);
        beCard.classList.toggle('neutral', be <= 80);
        const hiShare = p.n > 0 ? (p.hiN / p.n) * 100 : 50;
        document.getElementById('barHigh').style.width = hiShare + '%';
        document.getElementById('barLow').style.width = (100 - hiShare) + '%';
        document.getElementById('tblHighNights').textContent = Math.round(p.hiN).toLocaleString('nl-NL');
        document.getElementById('tblLowNights').textContent = Math.round(p.loN).toLocaleString('nl-NL');
        document.getElementById('tblHighRev').textContent = euro(p.hiRev);
        document.getElementById('tblLowRev').textContent = euro(p.loRev);
        document.getElementById('monthWarn').classList.toggle('d-none', months === 12);
        const rows = [
            ['Extra energie/water', num('extra_energy_water')],
            ['Verzekering', num('insurance')],
            ['Schoonmaak', num('cleaning')],
            ['Linnen & was', num('linen_laundry')],
            ['Marketing', num('marketing')],
            ['Onderhoud', num('maintenance')],
            ['Administratie', num('administration')],
            ['Platformcommissie', p.commission],
            ['Ontbijt', p.breakfast]
        ];
        const list = document.getElementById('costList');
        list.innerHTML = rows.filter(function (r) { return r[1] > 0; }).map(function (r) {
            return '<li class="d-flex justify-content-between py-1 border-bottom"><span>' + r[0] + '</span><strong>' + euro(r[1]) + '</strong></li>';
        }).join('');
        document.getElementById('costEmpty').style.display = list.innerHTML ? 'none' : '';
        document.getElementById('limitWarn').classList.toggle('d-none', p.revY <= LIMIT);
        document.getElementById('beFill').style.width = Math.min(100, Math.max(4, be)) + '%';
        document.getElementById('beMark').style.left = Math.min(96, Math.max(2, occ)) + '%';
        document.getElementById('beLabel').textContent = be > 100 ? '>100%' : be.toLocaleString('nl-NL', { maximumFractionDigits: 1 }) + '%';
        document.getElementById('occLabel').textContent = occ.toLocaleString('nl-NL', { maximumFractionDigits: 1 }) + '%';
        let alertHtml = '';
        if (be > 100) alertHtml = '<div class="alert alert-danger mb-3">Met deze prijs en kosten haal je geen break-even, ook niet bij 100% bezetting.</div>';
        else if (be > 80) alertHtml = '<div class="alert alert-warning mb-3">Break-even is hoog (&gt;80%). Weinig ruimte voor een mindere maand.</div>';
        else alertHtml = '<div class="alert alert-success mb-3">Break-even is haalbaar. Er blijft marge over je huidige bezetting.</div>';
        document.getElementById('beAlert').innerHTML = alertHtml;
        const body = document.querySelector('#scenarioTable tbody');
        body.innerHTML = [40, 50, 60, 70, 80].map(function (pct) {
            const sc = pack(pct, pct, 6, 6);
            const cls = sc.netTax >= 0 ? 'text-success' : 'text-danger';
            return '<tr class="' + cls + '"><td>' + pct + '%</td><td class="text-end">' + euro(sc.rev) + '</td><td class="text-end">' + euro(sc.exp) + '</td><td class="text-end">' + euro(sc.tax) + '</td><td class="text-end">' + euro(sc.netTax) + '</td></tr>';
        }).join('');
    }
    document.querySelectorAll('.bnb-live').forEach(function (el) {
        el.addEventListener('input', render);
        el.addEventListener('change', render);
    });
})();
</script>
<?= $this->endSection() ?>
