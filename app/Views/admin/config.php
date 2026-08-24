<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-sliders"></i> Config</h1>
    <p class="text-muted">Abonnementen en betalingen. Tot je het vinkje aanzet, krijgen gebruikers automatisch een jaar toegang en is betalen niet verplicht.</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <div><?= esc($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form action="/admin/config" method="post">
                    <?= csrf_field() ?>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="billing_enabled"
                               name="billing_enabled" value="1"
                               <?= !empty($settings['billing_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="billing_enabled">
                            <strong>Betaling verplicht (PayPal)</strong>
                            <div class="text-muted small">
                                Aan: nieuwe gebruikers moeten betalen. Uit: nieuwe gebruikers krijgen 1 maand gratis.
                            </div>
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price_month" class="form-label">Prijs per maand</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="price_month"
                                       name="price_month" value="<?= esc(old('price_month', number_format((float) $settings['price_month'], 2, '.', ''))) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price_year" class="form-label">Prijs per jaar</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="price_year"
                                       name="price_year" value="<?= esc(old('price_year', number_format((float) $settings['price_year'], 2, '.', ''))) ?>" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">PayPal</h5>
                <p class="small text-muted">Keys staan in <code>.env</code>, niet in deze database.</p>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        Status:
                        <?php if ($settings['paypal_configured']): ?>
                            <span class="badge bg-success">Geconfigureerd</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Nog niet ingesteld</span>
                        <?php endif; ?>
                    </li>
                    <li class="mb-2">Modus: <code><?= esc($settings['paypal_mode']) ?></code></li>
                    <li>Webhook: <code><?= esc(site_url('webhooks/paypal')) ?></code></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
