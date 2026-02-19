<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-person"></i> Profiel</h1>
    <p class="text-muted">Beheer je persoonlijke gegevens</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="/profile/update" method="post">
                    <?= csrf_field() ?>

                    <h5 class="card-title mb-3"><i class="bi bi-person-fill"></i> Persoonlijke Gegevens</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Voornaam</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?= esc($profile['first_name'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Achternaam</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?= esc($profile['last_name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Geboortedatum</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                   value="<?= esc($profile['date_of_birth'] ?? '') ?>">
                            <small class="text-muted">Voor leeftijdsberekeningen</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="retirement_age" class="form-label">Gewenste Pensioenleeftijd</label>
                            <input type="number" class="form-control" id="retirement_age" name="retirement_age" 
                                   value="<?= esc($profile['retirement_age'] ?? 67) ?>" min="55" max="75">
                            <small class="text-muted">Standaard: 67 jaar</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="card-title mb-3"><i class="bi bi-person-hearts"></i> Partner Gegevens</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="partner_name" class="form-label">Partner Naam</label>
                            <input type="text" class="form-control" id="partner_name" name="partner_name" 
                                   value="<?= esc($profile['partner_name'] ?? '') ?>" 
                                   placeholder="bijv. Angela">
                            <small class="text-muted">Wordt gebruikt i.p.v. "vrouw" in de app</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="partner_date_of_birth" class="form-label">Partner Geboortedatum</label>
                            <input type="date" class="form-control" id="partner_date_of_birth" name="partner_date_of_birth" 
                                   value="<?= esc($profile['partner_date_of_birth'] ?? '') ?>">
                            <small class="text-muted">Voor AOW berekeningen</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="partner_retirement_age" class="form-label">Partner Pensioenleeftijd</label>
                            <input type="number" class="form-control" id="partner_retirement_age" name="partner_retirement_age" 
                                   value="<?= esc($profile['partner_retirement_age'] ?? 67) ?>" min="55" max="75">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="card-title mb-3"><i class="bi bi-globe"></i> Emigratie Gegevens</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emigration_date" class="form-label">Emigratiedatum naar Italië</label>
                            <input type="date" class="form-control" id="emigration_date" name="emigration_date" 
                                   value="<?= esc($profile['emigration_date'] ?? '') ?>">
                            <small class="text-muted">Belangrijk voor AOW-berekening</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="card-title mb-3"><i class="bi bi-gear"></i> Overige Instellingen</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Telefoon</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="<?= esc($profile['phone'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="language" class="form-label">Taal</label>
                            <select class="form-select" id="language" name="language">
                                <option value="nl" <?= ($profile['language'] ?? 'nl') == 'nl' ? 'selected' : '' ?>>Nederlands</option>
                                <option value="it" <?= ($profile['language'] ?? 'nl') == 'it' ? 'selected' : '' ?>>Italiano</option>
                                <option value="en" <?= ($profile['language'] ?? 'nl') == 'en' ? 'selected' : '' ?>>English</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Opslaan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5>Account Informatie</h5>
                <p><strong>Gebruikersnaam:</strong> <?= esc(session()->get('username')) ?></p>
                <p><strong>Email:</strong> <?= esc(session()->get('email')) ?></p>
                <p><strong>Rol:</strong> <?= ucfirst(session()->get('role')) ?></p>
            </div>
        </div>

        <?php if (!empty($profile['date_of_birth'])): ?>
        <div class="card mt-3">
            <div class="card-body">
                <h5><i class="bi bi-calendar-event"></i> Leeftijdsinfo</h5>
                <?php
                $birthDate = new DateTime($profile['date_of_birth']);
                $today = new DateTime();
                $age = $today->diff($birthDate)->y;
                $retirementAge = $profile['retirement_age'] ?? 67;
                $yearsToRetirement = max(0, $retirementAge - $age);
                ?>
                <p><strong>Huidige leeftijd:</strong> <?= $age ?> jaar</p>
                <p><strong>Jaren tot pensioen:</strong> <?= $yearsToRetirement ?> jaar</p>
                
                <?php if (!empty($profile['partner_date_of_birth'])): ?>
                <?php
                $partnerBirthDate = new DateTime($profile['partner_date_of_birth']);
                $partnerAge = $today->diff($partnerBirthDate)->y;
                $partnerName = $profile['partner_name'] ?? 'Partner';
                ?>
                <hr>
                <p><strong><?= esc($partnerName) ?> leeftijd:</strong> <?= $partnerAge ?> jaar</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($profile['emigration_date']) && (!empty($profile['partner_date_of_birth']) || !empty($profile['date_of_birth']))): ?>
        <div class="card mt-3 border-info">
            <div class="card-body">
                <h5><i class="bi bi-percent"></i> AOW Percentages</h5>
                <small class="text-muted">Op basis van emigratiedatum: <?= date('d-m-Y', strtotime($profile['emigration_date'])) ?></small>
                
                <?php if (!empty($profile['partner_date_of_birth'])): ?>
                <hr class="my-3">
                <h6><?= esc($profile['partner_name'] ?? 'Partner') ?> AOW</h6>
                <?php
                $partnerAowPercentage = calculate_AOW_percentage(
                    $profile['emigration_date'],
                    $profile['partner_date_of_birth'],
                    $profile['partner_retirement_age'] ?? 67
                );
                ?>
                <div class="progress mb-2" style="height: 25px;">
                    <div class="progress-bar <?= $partnerAowPercentage >= 80 ? 'bg-success' : ($partnerAowPercentage >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                         role="progressbar" 
                         style="width: <?= $partnerAowPercentage ?>%;"
                         aria-valuenow="<?= $partnerAowPercentage ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?= number_format($partnerAowPercentage, 1) ?>%
                    </div>
                </div>
                <small class="text-muted">
                    AOW-rechten opgebouwd van 15 tot <?= $profile['partner_retirement_age'] ?? 67 ?> jaar
                </small>
                <?php endif; ?>
                
                <?php if (!empty($profile['date_of_birth'])): ?>
                <hr class="my-3">
                <h6>Jouw AOW</h6>
                <?php
                $ownAowPercentage = calculate_AOW_percentage(
                    $profile['emigration_date'],
                    $profile['date_of_birth'],
                    $profile['retirement_age'] ?? 67
                );
                ?>
                <div class="progress mb-2" style="height: 25px;">
                    <div class="progress-bar <?= $ownAowPercentage >= 80 ? 'bg-success' : ($ownAowPercentage >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                         role="progressbar" 
                         style="width: <?= $ownAowPercentage ?>%;"
                         aria-valuenow="<?= $ownAowPercentage ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?= number_format($ownAowPercentage, 1) ?>%
                    </div>
                </div>
                <small class="text-muted">
                    AOW-rechten opgebouwd van 15 tot <?= $profile['retirement_age'] ?? 67 ?> jaar
                </small>
                <?php endif; ?>
                
                <hr class="my-3">
                <small class="text-info">
                    <i class="bi bi-info-circle"></i> 
                    Emigreren vóór pensioenleeftijd verlaagt de AOW. Deze percentages zijn verwerkt in alle dashboard berekeningen.
                </small>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
