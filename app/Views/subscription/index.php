<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$endsLabel = !empty($subscription['ends_at'])
    ? date('d-m-Y', strtotime($subscription['ends_at']))
    : null;
$sourceLabel = [
    'complimentary' => 'Gratis maand',
    'paypal' => 'PayPal',
][$subscription['source'] ?? ''] ?? ($subscription['source'] ?? '');
$planLabel = ($subscription['plan'] ?? '') === 'month' ? 'Maand' : 'Jaar';
$fmt = fn (float $n) => '€ ' . number_format($n, 2, ',', '.');
?>

<div class="billing-hero mb-4">
    <div>
        <div class="reno-kicker">Account</div>
        <h1 class="mb-2">Abonnement</h1>
        <p class="reno-lead mb-0">
            <?php if ($isAdmin): ?>
                Als beheerder heb je altijd toegang tot de applicatie.
            <?php elseif (!$settings['billing_enabled']): ?>
                Betalen is nog niet verplicht. Nieuwe accounts krijgen automatisch een maand toegang.
            <?php elseif ($active): ?>
                Je abonnement is actief tot <?= esc($endsLabel) ?>.
            <?php else: ?>
                Kies een maand- of jaarabonnement via PayPal om verder te gaan.
            <?php endif; ?>
        </p>
    </div>
    <div class="text-end">
        <?php if ($active): ?>
            <span class="badge bg-success fs-6">Actief tot <?= esc($endsLabel) ?></span>
        <?php elseif ($isAdmin): ?>
            <span class="badge bg-danger fs-6">Admin</span>
        <?php else: ?>
            <span class="badge bg-secondary fs-6">Geen actief abonnement</span>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Huidige status</h5>
                <?php if ($subscription): ?>
                    <dl class="row mb-0">
                        <dt class="col-5">Plan</dt>
                        <dd class="col-7"><?= esc($planLabel) ?></dd>
                        <dt class="col-5">Bron</dt>
                        <dd class="col-7"><?= esc($sourceLabel) ?></dd>
                        <dt class="col-5">Geldig tot</dt>
                        <dd class="col-7"><?= esc($endsLabel ?? '—') ?></dd>
                    </dl>
                <?php else: ?>
                    <p class="text-muted mb-0">Er is nog geen abonnement gekoppeld aan dit account.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card billing-plan h-100">
                    <div class="card-body d-flex flex-column">
                        <h5>Maand</h5>
                        <p class="display-6 mb-2"><?= $fmt((float) $settings['price_month']) ?></p>
                        <p class="text-muted">Een maand extra toegang. Verlengt vanaf de huidige einddatum.</p>
                        <?php if ($settings['billing_enabled'] && $settings['paypal_configured'] && !$isAdmin): ?>
                            <form action="/subscription/checkout/month" method="post" class="mt-auto">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary w-100">
                                    Betalen met PayPal
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-secondary w-100 mt-auto" disabled>
                                <?= $settings['billing_enabled'] ? 'PayPal nog niet ingesteld' : 'Nog niet nodig' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card billing-plan billing-plan-year h-100">
                    <div class="card-body d-flex flex-column">
                        <h5>Jaar</h5>
                        <p class="display-6 mb-2"><?= $fmt((float) $settings['price_year']) ?></p>
                        <p class="text-muted">Twaalf maanden extra toegang. Voordeliger dan maandelijks.</p>
                        <?php if ($settings['billing_enabled'] && $settings['paypal_configured'] && !$isAdmin): ?>
                            <form action="/subscription/checkout/year" method="post" class="mt-auto">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary w-100">
                                    Betalen met PayPal
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-secondary w-100 mt-auto" disabled>
                                <?= $settings['billing_enabled'] ? 'PayPal nog niet ingesteld' : 'Nog niet nodig' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
