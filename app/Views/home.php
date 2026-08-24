<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="landing mb-5">
    <h1 class="display-5 mb-3">Emigreren naar Italië, helder doorgerekend</h1>
    <p class="lead mb-4">
        Van woningverkoop in Nederland tot AOW, forfettario, IMU en B&amp;B-rendement.
        Eén dashboard met cashflow, vermogen en een realistische meerjarenprojectie.
    </p>
    <a href="/login" class="btn btn-light btn-lg me-2">Inloggen</a>
    <a href="/register" class="btn btn-outline-light btn-lg">Account maken</a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5>AOW &amp; pensioen</h5>
                <p class="text-muted mb-0">2% per verzekerd jaar (SVB), vrijwillige opbouw en leeftijdspoorten tot pensioen.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5>B&amp;B forfettario</h5>
                <p class="text-muted mb-0">Omzet, ontbijt, commissie, 67% coefficiente en starttarief eerste vijf jaar.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5>Scenario's</h5>
                <p class="text-muted mb-0">Bewaar varianten, vergelijk ze en laad een scenario terug als live situatie.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
