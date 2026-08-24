<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$settings = $settings ?? [];
$fmt = fn (float $n) => '€ ' . number_format($n, 2, ',', '.');
$saleOn = !empty($settings['discount_active']);
$freeMonth = empty($settings['billing_enabled']);
$pctLabel = rtrim(rtrim(number_format((float) ($settings['discount_percent'] ?? 0), 1, ',', ''), '0'), ',');
?>

<section class="mkt-hero">
    <p class="mkt-eyebrow">EmigreerItalia</p>
    <h1>Emigreren naar Italië, helder doorgerekend</h1>
    <p class="lead">
        Van de verkoop van je huis in Nederland tot AOW, forfettario, IMU, verbouwing en B&amp;B.
        Eén overzicht met cashflow, vermogen en een realistische meerjarenprojectie.
    </p>
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="/register" class="btn btn-light btn-lg">Gratis starten</a>
        <a href="/login" class="btn btn-outline-light btn-lg">Inloggen</a>
    </div>
    <?php if ($freeMonth): ?>
        <p class="mkt-hero-note mb-0">De <strong>eerste maand is gratis</strong>. Daarna kies je zelf of je doorgaat.</p>
    <?php elseif ($saleOn): ?>
        <p class="mkt-hero-note mb-0">Nu <strong><?= esc($pctLabel) ?>% korting</strong> tot en met <?= esc($settings['discount_until_label']) ?>.</p>
    <?php else: ?>
        <p class="mkt-hero-note mb-0">Maak een account, log in en reken je emigratie door.</p>
    <?php endif; ?>
</section>

<section class="mkt-section" id="features">
    <h2>Wat je krijgt</h2>
    <p class="text-muted mb-4">Alles wat je nodig hebt om de cijfers van NL naar IT op één plek te houden.</p>
    <div class="row g-3">
        <?php
        $features = [
            ['bi-house-door', 'Startpositie NL', 'Overwaarde, spaargeld en startvermogen na verkoop van je Nederlandse woning.'],
            ['bi-cash-coin', 'Inkomsten', 'Loon, uitkeringen, AOW en pensioen — van jou en je partner.'],
            ['bi-building', 'Vastgoed IT', 'Hoofdwoning en optionele tweede woning in Italië, met kosten en waarde.'],
            ['bi-hammer', 'Verbouwen & planning', 'Posten, budget en een agenda met afspraken — ook naar Google Agenda.'],
            ['bi-wallet2', 'Maandlasten', 'Vaste en variabele kosten, zodat je ziet wat er écht overblijft.'],
            ['bi-receipt', 'Belastingen', 'Forfettario, IMU, TARI en sociale bijdragen in één module.'],
            ['bi-shop', 'B&B', 'Bezetting, omzet, break-even en of een B&amp;B jouw plan draagt.'],
            ['bi-check2-square', 'Checklist', 'Praktische stappen naast de cijfers, zodat je niets vergeet.'],
            ['bi-diagram-3', 'Scenario’s & export', 'Vergelijk varianten, bekijk het dashboard en exporteer CSV of PDF.'],
        ];
        foreach ($features as [$icon, $title, $copy]):
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card mkt-feature h-100">
                    <div class="card-body">
                        <div class="mkt-feature-icon"><i class="bi <?= esc($icon) ?>"></i></div>
                        <h3 class="h5"><?= esc($title) ?></h3>
                        <p class="text-muted mb-0"><?= $copy ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mkt-section">
    <h2>Hoe het werkt</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="mkt-step">
                <span>1</span>
                <h3 class="h5">Account maken</h3>
                <p class="text-muted mb-0">Registreer met e-mail. <?= $freeMonth ? 'Je krijgt meteen een maand toegang.' : 'Daarna log je in op je eigen omgeving.' ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mkt-step">
                <span>2</span>
                <h3 class="h5">Gegevens invullen</h3>
                <p class="text-muted mb-0">Startpositie, inkomsten, woning, lasten en belastingen. In je tempo, per scherm.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mkt-step">
                <span>3</span>
                <h3 class="h5">Inzicht op het dashboard</h3>
                <p class="text-muted mb-0">Cashflow, vermogen en jaren vooruit. Later weer inloggen via dezelfde pagina.</p>
            </div>
        </div>
    </div>
