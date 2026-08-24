<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$fmt = static fn ($n) => '€ ' . number_format((float) $n, 0, ',', '.');
$profile = $profile ?? [];
$calculations = $calculations ?? [];
$yearlyProjections = $yearlyProjections ?? [];
$hasPartner = !empty($calculations['has_partner']);
$youName = trim((string) ($profile['first_name'] ?? '')) !== '' ? $profile['first_name'] : 'Gebruiker';
$partnerName = trim((string) ($profile['partner_name'] ?? '')) !== '' ? $profile['partner_name'] : 'Partner';
$year0 = $yearlyProjections[0] ?? null;
?>

<div class="mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <p class="text-muted mb-1">Admin · alleen lezen</p>
        <h1 class="mb-2">Financiële projectie</h1>
        <p class="mb-0">
            <strong><?= esc($user['username']) ?></strong>
            · <?= esc($user['email']) ?>
            <?php if (!empty($profile['first_name']) || !empty($profile['last_name'])): ?>
                · <?= esc(trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''))) ?>
            <?php endif; ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/users/edit/<?= (int) $user['id'] ?>" class="btn btn-outline-primary">Bewerken</a>
        <a href="/admin/users" class="btn btn-outline-secondary">Alle gebruikers</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted">Startvermogen</div>
                <div class="fs-4"><?= $fmt($calculations['starting_capital'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted">Netto / maand (jaar <?= esc((string) ($year0['year'] ?? date('Y'))) ?>)</div>
                <div class="fs-4 <?= (($year0['monthly_net'] ?? 0) >= 0) ? 'text-success' : 'text-danger' ?>">
                    <?= $year0 ? $fmt($year0['monthly_net']) : '—' ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted">Vermogen einde dit jaar</div>
                <div class="fs-4 <?= (($year0['capital'] ?? 0) >= 0) ? '' : 'text-danger' ?>">
                    <?= $year0 ? $fmt($year0['capital']) : '—' ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted">Verbouw in projectie</div>
                <div class="fs-4"><?= $fmt($calculations['renovation_outlay'] ?? 0) ?></div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($warnings)): ?>
    <div class="alert alert-warning">
        <strong>Let op</strong>
        <ul class="mb-0">
            <?php foreach ($warnings as $warning): ?>
                <li><?= esc(is_array($warning) ? ($warning['message'] ?? json_encode($warning)) : $warning) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (empty($profile['date_of_birth']) || empty($profile['emigration_date'])): ?>
    <div class="alert alert-info">
        Profiel is incompleet
        (<?= empty($profile['date_of_birth']) ? 'geen geboortedatum' : '' ?>
        <?= empty($profile['date_of_birth']) && empty($profile['emigration_date']) ? ', ' : '' ?>
        <?= empty($profile['emigration_date']) ? 'geen emigratiedatum' : '' ?>).
        De projectie kan daardoor afwijken of leeg zijn.
    </div>
<?php endif; ?>

<?php if (empty($yearlyProjections)): ?>
    <div class="card">
        <div class="card-body text-muted">Nog geen jaarprojectie. Deze gebruiker heeft waarschijnlijk te weinig gegevens ingevuld.</div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <h2 class="h5 mb-0">Per jaar</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Jaar</th>
                            <th><?= esc($youName) ?></th>
                            <?php if ($hasPartner): ?>
                                <th><?= esc($partnerName) ?></th>
                            <?php endif; ?>
                            <th class="text-end">Inkomen/mnd</th>
                            <th class="text-end">Kosten/mnd</th>
                            <th class="text-end">Belasting/mnd</th>
                            <th class="text-end">Netto/mnd</th>
                            <th class="text-end">Netto/jr</th>
                            <th class="text-end">Vermogen</th>
                            <th class="text-end">Verbouw</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($yearlyProjections as $row): ?>
                            <tr>
                                <td><strong><?= (int) $row['year'] ?></strong></td>
                                <td><?= esc((string) ($row['user_age'] ?? '—')) ?></td>
                                <?php if ($hasPartner): ?>
                                    <td><?= esc((string) ($row['partner_age'] ?? '—')) ?></td>
                                <?php endif; ?>
                                <td class="text-end"><?= $fmt($row['monthly_income'] ?? 0) ?></td>
                                <td class="text-end"><?= $fmt(($row['yearly_expenses'] ?? 0) / 12) ?></td>
                                <td class="text-end"><?= $fmt(($row['yearly_taxes'] ?? 0) / 12) ?></td>
                                <td class="text-end <?= (($row['monthly_net'] ?? 0) >= 0) ? 'text-success' : 'text-danger' ?>">
                                    <?= $fmt($row['monthly_net'] ?? 0) ?>
                                </td>
                                <td class="text-end <?= (($row['yearly_net'] ?? 0) >= 0) ? 'text-success' : 'text-danger' ?>">
                                    <?= $fmt($row['yearly_net'] ?? 0) ?>
                                </td>
                                <td class="text-end <?= (($row['capital'] ?? 0) >= 0) ? '' : 'text-danger' ?>">
                                    <strong><?= $fmt($row['capital'] ?? 0) ?></strong>
                                </td>
                                <td class="text-end">
                                    <?php if (($row['renovation_outlay'] ?? 0) > 0): ?>
                                        <?= $fmt($row['renovation_outlay']) ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <p class="small text-muted mt-3 mb-0">Zelfde berekening als het dashboard van deze gebruiker. Alleen inzien, niet bewerken.</p>
<?php endif; ?>
<?= $this->endSection() ?>
