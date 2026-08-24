<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-3">
                        <i class="bi bi-key"></i> Wachtwoord vergeten
                    </h2>
                    <p class="text-muted text-center">Vul je e-mailadres in. Je ontvangt een link om een nieuw wachtwoord te kiezen. De link is 1 uur geldig.</p>

                    <form action="/password-reset/send" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mailadres</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email')) ?>" required autofocus>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-envelope"></i> Stuur resetlink
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