</section>

<section class="mkt-section" id="prijzen">
    <h2>Prijzen</h2>
    <?php if ($freeMonth): ?>
        <p class="text-muted mb-4">Nieuwe accounts krijgen de <strong>eerste maand gratis</strong>. Daarna kun je een abonnement nemen.</p>
    <?php elseif ($saleOn): ?>
        <p class="text-muted mb-4">Tijdelijke actie: <strong><?= esc($pctLabel) ?>% korting</strong> tot en met <?= esc($settings['discount_until_label']) ?>.</p>
    <?php else: ?>
        <p class="text-muted mb-4">Kies maand of jaar. Betalen gaat via PayPal.</p>
    <?php endif; ?>

    <div class="row g-3 justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card billing-plan h-100<?= $saleOn ? ' is-sale' : '' ?>">
                <div class="card-body d-flex flex-column">
                    <?php if ($saleOn): ?><span class="billing-sale-tag">Actieprijs</span><?php endif; ?>
                    <h3 class="h5">Maand</h3>
                    <?php if ($saleOn): ?>
                        <p class="billing-price-old mb-0"><?= $fmt((float) $settings['price_month']) ?></p>
                        <p class="display-6 billing-price-now mb-1"><?= $fmt((float) $settings['sale_price_month']) ?></p>
                        <p class="billing-save mb-3">Tot <?= esc($settings['discount_until_label']) ?></p>
                    <?php else: ?>
                        <p class="display-6 mb-3"><?= $fmt((float) $settings['price_month']) ?></p>
                    <?php endif; ?>
                    <p class="text-muted">Een maand extra toegang tot alle modules.</p>
                    <a href="/register" class="btn btn-outline-primary mt-auto">Gratis starten</a>
                </div>
            </div>
        </div>
        <div class="col-md-5 col-lg-4">
            <div class="card billing-plan billing-plan-year h-100<?= $saleOn ? ' is-sale' : '' ?>">
                <div class="card-body d-flex flex-column">
                    <?php if ($saleOn): ?><span class="billing-sale-tag">Actieprijs</span><?php endif; ?>
                    <h3 class="h5">Jaar</h3>
                    <?php if ($saleOn): ?>
                        <p class="billing-price-old mb-0"><?= $fmt((float) $settings['price_year']) ?></p>
                        <p class="display-6 billing-price-now mb-1"><?= $fmt((float) $settings['sale_price_year']) ?></p>
                        <p class="billing-save mb-3">Tot <?= esc($settings['discount_until_label']) ?></p>
                    <?php else: ?>
                        <p class="display-6 mb-3"><?= $fmt((float) $settings['price_year']) ?></p>
                    <?php endif; ?>
                    <p class="text-muted">Twaalf maanden. Voordeliger dan maandelijks.</p>
                    <a href="/register" class="btn btn-primary mt-auto">Gratis starten</a>
                </div>
            </div>
        </div>
    </div>
    <p class="small text-muted text-center mt-3 mb-0">Heb je al een account? <a href="/login">Log in</a> om je abonnement te beheren.</p>
</section>

<section class="mkt-disclaimer">
    <p class="mb-0"><strong>Geen advies.</strong> EmigreerItalia is een rekentool. Het is geen persoonlijk financieel, fiscaal of juridisch advies. Controleer bedragen altijd zelf of met een adviseur.</p>
</section>

<footer class="mkt-footer">
    <div>
        <strong>EmigreerItalia</strong>
        <span class="text-muted"> · emigreren naar Italië, in cijfers</span>
    </div>
    <div class="d-flex flex-wrap gap-3">
        <a href="/login">Inloggen</a>
        <a href="/register">Account maken</a>
        <a href="/contact">Contact</a>
        <a href="/help">Help</a>
    </div>
</footer>
<?= $this->endSection() ?>
