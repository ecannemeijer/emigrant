<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
$income = $income ?? [];
$profile = $profile ?? [];
$youName = trim(($profile['first_name'] ?? '') !== '' ? $profile['first_name'] : 'Jij');
$partnerName = trim($profile['partner_name'] ?? '') !== '' ? $profile['partner_name'] : 'Partner';
$hasPartner = array_key_exists('has_partner', $profile) && $profile['has_partner'] !== null
    ? (int) $profile['has_partner'] === 1
    : (!empty($profile['partner_date_of_birth']) || !empty($profile['partner_name']));
$ownType = $income['own_benefit_type'] ?? ((((float) ($income['own_income'] ?? 0)) > 0) ? 'other' : 'none');
$partnerType = $income['partner_benefit_type'] ?? ((((int) ($income['partner_has_wia'] ?? 0)) === 1) ? 'wia' : 'other');
$ownAowAge = $income['own_aow_start_age'] ?? ($profile['retirement_age'] ?? 67);
$partnerAowAge = $income['partner_aow_start_age'] ?? $income['aow_start_age'] ?? ($profile['partner_retirement_age'] ?? 67);
$ownOther = $income['own_other_income'] ?? 0;
$partnerOther = $income['partner_other_income'] ?? 0;
if ((float) $ownOther === 0.0 && (float) $partnerOther === 0.0 && (float) ($income['other_income'] ?? 0) > 0) {
    $ownOther = $income['other_income'];
}
?>
<div class="mb-4">
    <h1><i class="bi bi-cash-coin"></i> Inkomsten</h1>
    <p class="text-muted mb-0">Per persoon: eerst loon (hoofdinkomen), daarna uitkering (WIA of anders) en AOW. Eén persoon mag ook.</p>
</div>

