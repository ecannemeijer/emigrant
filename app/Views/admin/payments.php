<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$statusLabels = [
    'completed' => 'Betaald',
    'pending' => 'In behandeling',
    'failed' => 'Mislukt',
];
$planLabels = [
    'month' => 'Maand',
    'year' => 'Jaar',
];
?>
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-receipt"></i> Betalingen</h1>
            <p class="text-muted">PayPal-betalingen: wie heeft betaald, wanneer en voor welk plan.</p>
        </div>
        <a href="/admin/users" class="btn btn-secondary">
            <i class="bi bi-people"></i> Gebruikers
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" action="/admin/payments" class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label fw-semibold">Gebruiker:</label>
            </div>
            <div class="col-auto">
                <select name="user_id" class="form-select form-select-sm" style="min-width:220px" onchange="this.form.submit()">
                    <option value="">— Alle gebruikers —</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= (string) $filterUser === (string) $u['id'] ? 'selected' : '' ?>>
                            <?= esc($u['username']) ?> (<?= esc($u['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($filterUser): ?>
            <div class="col-auto">
                <a href="/admin/payments" class="btn btn-sm btn-outline-secondary">Filter wissen</a>
            </div>
            <?php endif; ?>
            <div class="col-auto ms-auto text-muted small">
                <?= count($payments) ?> betalingen
            </div>
        </form>
    </div>
</div>

<?php if (empty($payments)): ?>
    <div class="alert alert-info mb-0">Nog geen PayPal-betalingen gevonden.</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Gebruiker</th>
                        <th>Email</th>
                        <th>Plan</th>
                        <th>Bedrag</th>
                        <th>Status</th>
                        <th>PayPal order</th>
                        <th>Capture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= !empty($payment['created_at']) ? date('d-m-Y H:i', strtotime($payment['created_at'])) : '—' ?></td>
                        <td>
                            <?php if (!empty($payment['user_id'])): ?>
                                <a href="/admin/users/edit/<?= (int) $payment['user_id'] ?>"><?= esc($payment['username'] ?? 'User #' . $payment['user_id']) ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?= esc($payment['email'] ?? '') ?></td>
                        <td><?= esc($planLabels[$payment['plan'] ?? ''] ?? ($payment['plan'] ?? '')) ?></td>
                        <td>€ <?= number_format((float) ($payment['amount'] ?? 0), 2, ',', '.') ?> <?= esc($payment['currency'] ?? 'EUR') ?></td>
                        <td>
                            <?php $st = $payment['status'] ?? ''; ?>
                            <?php if ($st === 'completed'): ?>
                                <span class="badge bg-success"><?= esc($statusLabels[$st]) ?></span>
                            <?php elseif ($st === 'pending'): ?>
                                <span class="badge bg-warning text-dark"><?= esc($statusLabels[$st]) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= esc($statusLabels[$st] ?? $st) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="small font-monospace"><?= esc($payment['paypal_order_id'] ?? '—') ?></td>
                        <td class="small font-monospace"><?= esc($payment['paypal_capture_id'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
