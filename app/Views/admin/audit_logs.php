<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2"><i class="bi bi-journal-text"></i> Audit Log</h1>
    <div class="d-flex gap-2">
        <!-- Delete old logs -->
        <form method="POST" action="/admin/audit-logs/delete-old" class="d-inline" onsubmit="return confirm('Logs verwijderen die ouder zijn dan de opgegeven dagen?')">
            <?= csrf_field() ?>
            <div class="input-group input-group-sm">
                <input type="number" name="days" value="90" min="1" max="365" class="form-control" style="width:80px" title="Aantal dagen">
                <button class="btn btn-outline-warning" type="submit">
                    <i class="bi bi-calendar-x"></i> Ouder dan X dagen
                </button>
            </div>
        </form>
        <!-- Clear all -->
        <form method="POST" action="/admin/audit-logs/clear" class="d-inline" onsubmit="return confirm('Weet je zeker dat je de VOLLEDIGE audit log wilt wissen?')">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-danger">
                <i class="bi bi-trash"></i> Alles wissen
            </button>
        </form>
        <a href="/admin/users" class="btn btn-sm btn-secondary">
            <i class="bi bi-people"></i> Gebruikersbeheer
        </a>
    </div>
</div>

<!-- Filter by user -->
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" action="/admin/audit-logs" class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label fw-semibold">Filteren op gebruiker:</label>
            </div>
            <div class="col-auto">
                <select name="user_id" class="form-select form-select-sm" style="min-width:180px" onchange="this.form.submit()">
                    <option value="">— Alle gebruikers —</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['user_id'] ?>" <?= $filterUser == $u['user_id'] ? 'selected' : '' ?>>
                            <?= esc($u['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($filterUser): ?>
            <div class="col-auto">
                <a href="/admin/audit-logs" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x"></i> Filter wissen
                </a>
            </div>
            <?php endif; ?>
            <div class="col-auto ms-auto text-muted small">
                <?= count($logs) ?> regels getoond
            </div>
        </form>
    </div>
</div>

<?php if (empty($logs)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Geen log regels gevonden.
    </div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:150px">Datum &amp; tijd</th>
                        <th style="width:120px">Gebruiker</th>
                        <th>Actie</th>
                        <th style="width:60px">Methode</th>
                        <th>URL</th>
                        <th style="width:120px">IP-adres</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="text-nowrap text-muted small">
                            <?= date('d-m-Y H:i:s', strtotime($log['created_at'])) ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= esc($log['username'] ?? '—') ?></span>
                        </td>
                        <td><?= esc($log['action']) ?></td>
                        <td>
                            <?php
                            $method = strtoupper($log['method']);
                            $badge = match($method) {
                                'POST'   => 'bg-warning text-dark',
                                'DELETE' => 'bg-danger',
                                'PUT'    => 'bg-info text-dark',
                                default  => 'bg-light text-dark border',
                            };
                            ?>
                            <span class="badge <?= $badge ?>"><?= $method ?></span>
                        </td>
                        <td class="text-muted small font-monospace"><?= esc($log['url']) ?></td>
                        <td class="text-muted small"><?= esc($log['ip_address'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($pager): ?>
<div class="mt-3 d-flex justify-content-center">
    <?= $pager->links('default', 'default_full') ?>
</div>
<?php endif; ?>

<?php endif; ?>

<?= $this->endSection() ?>
