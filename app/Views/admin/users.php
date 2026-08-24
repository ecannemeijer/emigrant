<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$sourceLabels = [
    'complimentary' => 'Gratis jaar',
    'paypal' => 'PayPal',
    'admin' => 'Handmatig',
];
?>
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-shield-lock"></i> Gebruikersbeheer</h1>
            <p class="text-muted">Abonnement en einddatum per gebruiker. Datums pas je aan via bewerken.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/payments" class="btn btn-outline-primary">
                <i class="bi bi-receipt"></i> Betalingen
            </a>
            <a href="/admin/users/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nieuwe gebruiker
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gebruikersnaam</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Account</th>
                    <th>Abonnement</th>
                    <th>Geldig tot</th>
                    <th>Aangemaakt</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <?php
                    $sub = $subscriptions[$user['id']] ?? null;
                    $endsTs = $sub ? strtotime((string) $sub['ends_at']) : 0;
                    $isActiveSub = $sub && ($sub['status'] ?? '') === 'active' && $endsTs > time();
                ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td>
                        <?php if ($user['role'] === 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-primary">User</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['is_active']): ?>
                            <span class="badge bg-success">Actief</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactief</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($sub): ?>
                            <?php if ($isActiveSub): ?>
                                <span class="badge bg-success">Actief</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Verlopen</span>
                            <?php endif; ?>
                            <div class="small text-muted mt-1">
                                <?= esc($sourceLabels[$sub['source'] ?? ''] ?? ($sub['source'] ?? '')) ?>
                                · <?= ($sub['plan'] ?? '') === 'month' ? 'Maand' : 'Jaar' ?>
                            </div>
                        <?php else: ?>
                            <span class="badge bg-secondary">Geen</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($sub && !empty($sub['ends_at'])): ?>
                            <?= date('d-m-Y H:i', $endsTs) ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?= date('d-m-Y', strtotime($user['created_at'])) ?></td>
                    <td>
                        <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($user['id'] != session()->get('userId')): ?>
                        <form action="/admin/users/delete/<?= $user['id'] ?>" method="post" class="d-inline"
                              onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
