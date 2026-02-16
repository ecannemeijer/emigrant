<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-person-plus"></i> Nieuwe Gebruiker</h1>
    <p class="text-muted">Maak een nieuwe gebruiker aan</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <?php foreach (session('errors') as $error): ?>
                            <div><?= esc($error) ?></div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <form action="/admin/users/store" method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Gebruikersnaam *</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Voornaam</label>
                            <input type="text" class="form-control" id="first_name" name="first_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Achternaam</label>
                            <input type="text" class="form-control" id="last_name" name="last_name">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Wachtwoord *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Minimaal 8 karakters</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Rol *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Account actief
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Aanmaken
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
