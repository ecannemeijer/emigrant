<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">
                        <i class="bi bi-person-plus"></i> Registreren
                    </h2>

                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session('errors') as $error): ?>
                                <div><?= esc($error) ?></div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>

                    <form action="/register" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Gebruikersnaam</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Wachtwoord</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Minimaal 8 karakters</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Bevestig wachtwoord</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Registreren</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <p>Al een account? <a href="/login">Login hier</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
