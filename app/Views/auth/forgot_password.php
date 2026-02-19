<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord vergeten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">
                            <i class="bi bi-key"></i> Wachtwoord vergeten
                        </h3>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <p class="text-muted">Voer je e-mailadres in. Als dit e-mailadres bij een account hoort, ontvang je een link om je wachtwoord te resetten.</p>

                        <form action="/password-reset/send" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mailadres</label>
                                <input type="email" class="form-control" id="email" name="email" required autofocus>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-envelope"></i> Verstuur reset link
                            </button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center mb-0">
                            <a href="/login"><i class="bi bi-arrow-left"></i> Terug naar login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
