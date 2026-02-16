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
</div>

<?= $this->endSection() ?>
