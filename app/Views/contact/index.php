<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-envelope"></i> Contact</h1>
    <p class="text-muted">Heb je een vraag of feedback? Stuur ons een bericht!</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="/contact/send" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Naam *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name', session()->get('username') ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mailadres *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email', session()->get('email') ?? '') ?>" required>
                        <small class="text-muted">We gebruiken dit om contact met je op te nemen</small>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Onderwerp *</label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               value="<?= old('subject') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Bericht *</label>
                        <textarea class="form-control" id="message" name="message" rows="8" required><?= old('message') ?></textarea>
                        <small class="text-muted">Minimaal 10 tekens</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Verzenden
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-info-circle"></i> Informatie</h5>
                <p>Ons team staat klaar om je te helpen met:</p>
                <ul>
                    <li>Vragen over de calculator</li>
                    <li>Technische problemen</li>
                    <li>Feature requests</li>
                    <li>Feedback & suggesties</li>
                    <li>Account gerelateerde vragen</li>
                </ul>

                <hr>

                <h6><i class="bi bi-clock"></i> Reactietijd</h6>
                <p class="text-muted">We streven ernaar binnen 1-2 werkdagen te reageren.</p>

                <hr>

                <h6><i class="bi bi-book"></i> Eerst hulp nodig?</h6>
                <p>Bekijk onze <a href="/help">Help & Documentatie</a> voor antwoorden op veelgestelde vragen.</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
