<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-pencil"></i> Gebruiker Bewerken</h1>
    <p class="text-muted">Wijzig gebruikersgegevens</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="/admin/users/update/<?= $user['id'] ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Gebruikersnaam</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= esc($user['username']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= esc($user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Voornaam</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?= esc($user['first_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Achternaam</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?= esc($user['last_name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nieuw Wachtwoord</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Laat leeg om wachtwoord niet te wijzigen</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Rol</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   <?= $user['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                Account actief
                            </label>
                        </div>
                    </div>

                    <?php
                        $sub = $subscription ?? null;
                        $toLocal = static function (?string $dt): string {
                            if (!$dt) {
                                return '';
                            }
                            $ts = strtotime($dt);
                            return $ts ? date('Y-m-d\TH:i', $ts) : '';
                        };
                    ?>
                    <hr>
                    <h5 class="mb-3">Abonnement</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="subscription_starts_at" class="form-label">Start</label>
                            <input type="datetime-local" class="form-control" id="subscription_starts_at"
                                   name="subscription_starts_at"
                                   value="<?= esc(old('subscription_starts_at', $toLocal($sub['starts_at'] ?? null))) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subscription_ends_at" class="form-label">Geldig tot</label>
                            <input type="datetime-local" class="form-control" id="subscription_ends_at"
                                   name="subscription_ends_at"
                                   value="<?= esc(old('subscription_ends_at', $toLocal($sub['ends_at'] ?? null))) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="subscription_plan" class="form-label">Plan</label>
                            <select class="form-select" id="subscription_plan" name="subscription_plan">
                                <option value="year" <?= ($sub['plan'] ?? 'year') === 'year' ? 'selected' : '' ?>>Jaar</option>
                                <option value="month" <?= ($sub['plan'] ?? '') === 'month' ? 'selected' : '' ?>>Maand</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subscription_source" class="form-label">Bron</label>
                            <select class="form-select" id="subscription_source" name="subscription_source">
                                <option value="complimentary" <?= ($sub['source'] ?? '') === 'complimentary' ? 'selected' : '' ?>>Gratis maand</option>
                                <option value="paypal" <?= ($sub['source'] ?? '') === 'paypal' ? 'selected' : '' ?>>PayPal</option>
                                <option value="admin" <?= ($sub['source'] ?? 'admin') === 'admin' ? 'selected' : '' ?>>Handmatig</option>
                            </select>
                        </div>
                    </div>
                    <p class="form-text">Vul start- en einddatum in om het abonnement te zetten of te verlengen. Einddatum in de toekomst = actief.</p>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Opslaan
                    </button>
                    <a href="/admin/users" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Annuleren
                    </a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">PayPal-betalingen</h5>
                <?php if (empty($payments)): ?>
                    <p class="text-muted mb-0">Nog geen betalingen voor deze gebruiker.</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($payments as $payment): ?>
                            <li class="border-bottom py-2">
                                <div class="fw-semibold">
                                    € <?= number_format((float) $payment['amount'], 2, ',', '.') ?>
                                    · <?= ($payment['plan'] ?? '') === 'month' ? 'Maand' : 'Jaar' ?>
                                </div>
                                <div class="small text-muted">
                                    <?= date('d-m-Y H:i', strtotime($payment['created_at'])) ?>
                                    · <?= esc($payment['status']) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="/admin/payments?user_id=<?= (int) $user['id'] ?>" class="btn btn-sm btn-outline-primary mt-3">Alle betalingen</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
