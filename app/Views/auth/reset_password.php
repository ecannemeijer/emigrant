<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">
                        <i class="bi bi-shield-lock"></i> Nieuw wachtwoord
                    </h2>

                    <form action="/password-reset/update" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">Nieuw wachtwoord</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8" autofocus>
                            <small class="text-muted">Minimaal 8 tekens</small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Bevestig wachtwoord</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="8">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Wachtwoord wijzigen
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="/login"><i class="bi bi-arrow-left"></i> Terug naar inloggen</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
