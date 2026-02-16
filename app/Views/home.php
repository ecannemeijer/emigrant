<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mb-4">
                <i class="bi bi-geo-alt-fill text-success"></i> 
                Emigratie Italië Calculator
            </h1>
            <p class="lead mb-4">
                Bereken je financiële situatie voor emigratie naar Italië. 
                Inclusief woningverkoop, vermogensberekening, maandelijkse lasten en B&B module.
            </p>
            
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="/login" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-box-arrow-in-right"></i> Inloggen
                </a>
                <a href="/register" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="bi bi-person-plus"></i> Registreren
                </a>
            </div>

            <div class="row mt-5">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="bi bi-calculator display-4 text-primary"></i>
                            <h5 class="card-title mt-3">Complete Berekeningen</h5>
                            <p class="card-text">Alles doorrekenen: van woningverkoop tot maandelijkse lasten</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="bi bi-shop display-4 text-primary"></i>
                            <h5 class="card-title mt-3">B&B Module</h5>
                            <p class="card-text">Bereken de rendabiliteit van je B&B inclusief break-even analyse</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="bi bi-graph-up display-4 text-primary"></i>
                            <h5 class="card-title mt-3">Scenario's & Grafieken</h5>
                            <p class="card-text">Sla verschillende scenario's op en visualiseer je financiën</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
