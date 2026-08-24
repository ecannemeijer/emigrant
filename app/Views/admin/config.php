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

                    <hr class="my-4">
                    <h2 class="h5 mb-3">Tijdelijke korting</h2>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="discount_enabled"
                               name="discount_enabled" value="1"
                               <?= old('discount_enabled', !empty($settings['discount_enabled']) ? '1' : '') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="discount_enabled">
                            <strong>Korting op abonnementen</strong>
                            <div class="text-muted small">
                                Streep door de officiële prijs op de abonnementspagina. PayPal rekent de actieprijs.
                            </div>
                        </label>
                    </div>
                    <?php if (!empty($settings['discount_enabled']) && empty($settings['discount_active'])): ?>
                        <div class="alert alert-warning py-2">De korting staat aan, maar is nu niet zichtbaar (datum verlopen of percentage 0).</div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="discount_percent" class="form-label">Kortingspercentage</label>
                            <div class="input-group">
                                <input type="number" step="1" min="0" max="100" class="form-control" id="discount_percent"
                                       name="discount_percent"
                                       value="<?= esc(old('discount_percent', (string) (int) round((float) ($settings['discount_percent'] ?? 0)))) ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discount_until" class="form-label">Geldig tot en met</label>
                            <input type="date" class="form-control" id="discount_until" name="discount_until"
                                   value="<?= esc(old('discount_until', $settings['discount_until'] ?? '')) ?>">
                            <div class="form-text">Op die dag tot 23:59 (Nederlandse tijd).</div>
                        </div>
                    </div>
                    <div class="billing-config-preview" id="discountPreview">
                        <div class="small text-muted mb-2">Voorbeeld op de abonnementspagina</div>
                        <div class="d-flex flex-wrap gap-3">
                            <div>
                                <span class="d-block small text-muted">Maand</span>
                                <span class="billing-price-old" id="previewMonthOld"></span>
                                <strong id="previewMonthNew"></strong>
                            </div>
                            <div>
                                <span class="d-block small text-muted">Jaar</span>
                                <span class="billing-price-old" id="previewYearOld"></span>
                                <strong id="previewYearNew"></strong>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Opslaan</button>
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

<?= $this->section('scripts') ?>
<script>
(function () {
    const euro = function (n) {
        return '€ ' + n.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
    const sale = function (list, pct) {
        const v = Math.round(list * (1 - pct / 100) * 100) / 100;
        return list > 0 && v < 0.01 ? 0.01 : Math.max(0, v);
    };
    const update = function () {
        const on = document.getElementById('discount_enabled').checked;
        const pct = parseFloat(String(document.getElementById('discount_percent').value).replace(',', '.')) || 0;
        const month = parseFloat(String(document.getElementById('price_month').value).replace(',', '.')) || 0;
        const year = parseFloat(String(document.getElementById('price_year').value).replace(',', '.')) || 0;
        const box = document.getElementById('discountPreview');
        const show = on && pct > 0;
        box.classList.toggle('is-on', show);
        document.getElementById('previewMonthOld').textContent = show ? euro(month) : '';
        document.getElementById('previewYearOld').textContent = show ? euro(year) : '';
        document.getElementById('previewMonthNew').textContent = euro(show ? sale(month, pct) : month);
        document.getElementById('previewYearNew').textContent = euro(show ? sale(year, pct) : year);
    };
    ['discount_enabled', 'discount_percent', 'price_month', 'price_year'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', update);
        if (el) el.addEventListener('change', update);
    });
    update();
})();
</script>
<?= $this->endSection() ?>