<form action="/income/save" method="post">
    <?= csrf_field() ?>

    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="has_partner" name="has_partner" value="1" <?= $hasPartner ? 'checked' : '' ?>>
                    <label class="form-check-label" for="has_partner"><strong>Inkomsten voor 2 personen (partner)</strong></label>
                </div>
                <small class="text-muted">Uit voor alleen jouw inkomsten. Aan voor jou én <?= esc($partnerName) ?>.</small>
            </div>
            <a href="/profile" class="btn btn-outline-secondary btn-sm">Namen en geboortedata in profiel</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card person-card person-card-you h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3"><i class="bi bi-person"></i> <?= esc($youName) ?></h2>

                    <div class="mb-3">
                        <label for="own_other_income" class="form-label">Loon per maand</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="own_other_income" name="own_other_income" value="<?= esc($ownOther) ?>">
                        </div>
                        <small class="text-muted">Netto loon of freelance. Dit is het hoofdinkomen en stopt niet automatisch bij AOW.</small>
                    </div>

                    <label class="form-label">Soort uitkering tot AOW</label>
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="own_benefit_type" id="own_type_wia" value="wia" <?= $ownType === 'wia' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-success" for="own_type_wia">WIA</label>
                        <input type="radio" class="btn-check" name="own_benefit_type" id="own_type_other" value="other" <?= $ownType === 'other' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-success" for="own_type_other">Andere uitkering</label>
                        <input type="radio" class="btn-check" name="own_benefit_type" id="own_type_none" value="none" <?= $ownType === 'none' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-success" for="own_type_none">Geen</label>
                    </div>

                    <div class="mb-3" id="own_benefit_wrap">
                        <label for="own_income" class="form-label" id="own_benefit_label">Uitkering netto per maand</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="own_income" name="own_income" value="<?= esc($income['own_income'] ?? 0) ?>">
                        </div>
                        <small class="text-muted">WIA en andere wettelijke uitkeringen groeien mee met de jaarlijkse indexatie en stoppen wanneer AOW ingaat.</small>
                    </div>

                    <div class="mb-3">
                        <label for="own_aow" class="form-label">AOW netto per maand</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="own_aow" name="own_aow" value="<?= esc($income['own_aow'] ?? 0) ?>">
                        </div>
                        <small class="text-muted">Vul het huidige nettobedrag in. In de projectie groeit AOW elk jaar mee met de indexatie, ook ná ingang.</small>
                    </div>
                    <div class="mb-3">
                        <label for="own_aow_start_age" class="form-label">AOW start op leeftijd</label>
                        <input type="number" min="60" max="75" class="form-control" id="own_aow_start_age" name="own_aow_start_age" value="<?= esc($ownAowAge) ?>">
                        <small class="text-muted">Telt mee vanaf deze leeftijd; het bedrag is dan al geïndexeerd vanaf nu (nu <?= (int) $ownAowAge ?>).</small>
                    </div>

                    <div class="mb-0">
                        <label for="pension" class="form-label">Aanvullend pensioen netto per maand</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="pension" name="pension" value="<?= esc($income['pension'] ?? 0) ?>">
                        </div>
                        <small class="text-muted">Start op pensioenleeftijd in je profiel (<?= (int) ($profile['retirement_age'] ?? 67) ?>). Niet geïndexeerd.</small>
                    </div>
                    <input type="hidden" name="pension_start_age" value="<?= esc($income['pension_start_age'] ?? ($profile['retirement_age'] ?? 67)) ?>">
                </div>
            </div>
        </div>

        <div class="col-lg-6" id="partner-income-col" <?= $hasPartner ? '' : 'style="display:none"' ?>>
            <div class="card person-card person-card-partner h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3"><i class="bi bi-person-heart"></i> <?= esc($partnerName) ?></h2>

                    <div class="mb-3">
                        <label for="partner_other_income" class="form-label">Loon per maand</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="partner_other_income" name="partner_other_income" value="<?= esc($partnerOther) ?>">
                        </div>
                        <small class="text-muted">Netto loon of freelance. Dit is het hoofdinkomen en stopt niet automatisch bij AOW.</small>
                    </div>

                    <label class="form-label">Soort uitkering tot AOW</label>
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="partner_benefit_type" id="partner_type_wia" value="wia" <?= $partnerType === 'wia' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-danger" for="partner_type_wia">WIA</label>
                        <input type="radio" class="btn-check" name="partner_benefit_type" id="partner_type_other" value="other" <?= $partnerType === 'other' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-danger" for="partner_type_other">Andere uitkering</label>
                        <input type="radio" class="btn-check" name="partner_benefit_type" id="partner_type_none" value="none" <?= $partnerType === 'none' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-danger" for="partner_type_none">Geen</label>
                    </div>

                    <div class="mb-3" id="partner_benefit_wrap">
                        <label for="wia_wife" class="form-label" id="partner_benefit_label">Uitkering netto per maand</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="wia_wife" name="wia_wife" value="<?= esc($income['wia_wife'] ?? 0) ?>">
                        </div>
                        <small class="text-muted">Stopt automatisch wanneer de AOW van <?= esc($partnerName) ?> ingaat; WIA wordt jaarlijks geïndexeerd.</small>
                    </div>

                    <div class="mb-3">
                        <label for="aow_future" class="form-label">AOW netto per maand</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="aow_future" name="aow_future" value="<?= esc($income['aow_future'] ?? 0) ?>">
                        </div>
                        <small class="text-muted">Huidig nettobedrag; groeit jaarlijks mee met de indexatie.</small>
                    </div>
                    <div class="mb-3">
                        <label for="partner_aow_start_age" class="form-label">AOW start op leeftijd</label>
                        <input type="number" min="60" max="75" class="form-control" id="partner_aow_start_age" name="partner_aow_start_age" value="<?= esc($partnerAowAge) ?>">
                        <small class="text-muted">Telt mee vanaf deze leeftijd van <?= esc($partnerName) ?> (nu <?= (int) $partnerAowAge ?>), dan al geïndexeerd vanaf nu.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="minimum_monthly_income" class="form-label">Minimum netto per maand (waarschuwing)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" step="0.01" class="form-control" id="minimum_monthly_income" name="minimum_monthly_income" value="<?= esc($income['minimum_monthly_income'] ?? 0) ?>">
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Opslaan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const partnerCol = document.getElementById('partner-income-col');
    const hasPartner = document.getElementById('has_partner');
    const ownIncome = document.getElementById('own_income');
    const partnerIncome = document.getElementById('wia_wife');

    function togglePartner() {
        partnerCol.style.display = hasPartner.checked ? '' : 'none';
    }
    function toggleOwnAmount() {
        const none = document.getElementById('own_type_none').checked;
        document.getElementById('own_benefit_wrap').style.display = none ? 'none' : '';
        document.getElementById('own_benefit_label').textContent = document.getElementById('own_type_wia').checked
            ? 'WIA netto per maand' : 'Uitkering netto per maand';
        if (none) ownIncome.value = 0;
    }
    function togglePartnerAmount() {
        const none = document.getElementById('partner_type_none').checked;
        document.getElementById('partner_benefit_wrap').style.display = none ? 'none' : '';
        document.getElementById('partner_benefit_label').textContent = document.getElementById('partner_type_wia').checked
            ? 'WIA netto per maand' : 'Uitkering netto per maand';
        if (none) partnerIncome.value = 0;
    }

    hasPartner.addEventListener('change', togglePartner);
    document.querySelectorAll('input[name="own_benefit_type"]').forEach(el => el.addEventListener('change', toggleOwnAmount));
    document.querySelectorAll('input[name="partner_benefit_type"]').forEach(el => el.addEventListener('change', togglePartnerAmount));
    togglePartner();
    toggleOwnAmount();
    togglePartnerAmount();
});
</script>
<?= $this->endSection() ?>
